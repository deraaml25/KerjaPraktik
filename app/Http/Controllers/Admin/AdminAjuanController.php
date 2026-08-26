<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ajuan;
use App\Models\ChecklistAjuan;
use App\Models\MilestoneTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminAjuanController extends Controller
{
    /**
     * Dashboard List Ajuan
     */
    public function index()
    {
        // Hanya tampilkan ajuan yang sudah di-submit ke atas (bukan draft)
        $ajuans = Ajuan::where('status', '!=', 'draft')
            ->with(['desa', 'jenisLayanan', 'pesertas.perangkatDesa'])
            ->orderBy('tgl_diajukan', 'desc')
            ->get();

        return view('admin.ajuan.index', compact('ajuans'));
    }

    /**
     * Split-Screen Verification View
     */
    public function show(Ajuan $ajuan)
    {
        // Pastikan bukan draft
        if ($ajuan->status === 'draft') {
            abort(404, 'Ajuan belum disubmit oleh desa.');
        }

        $ajuan->load(['desa', 'jenisLayanan', 'pesertas.perangkatDesa', 'checklistAjuans.templateChecklist', 'milestoneTrackings']);

        $dokumenList = $ajuan->checklistAjuans->sortBy('templateChecklist.urutan');

        $tahapAktif = match ($ajuan->posisi_surat) {
            'Pegawai' => 2,
            'Kabid PDPD' => 4,
            'Sekretaris Dinas' => 5,
            'Kepala Dinas' => 6,
            'Asisten Setda / Sekda' => 8,
            'Bupati' => 9,
            'TU Umum Setda' => 10,
            'Dinpermasdes' => 11,
            'Selesai (Surat Terbit)' => 12,
            default => 1,
        };

        $nextPosisi = match ($ajuan->posisi_surat) {
            'Pegawai' => 'Kabid PDPD',
            'Kabid PDPD' => 'Sekretaris Dinas',
            'Sekretaris Dinas' => 'Kepala Dinas',
            'Kepala Dinas' => 'Asisten Setda / Sekda',
            'Asisten Setda / Sekda' => 'Bupati',
            'Bupati' => 'TU Umum Setda',
            'TU Umum Setda' => 'Dinpermasdes',
            'Dinpermasdes' => 'Selesai (Surat Terbit)',
            default => 'Pegawai',
        };

        return view('admin.ajuan.show', compact('ajuan', 'dokumenList', 'tahapAktif', 'nextPosisi'));
    }

    /**
     * Verifikasi Granular (Valid/Tolak per dokumen)
     */
    public function verifyDokumen(Request $request, Ajuan $ajuan, ChecklistAjuan $checklistAjuan)
    {
        $request->validate([
            'status' => 'required|in:menunggu,valid,kurang,tidak_sesuai',
            'catatan' => 'nullable|string',
        ]);

        Log::info("verifyDokumen received for ajuan {$ajuan->id}, checklist {$checklistAjuan->id} with status: ".$request->status);

        $checklistAjuan->update([
            'status' => $request->status,
            'catatan' => $request->catatan,
        ]);

        // Jika mengubah status menjadi ditolak (kurang/tidak sesuai), otomatis ubah status Ajuan
        if (in_array($request->status, ['kurang', 'tidak_sesuai']) && $ajuan->status !== 'direvisi') {
            $ajuan->update(['status' => 'direvisi']);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $checklistAjuan->status,
                'message' => 'Status dokumen berhasil diperbarui!',
            ]);
        }

        return back()->with('success', 'Status dokumen berhasil diperbarui!');
    }

    /**
     * Verifikasi Bulk (Simpan semua centang sekaligus)
     */
    public function verifyDokumenBulk(Request $request, Ajuan $ajuan)
    {
        $request->validate([
            'status' => 'nullable|array',
            'status.*' => 'in:valid,lengkap',
        ]);

        $statuses = $request->input('status', []);

        foreach ($ajuan->checklistAjuans as $checklist) {
            $status = isset($statuses[$checklist->id]) ? 'valid' : 'menunggu';
            $checklist->update(['status' => $status]);
        }

        return back()->with('success', 'Semua centang dokumen berhasil disimpan!');
    }

    /**
     * Disposisi Surat
     */
    public function updateDisposisi(Request $request, Ajuan $ajuan)
    {
        $request->validate([
            'posisi_baru' => 'required|string',
            'status_ajuan_baru' => 'nullable|in:submitted,direvisi,diproses,selesai,ditolak',
            'catatan_milestone' => 'nullable|string',
        ]);

        $tahapLama = match ($ajuan->posisi_surat) {
            'Pegawai' => 2,
            'Kabid PDPD' => 4,
            'Sekretaris Dinas' => 5,
            'Kepala Dinas' => 6,
            'Asisten Setda / Sekda' => 8,
            'Bupati' => 9,
            'TU Umum Setda' => 10,
            'Dinpermasdes' => 11,
            'Selesai (Surat Terbit)' => 12,
            default => 1,
        };

        // Selesaikan tahap lama (jika ada yang belum selesai)
        MilestoneTracking::where('ajuan_id', $ajuan->id)
            ->where('tahap', $tahapLama)
            ->whereNull('tgl_selesai')
            ->update([
                'tgl_selesai' => now(),
                'catatan' => $request->status_ajuan_baru === 'direvisi' ? 'Berkas dikembalikan ke desa.' : 'Selesai diproses dan diteruskan.',
                'updated_by' => auth()->id(),
            ]);

        $ajuan->update([
            'posisi_surat' => $request->posisi_baru,
            'status' => $request->status_ajuan_baru ?? $ajuan->status,
        ]);

        $tahapBaru = match ($request->posisi_baru) {
            'Pegawai' => 2,
            'Kabid PDPD' => 4,
            'Sekretaris Dinas' => 5,
            'Kepala Dinas' => 6,
            'Asisten Setda / Sekda' => 8,
            'Bupati' => 9,
            'TU Umum Setda' => 10,
            'Dinpermasdes' => 11,
            'Selesai (Surat Terbit)' => 12,
            default => 1,
        };

        // Mulai tahap baru
        MilestoneTracking::create([
            'ajuan_id' => $ajuan->id,
            'tahap' => $tahapBaru,
            'tgl_mulai' => now(),
            'tgl_selesai' => $tahapBaru == 10 ? now() : null,
            'catatan' => $request->status_ajuan_baru === 'direvisi' ? 'Menunggu revisi dari desa.' : 'Menunggu pengecekan.',
            'updated_by' => auth()->id(),
        ]);

        return back()
            ->with('success', 'Ajuan berhasil diteruskan ke: ' . $request->posisi_baru)
            ->with('posisi_baru', $request->posisi_baru);
    }

    /**
     * Update Catatan Admin untuk Keseluruhan Berkas
     */
    public function updateCatatanAdmin(Request $request, Ajuan $ajuan)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string',
        ]);

        $ajuan->update([
            'catatan_admin' => $request->catatan_admin,
        ]);

        return back()->with('success', 'Catatan admin berhasil disimpan!');
    }

    /**
     * Print Checklist Syarat
     */
    public function printSyarat(Ajuan $ajuan)
    {
        $ajuan->load(['desa', 'jenisLayanan', 'pesertas.perangkatDesa', 'checklistAjuans.templateChecklist']);
        $dokumenList = $ajuan->checklistAjuans->sortBy('templateChecklist.urutan');

        return view('admin.ajuan.print-syarat', compact('ajuan', 'dokumenList'));
    }
}

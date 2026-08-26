<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChecklistPjKades;
use App\Models\PerangkatDesa;
use App\Models\PjKades;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PjKadesController extends Controller
{
    public function index()
    {
        $pjkades = PjKades::withoutGlobalScopes()
            ->with(['desa', 'alasanPemberhentian', 'checklists'])
            ->where('status', '!=', 'draft')
            ->latest()
            ->paginate(15);

        return view('admin.pjkades.index', compact('pjkades'));
    }

    public function show($id)
    {
        $pjkades = PjKades::withoutGlobalScopes()
            ->with(['desa.kecamatan', 'alasanPemberhentian', 'checklists'])
            ->findOrFail($id);

        return view('admin.pjkades.show', compact('pjkades'));
    }

    /**
     * Verifikasi dokumen checklist individual oleh Admin Dinpermasdes
     */
    public function verifyChecklist(Request $request, $id, $checklistId)
    {
        $pjkades = PjKades::withoutGlobalScopes()->findOrFail($id);
        $checklist = ChecklistPjKades::where('pj_kades_id', $pjkades->id)->findOrFail($checklistId);

        $request->validate([
            'status_verifikasi' => ['required', 'in:valid,tidak_sesuai,menunggu'],
        ]);

        $checklist->update([
            'status_verifikasi' => $request->status_verifikasi,
            'catatan_revisi' => null, // We use global admin notes now
        ]);

        $statusText = $request->status_verifikasi === 'valid' ? 'Valid' : 'Tidak Sesuai';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $checklist->status_verifikasi,
                'message' => "Dokumen {$checklist->nama_dokumen} ditandai sebagai {$statusText}.",
            ]);
        }

        return back()->with('success', "Dokumen {$checklist->nama_dokumen} ditandai sebagai {$statusText}.");
    }

    /**
     * Verifikasi Bulk (Simpan semua centang sekaligus)
     */
    public function verifyChecklistBulk(Request $request, $id)
    {
        $pjkades = PjKades::withoutGlobalScopes()->findOrFail($id);

        $request->validate([
            'status' => 'nullable|array',
            'status.*' => 'in:valid',
        ]);

        $statuses = $request->input('status', []);

        foreach ($pjkades->checklists as $checklist) {
            $status = isset($statuses[$checklist->id]) ? 'valid' : 'menunggu';
            $checklist->update(['status_verifikasi' => $status]);
        }

        return back()->with('success', 'Semua centang dokumen berhasil disimpan!');
    }

    public function updateCatatanAdmin(Request $request, $id)
    {
        $pjkades = PjKades::withoutGlobalScopes()->findOrFail($id);

        $request->validate([
            'catatan_admin' => 'nullable|string',
        ]);

        // Jika metode online, otomatis direvisi jika ada catatan
        $status = $pjkades->status;
        if ($pjkades->metode === 'online' && $request->filled('catatan_admin') && in_array($pjkades->status, ['submitted', 'direvisi'])) {
            $status = 'direvisi';
        }

        $pjkades->update([
            'catatan_admin' => $request->catatan_admin,
            'status' => $status,
        ]);

        return back()->with('success', 'Catatan evaluasi kelengkapan berhasil disimpan.');
    }

    public function updateDisposisi(Request $request, $id)
    {
        $pjkades = PjKades::withoutGlobalScopes()->findOrFail($id);

        $request->validate([
            'posisi_surat' => 'required|string',
            'status_baru' => 'nullable|string',
        ]);

        $updateData = [
            'posisi_surat' => $request->posisi_surat,
        ];

        if ($request->filled('status_baru')) {
            $updateData['status'] = $request->status_baru;
        }

        $pjkades->update($updateData);

        return back()
            ->with('success', "Ajuan berhasil diteruskan ke: {$request->posisi_surat}")
            ->with('posisi_baru', $request->posisi_surat);
    }

    /**
     * Penerbitan SK Bupati / Camat & Finalisasi Usulan
     */
    public function generateSk(Request $request, $id)
    {
        $pjkades = PjKades::withoutGlobalScopes()->findOrFail($id);

        if ($pjkades->kategori === 'pj_kades') {
            $request->validate([
                'status_bebas_hukdis' => 'required|in:clean,has_issues',
            ]);

            if ($request->status_bebas_hukdis === 'has_issues') {
                $pjkades->update([
                    'status_bebas_hukdis' => 'has_issues',
                    'status' => 'rejected',
                ]);

                return redirect()->route('admin.pjkades.show', $pjkades)
                    ->with('error', 'PNS sedang menjalani hukuman disiplin. Usulan Pj Kades ditolak.');
            }
        }

        $request->validate([
            'sk_bupati' => 'required|file|mimes:pdf|max:10240',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
        ]);

        $tglMulai = Carbon::parse($request->tgl_mulai);
        $tglSelesai = Carbon::parse($request->tgl_selesai);
        $maxSelesai = $tglMulai->copy()->addYear();

        if ($tglSelesai->greaterThan($maxSelesai)) {
            return back()->withErrors([
                'tgl_selesai' => 'Masa berlaku SK tidak boleh lebih dari 1 (satu) tahun sejak tanggal mulai berlaku.',
            ])->withInput();
        }

        $skPath = $request->file('sk_bupati')->store('pjkades/sk_bupati', 'public');
        $isPj = $pjkades->kategori === 'pj_kades';

        $pjkades->update([
            'status_bebas_hukdis' => $isPj ? 'clean' : 'clean',
            'sk_bupati_path' => $skPath,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'status' => 'approved',
        ]);

        // ── Integrasi Data Master: Sinkronisasi ke perangkat_desas ──
        if ($isPj) {
            // Non-aktifkan Kades lama di desa ini
            PerangkatDesa::where('desa_id', $pjkades->desa_id)
                ->where('jabatan', 'Kepala Desa')
                ->update(['status_aktif' => false]);

            // Set Pj Kades sebagai Kepala Desa (Pj)
            PerangkatDesa::updateOrCreate(
                ['desa_id' => $pjkades->desa_id, 'jabatan' => 'Kepala Desa'],
                [
                    'nama' => $pjkades->nama_pns.' (Pj)',
                    'status_aktif' => true,
                    'tgl_mulai_jabatan' => $request->tgl_mulai,
                    'no_sk_terakhir' => 'SK Bupati Pj Kades #'.$pjkades->id,
                ]
            );
        } else {
            // Plt Kades: update keterangan Plt pada Kades/Sekdes
            $namaPlt = $pjkades->nama_plt ?? 'Sekdes (Plt)';
            PerangkatDesa::where('desa_id', $pjkades->desa_id)
                ->where('jabatan', 'LIKE', '%Sekretaris Desa%')
                ->update([
                    'no_sk_terakhir' => 'SK Plt Kades #'.$pjkades->id,
                ]);
        }

        $labelSK = $isPj ? 'SK Bupati Pj Kades' : 'SK Bupati/Camat Plt Kades';

        return redirect()->route('admin.pjkades.show', $pjkades)
            ->with('success', "{$labelSK} berhasil diterbitkan. Status usulan resmi disetujui.");
    }

    /**
     * Print Checklist Syarat
     */
    public function printSyarat($id)
    {
        $pjkades = PjKades::withoutGlobalScopes()
            ->with(['desa.kecamatan', 'alasanPemberhentian', 'checklists'])
            ->findOrFail($id);

        return view('admin.pjkades.print-syarat', compact('pjkades'));
    }

    public function destroy($id)
    {
        $pjkades = PjKades::withoutGlobalScopes()->findOrFail($id);

        if ($pjkades->folder_path && Storage::disk('public')->exists($pjkades->folder_path)) {
            Storage::disk('public')->deleteDirectory($pjkades->folder_path);
        }

        $pjkades->checklists()->delete();
        $pjkades->delete();

        return redirect()->route('admin.pjkades.index')->with('success', 'Data usulan SK Kades berhasil dihapus oleh Admin.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AjuanBpd;
use App\Models\ChecklistAjuanBpd;
use App\Models\MilestoneAjuanBpd;
use Illuminate\Http\Request;

class AjuanBpdController extends Controller
{
    public function index()
    {
        $ajuans = AjuanBpd::with('desa')->latest()->paginate(10);

        return view('admin.ajuan-bpd.index', compact('ajuans'));
    }

    public function show(AjuanBpd $ajuanBpd)
    {
        $ajuanBpd->load(['desa', 'pesertas.bpd', 'checklists.templateChecklist', 'milestones']);

        return view('admin.ajuan-bpd.show', compact('ajuanBpd'));
    }

    public function verifyChecklist(Request $request, $id, $checklistId)
    {
        $ajuanBpd = AjuanBpd::findOrFail($id);
        $checklist = ChecklistAjuanBpd::where('ajuan_bpd_id', $ajuanBpd->id)->findOrFail($checklistId);

        $request->validate([
            'status' => 'required|in:terverifikasi,ditolak,menunggu_verifikasi',
            'catatan' => 'nullable|string',
        ]);

        $checklist->update([
            'status' => $request->status,
            'catatan' => $request->catatan,
            'updated_by' => auth()->id(),
        ]);

        $statusText = $request->status === 'terverifikasi' ? 'Disetujui' : 'Ditolak';

        return back()->with('success', "Dokumen {$checklist->templateChecklist->nama_dokumen} ditandai sebagai {$statusText}.");
    }

    public function updateCatatanAdmin(Request $request, $id)
    {
        $ajuanBpd = AjuanBpd::findOrFail($id);

        $request->validate([
            'catatan_admin' => 'nullable|string',
        ]);

        $ajuanBpd->update([
            'catatan_admin' => $request->catatan_admin,
            'status' => 'revisi', // or leave it, depending on business logic
        ]);

        MilestoneAjuanBpd::create([
            'ajuan_bpd_id' => $ajuanBpd->id,
            'tahapan' => 'Catatan Revisi Diberikan Admin',
            'status' => 'selesai',
            'tgl_selesai' => now(),
            'catatan' => $request->catatan_admin,
        ]);

        return back()->with('success', 'Catatan evaluasi kelengkapan berhasil disimpan.');
    }

    public function updateDisposisi(Request $request, $id)
    {
        $ajuanBpd = AjuanBpd::findOrFail($id);

        $request->validate([
            'tahapan' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        MilestoneAjuanBpd::create([
            'ajuan_bpd_id' => $ajuanBpd->id,
            'tahapan' => $request->tahapan,
            'status' => 'selesai',
            'tgl_selesai' => now(),
            'catatan' => $request->catatan,
        ]);

        // If it's the final stage (e.g. SK Terbit)
        if (str_contains(strtolower($request->tahapan), 'sk terbit') || str_contains(strtolower($request->tahapan), 'selesai')) {
            $ajuanBpd->update(['status' => 'selesai', 'posisi_surat' => $request->tahapan]);
        } else {
            $ajuanBpd->update(['status' => 'diproses', 'posisi_surat' => $request->tahapan]);
        }

        return back()
            ->with('success', "Ajuan berhasil diteruskan ke: {$request->tahapan}")
            ->with('posisi_baru', $request->tahapan);
    }

    /**
     * Print Checklist Syarat
     */
    public function printSyarat($id)
    {
        $ajuanBpd = AjuanBpd::findOrFail($id);
        $ajuanBpd->load(['desa', 'pesertas.bpd', 'checklists.templateChecklist', 'milestones']);

        return view('admin.ajuan-bpd.print-syarat', compact('ajuanBpd'));
    }

    public function destroy($id)
    {
        $ajuanBpd = AjuanBpd::findOrFail($id);
        $ajuanBpd->delete();

        return redirect()->route('admin.ajuan-bpd.index')->with('success', 'Data ajuan BPD berhasil dihapus.');
    }
}

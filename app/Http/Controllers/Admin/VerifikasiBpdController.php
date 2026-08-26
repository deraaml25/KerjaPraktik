<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bpd;
use Illuminate\Http\Request;

class VerifikasiBpdController extends Controller
{
    public function index(Request $request)
    {
        // Get all pending requests
        $pending = Bpd::with('desa')
            ->whereIn('status_verifikasi', ['pending_tambah', 'pending_ubah', 'pending_nonaktif', 'pending_aktif'])
            ->latest()
            ->paginate(10, ['*'], 'pending_page');

        // Get all approved BPD (data master)
        $query = Bpd::with('desa.kecamatan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('jabatan', 'like', "%{$search}%")
                ->orWhereHas('desa', function ($q) use ($search) {
                    $q->where('nama_desa', 'like', "%{$search}%");
                });
        }

        $bpds = $query->whereNotIn('status_verifikasi', ['pending_tambah'])
            ->orderBy('desa_id')
            ->orderByRaw("CASE WHEN jabatan = 'Ketua BPD' THEN 0 ELSE 1 END")
            ->paginate(15, ['*'], 'bpd_page');

        return view('admin.verifikasi_bpd.index', compact('pending', 'bpds'));
    }

    public function approve(Request $request, $id)
    {
        $bpd = Bpd::findOrFail($id);

        if ($bpd->status_verifikasi === 'pending_tambah') {
            $bpd->status_aktif = true;
            $bpd->status_verifikasi = 'disetujui';
        } elseif ($bpd->status_verifikasi === 'pending_ubah') {
            $draft = $bpd->draft_perubahan;
            if ($draft) {
                $bpd->nama = $draft['nama'] ?? $bpd->nama;
                $bpd->jabatan = $draft['jabatan'] ?? $bpd->jabatan;
                $bpd->no_sk_terakhir = $draft['no_sk_terakhir'] ?? $bpd->no_sk_terakhir;
                $bpd->tgl_mulai_jabatan = $draft['tgl_mulai_jabatan'] ?? $bpd->tgl_mulai_jabatan;
                if (isset($draft['file_sk'])) {
                    $bpd->file_sk = $draft['file_sk'];
                }
            }
            $bpd->draft_perubahan = null;
            $bpd->status_verifikasi = 'disetujui';
        } elseif ($bpd->status_verifikasi === 'pending_nonaktif') {
            $bpd->status_aktif = false;
            $bpd->status_verifikasi = 'disetujui';
        } elseif ($bpd->status_verifikasi === 'pending_aktif') {
            $bpd->status_aktif = true;
            $bpd->status_verifikasi = 'disetujui';
        }

        $bpd->save();

        return back()->with('success', 'Usulan BPD disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $bpd = Bpd::findOrFail($id);

        if ($bpd->status_verifikasi === 'pending_tambah') {
            // Delete if it was a new record that got rejected
            $bpd->delete();
        } else {
            // Just clear draft and set back to approved (so it reverts to active state)
            $bpd->draft_perubahan = null;
            $bpd->status_verifikasi = 'disetujui';
            $bpd->save();
        }

        return back()->with('success', 'Usulan BPD ditolak.');
    }
}

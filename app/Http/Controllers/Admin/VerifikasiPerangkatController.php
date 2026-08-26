<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerangkatDesa;
use Illuminate\Http\Request;

class VerifikasiPerangkatController extends Controller
{
    public function index(Request $request)
    {
        // Get all pending requests
        $pending = PerangkatDesa::with('desa')
            ->whereIn('status_verifikasi', ['pending_tambah', 'pending_ubah', 'pending_nonaktif', 'pending_aktif'])
            ->latest()
            ->paginate(10, ['*'], 'pending_page');

        // Get all approved devices (data master)
        $query = PerangkatDesa::with('desa.kecamatan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhereHas('desa', function ($qDesa) use ($search) {
                      $qDesa->where('nama_desa', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status_aktif', $request->status);
        }

        $perangkats = $query->whereNotIn('status_verifikasi', ['pending_tambah'])
            ->orderBy('desa_id')
            ->orderByRaw("CASE WHEN jabatan = 'Kepala Desa' THEN 0 ELSE 1 END")
            ->paginate(15, ['*'], 'perangkat_page');

        $totalAktif = PerangkatDesa::whereNotIn('status_verifikasi', ['pending_tambah'])
            ->where('status_aktif', true)->count();
        $totalNonaktif = PerangkatDesa::whereNotIn('status_verifikasi', ['pending_tambah'])
            ->where('status_aktif', false)->count();

        return view('admin.verifikasi_perangkat.index', compact('pending', 'perangkats', 'totalAktif', 'totalNonaktif'));
    }

    public function approve(Request $request, $id)
    {
        $perangkat = PerangkatDesa::findOrFail($id);

        if ($perangkat->status_verifikasi === 'pending_tambah') {
            $perangkat->status_aktif = true;
            $perangkat->status_verifikasi = 'disetujui';
        } elseif ($perangkat->status_verifikasi === 'pending_ubah') {
            $draft = $perangkat->draft_perubahan;
            if ($draft) {
                $perangkat->nama = $draft['nama'] ?? $perangkat->nama;
                $perangkat->jabatan = $draft['jabatan'] ?? $perangkat->jabatan;
                $perangkat->no_sk_terakhir = $draft['no_sk_terakhir'] ?? $perangkat->no_sk_terakhir;
                $perangkat->tgl_mulai_jabatan = $draft['tgl_mulai_jabatan'] ?? $perangkat->tgl_mulai_jabatan;
                
                if (isset($draft['file_sk'])) {
                    $perangkat->file_sk = $draft['file_sk'];
                }
            }
            $perangkat->draft_perubahan = null;
            $perangkat->status_verifikasi = 'disetujui';
        } elseif ($perangkat->status_verifikasi === 'pending_nonaktif') {
            $perangkat->status_aktif = false;
            $perangkat->status_verifikasi = 'disetujui';
        } elseif ($perangkat->status_verifikasi === 'pending_aktif') {
            $perangkat->status_aktif = true;
            $perangkat->status_verifikasi = 'disetujui';
        }

        $perangkat->save();

        return back()->with('success', 'Usulan perangkat desa disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $perangkat = PerangkatDesa::findOrFail($id);

        if ($perangkat->status_verifikasi === 'pending_tambah') {
            // Delete if it was a new record that got rejected
            $perangkat->delete();
        } else {
            // Just clear draft and set back to approved (so it reverts to active state)
            $perangkat->draft_perubahan = null;
            $perangkat->status_verifikasi = 'disetujui';
            $perangkat->save();
        }

        return back()->with('success', 'Usulan perangkat desa ditolak.');
    }
}

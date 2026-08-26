<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Desa\PerangkatRequest;
use App\Models\PerangkatDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerangkatController extends Controller
{
    public function index(Request $request)
    {
        // TenantDesaScope memastikan hanya mengambil perangkat sesuai desa_id user login
        $query = PerangkatDesa::orderByRaw("CASE 
                WHEN jabatan = 'Kepala Desa' THEN 0
                WHEN jabatan = 'Sekretaris Desa' THEN 1
                WHEN jabatan LIKE 'Kasi%' THEN 2
                WHEN jabatan LIKE 'Kaur%' THEN 3
                WHEN jabatan LIKE 'Kadus%' THEN 4
                WHEN jabatan LIKE '%BPD%' THEN 5
                WHEN jabatan LIKE '%Perangkat%' THEN 6
                WHEN jabatan LIKE '%Non Perangkat%' THEN 7
                ELSE 8
            END")
            ->orderBy('jabatan')
            ->orderBy('nama');

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', '%'.$request->search.'%')
                ->orWhere('jabatan', 'like', '%'.$request->search.'%');
        }

        $perangkat = $query->paginate(15);
        $totalAktif = PerangkatDesa::where('status_aktif', true)->count();

        return view('desa.perangkat.index', compact('perangkat', 'totalAktif'));
    }

    public function create()
    {
        return view('desa.perangkat.create');
    }

    public function store(PerangkatRequest $request)
    {
        $validated = $request->validated();

        $validated['desa_id'] = auth()->user()->desa_id;
        $validated['status_aktif'] = false;
        $validated['status_verifikasi'] = 'pending_tambah';

        if ($request->hasFile('file_sk')) {
            $path = $request->file('file_sk')->store('perangkat_sk', 'public');
            $validated['file_sk'] = $path;
        }

        PerangkatDesa::create($validated);

        return redirect()->route('desa.perangkat.index')
            ->with('success', 'Usulan penambahan perangkat desa berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function edit(PerangkatDesa $perangkat)
    {
        // $perangkat ini sudah ter-filter secara otomatis oleh TenantDesaScope
        // sehingga mereka tidak akan pernah bisa mengedit perangkat desa lain
        return view('desa.perangkat.edit', compact('perangkat'));
    }

    public function update(PerangkatRequest $request, PerangkatDesa $perangkat)
    {
        $validated = $request->validated();

        // Hanya update field administratif.
        // Kita tidak memperbarui desa_id atau id
        
        $draft = [
            'nama' => $validated['nama'],
            'jabatan' => $validated['jabatan'],
            'no_sk_terakhir' => $validated['no_sk_terakhir'],
            'tgl_mulai_jabatan' => $validated['tgl_mulai_jabatan'],
        ];

        if ($request->hasFile('file_sk')) {
            $path = $request->file('file_sk')->store('perangkat_sk', 'public');
            $draft['file_sk'] = $path;
        } elseif ($perangkat->file_sk) {
            // Keep the old file_sk in draft so it is remembered if not uploaded new
            $draft['file_sk'] = $perangkat->file_sk;
        }

        $perangkat->update([
            'status_verifikasi' => 'pending_ubah',
            'draft_perubahan' => $draft,
        ]);

        return redirect()->route('desa.perangkat.index')
            ->with('success', 'Usulan perubahan data perangkat desa berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function destroy(PerangkatDesa $perangkat)
    {
        // Alih-alih hard delete, kita lakukan soft delete flag.
        // Ini menjaga integritas data riwayat jika digunakan untuk pendaftaran dsb.
        $perangkat->update([
            'status_verifikasi' => 'pending_nonaktif',
        ]);

        return redirect()->route('desa.perangkat.index')
            ->with('success', 'Usulan penonaktifan perangkat desa berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function activate(PerangkatDesa $perangkat)
    {
        $perangkat->update([
            'status_verifikasi' => 'pending_aktif',
        ]);

        return redirect()->route('desa.perangkat.index')
            ->with('success', 'Usulan pengaktifan kembali perangkat desa berhasil dikirim dan menunggu verifikasi admin.');
    }
}

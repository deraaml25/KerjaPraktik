<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Desa\BpdRequest;
use App\Models\Bpd;
use Illuminate\Http\Request;

class BpdController extends Controller
{
    public function index(Request $request)
    {
        $query = Bpd::orderByRaw("CASE 
                WHEN jabatan = 'Ketua' THEN 0
                WHEN jabatan = 'Wakil Ketua' THEN 1
                WHEN jabatan = 'Sekretaris' THEN 2
                ELSE 3
            END")
            ->orderBy('jabatan')
            ->orderBy('nama');

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', '%'.$request->search.'%')
                ->orWhere('jabatan', 'like', '%'.$request->search.'%');
        }

        $bpd = $query->paginate(15);
        $totalAktif = Bpd::where('status_aktif', true)->count();

        return view('desa.bpd.index', compact('bpd', 'totalAktif'));
    }

    public function create()
    {
        return view('desa.bpd.create');
    }

    public function store(BpdRequest $request)
    {
        $validated = $request->validated();
        $validated['desa_id'] = auth()->user()->desa_id;
        $validated['status_aktif'] = false;
        $validated['status_verifikasi'] = 'pending_tambah';

        if ($request->hasFile('file_sk')) {
            $path = $request->file('file_sk')->store('bpd_sk', 'public');
            $validated['file_sk'] = $path;
        }

        Bpd::create($validated);

        return redirect()->route('desa.bpd.index')
            ->with('success', 'Usulan penambahan data BPD berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function edit(Bpd $bpd)
    {
        return view('desa.bpd.edit', compact('bpd'));
    }

    public function update(BpdRequest $request, Bpd $bpd)
    {
        $validated = $request->validated();
        $draft = [
            'nama' => $validated['nama'],
            'jabatan' => $validated['jabatan'],
            'no_sk_terakhir' => $validated['no_sk_terakhir'],
            'tgl_mulai_jabatan' => $validated['tgl_mulai_jabatan'],
        ];

        if ($request->hasFile('file_sk')) {
            $path = $request->file('file_sk')->store('bpd_sk', 'public');
            $draft['file_sk'] = $path;
        } elseif ($bpd->file_sk) {
            $draft['file_sk'] = $bpd->file_sk;
        }

        $bpd->update([
            'status_verifikasi' => 'pending_ubah',
            'draft_perubahan' => $draft,
        ]);

        return redirect()->route('desa.bpd.index')
            ->with('success', 'Usulan perubahan data BPD berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function destroy(Bpd $bpd)
    {
        $bpd->update([
            'status_verifikasi' => 'pending_nonaktif',
        ]);

        return redirect()->route('desa.bpd.index')
            ->with('success', 'Usulan penonaktifan BPD berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function activate(Bpd $bpd)
    {
        $bpd->update([
            'status_verifikasi' => 'pending_aktif',
        ]);

        return redirect()->route('desa.bpd.index')
            ->with('success', 'Usulan pengaktifan kembali BPD berhasil dikirim dan menunggu verifikasi admin.');
    }
}

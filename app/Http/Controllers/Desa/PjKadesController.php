<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\AlasanPemberhentian;
use App\Models\ChecklistPjKades;
use App\Models\JenisLayanan;
use App\Models\PerangkatDesa;
use App\Models\PjKades;
use App\Models\TemplateChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PjKadesController extends Controller
{
    public function index()
    {
        $desaId = Auth::user()->desa_id;
        $pjkades = PjKades::withoutGlobalScopes()
            ->with(['alasanPemberhentian', 'checklists'])
            ->where('desa_id', $desaId)
            ->latest()
            ->paginate(15);

        return view('desa.pjkades.index', compact('pjkades'));
    }

    public function create()
    {
        $desaId = Auth::user()->desa_id;

        // Ambil Jenis Layanan SK Kades
        $layananPj = JenisLayanan::where('nama', 'LIKE', '%Pj Kades%')->first();
        $layananPlt = JenisLayanan::where('nama', 'LIKE', '%Plt Kades%')->first();

        // Alasan Pj Kades (Definitif)
        $alasanPj = AlasanPemberhentian::whereIn('nama', [
            'Meninggal Dunia',
            'Permintaan Sendiri',
            'Diberhentikan',
        ])->get();

        // Alasan Plt Kades (Sementara)
        $alasanSementara = AlasanPemberhentian::whereIn('nama', [
            'Pemberhentian Sementara',
        ])->get();

        // Alasan Plt Kades (Cuti)
        $alasanCuti = AlasanPemberhentian::whereIn('nama', [
            'Cuti Sakit',
            'Cuti Tahunan',
            'Cuti Bersalin',
            'Cuti Alasan Penting',
        ])->get();

        // Ambil data Sekretaris Desa aktif
        $sekdes = PerangkatDesa::where('desa_id', $desaId)
            ->where('status_aktif', true)
            ->where('jabatan', 'Sekretaris Desa')
            ->first();

        return view('desa.pjkades.create', compact(
            'layananPj',
            'layananPlt',
            'alasanPj',
            'alasanSementara',
            'alasanCuti',
            'sekdes'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'metode' => ['required', 'in:online,offline'],
            'kategori' => ['required', 'in:pj_kades,plt_sementara,plt_cuti'],
            'alasan_pemberhentian_id' => ['required', 'exists:alasan_pemberhentians,id'],
            'keterangan_cuti' => ['nullable', 'string', 'max:255'],

            // Pj Kades Validation
            'nama_pns' => ['required_if:kategori,pj_kades', 'nullable', 'string', 'max:255'],
            'nip' => ['required_if:kategori,pj_kades', 'nullable', 'string', 'max:30'],
            'pangkat' => ['required_if:kategori,pj_kades', 'nullable', 'string', 'max:100'],

            // Plt Kades Validation
            'nama_plt' => ['required_unless:kategori,pj_kades', 'nullable', 'string', 'max:255'],
            'nip_plt' => ['nullable', 'string', 'max:30'],
            'pangkat_plt' => ['nullable', 'string', 'max:100'],
        ]);

        $desa = Auth::user()->desa;
        $alasan = AlasanPemberhentian::findOrFail($request->alasan_pemberhentian_id);
        $kategori = $request->kategori;
        $isPlt = in_array($kategori, ['plt_sementara', 'plt_cuti']);
        $dbKategori = $isPlt ? 'plt_kades' : 'pj_kades';

        // Tentukan Jenis Layanan
        $jenisLayananNama = ! $isPlt
            ? 'Pj Kades (Pemberhentian Definitif & Penunjukan Pj)'
            : 'Plt Kades (Pemberhentian Sementara / Cuti & Penunjukan Plt)';

        $jenisLayanan = JenisLayanan::where('nama', $jenisLayananNama)->first();

        // No Registrasi SK Kades
        $prefix = ! $isPlt ? 'PJK' : 'PLT';
        $noRegistrasi = $prefix.'/'.now()->format('Y').'/'.now()->format('m').'/'.str_pad(PjKades::count() + 1, 4, '0', STR_PAD_LEFT);

        $kecamatanSlug = Str::slug($desa->kecamatan->nama_kecamatan ?? 'kecamatan');
        $desaSlug = Str::slug($desa->nama_desa);
        $folderPath = "dokumen/{$kecamatanSlug}/{$desaSlug}/sk-kades-{$dbKategori}";

        $pjKades = PjKades::create([
            'desa_id' => $desa->id,
            'kategori' => $dbKategori,
            'alasan_pemberhentian_id' => $alasan->id,
            'alasan_nama' => $alasan->nama,
            'keterangan_cuti' => $kategori === 'plt_cuti' && $alasan->nama === 'Cuti Alasan Penting' ? $request->keterangan_cuti : null,
            'no_registrasi' => $noRegistrasi,
            'nama_pns' => ! $isPlt ? $request->nama_pns : null,
            'nip' => ! $isPlt ? $request->nip : null,
            'pangkat' => ! $isPlt ? $request->pangkat : null,
            'nama_plt' => $isPlt ? $request->nama_plt : null,
            'nip_plt' => $isPlt ? $request->nip_plt : null,
            'pangkat_plt' => $isPlt ? $request->pangkat_plt : null,
            'folder_path' => $folderPath,
            'tgl_diajukan' => now()->toDateString(),
            'status_bebas_hukdis' => 'pending',
            'status' => 'draft',
            'metode' => $request->metode,
        ]);

        // Ambil ID Alasan Pemberhentian dan ID Pengangkatan yang bersesuaian
        $alasanIds = [$alasan->id];

        if (! $isPlt) {
            $alasanPengangkatan = AlasanPemberhentian::where('nama', 'Pengangkatan Pj Kades')->first();
            if ($alasanPengangkatan) {
                $alasanIds[] = $alasanPengangkatan->id;
            }
        } else {
            $alasanPengangkatan = AlasanPemberhentian::where('nama', 'Pengangkatan Plt Kades')->first();
            if ($alasanPengangkatan) {
                $alasanIds[] = $alasanPengangkatan->id;
            }
        }

        // Generate Checklist Items dari TemplateChecklist
        $alasanIdsString = implode(',', $alasanIds);
        $templates = TemplateChecklist::where('jenis_layanan_id', $jenisLayanan->id ?? 0)
            ->whereIn('alasan_pemberhentian_id', $alasanIds)
            ->orderByRaw("FIELD(alasan_pemberhentian_id, {$alasanIdsString})")
            ->orderBy('urutan')
            ->get();

        foreach ($templates as $tmpl) {
            ChecklistPjKades::create([
                'pj_kades_id' => $pjKades->id,
                'template_checklist_id' => $tmpl->id,
                'nama_dokumen' => $tmpl->nama_dokumen,
                'wajib' => $tmpl->wajib,
                'urutan' => $tmpl->urutan,
                'status_verifikasi' => 'pending',
            ]);
        }

        return redirect()->route('desa.pjkades.show', $pjKades->id)
            ->with('success', 'Usulan SK Kades berhasil dibuat. Silakan lengkapi unggahan dokumen persyaratan di bawah ini.');
    }

    public function show($id)
    {
        $desaId = Auth::user()->desa_id;
        $pjkades = PjKades::withoutGlobalScopes()
            ->with(['alasanPemberhentian', 'checklists'])
            ->where('desa_id', $desaId)
            ->findOrFail($id);

        $tahapAktif = match ($pjkades->posisi_surat) {
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

        if ($pjkades->status === 'draft' || $pjkades->status === 'rejected') {
            $tahapAktif = 0;
        }

        return view('desa.pjkades.show', compact('pjkades', 'tahapAktif'));
    }

    public function uploadChecklist(Request $request, $id, $checklistId)
    {
        $desaId = Auth::user()->desa_id;
        $pjKades = PjKades::withoutGlobalScopes()
            ->where('desa_id', $desaId)
            ->findOrFail($id);

        $checklist = ChecklistPjKades::where('pj_kades_id', $pjKades->id)
            ->findOrFail($checklistId);

        $request->validate([
            'file_dokumen' => ['required', 'file', 'max:10240'],
        ], [
            'file_dokumen.required' => 'File dokumen wajib dipilih.',
            'file_dokumen.max' => 'Ukuran file tidak boleh melebihi 10MB.',
        ]);

        // Manual extension check - bypass PHP MIME detection bug
        $fileExt = strtolower($request->file('file_dokumen')->getClientOriginalExtension());
        if (! in_array($fileExt, ['pdf', 'jpg', 'jpeg', 'png'])) {
            return back()->withErrors(['file_dokumen' => 'Format file harus berupa PDF atau Gambar (JPG/PNG).']);
        }

        if ($checklist->file_path && Storage::disk('public')->exists($checklist->file_path)) {
            Storage::disk('public')->delete($checklist->file_path);
        }

        $filename = Str::slug($checklist->nama_dokumen).'_'.time().'.'.$request->file('file_dokumen')->getClientOriginalExtension();
        $path = $request->file('file_dokumen')->storeAs($pjKades->folder_path ?? 'dokumen/sk-kades', $filename, 'public');

        $checklist->update([
            'file_path' => $path,
            'status_verifikasi' => 'pending',
            'catatan_revisi' => null,
            'tgl_diunggah' => now(),
        ]);

        return back()->with('success', "Dokumen {$checklist->nama_dokumen} berhasil diunggah.");
    }

    public function bulkUpload(Request $request, $id)
    {
        $desaId = Auth::user()->desa_id;
        $pjKades = PjKades::withoutGlobalScopes()
            ->where('desa_id', $desaId)
            ->findOrFail($id);

        $isSubmit = $request->input('submit_ajuan') == '1';

        // Validate file if uploaded - bypass PHP MIME detection bug with manual check
        if ($request->hasFile('berkas_zip')) {
            $request->validate(['berkas_zip' => ['file', 'max:51200']]);
            $fileExt = strtolower($request->file('berkas_zip')->getClientOriginalExtension());
            if (! in_array($fileExt, ['zip', 'rar', 'pdf'])) {
                return back()->withErrors(['berkas_zip' => 'File harus berformat ZIP, RAR, atau PDF.'])->withInput();
            }
        }

        if (! in_array($pjKades->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Dokumen tidak dapat diubah karena usulan sedang diproses oleh dinas.');
        }

        Storage::disk('public')->makeDirectory($pjKades->folder_path);

        if ($request->hasFile('berkas_zip')) {
            $file = $request->file('berkas_zip');
            $ext = $file->extension();
            $safeNoReg = str_replace('/', '-', $pjKades->no_registrasi);
            $filename = $safeNoReg.'_berkas_persyaratan.'.$ext;

            if ($pjKades->berkas_zip && Storage::disk('public')->exists($pjKades->berkas_zip)) {
                Storage::disk('public')->delete($pjKades->berkas_zip);
            }

            $path = $file->storeAs(
                $pjKades->folder_path,
                $filename,
                'public'
            );

            $pjKades->update(['berkas_zip' => $path]);
        }

        if ($isSubmit) {
            // Validasi jika belum unggah file zip (hanya jika online)
            if ($pjKades->metode === 'online' && ! $pjKades->berkas_zip) {
                return back()->with('error', 'Gagal mengirim. Anda belum mengunggah file ZIP persyaratan.');
            }
            $pjKades->update(['status' => 'submitted']);

            return redirect()->route('desa.pjkades.index')->with('success', 'Usulan SK Kades berhasil dikirim ke Dinpermasdes.');
        }

        return back()->with('success', 'File gabungan berhasil diunggah dan disimpan sebagai draft.');
    }

    public function submitUsulan($id)
    {
        $desaId = Auth::user()->desa_id;
        $pjKades = PjKades::withoutGlobalScopes()
            ->where('desa_id', $desaId)
            ->findOrFail($id);

        if ($pjKades->metode === 'online') {
            if (! $pjKades->berkas_zip) {
                return back()->with('error', 'Gagal mengirim. Anda belum mengunggah file ZIP persyaratan.');
            }
        } else {
            // Cek kelengkapan dokumen wajib untuk metode offline
            $unuploadedWajib = $pjKades->checklists()->where('wajib', true)->whereNull('file_path')->count();
            if ($unuploadedWajib > 0) {
                return back()->with('error', "Masih terdapat {$unuploadedWajib} dokumen wajib yang belum diunggah. Mohon lengkapi seluruh berkas terlebih dahulu.");
            }
        }

        $pjKades->update([
            'status' => 'submitted',
            'tgl_diajukan' => now()->toDateString(),
        ]);

        return redirect()->route('desa.pjkades.index')
            ->with('success', 'Usulan SK Kades berhasil dikirim ke Dinpermasdes untuk proses verifikasi.');
    }

    public function destroy($id)
    {
        $desaId = Auth::user()->desa_id;
        $pjKades = PjKades::withoutGlobalScopes()
            ->where('desa_id', $desaId)
            ->findOrFail($id);

        if ($pjKades->folder_path && Storage::disk('public')->exists($pjKades->folder_path)) {
            Storage::disk('public')->deleteDirectory($pjKades->folder_path);
        }

        $pjKades->checklists()->delete();
        $pjKades->delete();

        return redirect()->route('desa.pjkades.index')->with('success', 'Data Usulan SK Kades berhasil dihapus.');
    }
}

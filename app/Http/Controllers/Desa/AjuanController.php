<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\Ajuan;
use App\Models\AjuanPeserta;
use App\Models\AlasanPemberhentian;
use App\Models\ChecklistAjuan;
use App\Models\JenisLayanan;
use App\Models\MilestoneTracking;
use App\Models\PerangkatDesa;
use App\Models\TemplateChecklist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AjuanController extends Controller
{
    public function index()
    {
        $desaId = Auth::user()->desa_id;
        $ajuans = Ajuan::with(['jenisLayanan', 'pesertas.perangkatDesa', 'milestoneTrackings'])
            ->where('desa_id', $desaId)
            ->latest()
            ->paginate(15);

        return view('desa.ajuan.index', compact('ajuans'));
    }

    public function create()
    {
        $jenisLayanans = JenisLayanan::whereIn('nama', ['Pengangkatan', 'Rotasi', 'Pemberhentian'])->get();
        $alasanPemberhentians = AlasanPemberhentian::whereIn('nama', ['Purna Tugas', 'Permintaan Sendiri', 'Diberhentikan'])->get();
        $perangkatDesas = PerangkatDesa::where('desa_id', Auth::user()->desa_id)
            ->where('status_aktif', true)
            ->where('jabatan', '!=', 'Kepala Desa')
            ->get();

        return view('desa.ajuan.create', compact('jenisLayanans', 'alasanPemberhentians', 'perangkatDesas'));
    }

    public function store(Request $request)
    {
        $rotasiLayanan = JenisLayanan::where('nama', 'Rotasi')->first();

        $request->validate([
            'metode' => ['required', 'in:online,offline'],
            'jenis_layanan_id' => ['required', 'exists:jenis_layanans,id'],
            'alasan_pemberhentian_id' => ['nullable', 'exists:alasan_pemberhentians,id'],
            'pesertas' => ['required', 'array', 'min:1'],
            'pesertas.*.perangkat_desa_id' => ['required', 'exists:perangkat_desas,id'],
            'pesertas.*.jabatan_baru' => ['nullable', 'required_if:jenis_layanan_id,'.($rotasiLayanan->id ?? 0), 'string', 'max:255'],
        ], [
            'pesertas.*.jabatan_baru.required_if' => 'Jabatan tujuan harus diisi untuk layanan Rotasi.',
            'pesertas.min' => 'Minimal 1 (satu) orang perangkat desa harus didaftarkan.',
        ]);

        $desa = Auth::user()->desa;
        $jenisLayanan = JenisLayanan::find($request->jenis_layanan_id);

        // Generate no registrasi
        $prefix = match ($jenisLayanan->nama) {
            'Pengangkatan' => 'PGKT',
            'Rotasi' => 'ROT',
            'Pemberhentian' => 'PBRH',
            default => 'AJU',
        };
        $noRegistrasi = $prefix.'/'.now()->format('Y').'/'.now()->format('m').'/'.str_pad(Ajuan::count() + 1, 4, '0', STR_PAD_LEFT);

        // Hitung SLA batas (20 hari kerja dari hari ini)
        $tglBatas = $this->hitungHariKerja(now(), 20);

        // Build folder path
        $kecamatan = Str::slug($desa->kecamatan->nama_kecamatan);
        $desaNama = Str::slug($desa->nama_desa);
        $jenis = Str::slug($jenisLayanan->nama);
        $folderPath = "dokumen/{$kecamatan}/{$desaNama}/{$jenis}";

        $isDraft = $request->has('draft');

        $ajuan = Ajuan::create([
            'no_registrasi' => $noRegistrasi,
            'desa_id' => $desa->id,
            'jenis_layanan_id' => $request->jenis_layanan_id,
            'alasan_pemberhentian_id' => $request->alasan_pemberhentian_id,
            'metode' => $request->metode,
            'status' => 'draft',
            'folder_path' => $folderPath,
            'tgl_diajukan' => now()->toDateString(),
            'tgl_sla_batas' => $tglBatas,
        ]);

        // Simpan Bulk Pesertas (Kolektif)
        foreach ($request->pesertas as $peserta) {
            AjuanPeserta::create([
                'ajuan_id' => $ajuan->id,
                'perangkat_desa_id' => $peserta['perangkat_desa_id'],
                'jabatan_baru' => $jenisLayanan->nama === 'Rotasi' ? ($peserta['jabatan_baru'] ?? null) : null,
            ]);
        }

        // Buat checklist_ajuan dari template
        $checklists = TemplateChecklist::where('jenis_layanan_id', $request->jenis_layanan_id)
            ->where(function ($q) use ($request) {
                $q->whereNull('alasan_pemberhentian_id')
                    ->orWhere('alasan_pemberhentian_id', $request->alasan_pemberhentian_id);
            })
            ->orderBy('urutan')
            ->get();

        foreach ($checklists as $template) {
            ChecklistAjuan::create([
                'ajuan_id' => $ajuan->id,
                'template_checklist_id' => $template->id,
                'status' => 'belum_diunggah',
                'versi' => 1,
            ]);
        }

        if ($isDraft) {
            return redirect()->route('desa.ajuan.show', $ajuan)->with('success', 'Draft ajuan tersimpan.');
        }

        return redirect()->route('desa.ajuan.show', $ajuan)
            ->with('success', 'Ajuan berhasil disubmit! No. Registrasi: '.$noRegistrasi.'. Silakan lengkapi dan unggah dokumen persyaratan di bawah.');
    }

    public function show(Ajuan $ajuan)
    {
        // Gate: hanya desa pemilik yang bisa lihat
        if ($ajuan->desa_id !== Auth::user()->desa_id) {
            abort(403, 'Anda tidak memiliki akses ke ajuan ini.');
        }

        $ajuan->load([
            'jenisLayanan',
            'alasanPemberhentian',
            'pesertas.perangkatDesa',
            'checklistAjuans.templateChecklist',
            'milestoneTrackings',
            'arsipRekom',
        ]);

        $templates = TemplateChecklist::where('jenis_layanan_id', $ajuan->jenis_layanan_id)
            ->where(function ($query) use ($ajuan) {
                $query->whereNull('alasan_pemberhentian_id')
                    ->orWhere('alasan_pemberhentian_id', $ajuan->alasan_pemberhentian_id);
            })
            ->orderBy('urutan')
            ->get();

        $existingTemplateIds = $ajuan->checklistAjuans->pluck('template_checklist_id')->filter()->all();

        foreach ($templates as $template) {
            if (! in_array($template->id, $existingTemplateIds, true)) {
                $ajuan->checklistAjuans()->create([
                    'template_checklist_id' => $template->id,
                    'status' => 'belum_diunggah',
                    'versi' => 1,
                ]);
                $existingTemplateIds[] = $template->id;
            }
        }

        $ajuan->load('checklistAjuans.templateChecklist');
        $tahapAktif = $this->hitungTahapAktif($ajuan->milestoneTrackings);

        return view('desa.ajuan.show', compact('ajuan', 'tahapAktif'));
    }

    public function uploadDokumen(Request $request, Ajuan $ajuan, ChecklistAjuan $checklistAjuan)
    {
        if ($ajuan->desa_id !== Auth::user()->desa_id) {
            abort(403);
        }

        $request->validate([
            'dokumen' => ['required', 'file', 'max:10240'],
        ]);

        // Manual extension check - bypass PHP MIME detection bug
        $docExt = strtolower($request->file('dokumen')->getClientOriginalExtension());
        if ($docExt !== 'pdf') {
            return back()->withErrors(['dokumen' => 'Dokumen harus berformat PDF.']);
        }

        // Enforcement: Rule Immutable status
        if (! in_array($ajuan->status, ['draft', 'direvisi'])) {
            return back()->with('error', 'Dokumen tidak dapat diubah karena ajuan sedang diproses oleh dinas.');
        }

        // Pastikan folder ada
        Storage::disk('public')->makeDirectory($ajuan->folder_path);

        $template = $checklistAjuan->templateChecklist;
        $urutan = str_pad($template->urutan, 2, '0', STR_PAD_LEFT);
        $ext = $request->file('dokumen')->extension();
        $safeNoReg = str_replace('/', '-', $ajuan->no_registrasi);
        $filename = $safeNoReg.'_'.$urutan.'_'.Str::slug($template->nama_dokumen).'.'.$ext;

        // Jika ada file lama, hapus dulu
        if ($checklistAjuan->file_path && Storage::disk('public')->exists($checklistAjuan->file_path)) {
            Storage::disk('public')->delete($checklistAjuan->file_path);
        }

        $path = $request->file('dokumen')->storeAs(
            $ajuan->folder_path,
            $filename,
            'public'
        );

        $checklistAjuan->update([
            'file_path' => $path,
            'status' => 'pending',
            'versi' => $checklistAjuan->versi,
        ]);

        return back()->with('success', 'Dokumen "'.$template->nama_dokumen.'" berhasil diunggah. Menunggu verifikasi Dinpermasdes.');
    }

    public function bulkUpload(Request $request, Ajuan $ajuan)
    {
        if ($ajuan->desa_id !== Auth::user()->desa_id) {
            abort(403);
        }

        $isSubmit = $request->input('submit_ajuan') == '1';

        // Validate file if uploaded - bypass PHP MIME detection bug with manual check
        if ($request->hasFile('berkas_zip')) {
            $request->validate(['berkas_zip' => ['file', 'max:51200']]);
            $allowedExt = ['zip', 'rar', 'pdf'];
            $fileExt = strtolower($request->file('berkas_zip')->getClientOriginalExtension());
            if (! in_array($fileExt, $allowedExt)) {
                return back()->withErrors(['berkas_zip' => 'File harus berformat ZIP, RAR, atau PDF.'])->withInput();
            }
        }

        // Enforcement: Rule Immutable status
        if (! in_array($ajuan->status, ['draft', 'direvisi'])) {
            return back()->with('error', 'Dokumen tidak dapat diubah karena ajuan sedang diproses oleh dinas.');
        }

        Storage::disk('public')->makeDirectory($ajuan->folder_path);

        if ($request->hasFile('berkas_zip')) {
            $file = $request->file('berkas_zip');
            $ext = $file->extension();
            $safeNoReg = str_replace('/', '-', $ajuan->no_registrasi);
            $filename = $safeNoReg.'_berkas_persyaratan.'.$ext;

            if ($ajuan->berkas_zip && Storage::disk('public')->exists($ajuan->berkas_zip)) {
                Storage::disk('public')->delete($ajuan->berkas_zip);
            }

            $path = $file->storeAs(
                $ajuan->folder_path,
                $filename,
                'public'
            );

            $ajuan->update([
                'berkas_zip' => $path,
            ]);
        }

        if ($isSubmit) {
            $ajuan->update([
                'status' => 'submitted',
                'posisi_surat' => 'Pegawai',
            ]);

            // Milestone 1: Berkas Diterima (Selesai)
            MilestoneTracking::create([
                'ajuan_id' => $ajuan->id,
                'tahap' => 1,
                'tgl_mulai' => now(),
                'tgl_selesai' => now(),
                'catatan' => 'Berkas pengajuan berhasil disubmit oleh Desa.',
                'updated_by' => auth()->id(),
            ]);

            // Milestone 2: Front Office (Aktif)
            MilestoneTracking::create([
                'ajuan_id' => $ajuan->id,
                'tahap' => 2,
                'tgl_mulai' => now(),
                'tgl_selesai' => null,
                'catatan' => 'Menunggu pengecekan awal oleh Pegawai.',
                'updated_by' => auth()->id(),
            ]);

            return redirect()->route('desa.ajuan.index')->with('success', 'Ajuan berhasil disubmit dan diteruskan ke Dinpermasdes!');
        }

        return back()->with('success', 'Draft dokumen berhasil disimpan.');
    }

    private function hitungTahapAktif($milestoneTrackings): int
    {
        if ($milestoneTrackings->isEmpty()) {
            return 1;
        }

        $tahapBelumSelesai = $milestoneTrackings
            ->whereNull('tgl_selesai')
            ->min('tahap');

        if ($tahapBelumSelesai) {
            return (int) $tahapBelumSelesai;
        }

        $maxTahap = $milestoneTrackings->max('tahap');

        return min((int) $maxTahap + 1, 9);
    }

    private function hitungHariKerja(Carbon $dari, int $jumlahHari): Carbon
    {
        $tanggal = $dari->copy();
        $hitung = 0;
        while ($hitung < $jumlahHari) {
            $tanggal->addDay();
            if (! $tanggal->isWeekend()) {
                $hitung++;
            }
        }

        return $tanggal;
    }

    public function destroy(Ajuan $ajuan)
    {
        if ($ajuan->desa_id !== Auth::user()->desa_id) {
            abort(403, 'Unauthorized action.');
        }

        // Hapus file fisik di storage
        if ($ajuan->folder_path && Storage::disk('public')->exists($ajuan->folder_path)) {
            Storage::disk('public')->deleteDirectory($ajuan->folder_path);
        }

        // Hapus data relasi dan utama
        $ajuan->checklistAjuans()->delete();
        $ajuan->pesertas()->delete();
        $ajuan->milestoneTrackings()->delete();
        $ajuan->delete();

        return redirect()->route('desa.ajuan.index')->with('success', 'Data usulan berhasil dihapus.');
    }
}

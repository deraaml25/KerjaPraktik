<x-app-layout>
    @section('title', 'Detail Usulan SK Kades')

    <div class="max-w-6xl mx-auto mb-8">
        <div class="flex flex-wrap items-center gap-3 mb-5">
            <a href="{{ route('desa.pjkades.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm group">
                <svg class="w-4 h-4 mr-2 text-slate-500 group-hover:text-slate-700 group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Usulan
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 text-sm mb-6 font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-50 text-red-800 rounded-lg border border-red-200 text-sm mb-6 font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- ===== KIRI: Detail & File ===== --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Header Card --}}
                <div class="bg-primary text-white rounded-card shadow-md p-6 relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-white opacity-5 rounded-full blur-xl"></div>
                    <div class="flex flex-wrap justify-between items-start gap-3 mb-5 relative z-10">
                        <div>
                            <p class="text-xs font-mono text-primary-soft tracking-widest mb-1">{{ $pjkades->no_registrasi ?? ('#PJK/2026/08/0004') }} &bull; <span class="font-bold uppercase">{{ $pjkades->kategori === 'plt_kades' ? 'Plt Kades' : 'Pj Kades' }}</span></p>
                            <h2 class="text-xl font-display font-bold">Usulan {{ $pjkades->kategori === 'plt_kades' ? 'Plt Kepala Desa' : 'Pj Kepala Desa' }} — Desa {{ $pjkades->desa->nama_desa }}</h2>
                        </div>
                        @php
                            $statusBadge = match($pjkades->status) {
                                'approved' => ['label' => 'Disetujui / SK Bupati Terbit', 'css' => 'bg-green-500 text-white'],
                                'submitted' => ['label' => 'Dalam Proses Verifikasi', 'css' => 'bg-blue-500 text-white'],
                                'rejected' => ['label' => 'Dikembalikan / Minta Revisi', 'css' => 'bg-red-500 text-white'],
                                'draft' => ['label' => 'Draft (Lengkapi Berkas)', 'css' => 'bg-gray-400 text-white'],
                                default => ['label' => $pjkades->status, 'css' => 'bg-gray-400 text-white'],
                            };
                        @endphp
                        <span class="px-3 py-1.5 rounded-full text-xs font-bold shadow-sm {{ $statusBadge['css'] }}">
                            {{ $statusBadge['label'] }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-black/10 rounded-xl p-4 border border-white/10 relative z-10">
                        <div>
                            <p class="text-xs text-primary-soft mb-0.5">Tgl Diajukan</p>
                            <p class="font-semibold text-sm">{{ $pjkades->tgl_diajukan ? \Carbon\Carbon::parse($pjkades->tgl_diajukan)->translatedFormat('d/m/y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-primary-soft mb-0.5">{{ $pjkades->kategori === 'plt_kades' ? 'Alasan Pemberhentian Sementara / Cuti' : 'Alasan Pemberhentian Kades' }}</p>
                            <p class="font-semibold text-sm">{{ $pjkades->alasan_nama ?? ($pjkades->alasanPemberhentian->nama ?? '-') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Info Card --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="bg-white p-5 rounded-card border border-border shadow-sm">
                <div class="text-xs text-muted font-medium uppercase tracking-wider mb-1">Calon {{ $pjkades->kategori === 'plt_kades' ? 'Plt' : 'Pj' }} Kades</div>
                @if($pjkades->kategori === 'plt_kades')
                    <div class="text-sm font-bold text-ink">{{ $pjkades->nama_plt ?? '-' }}</div>
                    <div class="text-xs text-muted">Sekretaris Desa / Plt</div>
                @else
                    <div class="text-sm font-bold text-ink">{{ $pjkades->nama_pns ?? '-' }}</div>
                    <div class="text-xs text-muted font-mono">NIP. {{ $pjkades->nip ?? '-' }} ({{ $pjkades->pangkat ?? '-' }})</div>
                @endif
            </div>

            <div class="bg-white p-5 rounded-card border border-border shadow-sm">
                <div class="text-xs text-muted font-medium uppercase tracking-wider mb-1">Progress Berkas</div>
                @php
                    $total = $pjkades->checklists->count();
                    $uploaded = $pjkades->checklists->whereNotNull('file_path')->count();
                    $approved = $pjkades->checklists->where('status_verifikasi', 'disetujui')->count();
                @endphp
                <div class="text-sm font-bold text-ink mb-1">{{ $uploaded }} dari {{ $total }} Berkas Diunggah</div>
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="bg-primary h-2 rounded-full" style="width: {{ $total > 0 ? round(($uploaded/$total)*100) : 0 }}%"></div>
                </div>
            </div>
            </div>

            {{-- Checklist Table & Upload Form --}}
                <div class="bg-surface rounded-card shadow-sm border border-border overflow-hidden" x-data="{ isSubmitting: false }">
                        <div class="p-6 border-b border-border bg-gray-50 flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-display font-semibold text-ink">Daftar Dokumen Persyaratan & Checklist</h3>
                                <p class="text-xs text-muted mt-0.5">Unggah dokumen sesuai persyaratan.</p>
                            </div>
                        </div>

                        <div class="divide-y divide-border">
                            @forelse ($pjkades->checklists as $index => $item)
                                <div class="px-6 py-4 flex flex-col lg:flex-row lg:items-center justify-between hover:bg-gray-50 transition-colors gap-4">
                                    <div class="flex items-start lg:items-center gap-3 flex-1 pr-4">
                                        <span class="font-medium text-ink flex-shrink-0">{{ $index + 1 }}.</span>
                                        <span class="text-sm text-ink">{{ $item->nama_dokumen }}</span>
                                    </div>

                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 lg:gap-4 flex-shrink-0">


                                        @if($item->status_verifikasi === 'valid')
                                            <div class="flex items-center text-success text-sm font-medium whitespace-nowrap">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Memenuhi
                                            </div>
                                        @elseif($item->status_verifikasi === 'tidak_sesuai')
                                            <div class="flex items-center text-danger text-sm font-medium whitespace-nowrap">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Tidak Memenuhi
                                            </div>
                                        @else
                                            <span class="text-xs text-muted italic">Belum Diperiksa</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="py-12 text-center text-muted text-sm">Tidak ada checklist dokumen.</div>
                            @endforelse
                        </div>

                    <form method="POST" action="{{ route('desa.pjkades.bulkUpload', $pjkades->id) }}" enctype="multipart/form-data" @submit="isSubmitting = true">
                        @csrf
                        @if($pjkades->metode === 'online')
                            @if(in_array($pjkades->status, ['draft', 'rejected']))
                            <div class="px-6 py-6 bg-white border-t border-border">
                                <label class="block text-sm font-medium text-ink mb-2">Unggah Keseluruhan Persyaratan (.ZIP / .RAR / .PDF)</label>
                                <input type="file" name="berkas_zip" accept=".zip,.rar,.pdf" 
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-light file:cursor-pointer cursor-pointer focus:outline-none border border-border rounded-md p-2">
                                @if($pjkades->berkas_zip)
                                    <div class="mt-3 text-sm text-success flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Berkas telah diunggah. <a href="{{ Storage::disk('public')->url($pjkades->berkas_zip) }}" target="_blank" class="ml-2 underline text-primary">Unduh / Lihat</a>
                                    </div>
                                @endif
                            </div>
                            @else
                                @if($pjkades->berkas_zip)
                                <div class="px-6 py-5 bg-gray-50 border-t border-border flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-center text-sm font-medium text-ink">
                                        <span class="material-symbols-outlined text-[20px] text-primary mr-2">folder_zip</span>
                                        Berkas Keseluruhan Persyaratan (ZIP/PDF)
                                    </div>
                                    <a href="{{ Storage::disk('public')->url($pjkades->berkas_zip) }}" target="_blank" class="inline-flex items-center text-sm text-primary hover:text-primary-light font-medium bg-primary-soft/10 px-4 py-2 rounded-lg transition-colors">
                                        Unduh / Lihat Berkas
                                    </a>
                                </div>
                                @endif
                            @endif
                        @endif

                        @if($pjkades->catatan_admin)
                        <div class="px-6 py-5 bg-yellow-50 border-t border-yellow-200 text-yellow-900 text-sm">
                            <strong class="block mb-1 font-bold">Catatan dari Admin:</strong>
                            <p class="whitespace-pre-line">{{ $pjkades->catatan_admin }}</p>
                        </div>
                        @endif

                        @if(in_array($pjkades->status, ['draft', 'rejected']))
                        <div class="px-6 py-5 bg-gray-50 border-t border-border flex flex-wrap items-center justify-end gap-3">
                            <input type="hidden" name="submit_ajuan" id="is_submit_hidden" value="0">
                            <button type="submit" 
                                    onclick="document.getElementById('is_submit_hidden').value='0'"
                                    :disabled="isSubmitting"
                                    :class="{'opacity-50 cursor-not-allowed': isSubmitting}"
                                    class="px-5 py-2.5 bg-white border border-border rounded-btn text-ink text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm">
                                <span x-show="!isSubmitting">Simpan Draft</span>
                                <span x-show="isSubmitting">Menyimpan...</span>
                            </button>
                            <button type="submit" 
                                    onclick="document.getElementById('is_submit_hidden').value='1'"
                                    :disabled="isSubmitting"
                                    :class="{'opacity-50 cursor-not-allowed': isSubmitting}"
                                    class="px-5 py-2.5 bg-success rounded-btn text-white text-sm font-medium hover:bg-green-700 transition-colors shadow-sm flex items-center">
                                <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-show="!isSubmitting">Kirim Pengajuan (Submit)</span>
                                <span x-show="isSubmitting">Mengunggah...</span>
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
            
            {{-- Kanan: Tracker --}}
            <div class="lg:col-span-1">
                <div class="bg-surface rounded-card shadow-sm border border-border p-6 sticky top-6">
                    <h3 class="text-base font-display font-semibold text-ink mb-1">Status Proses</h3>
                    <p class="text-xs text-muted mb-4 pb-4 border-b border-border">Pantau tahapan perjalanan usulan SK Kades Anda secara real-time.</p>
                    
                    <x-pjkades-tracker :posisiAktif="$pjkades->posisi_surat ?? 'Berkas Diterima'" :status="$pjkades->status" :pjkades="$pjkades" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

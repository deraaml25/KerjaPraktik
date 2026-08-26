<x-app-layout>
    @section('title', 'Detail Ajuan: ' . $ajuan->no_registrasi)

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('desa.ajuan.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm group">
            <svg class="w-4 h-4 mr-2 text-slate-500 group-hover:text-slate-700 group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Ajuan
        </a>
        @if($ajuan->status === 'selesai' && $ajuan->arsipRekom)
            <div class="flex flex-wrap items-center gap-2 justify-end">
                <a href="{{ Storage::disk('public')->url($ajuan->arsipRekom->file_path) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-success text-white text-sm font-medium rounded-btn hover:bg-green-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Surat Rekomendasi
                </a>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 rounded-card bg-green-50 border border-green-200 text-green-800 flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-5 p-4 rounded-card bg-red-50 border border-red-200 text-red-800 flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <ul class="text-sm list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- ===== KIRI: Info + Checklist Upload ===== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Header Card --}}
            @php
                $statusBadge = match($ajuan->status) {
                    'submitted' => ['label' => 'Menunggu Verifikasi', 'css' => 'bg-blue-500 text-white'],
                    'direvisi'  => ['label' => 'Perlu Perbaikan Dokumen', 'css' => 'bg-red-500 text-white'],
                    'diproses'  => ['label' => 'Sedang Diproses', 'css' => 'bg-yellow-400 text-yellow-900'],
                    'selesai'   => ['label' => 'Selesai - Rekomendasi Terbit', 'css' => 'bg-green-500 text-white'],
                    'draft'     => ['label' => 'Draft', 'css' => 'bg-gray-400 text-white'],
                    default     => ['label' => $ajuan->status, 'css' => 'bg-gray-400 text-white'],
                };
            @endphp

            <div class="bg-primary text-white rounded-card shadow-md p-6 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white opacity-5 rounded-full blur-xl"></div>
                <div class="flex flex-wrap justify-between items-start gap-3 mb-5">
                    <div>
                        <p class="text-xs font-mono text-primary-soft tracking-widest mb-1">{{ $ajuan->no_registrasi }} &bull; Metode: <span class="font-bold uppercase">{{ $ajuan->metode }}</span></p>
                        <h2 class="text-xl font-display font-bold">Ajuan {{ $ajuan->jenisLayanan->nama }}</h2>
                        <div class="mt-2 text-sm text-primary-soft space-y-1 max-h-32 overflow-y-auto custom-scrollbar pr-2">
                            @foreach($ajuan->pesertas as $index => $peserta)
                                <p>{{ $index + 1 }}. {{ $peserta->perangkatDesa->nama }} ({{ $peserta->perangkatDesa->jabatan }}) 
                                   @if($peserta->jabatan_baru) <br><span class="opacity-80 text-xs ml-3 font-semibold text-white">↳ Rotasi ke: {{ $peserta->jabatan_baru }}</span> @endif
                                </p>
                            @endforeach
                        </div>
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold shadow-sm {{ $statusBadge['css'] }}">
                        {{ $statusBadge['label'] }}
                    </span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 bg-black/10 rounded-xl p-4 border border-white/10">
                    <div>
                        <p class="text-xs text-primary-soft mb-0.5">Tgl Diajukan</p>
                        <p class="font-semibold text-sm">{{ $ajuan->tgl_diajukan ? $ajuan->tgl_diajukan->format('d/m/y') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-primary-soft mb-0.5">Target SLA (20 HK)</p>
                        <p class="font-semibold text-sm">{{ $ajuan->tgl_sla_batas ? $ajuan->tgl_sla_batas->format('d/m/y') : '-' }}</p>
                    </div>
                    @if($ajuan->alasanPemberhentian)
                    <div>
                        <p class="text-xs text-primary-soft mb-0.5">Alasan</p>
                        <p class="font-semibold text-sm">{{ $ajuan->alasanPemberhentian->nama }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Dokumen Persyaratan --}}
            <div class="bg-surface rounded-card border border-border shadow-sm overflow-hidden" x-data="{ isSubmitting: false }">
                    <div class="px-6 py-4 border-b border-border bg-gray-50 flex flex-wrap justify-between items-center gap-3">
                        <div>
                            <h3 class="text-base font-display font-semibold text-ink">Checklist Berkas Persyaratan ({{ $ajuan->checklistAjuans->count() }} item)</h3>
                            <p class="text-xs text-muted mt-0.5">Unggah dokumen sesuai persyaratan.</p>
                        </div>
                    </div>

                    <div class="divide-y divide-border">
                        @php $inSubSection = false; $subIndex = 0; @endphp
                        @forelse($ajuan->checklistAjuans->sortBy('templateChecklist.urutan') as $item)
                            @if(!$item->templateChecklist->wajib)
                                @php $inSubSection = true; $subIndex = 0; @endphp
                                {{-- Section header / label kondisional (bukan dokumen upload) --}}
                                <div class="px-6 py-3 bg-amber-50 border-l-4 border-amber-400 flex items-start gap-3">
                                    <span class="font-bold text-amber-700 flex-shrink-0 text-sm">{{ $item->templateChecklist->urutan }}.</span>
                                    <span class="text-sm font-semibold text-amber-800 italic">{{ $item->templateChecklist->nama_dokumen }}</span>
                                </div>
                            @else
                                @php $subIndex++; $label = $inSubSection ? chr(96 + $subIndex) . '.' : $item->templateChecklist->urutan . '.'; @endphp
                            <div class="px-6 py-4 {{ $inSubSection ? 'pl-10' : '' }} flex flex-col lg:flex-row lg:items-center justify-between hover:bg-gray-50 transition-colors gap-4">
                                <div class="flex items-start lg:items-center gap-3 flex-1 pr-4">
                                    <span class="font-medium text-ink flex-shrink-0">{{ $label }}</span>
                                    <span class="text-sm text-ink">{{ $item->templateChecklist->nama_dokumen }}</span>
                                    @if($item->templateChecklist->wajib && !in_array(strtolower($ajuan->jenisLayanan->nama), ['rotasi', 'pengangkatan']))
                                        <span class="text-danger text-xs font-bold flex-shrink-0">*</span>
                                    @endif
                                </div>
                                
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 lg:gap-4 flex-shrink-0">
                                    @if($ajuan->metode === 'online')
                                        @if($item->file_path)
                                            <a href="{{ Storage::disk('public')->url($item->file_path) }}" target="_blank" class="flex items-center text-xs font-medium text-primary hover:text-primary-light transition-colors bg-primary-soft/10 px-3 py-1.5 rounded-full">
                                                Lihat File
                                            </a>
                                        @endif
                                        

                                    @endif

                                    @if($item->status === 'lengkap' || $item->status === 'valid')
                                        <div class="flex items-center text-success text-sm font-medium whitespace-nowrap">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Memenuhi
                                        </div>
                                    @elseif(in_array($item->status, ['kurang', 'tidak_sesuai']))
                                        <div class="flex items-center text-danger text-sm font-medium whitespace-nowrap">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Tidak Memenuhi
                                        </div>
                                    @else
                                        <span class="text-xs text-muted italic">Belum Diperiksa</span>
                                    @endif
                                </div>
                            </div>

                            @if($item->catatan && in_array($item->status, ['kurang', 'tidak_sesuai']))
                                <div class="px-6 py-2 bg-red-50 text-xs text-red-800 border-b border-border">
                                    <strong class="font-semibold">Catatan Perbaikan:</strong> {{ $item->catatan }}
                                </div>
                            @endif
                            @endif
                        @empty
                            <div class="py-12 text-center text-muted text-sm">Tidak ada checklist dokumen untuk ajuan ini.</div>
                        @endforelse
                    </div>

                <form method="POST" action="{{ route('desa.ajuan.bulk-upload', $ajuan) }}" enctype="multipart/form-data" @submit="isSubmitting = true">
                    @csrf
                    @if($ajuan->metode === 'online')
                        @if(auth()->user()->can('update', $ajuan))
                        <div class="px-6 py-6 bg-white border-t border-border">
                            <label class="block text-sm font-medium text-ink mb-2">Unggah Keseluruhan Persyaratan (.ZIP / .RAR / .PDF)</label>
                            <input type="file" name="berkas_zip" accept=".zip,.rar,.pdf" 
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-light file:cursor-pointer cursor-pointer focus:outline-none border border-border rounded-md p-2">
                            @if($ajuan->berkas_zip)
                                <div class="mt-3 text-sm text-success flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Berkas telah diunggah. <a href="{{ Storage::disk('public')->url($ajuan->berkas_zip) }}" target="_blank" class="ml-2 underline text-primary">Unduh / Lihat</a>
                                </div>
                            @endif
                        </div>
                        @else
                            @if($ajuan->berkas_zip)
                            <div class="px-6 py-5 bg-gray-50 border-t border-border flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center text-sm font-medium text-ink">
                                    <span class="material-symbols-outlined text-[20px] text-primary mr-2">folder_zip</span>
                                    Berkas Keseluruhan Persyaratan (ZIP/PDF)
                                </div>
                                <a href="{{ Storage::disk('public')->url($ajuan->berkas_zip) }}" target="_blank" class="inline-flex items-center text-sm text-primary hover:text-primary-light font-medium bg-primary-soft/10 px-4 py-2 rounded-lg transition-colors">
                                    Unduh / Lihat Berkas
                                </a>
                            </div>
                            @endif
                        @endif
                    @endif

                    @if($ajuan->catatan_admin)
                    <div class="px-6 py-5 bg-yellow-50 border-t border-yellow-200 text-yellow-900 text-sm">
                        <strong class="block mb-1 font-bold">Catatan dari Admin:</strong>
                        <p class="whitespace-pre-line">{{ $ajuan->catatan_admin }}</p>
                    </div>
                    @endif

                    @can('update', $ajuan)
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
                    @endcan
                </form>
            </div>
        </div>

        {{-- ===== KANAN: Milestone Tracker ===== --}}
        <div>
            <div class="bg-surface rounded-card border border-border shadow-sm p-5 sticky top-6">
                <h3 class="text-base font-display font-semibold text-ink mb-1">Status Proses</h3>
                <p class="text-xs text-muted mb-4 pb-4 border-b border-border">Pantau tahapan perjalanan ajuan Anda secara real-time.</p>
                <x-milestone-tracker :tahapAktif="$tahapAktif" :milestones="$ajuan->milestoneTrackings" :ajuan="$ajuan" />
            </div>
        </div>
    </div>
</x-app-layout>

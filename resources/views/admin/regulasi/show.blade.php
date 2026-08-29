<x-app-layout>
    @section('title', 'Tinjau Regulasi')

    <div class="mb-5 flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.regulasi.index') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm group">
            <svg class="w-4 h-4 mr-2 text-slate-500 group-hover:text-slate-700 group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Regulasi
        </a>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-140px)] min-h-[600px]">
        
        <!-- KIRI: Layar Tinjauan Dokumen -->
        <div class="w-full flex flex-col gap-0" style="width: 70%;">

            @php $ext = $regulasi->file_path ? strtolower(pathinfo($regulasi->file_path, PATHINFO_EXTENSION)) : null; @endphp

            <div class="rounded-2xl border border-slate-200 overflow-hidden shadow-sm bg-white flex flex-col" style="height: 100%;">

                {{-- Header kartu: nama dokumen + tombol unduh --}}
                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-slate-200 flex-shrink-0">
                    <div>
                        <p class="text-sm font-medium text-ink">Draf Peraturan Desa</p>
                        <p class="text-xs text-muted mt-0.5">
                            {{ $regulasi->file_path ? $regulasi->judul : 'Belum ada dokumen diunggah' }}
                        </p>
                    </div>
                    @if($regulasi->file_path)
                        <a href="{{ asset('storage/' . $regulasi->file_path) }}" target="_blank"
                            class="inline-flex items-center px-3 py-1.5 bg-primary text-white text-xs font-medium rounded hover:bg-primary-light transition-colors flex-shrink-0" title="Unduh Berkas">
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Unduh
                        </a>
                    @endif
                </div>

                {{-- Area preview dokumen --}}
                <div class="flex-1 overflow-y-auto bg-slate-50" style="min-height: 0;">
                    @if($regulasi->file_path)
                        @if($ext === 'pdf')
                            <iframe src="{{ asset('storage/' . $regulasi->file_path) }}"
                                class="w-full h-full border-0" style="min-height: 600px;"></iframe>

                        @elseif(in_array($ext, ['doc', 'docx']))
                            <div class="p-6 md:p-10">
                                <div id="docx-loading" class="flex flex-col items-center justify-center py-16 text-slate-400 gap-3">
                                    <svg class="animate-spin w-8 h-8 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                    <p class="text-sm">Memuat dokumen...</p>
                                </div>
                                <div id="docx-error" class="hidden flex-col items-center justify-center py-16 text-slate-400 gap-3">
                                    <span class="material-symbols-outlined text-5xl">description</span>
                                    <p class="text-sm">Gagal memuat pratinjau. Gunakan tombol Unduh.</p>
                                </div>
                                <div id="docx-content" class="hidden prose prose-sm max-w-none text-slate-800 leading-relaxed"></div>
                            </div>
                            <script>
                            (function () {
                                var fileUrl = '{{ asset('storage/' . $regulasi->file_path) }}';
                                var loading = document.getElementById('docx-loading');
                                var errorEl = document.getElementById('docx-error');
                                var contentEl = document.getElementById('docx-content');
                                function showError() {
                                    loading.classList.add('hidden');
                                    errorEl.classList.remove('hidden');
                                    errorEl.style.display = 'flex';
                                }
                                function loadScript(src, cb) {
                                    var s = document.createElement('script');
                                    s.src = src; s.onload = cb;
                                    s.onerror = function() { showError(); };
                                    document.head.appendChild(s);
                                }
                                loadScript('https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.8.0/mammoth.browser.min.js', function () {
                                    fetch(fileUrl).then(function(r) { return r.arrayBuffer(); })
                                        .then(function(buf) { return mammoth.convertToHtml({ arrayBuffer: buf }); })
                                        .then(function(result) {
                                            loading.classList.add('hidden');
                                            contentEl.innerHTML = result.value;
                                            contentEl.classList.remove('hidden');
                                        }).catch(function() { showError(); });
                                });
                            })();
                            </script>

                        @else
                            <div class="flex flex-col items-center justify-center py-20 text-slate-400 gap-3">
                                <span class="material-symbols-outlined text-6xl">description</span>
                                <p class="text-sm">Format file tidak dapat ditampilkan langsung.</p>
                                <a href="{{ asset('storage/' . $regulasi->file_path) }}" class="text-primary hover:underline text-sm">Unduh file</a>
                            </div>
                        @endif
                    @else
                        <div class="flex flex-col items-center justify-center py-20 text-slate-400 gap-3">
                            <span class="material-symbols-outlined text-6xl">upload_file</span>
                            <p class="text-sm">Draf belum diunggah oleh desa.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <!-- KANAN: Panel Informasi & Form -->
        <div class="w-full flex flex-col h-full overflow-y-auto pr-2 custom-scrollbar" style="width: 30%;">
            
            <!-- Info Card -->
            <!-- Info Card -->
            <div class="bg-primary text-white rounded-card shadow-sm p-4 mb-5">
                <div class="flex justify-between items-start mb-2 gap-2">
                    <div>
                        <p class="text-[10px] font-mono text-white uppercase tracking-wider">{{ $regulasi->no_regulasi }}</p>
                        <h2 class="text-lg font-display font-bold leading-tight mt-0.5">{{ strtoupper($regulasi->desa->nama_desa) }}</h2>
                    </div>
                </div>
                <div class="text-xs border-t border-white/20 pt-2 flex flex-col gap-1.5">
                    <p><span class="text-white inline-block w-20">Layanan:</span> <span class="font-medium">Evaluasi Hukum ({{ ucfirst($regulasi->tipe) }})</span></p>
                    <p><span class="text-white inline-block w-20">Tanggal:</span> <span class="font-medium">{{ $regulasi->tgl_diajukan ? $regulasi->tgl_diajukan->format('d/m/y') : '-' }}</span></p>
                </div>
            </div>

            <div class="bg-white rounded-card shadow-sm border border-border p-5 mb-5">
                <h3 class="text-xs font-bold text-ink uppercase tracking-wide mb-2">{{ $regulasi->judul }}</h3>
                @if($regulasi->deskripsi)
                    <p class="text-sm text-muted leading-relaxed whitespace-pre-wrap">{{ $regulasi->deskripsi }}</p>
                @else
                    <p class="text-sm text-muted italic">Tidak ada keterangan yang dilampirkan.</p>
                @endif
            </div>

            <!-- Panel Aksi -->
            <div class="bg-surface rounded-card border border-border shadow-sm flex flex-col mb-6 overflow-hidden flex-shrink-0">
                <div class="px-5 py-4 border-b border-border bg-gray-50 flex justify-between items-center">
                    <h3 class="font-display font-semibold text-ink">Tindakan Admin</h3>
                </div>

                @if($regulasi->status === 'disahkan')
                    <div class="p-5">
                        <div class="p-4 bg-green-50 text-green-800 rounded-lg text-sm border border-green-100">
                            <div class="flex items-center gap-2 mb-2 font-bold">
                                <span class="material-symbols-outlined">check_circle</span>
                                Status: Disahkan
                            </div>
                            <p class="text-xs">Regulasi ini telah terbit di Lembaran Desa.</p>
                            @if($regulasi->catatan_revisi)
                                <div class="mt-3 p-3 bg-white rounded border border-green-200">
                                    <strong class="text-xs block mb-1">Catatan Akhir Sanksi/Legal Note:</strong>
                                    <p class="text-xs">{{ $regulasi->catatan_revisi }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    
                    <!-- Area Form Catatan & Revisi -->
                    <div class="p-5 flex flex-col gap-5">
                        <form action="{{ route('admin.regulasi.kembalikan', $regulasi) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                            @csrf
                            
                            <div>
                                <label for="catatan" class="block text-sm font-medium text-ink mb-1.5">Catatan Admin <span class="text-red-500">*</span></label>
                                <textarea name="catatan" id="catatan" rows="3"
                                    class="w-full rounded-md border-gray-300 text-ink bg-white focus:border-primary focus:ring focus:ring-primary/20 shadow-sm text-sm"
                                    placeholder="Tuliskan catatan perbaikan" required></textarea>
                            </div>

                            <div>
                                <label for="file_catatan_dinas" class="block text-sm font-medium text-ink mb-1.5">Unggah Draf Coretan (Opsional)</label>
                                <input type="file" name="file_catatan_dinas" id="file_catatan_dinas"
                                    class="w-full text-xs box-border rounded-md border border-gray-300 p-2 bg-gray-50 focus:border-primary focus:ring-primary shadow-sm" accept=".doc,.docx,.pdf">
                            </div>

                            <button type="submit"
                                class="w-full py-2 px-3 bg-white border border-red-300 text-red-600 rounded text-xs font-medium hover:bg-red-50 transition-colors shadow-sm flex justify-center items-center gap-1.5" title="Kembalikan ke Desa (Butuh Revisi)">
                                Kembalikan untuk Revisi
                            </button>
                        </form>
                    </div>

                    <!-- Area Tindak Lanjut Setuju -->
                    <div class="px-5 py-4 bg-gray-50 border-t border-border mt-auto">
                        <h3 class="text-xs font-semibold text-muted mb-2">Tindak Lanjut Cepat</h3>
                        <form action="{{ route('admin.regulasi.setujui', $regulasi) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Apakah Anda yakin draf regulasi ini sudah benar dan disetujui?')"
                                class="w-full py-2 px-3 bg-green-600 rounded text-white text-xs font-medium hover:bg-green-700 transition-colors flex items-center justify-center shadow-sm">
                                Setujui Draft Regulasi
                            </button>
                        </form>
                    </div>

                @endif
            </div>

        </div>
    </div>

    @push('scripts')
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
    </style>
    @endpush
</x-app-layout>

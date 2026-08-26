<x-app-layout>
    @section('title', 'Tinjau Regulasi')

    <div class="mb-4 flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.regulasi.index') }}"
            class="text-sm font-medium text-slate-500 hover:text-slate-800 flex items-center gap-1 transition-colors">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
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
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-btn hover:bg-primary-light transition-colors flex-shrink-0">
                            📥 Unduh
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
            <div class="rounded-xl p-5 shadow-sm mb-5 relative overflow-hidden flex-shrink-0" style="background-color: #e0f2fe; color: #0c4a6e;">
                <div class="absolute top-0 right-0 p-4 opacity-20">
                    <span class="material-symbols-outlined text-8xl" style="color: #0284c7;">account_balance</span>
                </div>
                <div class="relative z-10">
                    <p class="text-xs mb-1 font-mono uppercase tracking-wider" style="color: #0369a1;">{{ $regulasi->no_regulasi }}</p>
                    <h2 class="text-xl font-bold mb-3" style="color: #082f49;">{{ strtoupper($regulasi->desa->nama_desa) }}</h2>
                    
                    <div class="text-sm" style="color: #0f172a;">
                        <p class="mb-1"><span class="opacity-70">Layanan:</span> Evaluasi Hukum ({{ ucfirst($regulasi->tipe) }})</p>
                        <p class="mb-1"><span class="opacity-70">Tanggal:</span> {{ $regulasi->tgl_diajukan ? $regulasi->tgl_diajukan->format('d/m/y') : '-' }}</p>
                    </div>

                    <div class="mt-4 pt-4 border-t border-blue-200">
                        <p class="text-sm font-semibold mb-1 leading-snug">{{ $regulasi->judul }}</p>
                        @if($regulasi->deskripsi)
                            <p class="text-xs mt-2 opacity-90 leading-relaxed italic border-l-2 border-blue-300 pl-2">{{ $regulasi->deskripsi }}</p>
                        @else
                            <p class="text-xs mt-2 opacity-60 italic">Tidak ada keterangan yang dilampirkan.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel Aksi -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6 flex-shrink-0">
                <h3 class="text-md font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Tindakan Admin</h3>

                @if($regulasi->status === 'disahkan')
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
                @else
                    
                    <!-- Form Setujui -->
                    <form action="{{ route('admin.regulasi.setujui', $regulasi) }}" method="POST" class="mb-6 pb-6 border-b border-slate-200">
                        @csrf
                        <div class="mb-4">
                            <h4 class="text-sm font-bold text-green-700 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">verified</span>
                                Setujui Draft Regulasi
                            </h4>
                            <p class="text-[10px] text-slate-500 mt-1">Gunakan form ini jika dokumen sudah dikoreksi dan benar. Desa kemudian akan mengunggah versi PDF final untuk disahkan.</p>
                        </div>

                        <button type="submit" onclick="return confirm('Apakah Anda yakin draf regulasi ini sudah benar dan disetujui?')"
                            class="w-full inline-flex justify-center items-center px-4 py-2 font-bold rounded-lg transition-colors text-sm shadow-sm bg-green-600 hover:bg-green-700 text-white">
                            Setujui Draft
                        </button>
                    </form>
                    
                    <!-- Form Revisi -->
                    <form action="{{ route('admin.regulasi.kembalikan', $regulasi) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="catatan" class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Kelengkapan dari Admin untuk Desa</label>
                            <textarea name="catatan" id="catatan" rows="5"
                                class="w-full text-sm rounded-lg border-slate-300 text-slate-800 bg-white focus:border-slate-500 focus:ring-slate-500 shadow-sm"
                                placeholder="Tuliskan catatan perbaikan jika ada dokumen yang kurang lengkap..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="file_catatan_dinas" class="block text-xs font-bold text-slate-700 mb-1.5">Unggah Draf Coretan (Opsional)</label>
                            <input type="file" name="file_catatan_dinas" id="file_catatan_dinas"
                                class="w-full text-xs box-border rounded-lg border-slate-300 p-1.5 bg-slate-50" accept=".doc,.docx,.pdf">
                            <p class="text-[10px] text-slate-500 mt-1">Lampirkan file bila ada coretan khusus.</p>
                        </div>

                        <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2 font-bold rounded-lg transition-colors text-sm shadow-sm"
                            style="background-color: #0A1A3A; color: white;">
                            Kembalikan untuk Revisi
                        </button>
                    </form>



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

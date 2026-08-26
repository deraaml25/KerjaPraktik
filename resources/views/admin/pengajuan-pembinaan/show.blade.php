<x-app-layout>
    @section('title', 'Detail Pengajuan — ' . $pengajuanPembinaan->judul_kegiatan)

    <div>
        <div class="mb-5 flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.pengajuan-pembinaan.index') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm group">
                <svg class="w-4 h-4 mr-2 text-slate-500 group-hover:text-slate-700 group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Pengajuan
            </a>
        </div>

        @if(session('success'))
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session("success") }}',
                        showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    position: 'top'
                    });
                });
            </script>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start h-[80vh]">
            <!-- Dokumen Persyaratan (Kiri) -->
            <div class="lg:col-span-7 h-full overflow-y-auto custom-scrollbar pr-2 space-y-5">
                    {{-- Surat Permohonan Narasumber --}}
                    <div class="bg-surface rounded-card border border-border shadow-sm flex flex-col overflow-hidden">
                        <div class="px-4 py-3 border-b border-border bg-gray-50 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-ink">Surat Permohonan Narasumber</p>
                            </div>
                            @if($pengajuanPembinaan->file_surat_permohonan)
                                <a href="{{ asset('storage/' . $pengajuanPembinaan->file_surat_permohonan) }}" target="_blank"
                                    class="inline-flex items-center px-3 py-1.5 bg-primary text-white text-xs font-medium rounded hover:bg-primary-light transition-colors flex-shrink-0" title="Unduh Berkas">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Unduh
                                </a>
                            @else
                                <span class="text-xs text-muted italic">Tidak dilampirkan</span>
                            @endif
                        </div>
                            @if($pengajuanPembinaan->file_surat_permohonan)
                                @php $ext1 = strtolower(pathinfo($pengajuanPembinaan->file_surat_permohonan, PATHINFO_EXTENSION)); @endphp
                                @if($ext1 === 'pdf')
                                    <iframe src="{{ asset('storage/' . $pengajuanPembinaan->file_surat_permohonan) }}"
                                        class="w-full border-0" style="height: 480px;"></iframe>
                                @elseif(in_array($ext1, ['doc', 'docx']))
                                    <div class="p-4 bg-white" style="min-height: 200px;">
                                        <div id="doc-loading-1" class="flex items-center gap-2 text-slate-400 text-sm py-4">
                                            <svg class="animate-spin w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                            Memuat dokumen...
                                        </div>
                                        <div id="doc-content-1" class="hidden prose prose-sm max-w-none text-slate-800 leading-relaxed"></div>
                                        <div id="doc-error-1" class="hidden text-xs text-slate-400 italic py-2">Gagal memuat pratinjau. Gunakan tombol Unduh.</div>
                                    </div>
                                    <script>
                                    (function(){
                                        var url = '{{ asset('storage/' . $pengajuanPembinaan->file_surat_permohonan) }}';
                                        function loadMammoth(cb) {
                                            if (window.mammoth) return cb();
                                            var s = document.createElement('script');
                                            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.8.0/mammoth.browser.min.js';
                                            s.onload = cb; s.onerror = function(){ showErr('1'); };
                                            document.head.appendChild(s);
                                        }
                                        function showErr(n){ document.getElementById('doc-loading-'+n).classList.add('hidden'); document.getElementById('doc-error-'+n).classList.remove('hidden'); }
                                        loadMammoth(function(){
                                            fetch(url).then(function(r){ return r.arrayBuffer(); })
                                            .then(function(buf){ return mammoth.convertToHtml({arrayBuffer:buf}); })
                                            .then(function(res){
                                                document.getElementById('doc-loading-1').classList.add('hidden');
                                                var el = document.getElementById('doc-content-1');
                                                el.innerHTML = res.value;
                                                el.classList.remove('hidden');
                                            }).catch(function(){ showErr('1'); });
                                        });
                                    })();
                                    </script>
                                @endif
                            @endif
                        </div>

                    {{-- Surat Undangan --}}
                    <div class="bg-surface rounded-card border border-border shadow-sm flex flex-col overflow-hidden">
                        <div class="px-4 py-3 border-b border-border bg-gray-50 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-ink">Surat Undangan</p>
                            </div>
                            @if($pengajuanPembinaan->file_undangan)
                                <a href="{{ asset('storage/' . $pengajuanPembinaan->file_undangan) }}" target="_blank"
                                    class="inline-flex items-center px-3 py-1.5 bg-primary text-white text-xs font-medium rounded hover:bg-primary-light transition-colors flex-shrink-0" title="Unduh Berkas">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Unduh
                                </a>
                            @else
                                <span class="text-xs text-muted italic">Tidak dilampirkan</span>
                            @endif
                        </div>
                            @if($pengajuanPembinaan->file_undangan)
                                @php $ext2 = strtolower(pathinfo($pengajuanPembinaan->file_undangan, PATHINFO_EXTENSION)); @endphp
                                @if($ext2 === 'pdf')
                                    <iframe src="{{ asset('storage/' . $pengajuanPembinaan->file_undangan) }}"
                                        class="w-full border-0" style="height: 480px;"></iframe>
                                @elseif(in_array($ext2, ['doc', 'docx']))
                                    <div class="p-4 bg-white" style="min-height: 200px;">
                                        <div id="doc-loading-2" class="flex items-center gap-2 text-slate-400 text-sm py-4">
                                            <svg class="animate-spin w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                            Memuat dokumen...
                                        </div>
                                        <div id="doc-content-2" class="hidden prose prose-sm max-w-none text-slate-800 leading-relaxed"></div>
                                        <div id="doc-error-2" class="hidden text-xs text-slate-400 italic py-2">Gagal memuat pratinjau. Gunakan tombol Unduh.</div>
                                    </div>
                                    <script>
                                    (function(){
                                        var url = '{{ asset('storage/' . $pengajuanPembinaan->file_undangan) }}';
                                        function loadMammoth(cb) {
                                            if (window.mammoth) return cb();
                                            var s = document.createElement('script');
                                            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.8.0/mammoth.browser.min.js';
                                            s.onload = cb; s.onerror = function(){ showErr('2'); };
                                            document.head.appendChild(s);
                                        }
                                        function showErr(n){ document.getElementById('doc-loading-'+n).classList.add('hidden'); document.getElementById('doc-error-'+n).classList.remove('hidden'); }
                                        loadMammoth(function(){
                                            fetch(url).then(function(r){ return r.arrayBuffer(); })
                                            .then(function(buf){ return mammoth.convertToHtml({arrayBuffer:buf}); })
                                            .then(function(res){
                                                document.getElementById('doc-loading-2').classList.add('hidden');
                                                var el = document.getElementById('doc-content-2');
                                                el.innerHTML = res.value;
                                                el.classList.remove('hidden');
                                            }).catch(function(){ showErr('2'); });
                                        });
                                    })();
                                    </script>
                                @endif
                            @endif
                        </div>
            </div>

            <!-- Panel Kanan -->
            <div class="lg:col-span-5 h-full overflow-y-auto custom-scrollbar pr-2 space-y-5">
                    <!-- Detail Pengajuan -->
                    <div class="bg-primary text-white rounded-card shadow-sm p-4">
                        <div class="flex justify-between items-start mb-2 gap-2">
                            <div>
                                <p class="text-[10px] font-mono text-blue-200 uppercase tracking-wider">{{ $pengajuanPembinaan->created_at->format('d/m/y, H:i') }}</p>
                                <h2 class="text-lg font-display font-bold leading-tight mt-0.5">{{ $pengajuanPembinaan->judul_kegiatan }}</h2>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] leading-none font-bold uppercase tracking-wider {{ $pengajuanPembinaan->status_color }} flex-shrink-0 shadow-sm border border-white/20">
                                {{ $pengajuanPembinaan->status_label }}
                            </span>
                        </div>
                        <div class="text-xs border-t border-white/20 pt-2 flex flex-col gap-1.5">
                            <p><span class="text-blue-200 inline-block w-20">Desa:</span> <span class="font-medium">{{ $pengajuanPembinaan->desa->nama_desa ?? '-' }}</span></p>
                            <p><span class="text-blue-200 inline-block w-20">Tgl Usulan:</span> <span class="font-medium">{{ $pengajuanPembinaan->tanggal_diajukan->format('d F Y') }}</span></p>
                        </div>
                    </div>

                    @if($pengajuanPembinaan->deskripsi)
                        <div class="bg-white rounded-card shadow-sm border border-border p-5">
                            <h3 class="text-xs font-bold text-ink uppercase tracking-wide mb-2">Deskripsi Kegiatan</h3>
                            <p class="text-sm text-muted leading-relaxed whitespace-pre-wrap">{{ $pengajuanPembinaan->deskripsi }}</p>
                        </div>
                    @endif



                    <!-- Form Balas -->
                    <div class="bg-white rounded-card shadow-sm border border-border p-6">
                    <h3 class="text-md font-display font-bold text-ink mb-4 pb-2 border-b border-border">
                        Berikan Balasan
                    </h3>

                    <form action="{{ route('admin.pengajuan-pembinaan.balas', $pengajuanPembinaan) }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label for="status" class="block text-sm font-medium text-ink mb-1">Status Keputusan <span class="text-red-500">*</span></label>
                            <select name="status" id="status" required
                                class="w-full rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm text-sm">
                                <option value="disetujui" {{ $pengajuanPembinaan->status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ $pengajuanPembinaan->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="selesai" {{ $pengajuanPembinaan->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>

                        <div>
                            <label for="catatan_admin" class="block text-sm font-medium text-ink mb-1">Catatan / Balasan <span class="text-red-500">*</span></label>
                            <textarea name="catatan_admin" id="catatan_admin" rows="6" required
                                class="w-full rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm text-sm"
                                placeholder="Tulis balasan resmi, informasi jadwal, atau alasan penolakan...">{{ $pengajuanPembinaan->catatan_admin }}</textarea>
                        </div>

                        <button type="submit"
                            class="w-full px-4 py-2.5 bg-primary text-white font-medium rounded-btn hover:bg-primary-light transition-colors shadow-sm text-sm">
                            Kirim Balasan
                        </button>
                    </form>

                    @if($errors->any())
                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded text-xs text-red-700">
                            @foreach($errors->all() as $err)
                                <p>{{ $err }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


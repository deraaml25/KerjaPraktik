<x-app-layout>
    @section('title', 'Ajuan BPD: ' . $ajuanBpd->no_registrasi)

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.ajuan-bpd.index') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm group">
            <svg class="w-4 h-4 mr-2 text-slate-500 group-hover:text-slate-700 group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Antrean
        </a>
        <div class="flex items-center gap-3">
            <!-- Badge status dihilangkan atas permintaan user -->
        </div>
    </div>

    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const posisi = '{{ session("posisi_baru") }}';
                const isRevisi = posisi === 'Pegawai';

                Swal.fire({
                    icon: isRevisi ? 'warning' : 'success',
                    title: isRevisi ? 'Dikembalikan untuk Revisi' : (posisi ? posisi : 'Berhasil!'),
                    text: '{{ session("success") }}',
                    showConfirmButton: false,
                    timer: 4000,
                    toast: true,
                    position: 'top'
                });
            });
        </script>
    @endif

    <div class="{{ $ajuanBpd->metode !== 'offline' ? 'grid grid-cols-1 lg:grid-cols-12 gap-6' : 'max-w-4xl mx-auto' }} h-[80vh]">

        @if($ajuanBpd->metode !== 'offline')
        {{-- PANEL KIRI: PREVIEW PDF --}}
        <div
            class="lg:col-span-7 bg-surface rounded-card border border-border shadow-sm flex flex-col overflow-hidden h-full">
            <div class="px-4 py-3 border-b border-border bg-gray-50 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-ink">Berkas Keseluruhan Persyaratan</p>
                </div>
                @if($ajuanBpd->berkas_zip)
                    <div class="flex gap-2">

                        <a href="{{ Storage::disk('public')->url($ajuanBpd->berkas_zip) }}" target="_blank"
                            class="inline-flex items-center px-3 py-1.5 bg-primary text-white text-xs font-medium rounded hover:bg-primary-light transition-colors flex-shrink-0">
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Unduh
                        </a>
                    </div>
                @else
                    <span id="preview-title" class="text-xs text-muted hidden"></span>
                @endif
            </div>
            <div class="flex-1 bg-gray-200 relative p-2" id="pdf-container">
                <!-- PDF Viewer / Empty State -->
                <div id="pdf-empty-state" class="absolute inset-0 flex flex-col items-center justify-center text-muted">
                    <svg class="w-16 h-16 mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    @if($ajuanBpd->berkas_zip && preg_match('/\.zip|\.rar$/i', $ajuanBpd->berkas_zip))
                        <p class="font-medium text-center px-4">Berkas persyaratan berupa file arsip (ZIP/RAR).<br>Silakan klik tombol "Unduh" di sudut kanan atas untuk melihat isinya.</p>
                    @elseif($ajuanBpd->berkas_zip && preg_match('/\.(pdf|jpe?g|png)$/i', $ajuanBpd->berkas_zip))
                        <p class="font-medium text-center px-4">Berkas persyaratan berupa file PDF/Gambar.<br>Memuat pratinjau otomatis...</p>
                    @else
                        <p class="font-medium text-center px-4">Pilih dokumen pada tabel di kanan untuk memuat pratinjau</p>
                    @endif
                </div>
                <iframe id="pdf-iframe" src="" class="w-full h-full rounded shadow-sm border border-gray-300 hidden" frameborder="0"></iframe>
                <img id="img-preview" src="" class="w-full h-full object-contain rounded shadow-sm border border-gray-300 hidden">
            </div>
        </div>
        @endif

        {{-- PANEL KANAN: VERIFIKASI GRANULAR & DISPOSISI --}}
        <div class="{{ $ajuanBpd->metode !== 'offline' ? 'lg:col-span-5' : 'w-full' }} flex flex-col gap-6 h-full overflow-y-auto pr-2 custom-scrollbar">

            {{-- IDENTITAS DESA --}}
            <div class="bg-primary text-white rounded-card shadow-sm p-4 flex-shrink-0">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-[10px] font-mono text-primary-soft">{{ $ajuanBpd->no_registrasi }}</p>
                        <h2 class="text-lg font-display font-bold leading-tight">{{ $ajuanBpd->desa->nama_desa }}</h2>
                    </div>
                </div>
                <div class="text-xs border-t border-white/20 pt-2 flex flex-col gap-1">
                    <p><span class="text-primary-soft inline-block w-16">Layanan:</span>
                        Ajuan BPD ({{ ucfirst($ajuanBpd->jenis_ajuan) }})</p>

                    <p class="text-primary-soft font-medium mt-1">Daftar BPD ({{ $ajuanBpd->pesertas->count() }} Orang):</p>
                    <div class="max-h-20 overflow-y-auto custom-scrollbar space-y-1.5 pr-1">
                        @foreach($ajuanBpd->pesertas as $index => $peserta)
                            <div class="bg-black/10 rounded p-1.5 border border-white/5 text-[10px]">
                                <span class="font-bold block">{{ $index + 1 }}. {{ $peserta->bpd->nama }}</span>
                                <span class="opacity-80 block">{{ $peserta->bpd->jabatan }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- LIST DOKUMEN CHECKLIST --}}
            <div class="bg-surface rounded-card border border-border shadow-sm flex flex-col overflow-hidden flex-shrink-0">
                <div class="px-5 py-4 border-b border-border bg-gray-50 flex justify-between items-center">
                    <h3 class="font-display font-semibold text-ink">Verifikasi Syarat</h3>
                    <a href="{{ route('admin.ajuan-bpd.print-syarat', $ajuanBpd->id) }}" target="_blank" class="inline-flex items-center text-xs px-2 py-1 bg-white border border-gray-300 rounded font-medium text-ink hover:bg-gray-50 transition-colors shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print Checklist
                    </a>
                </div>

                <div class="divide-y divide-border">
                    @foreach($ajuanBpd->checklists->sortBy('templateChecklist.urutan') as $item)
                        <div class="p-3 transition-colors hover:bg-slate-50">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white text-[10px] font-bold text-ink border border-slate-200 shadow-sm flex-shrink-0">{{ $item->templateChecklist->urutan }}</span>
                                <div class="flex-1 flex flex-col sm:flex-row sm:items-center gap-2 justify-between">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-xs font-semibold text-ink leading-tight">
                                            {{ $item->templateChecklist->nama_dokumen }}
                                        </p>

                                        @if($item->file_path)
                                            <button
                                                onclick="previewFile('{{ Storage::disk('public')->url($item->file_path) }}', '{{ addslashes($item->templateChecklist->nama_dokumen) }}')"
                                                class="ml-2 inline-flex items-center text-[10px] px-2 py-1 bg-white hover:bg-gray-50 border border-gray-300 rounded font-medium text-ink transition-colors shadow-sm">
                                                Lihat Berkas
                                            </button>
                                        @elseif(!$ajuanBpd->berkas_zip)
                                            <span class="ml-2 inline-block px-2 py-1 bg-gray-100 text-gray-500 text-[10px] font-medium rounded border border-gray-200">Belum Terunggah</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 flex-shrink-0 ml-auto sm:ml-4">
                                        <span class="verify-saved-indicator text-[10px] text-green-600 font-medium hidden">✓ Tersimpan</span>
                                        <form action="{{ route('admin.ajuan-bpd.verify-checklist', [$ajuanBpd->id, $item->id]) }}" method="POST" class="verify-form flex-shrink-0" data-url="{{ route('admin.ajuan-bpd.verify-checklist', [$ajuanBpd->id, $item->id]) }}">
                                            @csrf
                                            <input type="checkbox" name="status" value="terverifikasi" 
                                                   class="w-5 h-5 text-primary focus:ring-primary border-gray-300 rounded shadow-sm cursor-pointer transition-colors verify-checkbox" 
                                                   {{ $item->status === 'terverifikasi' ? 'checked' : '' }}
                                                   title="Tandai Sesuai">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- PANEL CATATAN ADMIN --}}
            <div class="bg-surface rounded-card border border-border shadow-sm flex flex-col mb-2 overflow-hidden flex-shrink-0">
                <div class="px-5 py-4 border-b border-border bg-gray-50 flex justify-between items-center">
                    <h3 class="font-display font-semibold text-ink">Catatan Perbaikan Desa</h3>
                </div>
                <form action="{{ route('admin.ajuan-bpd.catatan', $ajuanBpd->id) }}" method="POST" class="p-5">
                    @csrf
                    <textarea name="catatan_admin" rows="3" class="w-full text-sm border-gray-300 rounded-md focus:border-primary focus:ring focus:ring-primary/20" placeholder="Tuliskan catatan perbaikan jika ada dokumen yang kurang lengkap...">{{ $ajuanBpd->catatan_admin }}</textarea>
                    <div class="mt-3 text-right">
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-light text-white text-xs font-medium rounded shadow-sm transition-colors">Kirim Catatan & Minta Revisi</button>
                    </div>
                </form>
            </div>

            {{-- Kolom Kanan: Disposisi & Penerbitan SK --}}
            <div class="bg-surface rounded-card shadow-sm border border-border flex flex-col mb-10 flex-shrink-0">
                <div class="p-5">
                    <h3 class="text-base font-display font-semibold text-ink mb-1">Status Proses</h3>
                    <p class="text-xs text-muted mb-4 pb-4 border-b border-border">Pantau tahapan perjalanan ajuan secara real-time.</p>
                    
                    <x-pjkades-tracker :posisiAktif="$ajuanBpd->posisi_surat ?? 'Berkas Diterima'" :status="$ajuanBpd->status" :pjkades="$ajuanBpd" />
                </div>

                <div class="px-5 py-4 bg-gray-50 border-t border-border mt-auto rounded-b-card">
                    <h3 class="text-xs font-semibold text-muted mb-2">Tindak Lanjut Cepat</h3>
                    <div class="flex flex-col gap-2">
                        @php
                            $posisiOptions = [
                                'Berkas Diterima',
                                'Verifikasi & Validasi Petugas',
                                'Penyusunan Draft Rekomendasi',
                                'Verifikasi & Validasi Kabid PDPD',
                                'Verifikasi & Validasi Sekretaris Dinas',
                                'Verifikasi & Validasi Kepala Dinas',
                                'Verifikasi & Validasi Kepala Bagian Hukum',
                                'Verifikasi & Validasi Asisten Pemerintahan & Kesra',
                                'Verifikasi & Validasi Sekda',
                                'Tanda Tangan Bupati',
                                'Penomoran TU Umum Setda',
                                'Sudah di Dinpermasdes',
                                'Sudah di Desa (Nama Penerima)'
                            ];
                            $currentIndex = array_search($ajuanBpd->posisi_surat ?? 'Berkas Diterima', $posisiOptions);
                            if ($currentIndex === false) $currentIndex = 0;
                            $nextPosisi = isset($posisiOptions[$currentIndex + 1]) ? $posisiOptions[$currentIndex + 1] : null;
                        @endphp

                        @if($nextPosisi === 'Sudah di Desa (Nama Penerima)')
                        <form action="{{ route('admin.ajuan-bpd.disposisi', $ajuanBpd->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tahapan" value="{{ $nextPosisi }}">
                            <button type="submit"
                                onclick="return confirm('Selesaikan dan setujui usulan ini?')"
                                class="w-full py-2 px-3 bg-green-600 rounded text-white text-xs font-medium hover:bg-green-700 transition-colors flex items-center justify-center shadow-sm">
                                Selesai & Setujui Usulan
                            </button>
                        </form>
                        @elseif($nextPosisi)
                        <form action="{{ route('admin.ajuan-bpd.disposisi', $ajuanBpd->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tahapan" value="{{ $nextPosisi }}">
                            <button type="submit"
                                class="w-full py-2 px-3 bg-primary rounded text-white text-xs font-medium hover:bg-primary-light transition-colors flex items-center justify-center shadow-sm">
                                Lanjutkan ke : {{ $nextPosisi }}
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('admin.ajuan-bpd.disposisi', $ajuanBpd->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tahapan" value="revisi">
                            <button type="button" onclick="const note = prompt('Masukkan catatan revisi:'); if(note) { this.form.insertAdjacentHTML('beforeend', '<input type=\'hidden\' name=\'catatan\' value=\''+note+'\'>'); this.form.submit(); }"
                                class="w-full py-2 px-3 bg-white border border-red-200 text-red-600 rounded text-xs font-medium hover:bg-red-50 transition-colors flex items-center justify-center shadow-sm">
                                Revisi
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            function previewFile(url, title) {
                document.getElementById('pdf-empty-state').classList.add('hidden');
                const iframe = document.getElementById('pdf-iframe');
                const img = document.getElementById('img-preview');
                
                iframe.classList.add('hidden');
                img.classList.add('hidden');

                if (url.toLowerCase().endsWith('.pdf')) {
                    iframe.src = url;
                    iframe.classList.remove('hidden');
                } else {
                    img.src = url;
                    img.classList.remove('hidden');
                }
                
                document.getElementById('preview-title').innerText = title;
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Auto preview berkas zip/pdf if available
                @if($ajuanBpd->berkas_zip && preg_match('/\.(pdf|jpe?g|png)$/i', $ajuanBpd->berkas_zip))
                    setTimeout(() => {
                        previewFile('{{ Storage::disk("public")->url($ajuanBpd->berkas_zip) }}', 'Berkas Keseluruhan Persyaratan');
                    }, 500);
                @endif

                document.querySelectorAll('.verify-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const form = this.closest('form');
                        const wrapper = form.closest('.flex.items-center.gap-2');
                        const indicator = wrapper ? wrapper.querySelector('.verify-saved-indicator') : null;
                        const url = form.getAttribute('data-url');
                        const token = form.querySelector('input[name="_token"]').value;
                        const status = this.checked ? 'terverifikasi' : 'menunggu_verifikasi';
                        
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ status: status })
                        }).then(response => {
                            if (indicator) {
                                indicator.classList.remove('hidden');
                                setTimeout(() => indicator.classList.add('hidden'), 2000);
                            }
                        }).catch(err => {
                            console.error('Network error during verification update', err);
                            this.checked = !this.checked;
                        });
                    });
                });
            });
        </script>
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background-color: #cbd5e1;
                border-radius: 20px;
            }
        </style>
    @endpush
</x-app-layout>


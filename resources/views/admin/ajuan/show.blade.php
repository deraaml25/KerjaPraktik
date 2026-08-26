<x-app-layout>
    @section('title', 'Verifikasi Granular: ' . $ajuan->no_registrasi)

    <div class="mb-5 flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.ajuan.index') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm group">
            <svg class="w-4 h-4 mr-2 text-slate-500 group-hover:text-slate-700 group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Antrean
        </a>
    </div>

    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const posisi = '{{ session("posisi_baru") }}';
                const isRevisi = posisi === 'Pegawai';

                const milestoneIcon = {
                    'Pegawai': '↩',
                    'Verifikasi & Validasi Petugas': '🔍',
                    'Penyusunan Draft Rekomendasi': '📝',
                    'Verifikasi & Validasi Kabid PDPD': '📋',
                    'Verifikasi & Validasi Sekretaris Dinas': '📋',
                    'Verifikasi & Validasi Kepala Dinas': '📋',
                    'Verifikasi & Validasi Kepala Bagian Hukum': '⚖️',
                    'Verifikasi & Validasi Asisten Pemerintahan & Kesra': '📋',
                    'Verifikasi & Validasi Sekda': '📋',
                    'Tanda Tangan Bupati': '✍️',
                    'Penomoran TU Umum Setda': '🔢',
                    'Sudah di Dinpermasdes': '🏛️',
                    'Selesai (Surat Terbit)': '🎉',
                };

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

    <div class="{{ $ajuan->metode !== 'offline' ? 'grid grid-cols-1 lg:grid-cols-12 gap-6' : 'max-w-4xl mx-auto' }} h-[80vh]">

        @if($ajuan->metode !== 'offline')
        {{-- PANEL KIRI: PREVIEW PDF --}}
        <div
            class="lg:col-span-7 bg-surface rounded-card border border-border shadow-sm flex flex-col overflow-hidden h-full">
            <div class="px-4 py-3 border-b border-border bg-gray-50 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-ink">Berkas Keseluruhan Persyaratan</p>
                </div>
                <div class="flex items-center gap-2">
                    <span id="preview-title" class="hidden"></span>
                    @if($ajuan->metode === 'online' && $ajuan->berkas_zip)
                        <div class="flex gap-2">

                            <a href="{{ Storage::disk('public')->url($ajuan->berkas_zip) }}" target="_blank"
                                class="inline-flex items-center px-3 py-1.5 bg-primary text-white text-xs font-medium rounded hover:bg-primary-light transition-colors flex-shrink-0">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Unduh
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="flex-1 bg-gray-200 relative p-2" id="pdf-container">
                <!-- PDF Viewer / Empty State -->
                @php
                    $isPdfZip = $ajuan->berkas_zip && str_ends_with(strtolower($ajuan->berkas_zip), '.pdf');
                    $pdfZipUrl = $isPdfZip ? Storage::disk('public')->url($ajuan->berkas_zip) : '';
                @endphp
                <div id="pdf-empty-state" class="absolute inset-0 flex flex-col items-center justify-center text-muted {{ $isPdfZip ? 'hidden' : '' }}">
                    <svg class="w-16 h-16 mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="font-medium">Klik tombol "Lihat" pada tabel di kanan</p>
                </div>
                <iframe id="pdf-iframe" src="{{ $pdfZipUrl }}" class="w-full h-full rounded shadow-sm border border-gray-300 {{ $isPdfZip ? '' : 'hidden' }}"
                    frameborder="0"></iframe>
            </div>
        </div>
        @endif

        {{-- PANEL KANAN: VERIFIKASI GRANULAR & DISPOSISI --}}
        <div class="{{ $ajuan->metode !== 'offline' ? 'lg:col-span-5 flex flex-col' : 'w-full flex flex-col lg:flex-row' }} gap-6 h-full overflow-y-auto pr-2 custom-scrollbar">

            {{-- Kolom Kiri (Atas untuk online, Kiri 70% untuk offline) --}}
            <div class="w-full flex flex-col gap-6" @if($ajuan->metode === 'offline') style="flex: 0 0 calc(70% - 12px); max-width: calc(70% - 12px);" @endif>
            {{-- IDENTITAS DESA --}}
            <div class="bg-primary text-white rounded-card shadow-sm p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-[10px] font-mono text-primary-soft">{{ $ajuan->no_registrasi }}</p>
                        <h2 class="text-lg font-display font-bold leading-tight">{{ $ajuan->desa->nama_desa }}</h2>
                    </div>
                </div>
                <div class="text-xs border-t border-white/20 pt-2 flex flex-col gap-1">
                    <p><span class="text-primary-soft inline-block w-16">Layanan:</span>
                        {{ $ajuan->jenisLayanan->nama }}</p>

                    <p class="text-primary-soft font-medium mt-1">Daftar Peserta ({{ $ajuan->pesertas->count() }}
                        Orang):</p>
                    <div class="max-h-20 overflow-y-auto custom-scrollbar space-y-1.5 pr-1">
                        @foreach($ajuan->pesertas as $index => $peserta)
                            <div class="bg-black/10 rounded p-1.5 border border-white/5 text-[10px]">
                                <span class="font-bold block">{{ $index + 1 }}. {{ $peserta->perangkatDesa->nama }}</span>
                                <span class="opacity-80 block">{{ $peserta->perangkatDesa->jabatan }}</span>
                                @if($peserta->jabatan_baru)
                                    <span class="bg-white/20 px-1 py-0.5 rounded text-[9px] mt-0.5 inline-block">M =>
                                        {{ $peserta->jabatan_baru }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- LIST DOKUMEN CHECKLIST --}}
            <div class="bg-surface rounded-card border border-border shadow-sm flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-border bg-gray-50 flex justify-between items-center">
                    <h3 class="font-display font-semibold text-ink">Verifikasi Syarat</h3>
                    <a href="{{ route('admin.ajuan.print-syarat', $ajuan) }}" target="_blank" class="inline-flex items-center text-xs px-2 py-1 bg-white border border-gray-300 rounded font-medium text-ink hover:bg-gray-50 transition-colors shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print Checklist
                    </a>
                </div>
                <div class="divide-y divide-border">
                        @foreach($ajuan->checklistAjuans as $index => $item)
                            <div class="p-4 border-l-4 transition-colors {{ $item->status == 'valid' || $item->status == 'lengkap' ? 'border-green-500 bg-green-50/40' : ($item->status == 'kurang' || $item->status == 'tidak_sesuai' ? 'border-red-500 bg-red-50/40' : 'border-amber-400 bg-amber-50/30') }}">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white text-xs font-bold text-ink border border-border shadow-sm flex-shrink-0">{{ $item->templateChecklist->urutan }}</span>
                                    <div class="flex-1 flex flex-col sm:flex-row sm:items-center gap-2 justify-between">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="text-sm font-semibold text-ink leading-tight">
                                                {{ $item->templateChecklist->nama_dokumen }}
                                            </p>

                                            @if($item->file_path)
                                                <button type="button"
                                                    onclick="previewPdf('{{ Storage::disk('public')->url($item->file_path) }}', '{{ addslashes($item->templateChecklist->nama_dokumen) }}')"
                                                    class="ml-2 inline-flex items-center text-xs px-2 py-1 bg-white hover:bg-gray-50 border border-gray-300 rounded font-medium text-ink transition-colors shadow-sm">
                                                    Lihat PDF
                                                </button>
                                            @elseif($ajuan->metode === 'online' && !$ajuan->berkas_zip)
                                                <span class="ml-2 inline-block px-2 py-1 bg-gray-100 text-gray-500 text-xs font-medium rounded border border-gray-200">Belum Terunggah</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-2 flex-shrink-0 ml-auto sm:ml-4">
                                            <span class="verify-saved-indicator text-xs text-green-600 font-medium hidden">✓ Tersimpan</span>
                                            <form action="{{ route('admin.ajuan.verify', [$ajuan->id, $item->id]) }}" method="POST" class="verify-form flex-shrink-0" data-url="{{ route('admin.ajuan.verify', [$ajuan->id, $item->id]) }}">
                                                @csrf
                                                <input type="checkbox" name="status" value="valid"
                                                       class="w-7 h-7 text-primary focus:ring-primary border-gray-300 rounded shadow-sm cursor-pointer transition-colors verify-checkbox"
                                                       {{ $item->status == 'valid' || $item->status == 'lengkap' ? 'checked' : '' }}
                                                       title="Tandai Sesuai">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
            </div>

            {{-- PANEL BERKAS ZIP & CATATAN ADMIN --}}
            <div class="bg-surface rounded-card border border-border shadow-sm flex flex-col mb-2 overflow-hidden">
                <div class="px-5 py-4 border-b border-border bg-gray-50 flex justify-between items-center">
                    <h3 class="font-display font-semibold text-ink">Keseluruhan Persyaratan & Catatan</h3>

                </div>
                <form action="{{ route('admin.ajuan.update-catatan', $ajuan) }}" method="POST" class="p-5">
                    @csrf
                    <label class="block text-sm font-medium text-ink mb-2">Catatan Kelengkapan dari Admin untuk Desa</label>
                    <textarea name="catatan_admin" rows="3" class="w-full text-sm border-gray-300 rounded-md focus:border-primary focus:ring focus:ring-primary/20" placeholder="Tuliskan catatan lengkap atau tidaknya berkas keseluruhan di sini...">{{ $ajuan->catatan_admin }}</textarea>
                    <div class="mt-3 text-right">
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-light text-white text-xs font-medium rounded shadow-sm transition-colors">Simpan Catatan Admin</button>
                    </div>
                </form>
            </div>

            </div>

            {{-- Kolom Kanan (Bawah untuk online, Kanan 30% untuk offline) --}}
            <div class="w-full flex flex-col gap-6" @if($ajuan->metode === 'offline') style="flex: 0 0 calc(30% - 12px); max-width: calc(30% - 12px);" @endif>
            {{-- PANEL MILESTONE TRACKER & ACTION BUTTONS --}}
            <div class="bg-surface rounded-card border border-border shadow-sm mb-10 flex flex-col">
                <div class="p-5">
                    <h3 class="text-base font-display font-semibold text-ink mb-1">Status Proses</h3>
                    <p class="text-xs text-muted mb-4 pb-4 border-b border-border">Pantau tahapan perjalanan ajuan ini secara real-time.</p>
                    <x-milestone-tracker :tahapAktif="$tahapAktif" :milestones="$ajuan->milestoneTrackings" :ajuan="$ajuan" />
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="px-5 py-4 bg-gray-50 border-t border-border mt-auto rounded-b-card">
                    <h3 class="text-xs font-semibold text-muted mb-2">Tindak Lanjut Cepat</h3>
                    <div class="flex flex-col gap-2">
                        @if($nextPosisi === 'Selesai (Surat Terbit)')
                        <form action="{{ route('admin.ajuan.disposisi', $ajuan) }}" method="POST">
                            @csrf
                            <input type="hidden" name="posisi_baru" value="{{ $nextPosisi }}">
                            <input type="hidden" name="status_ajuan_baru" value="selesai">
                            <button type="submit"
                                onclick="return confirm('Selesaikan usulan ini?')"
                                class="w-full py-2 px-3 bg-green-600 rounded text-white text-xs font-medium hover:bg-green-700 transition-colors flex items-center justify-center shadow-sm">
                                Selesai
                            </button>
                        </form>
                        @elseif($ajuan->posisi_surat !== 'Selesai (Surat Terbit)')
                        <form action="{{ route('admin.ajuan.disposisi', $ajuan) }}" method="POST">
                            @csrf
                            <input type="hidden" name="posisi_baru" value="{{ $nextPosisi }}">
                            <button type="submit"
                                class="w-full py-2 px-3 bg-primary rounded text-white text-xs font-medium hover:bg-primary-light transition-colors flex items-center justify-center shadow-sm">
                                Lanjutkan ke : {{ $nextPosisi }}
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('admin.ajuan.disposisi', $ajuan) }}" method="POST">
                            @csrf
                            <input type="hidden" name="posisi_baru" value="Pegawai">
                            <input type="hidden" name="status_ajuan_baru" value="direvisi">
                            <button type="submit"
                                class="w-full py-2 px-3 bg-white border border-red-300 text-red-600 rounded text-xs font-medium hover:bg-red-50 transition-colors flex items-center justify-center shadow-sm" title="Kembalikan ke Front Office (Butuh Revisi)">
                                Revisi
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleCatatan(radio, id) {
                const box = document.getElementById('catatan-box-' + id);
                if (radio.value === 'kurang') {
                    box.classList.remove('hidden');
                    box.classList.add('block');
                } else {
                    box.classList.remove('block');
                    box.classList.add('hidden');
                }
            }
            function previewPdf(url, title = 'Pratinjau Dokumen') {
                document.getElementById('pdf-empty-state').classList.add('hidden');
                document.getElementById('pdf-iframe').classList.remove('hidden');
                document.getElementById('pdf-iframe').src = url;
                
                const titleEl = document.getElementById('preview-title');
                if (titleEl) {
                    titleEl.innerText = title;
                    titleEl.classList.remove('hidden');
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Set default preview if available (like the bulk ZIP)
                const pdfIframe = document.getElementById('pdf-iframe');
                if(pdfIframe && pdfIframe.src && pdfIframe.src !== window.location.href) {
                    document.getElementById('pdf-empty-state').classList.add('hidden');
                    pdfIframe.classList.remove('hidden');
                }

                document.querySelectorAll('.verify-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const form = this.closest('.verify-form');
                        const indicator = form.parentElement.querySelector('.verify-saved-indicator');
                        const url = form.getAttribute('data-url');
                        const token = form.querySelector('input[name="_token"]').value;
                        const status = this.checked ? 'valid' : 'menunggu';
                        
                        const row = form.closest('.p-4');
                        if (this.checked) {
                            row.classList.remove('border-red-500', 'bg-red-50/40', 'border-amber-400', 'bg-amber-50/30');
                            row.classList.add('border-green-500', 'bg-green-50/40');
                        } else {
                            row.classList.remove('border-green-500', 'bg-green-50/40', 'border-red-500', 'bg-red-50/40');
                            row.classList.add('border-amber-400', 'bg-amber-50/30');
                        }

                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ 
                                status: status 
                            })
                        }).then(async response => {
                            if (!response.ok) {
                                const errData = await response.json().catch(() => ({}));
                                throw new Error(errData.message || 'Server error ' + response.status);
                            }
                            return response.json();
                        }).then(data => {
                            if (indicator) {
                                indicator.classList.remove('hidden');
                                setTimeout(() => indicator.classList.add('hidden'), 2000);
                            }
                        }).catch(err => {
                            console.error('Network error during verification update', err);
                            alert('Gagal menyimpan: ' + err.message);
                            this.checked = !this.checked;
                            if (this.checked) {
                                row.classList.remove('border-amber-400', 'bg-amber-50/30', 'border-red-500', 'bg-red-50/40');
                                row.classList.add('border-green-500', 'bg-green-50/40');
                            } else {
                                row.classList.remove('border-green-500', 'bg-green-50/40', 'border-red-500', 'bg-red-50/40');
                                row.classList.add('border-amber-400', 'bg-amber-50/30');
                            }
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

<x-app-layout>
    @section('title', 'Verifikasi Usulan SK Kades')

    <div class="mb-5 flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.pjkades.index') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm group">
            <svg class="w-4 h-4 mr-2 text-slate-500 group-hover:text-slate-700 group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Evaluasi SK Kades
        </a>
    </div>

    @if(session('success') || session('error'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const posisi = '{{ session("posisi_baru") }}';
                const isError = {{ session('error') ? 'true' : 'false' }};
                const isRevisi = posisi.toLowerCase().includes('revisi') || posisi === 'Pegawai';

                Swal.fire({
                    icon: isError ? 'error' : (isRevisi ? 'warning' : 'success'),
                    title: isError ? 'Gagal!' : (isRevisi ? 'Dikembalikan untuk Revisi' : (posisi ? posisi : 'Berhasil!')),
                    text: '{{ session("success") ?? session("error") }}',
                    showConfirmButton: false,
                    timer: 4000,
                    toast: true,
                    position: 'top'
                });
            });
        </script>
    @endif

    <div class="{{ $pjkades->metode !== 'offline' ? 'grid grid-cols-1 lg:grid-cols-12 gap-6' : 'max-w-4xl mx-auto' }} h-[80vh]">

        @if($pjkades->metode !== 'offline')
        {{-- PANEL KIRI: PREVIEW PDF --}}
        <div class="lg:col-span-7 bg-surface rounded-card border border-border shadow-sm flex flex-col overflow-hidden h-full">
            <div class="px-4 py-3 border-b border-border bg-gray-50 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-ink">Berkas Keseluruhan Persyaratan</p>
                    <p class="text-xs text-muted">SK Kades ({{ $pjkades->kategori === 'plt_kades' ? 'Plt Kades' : 'Pj Kades' }}) — {{ $pjkades->desa->nama_desa }}</p>
                </div>
                @if($pjkades->berkas_zip)
                    <div class="flex gap-2">
                        <a href="{{ Storage::disk('public')->url($pjkades->berkas_zip) }}" target="_blank"
                            class="inline-flex items-center px-3 py-1.5 bg-primary text-white text-xs font-medium rounded hover:bg-primary-light transition-colors flex-shrink-0">
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Unduh
                        </a>
                    </div>
                @endif
            </div>
            <div class="flex-1 bg-gray-200 relative p-2" id="pdf-container">
                <div id="pdf-empty-state" class="absolute inset-0 flex flex-col items-center justify-center text-muted">
                    <svg class="w-16 h-16 mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    @if($pjkades->berkas_zip && preg_match('/\.zip|\.rar$/i', $pjkades->berkas_zip))
                        <p class="font-medium text-center px-4">Berkas gabungan berupa file ZIP/RAR.<br>Silakan klik tombol "Unduh" di sudut kanan atas untuk melihat isinya.</p>
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
        <div class="{{ $pjkades->metode !== 'offline' ? 'lg:col-span-5' : 'w-full' }} flex flex-col gap-6 h-full overflow-y-auto pr-2 custom-scrollbar">

            {{-- IDENTITAS DESA / PROFIL CALON --}}
            <div class="bg-primary text-white rounded-card shadow-sm p-4 flex-shrink-0">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-[10px] font-mono text-primary-soft">{{ $pjkades->no_registrasi ?? ('SKK-' . $pjkades->id) }}</p>
                        <h2 class="text-lg font-display font-bold leading-tight">{{ $pjkades->desa->nama_desa }} (Kec. {{ $pjkades->desa->kecamatan->nama_kecamatan ?? '-' }})</h2>
                    </div>
                </div>
                <div class="text-xs border-t border-white/20 pt-2 flex flex-col gap-1">
                    <p><span class="text-primary-soft inline-block w-16">Layanan:</span>
                        Usulan SK Kades ({{ $pjkades->kategori === 'plt_kades' ? 'Plt / Pelaksana Tugas' : 'Pj / Penjabat' }})</p>
                    <p><span class="text-primary-soft inline-block w-16">Alasan:</span>
                        {{ $pjkades->alasan_nama ?? ($pjkades->alasanPemberhentian->nama ?? '-') }}</p>

                    <p class="text-primary-soft font-medium mt-1">Profil Calon:</p>
                    <div class="bg-black/10 rounded p-1.5 border border-white/5 text-[10px]">
                        @if($pjkades->kategori === 'plt_kades')
                            <span class="font-bold block">{{ $pjkades->nama_plt ?? '-' }}</span>
                            <span class="opacity-80 block">{{ $pjkades->nip_plt ?? '-' }} - {{ $pjkades->pangkat_plt ?? 'Sekretaris Desa' }}</span>
                        @else
                            <span class="font-bold block">{{ $pjkades->nama_pns ?? '-' }}</span>
                            <span class="opacity-80 block">{{ $pjkades->nip ?? '-' }} - {{ $pjkades->pangkat ?? '-' }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Checklist Dokumen Verification Table --}}
            <div class="bg-surface rounded-card shadow-sm border border-border overflow-hidden flex-shrink-0">
                <div class="px-5 py-4 border-b border-border bg-gray-50 flex items-center justify-between">
                    <h3 class="font-display font-semibold text-ink">Verifikasi Syarat</h3>
                    <a href="{{ route('admin.pjkades.print-syarat', $pjkades->id) }}" target="_blank" class="inline-flex items-center text-xs px-2 py-1 bg-white border border-gray-300 rounded font-medium text-ink hover:bg-gray-50 transition-colors shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print Checklist
                    </a>
                </div>

                <div class="divide-y divide-border">
                    @foreach($pjkades->checklists as $index => $item)
                        <div class="p-3 transition-colors hover:bg-slate-50">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white text-[10px] font-bold text-ink border border-slate-200 shadow-sm flex-shrink-0">{{ $index + 1 }}</span>
                                
                                <div class="flex-1 flex flex-col sm:flex-row sm:items-center gap-2 justify-between">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-xs font-semibold text-ink leading-tight">
                                            {{ $item->nama_dokumen }}
                                        </p>

                                        @if($item->file_path)
                                            <button type="button"
                                                onclick="previewFile('{{ asset('storage/' . $item->file_path) }}')"
                                                class="ml-2 inline-flex items-center text-[10px] px-2 py-1 bg-white hover:bg-gray-50 border border-gray-300 rounded font-medium text-ink transition-colors shadow-sm">
                                                Lihat Dokumen
                                            </button>
                                        @elseif(!$pjkades->berkas_zip)
                                            <span class="ml-2 inline-block px-2 py-1 bg-gray-100 text-gray-500 text-[10px] font-medium rounded border border-gray-200">Belum Terunggah</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 flex-shrink-0 ml-auto sm:ml-4">
                                        <span class="verify-saved-indicator text-[10px] text-green-600 font-medium hidden">✓ Tersimpan</span>
                                        <form action="{{ route('admin.pjkades.verify-checklist', [$pjkades->id, $item->id]) }}" method="POST" class="verify-form flex-shrink-0" data-url="{{ route('admin.pjkades.verify-checklist', [$pjkades->id, $item->id]) }}">
                                            @csrf
                                            <input type="checkbox" name="status_verifikasi" value="valid" 
                                                   class="verify-checkbox w-5 h-5 text-primary focus:ring-primary border-gray-300 rounded shadow-sm cursor-pointer transition-colors" 
                                                   {{ $item->status_verifikasi == 'valid' ? 'checked' : '' }}
                                                   title="Tandai Sesuai">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- PANEL BERKAS ZIP --}}
            <div class="bg-surface rounded-card shadow-sm border border-border flex flex-col flex-shrink-0">
                <div class="px-5 py-4 border-b border-border bg-gray-50 flex justify-between items-center">
                    <h3 class="font-display font-semibold text-ink">Keseluruhan Persyaratan</h3>
                    @if($pjkades->metode === 'offline')
                        <span class="text-xs font-semibold px-2 py-1 bg-gray-200 text-gray-700 rounded">Metode: Offline</span>
                    @elseif($pjkades->metode === 'online')
                        @if(!$pjkades->berkas_zip)
                            <span class="text-xs font-semibold px-2 py-1 bg-red-100 text-red-700 rounded">Berkas ZIP/PDF belum diunggah</span>
                        @else
                            <a href="{{ Storage::disk('public')->url($pjkades->berkas_zip) }}" target="_blank" class="inline-flex items-center text-xs font-semibold px-3 py-1 bg-primary text-white rounded hover:bg-primary-light transition-colors">
                                Lihat ZIP/PDF Upload
                            </a>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Kolom Catatan Admin --}}
            <div class="bg-surface rounded-card shadow-sm border border-border p-5 relative flex-shrink-0">
                <form action="{{ route('admin.pjkades.update-catatan', $pjkades->id) }}" method="POST">
                    @csrf
                    <label class="block text-sm font-display font-semibold text-ink mb-2">Catatan Admin</label>
                    
                    <textarea name="catatan_admin" rows="3" placeholder="Tulis catatan jika ada berkas yang kurang/salah..." 
                              class="w-full text-sm rounded-lg border-border focus:ring-primary focus:border-primary placeholder-gray-400">{{ old('catatan_admin', $pjkades->catatan_admin) }}</textarea>
                              
                    <div class="mt-3 text-right">
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-light text-white text-xs font-medium rounded shadow-sm transition-colors">
                            Kirim Catatan / Simpan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Kolom Kanan: Disposisi & Penerbitan SK --}}
            
            <div class="bg-surface rounded-card shadow-sm border border-border flex flex-col mb-10 flex-shrink-0">
                <div class="p-5">
                    <h3 class="text-base font-display font-semibold text-ink mb-1">Status Proses</h3>
                    <p class="text-xs text-muted mb-4 pb-4 border-b border-border">Pantau tahapan perjalanan usulan SK Kades.</p>
                    <x-pjkades-tracker :posisiAktif="$pjkades->posisi_surat ?? 'Berkas Diterima'" :status="$pjkades->status" :pjkades="$pjkades" />
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
                            $currentIndex = array_search($pjkades->posisi_surat ?? 'Berkas Diterima', $posisiOptions);
                            if ($currentIndex === false) $currentIndex = 0;
                            $nextPosisi = isset($posisiOptions[$currentIndex + 1]) ? $posisiOptions[$currentIndex + 1] : null;
                        @endphp

                        @if($nextPosisi === 'Sudah di Desa (Nama Penerima)')
                        <form action="{{ route('admin.pjkades.disposisi', $pjkades->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="posisi_surat" value="{{ $nextPosisi }}">
                            <input type="hidden" name="status_baru" value="approved">
                            <button type="submit"
                                onclick="return confirm('Selesaikan dan setujui usulan ini?')"
                                class="w-full py-2 px-3 bg-green-600 rounded text-white text-xs font-medium hover:bg-green-700 transition-colors flex items-center justify-center shadow-sm">
                                Selesai & Setujui Usulan
                            </button>
                        </form>
                        @elseif($nextPosisi)
                        <form action="{{ route('admin.pjkades.disposisi', $pjkades->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="posisi_surat" value="{{ $nextPosisi }}">
                            <button type="submit"
                                class="w-full py-2 px-3 bg-primary rounded text-white text-xs font-medium hover:bg-primary-light transition-colors flex items-center justify-center shadow-sm">
                                Lanjutkan ke : {{ $nextPosisi }}
                            </button>
                        </form>
                        @elseif($pjkades->status !== 'approved')
                        <form action="{{ route('admin.pjkades.disposisi', $pjkades->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="posisi_surat" value="{{ $pjkades->posisi_surat }}">
                            <input type="hidden" name="status_baru" value="approved">
                            <button type="submit"
                                onclick="return confirm('Selesaikan dan setujui usulan ini?')"
                                class="w-full py-2 px-3 bg-green-600 rounded text-white text-xs font-medium hover:bg-green-700 transition-colors flex items-center justify-center shadow-sm">
                                Selesai & Setujui Usulan
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('admin.pjkades.disposisi', $pjkades->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="posisi_surat" value="Berkas Diterima">
                            <input type="hidden" name="status_baru" value="direvisi">
                            <button type="submit"
                                class="w-full py-2 px-3 bg-white border border-red-300 text-red-600 rounded text-xs font-medium hover:bg-red-50 transition-colors flex items-center justify-center shadow-sm" title="Kembalikan ke awal (Butuh Revisi)">
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
            function previewFile(url) {
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
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Auto preview berkas zip/pdf if available
                @if($pjkades->berkas_zip && preg_match('/\.(pdf|jpe?g|png)$/i', $pjkades->berkas_zip))
                    setTimeout(() => {
                        previewFile('{{ Storage::disk("public")->url($pjkades->berkas_zip) }}');
                    }, 500);
                @endif

                document.querySelectorAll('.verify-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const form = this.closest('form');
                        const wrapper = form.closest('.flex.items-center.gap-2');
                        const indicator = wrapper ? wrapper.querySelector('.verify-saved-indicator') : null;
                        const url = form.getAttribute('data-url');
                        const token = form.querySelector('input[name="_token"]').value;
                        const status = this.checked ? 'valid' : 'menunggu';
                        
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ status_verifikasi: status })
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

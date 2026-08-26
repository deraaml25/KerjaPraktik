<x-app-layout>
    @section('title', 'Detail Pengajuan Pembinaan')

    <div class="max-w-3xl mx-auto">
        <div class="flex flex-wrap items-center gap-3 mb-5">
            <a href="{{ route('desa.pengajuan-pembinaan.index') }}"
                class="text-sm text-primary hover:underline flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Halaman Pembinaan
            </a>
        </div>

        <!-- Status Card -->
        <div class="bg-white rounded-card shadow-sm border border-border p-6 mb-5">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h2 class="text-xl font-display font-bold text-ink">{{ $pengajuanPembinaan->judul_kegiatan }}</h2>
                    <p class="text-sm text-muted mt-1">Diajukan pada {{ $pengajuanPembinaan->created_at->format('d/m/y, H:i') }}</p>
                    <p class="text-sm text-muted">Rencana tanggal: <span class="text-ink font-medium">{{ $pengajuanPembinaan->tanggal_diajukan->format('d/m/y') }}</span></p>
                </div>
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $pengajuanPembinaan->status_color }} ml-4 flex-shrink-0">
                    {{ $pengajuanPembinaan->status_label }}
                </span>
            </div>

            @if($pengajuanPembinaan->deskripsi)
                <div class="mt-4 pt-4 border-t border-border">
                    <p class="text-xs font-medium text-muted uppercase tracking-wide mb-2">Deskripsi Kegiatan</p>
                    <p class="text-sm text-ink leading-relaxed">{{ $pengajuanPembinaan->deskripsi }}</p>
                </div>
            @endif
        </div>

        <!-- Dokumen -->
        <div class="bg-white rounded-card shadow-sm border border-border p-6 mb-5">
            <h3 class="text-md font-display font-bold text-ink mb-4 pb-2 border-b border-border">📎 Dokumen Persyaratan</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-ink">Surat Permohonan Narasumber</p>
                    </div>
                    @if($pengajuanPembinaan->file_surat_permohonan)
                        <a href="{{ asset('storage/' . $pengajuanPembinaan->file_surat_permohonan) }}" target="_blank"
                            class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-btn hover:bg-primary-light transition-colors">
                            📥 Lihat
                        </a>
                    @else
                        <span class="text-xs text-muted italic">Tidak dilampirkan</span>
                    @endif
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-ink">Surat Undangan</p>
                    </div>
                    @if($pengajuanPembinaan->file_undangan)
                        <a href="{{ asset('storage/' . $pengajuanPembinaan->file_undangan) }}" target="_blank"
                            class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-btn hover:bg-primary-light transition-colors">
                            📥 Lihat
                        </a>
                    @else
                        <span class="text-xs text-muted italic">Tidak dilampirkan</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Balasan Dinpermasdes -->
        @if($pengajuanPembinaan->catatan_admin)
            <div class="bg-blue-50 rounded-card border border-blue-200 p-6">
                <h3 class="text-sm font-display font-bold text-blue-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                        </path>
                    </svg>
                    Balasan dari Dinpermasdes
                    @if($pengajuanPembinaan->dibalas_at)
                        <span class="text-xs font-normal text-blue-500">({{ $pengajuanPembinaan->dibalas_at->format('d/m/y, H:i') }})</span>
                    @endif
                </h3>
                <p class="text-sm text-blue-900 leading-relaxed">{{ $pengajuanPembinaan->catatan_admin }}</p>
            </div>
        @else
            <div class="bg-yellow-50 rounded-card border border-yellow-200 p-5 flex items-center gap-3">
                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-yellow-800">Pengajuan masih menunggu balasan dari Dinpermasdes. Kami akan segera memberikan konfirmasi.</p>
            </div>
        @endif
    </div>
</x-app-layout>

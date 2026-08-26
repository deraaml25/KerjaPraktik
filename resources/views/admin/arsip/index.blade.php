<x-app-layout>
    @section('title', 'Arsip Rekomendasi')

    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-muted text-sm mt-1">Daftar surat rekomendasi yang telah diterbitkan (Ajuan Selesai).</p>
        </div>
    </div>

    <!-- Alert Messages -->
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

    <div class="bg-surface rounded-card border border-border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">No. Rekomendasi</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">No. Registrasi Ajuan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">Desa / Kecamatan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">Jenis Layanan</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Tgl Diunggah</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-ink uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($arsips as $arsip)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-ink">{{ $arsip->no_surat_rekom }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.ajuan.show', $arsip->ajuan) }}" class="font-mono text-sm font-semibold text-primary hover:underline">
                                    {{ $arsip->ajuan->no_registrasi }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-ink">{{ $arsip->ajuan->desa->nama_desa }}</div>
                                <div class="text-xs text-muted">Kec. {{ $arsip->ajuan->desa->kecamatan->nama_kecamatan }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-ink">
                                    {{ $arsip->ajuan->jenisLayanan->nama }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-muted text-center">
                                {{ $arsip->created_at->format('d/m/y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('admin.arsip.download', $arsip) }}" class="inline-flex items-center px-4 py-2 bg-primary-soft text-primary text-sm font-medium rounded-btn hover:bg-primary hover:text-white transition-all">
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Unduh
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center text-muted">
                                    <svg class="w-12 h-12 mb-3 text-border" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="font-medium">Belum ada arsip rekomendasi</p>
                                    <p class="text-sm mt-1">Arsip akan muncul di sini setelah Anda mengunggah Surat Rekomendasi pada ajuan yang telah selesai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($arsips->hasPages())
            <div class="px-6 py-4 border-t border-border">
                {{ $arsips->links() }}
            </div>
        @endif
    </div>
</x-app-layout>


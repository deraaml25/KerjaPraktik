<x-app-layout>
    @section('title', 'Arsip Rekomendasi Desa')

    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-muted text-sm mt-1">Daftar surat rekomendasi dari Bupati yang telah terbit untuk desa Anda.
            </p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-card bg-green-50 border border-green-200 text-green-700 flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="bg-surface rounded-card border border-border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">No.
                            Surat Rekomendasi</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">
                            Perangkat Desa / Layanan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">No.
                            Registrasi Ajuan</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Tanggal
                            Terbit</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-ink uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($arsips as $arsip)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-ink">{{ $arsip->no_surat_rekom }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-ink">
                                    {{ $arsip->ajuan->pesertas->first() ? $arsip->ajuan->pesertas->first()->perangkatDesa->nama : '-' }}
                                    @if($arsip->ajuan->pesertas->count() > 1) <span
                                        class="text-primary font-bold">(+{{ $arsip->ajuan->pesertas->count() - 1 }})</span>
                                    @endif
                                </div>
                                <div class="text-xs text-muted">{{ $arsip->ajuan->jenisLayanan->nama }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('desa.ajuan.show', $arsip->ajuan) }}"
                                    class="font-mono text-sm text-primary hover:underline">
                                    {{ $arsip->ajuan->no_registrasi }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-muted text-center">
                                {{ $arsip->created_at->format('d/m/y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ asset('storage/' . $arsip->file_path) }}" target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-success text-white text-sm font-medium rounded-btn hover:bg-green-600 transition-all shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Unduh PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-0">
                                <x-empty-state
                                    icon="<path stroke-linecap='round' stroke-linejoin='round' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' />"
                                    title="Belum ada Surat Rekomendasi Terbit"
                                    message="Surat Rekomendasi dari Bupati akan otomatis muncul di sini jika ajuan Anda telah selesai diproses oleh Dinpermasdes." />
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
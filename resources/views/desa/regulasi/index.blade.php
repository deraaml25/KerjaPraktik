<x-app-layout>
    @section('title', 'Draft Regulasi')
    <div class="flex items-center justify-between mb-6 mt-1">
        <div>
            <p class="text-muted text-sm mt-1">Fasilitasi draf produk hukum desa (Perdes, Perkades, SK Kades).</p>
        </div>
        <a href="{{ route('desa.regulasi.create') }}"
            class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-btn hover:bg-primary-light hover:-translate-y-0.5 hover:shadow-lg transition-all active:scale-95 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Regulasi Baru
        </a>
    </div>

    <!-- List -->
    <div class="bg-white rounded-card shadow-sm border border-border overflow-hidden transition-shadow duration-300 hover:shadow-md">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-gray-50">
                    <tr>

                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">No. Regulasi</th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Judul /
                            Tipe</th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Tanggal
                            Diajukan</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-ink tracking-wider uppercase">Status
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    @forelse ($regulasis as $reg)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-ink font-medium">
                                {{ $reg->no_regulasi ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm font-medium text-ink font-display">{{ $reg->judul }}</div>
                                <div class="text-xs font-bold text-blue-700 mt-1 capitalize">{{ $reg->tipe }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-muted">
                                {{ $reg->tgl_diajukan ? $reg->tgl_diajukan->format('d/m/y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($reg->status === 'disahkan')
                                    <span
                                        class="inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-bold uppercase bg-green-100 text-green-800" style="width: 170px;">DISAHKAN</span>
                                @elseif($reg->status === 'perlu_revisi')
                                    <span
                                        class="inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-bold uppercase bg-red-100 text-red-800" style="width: 170px;">PERLU REVISI</span>
                                @elseif($reg->status === 'disetujui')
                                    <span
                                        class="inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-bold uppercase bg-emerald-100 text-emerald-800" style="width: 170px;">SIAP DISAHKAN</span>
                                @else
                                    <span
                                        class="inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-bold uppercase bg-blue-100 text-blue-800" style="width: 170px;">MENUNGGU VERIFIKASI</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('desa.regulasi.show', $reg) }}"
                                        class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium rounded bg-primary text-white hover:bg-primary-light transition-all hover:scale-105 shadow-sm">
                                        Lihat Detail &rarr;
                                    </a>
                                    <form action="{{ route('desa.regulasi.destroy', $reg->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data regulasi ini secara permanen?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 text-xs font-medium rounded border border-red-200 transition-all hover:scale-105" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-0">
                                <x-empty-state
                                    icon="<path stroke-linecap='round' stroke-linejoin='round' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' />"
                                    title="Regulasi Kosong"
                                    message="Belum ada usulan produk hukum yang diajukan oleh desa Anda." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($regulasis->hasPages())
            <div class="px-6 py-4 border-t border-border">
                {{ $regulasis->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
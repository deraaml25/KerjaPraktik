<x-app-layout>
    @section('title', 'Verifikasi Ajuan BPD (PAW)')

    <!-- Tabel -->
    <div>
        <div class="bg-white rounded-card shadow-sm border border-border overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-border">
                        <th class="py-3 px-4 text-xs font-bold text-ink uppercase tracking-wider">No Registrasi</th>
                        <th class="py-3 px-4 text-xs font-bold text-ink uppercase tracking-wider">Desa</th>
                        <th class="py-3 px-4 text-xs font-bold text-ink uppercase tracking-wider">Jenis Ajuan</th>
                        <th class="py-3 px-4 text-xs font-bold text-ink uppercase tracking-wider">Metode</th>
                        <th class="py-3 px-4 text-xs font-bold text-ink uppercase tracking-wider text-center">Tgl Diajukan</th>
                        <th class="py-3 px-4 text-xs font-bold text-ink uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 text-center text-xs font-bold text-ink uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($ajuans as $ajuan)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4">
                                <span class="font-bold text-ink">{{ $ajuan->no_registrasi }}</span>
                            </td>
                            <td class="py-3 px-4">
                                {{ $ajuan->desa->nama_desa ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-sm">
                                <span class="uppercase font-bold">{{ str_replace('_', ' ', $ajuan->jenis_ajuan) }}</span>
                            </td>
                            <td class="py-3 px-4 text-sm">
                                {{ ucfirst($ajuan->metode) }}
                            </td>
                            <td class="py-3 px-4 text-sm text-center">
                                {{ $ajuan->tgl_diajukan ? \Carbon\Carbon::parse($ajuan->tgl_diajukan)->translatedFormat('d/m/y') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($ajuan->status === 'menunggu_verifikasi')
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-yellow-100 text-yellow-800" style="width: 150px;">
                                        Perlu Verifikasi
                                    </span>
                                @elseif($ajuan->status === 'revisi')
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-red-100 text-red-800" style="width: 150px;">
                                        Revisi
                                    </span>
                                @elseif($ajuan->status === 'diproses')
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-green-100 text-green-800" style="width: 150px;">
                                        Diproses
                                    </span>
                                @elseif($ajuan->status === 'selesai')
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-green-100 text-green-800" style="width: 150px;">
                                        Selesai
                                    </span>
                                @elseif($ajuan->status === 'ditolak')
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-red-100 text-red-800" style="width: 150px;">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-gray-100 text-gray-800" style="width: 150px;">
                                        {{ str_replace('_', ' ', $ajuan->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.ajuan-bpd.show', $ajuan) }}" class="inline-flex items-center px-2 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 text-xs font-medium rounded border border-blue-200 transition-all hover:scale-105" title="Verifikasi">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    </a>
                                    <form action="{{ route('admin.ajuan-bpd.destroy', $ajuan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ajuan BPD ini secara permanen? Semua berkas terkait akan ikut terhapus.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 text-xs font-medium rounded border border-red-200 transition-all hover:scale-105" title="Hapus">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-muted">Belum ada ajuan BPD.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ajuans->hasPages())
            <div class="mt-6">{{ $ajuans->links() }}</div>
        @endif
    </div>
</x-app-layout>


<x-app-layout>
    @section('title', 'Rencana P3D')

    <div class="flex items-center justify-between mb-6 mt-1">
        <div>
            <p class="text-muted text-sm mt-1">Kelola data formasi jabatan perangkat desa yang kosong beserta rencana pelaksanaan P3D dan anggarannya.</p>
        </div>
        <a href="{{ route('desa.rencana-p3d.create') }}"
            class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-btn hover:bg-primary-light hover:-translate-y-0.5 hover:shadow-lg transition-all active:scale-95 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Input Data P3D Baru
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 text-sm mb-6 font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 text-red-800 rounded-lg border border-red-200 text-sm mb-6 font-medium">
            {{ session('error') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-card shadow-sm border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Tahun / Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">Formasi Kosong</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">Jabatan yang Kosong</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">Rencana Pelaksanaan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">Rencana Anggaran</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">Kondisi / Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    @forelse ($rencana as $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm font-semibold text-ink font-mono text-center">Tahun {{ $item->tahun ?? '-' }}</div>
                                <div class="text-xs text-muted mt-0.5 text-center">{{ $item->created_at->format('d/m/y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                                    {{ $item->jumlah_formasi_kosong }} Formasi
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-ink max-w-[200px] truncate" title="{{ $item->jabatan_kosong }}">
                                    {{ $item->jabatan_kosong }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm text-ink font-medium text-center">
                                    {{ $item->rencana_pelaksanaan_mulai ? $item->rencana_pelaksanaan_mulai->format('d/m/y') : '-' }} s/d {{ $item->rencana_pelaksanaan_selesai ? $item->rencana_pelaksanaan_selesai->format('d/m/y') : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-ink">
                                    Rp {{ number_format($item->rencana_anggaran, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-muted max-w-[200px] truncate" title="{{ $item->keterangan }}">
                                    {{ $item->keterangan ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->status === 'disetujui')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Disetujui Admin
                                    </span>
                                @elseif($item->status === 'dikirim')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Dikirim / Proses
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('desa.rencana-p3d.edit', $item->id) }}"
                                        class="inline-flex items-center px-3 py-1 bg-yellow-50 text-yellow-700 hover:bg-yellow-100 border border-yellow-200 text-xs font-semibold rounded-btn transition-colors">
                                        <span class="material-symbols-outlined text-[14px] mr-1">edit</span>
                                        Ubah
                                    </a>
                                    <form action="{{ route('desa.rencana-p3d.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data rencana P3D ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1 bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 text-xs font-semibold rounded-btn transition-colors">
                                            <span class="material-symbols-outlined text-[14px] mr-1">delete</span>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-symbols-outlined text-[48px] text-gray-300 mb-2">assignment_late</span>
                                    <p class="font-medium text-sm">Belum ada data Rencana P3D.</p>
                                    <p class="text-xs text-slate-400 mt-1">Klik tombol 'Input Data P3D Baru' untuk mulai menambahkan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rencana->hasPages())
            <div class="px-6 py-4 border-t border-border">
                {{ $rencana->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

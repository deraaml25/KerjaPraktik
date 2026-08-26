<x-app-layout>
    @section('title', 'Informasi & Pengajuan Pembinaan')

    <!-- Tabs Nav -->
    <div class="border-b border-slate-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('desa.bimtek-informasi.index') }}"
               class="border-b-2 py-4 px-1 text-sm font-semibold {{ request()->routeIs('desa.bimtek-informasi.*') ? 'border-blue-700 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                Berita & Informasi Pembinaan
            </a>
            <a href="{{ route('desa.pengajuan-pembinaan.index') }}"
               class="border-b-2 py-4 px-1 text-sm font-semibold {{ request()->routeIs('desa.pengajuan-pembinaan.*') ? 'border-blue-700 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                Pengajuan Pembinaan Desa
            </a>
        </nav>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 transition-shadow duration-300 hover:shadow-md">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Daftar Pengajuan Pembinaan</h3>
                <p class="text-sm text-slate-500 mt-1">Kelola pengajuan permohonan narasumber atau pembinaan untuk desa Anda.</p>
            </div>
            <a href="{{ route('desa.pengajuan-pembinaan.create') }}" class="bg-[#0A1A3A] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-900 transition-all hover:-translate-y-0.5 hover:shadow-lg active:scale-95 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat Pengajuan Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="py-3 px-4 text-center text-xs font-bold text-ink uppercase tracking-wider">Tgl Pengajuan</th>
                        <th class="py-3 px-4 text-xs font-bold text-ink uppercase tracking-wider">Judul Kegiatan</th>
                        <th class="py-3 px-4 text-center text-xs font-bold text-ink uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 text-center text-xs font-bold text-ink uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuans as $pengajuan)
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors group">
                        <td class="py-3 px-4 text-center text-sm text-slate-600 whitespace-nowrap">
                            {{ $pengajuan->created_at->format('d/m/y') }}
                        </td>
                        <td class="py-3 px-4">
                            <p class="text-sm font-bold text-slate-900">{{ $pengajuan->judul_kegiatan }}</p>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @php
                                $statusColor = match($pengajuan->status) {
                                    'menunggu' => 'bg-yellow-100 text-yellow-800',
                                    'disetujui' => 'bg-green-100 text-green-800',
                                    'ditolak' => 'bg-red-100 text-red-800',
                                    'selesai' => 'bg-green-100 text-green-800',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] tracking-wider font-bold uppercase {{ $statusColor }}" style="width: 110px;">
                                {{ $pengajuan->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('desa.pengajuan-pembinaan.show', $pengajuan->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium rounded bg-primary text-white hover:bg-primary-light transition-all hover:scale-105 shadow-sm">
                                    Detail
                                </a>
                                <form action="{{ route('desa.pengajuan-pembinaan.destroy', $pengajuan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pengajuan pembinaan ini secara permanen?');" class="inline">
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
                        <td colspan="4" class="py-10 text-center text-slate-500">
                            Belum ada pengajuan pembinaan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    @section('title', 'Rencana P3D')



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

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Card 1: Total Formasi Kosong --}}
        <div class="bg-white p-6 rounded-card border border-border shadow-sm flex items-center justify-between overflow-hidden">
            <div class="min-w-0 flex-1">
                <span class="text-xs font-semibold text-muted uppercase tracking-wider block truncate">Total Formasi Kosong</span>
                <span class="text-xl font-extrabold text-ink block mt-2 font-display whitespace-nowrap truncate">{{ $totalFormasi }}</span>
                <span class="text-xs text-muted block mt-1 truncate">Jabatan Perangkat Desa</span>
            </div>
            <div class="h-12 w-12 rounded-full bg-red-50 flex-shrink-0 flex items-center justify-center text-red-600 ml-4">
                <span class="material-symbols-outlined text-[28px]">assignment_late</span>
            </div>
        </div>

        {{-- Card 2: Total Rencana Anggaran --}}
        <div class="bg-white p-6 rounded-card border border-border shadow-sm flex items-center justify-between overflow-hidden">
            <div class="min-w-0 flex-1">
                <span class="text-xs font-semibold text-muted uppercase tracking-wider block truncate">Total Rencana Anggaran P3D</span>
                <span class="text-xl font-extrabold text-ink block mt-2 font-display whitespace-nowrap truncate">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</span>
                <span class="text-xs text-muted block mt-1 truncate">Alokasi Anggaran Terkumpul</span>
            </div>
            <div class="h-12 w-12 rounded-full bg-emerald-50 flex-shrink-0 flex items-center justify-center text-emerald-600 ml-4">
                <span class="material-symbols-outlined text-[28px]">payments</span>
            </div>
        </div>

        {{-- Card 3: Jumlah Desa Melapor --}}
        <div class="bg-white p-6 rounded-card border border-border shadow-sm flex items-center justify-between overflow-hidden">
            <div class="min-w-0 flex-1">
                <span class="text-xs font-semibold text-muted uppercase tracking-wider block truncate">Desa yang Sudah Melapor</span>
                <span class="text-xl font-extrabold text-ink block mt-2 font-display whitespace-nowrap truncate">{{ $totalDesa }}</span>
                <span class="text-xs text-muted block mt-1 truncate">Desa Terdata</span>
            </div>
            <div class="h-12 w-12 rounded-full bg-indigo-50 flex-shrink-0 flex items-center justify-center text-indigo-600 ml-4">
                <span class="material-symbols-outlined text-[28px]">holiday_village</span>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="bg-white rounded-card shadow-sm border border-border p-6 mb-6">
        <form action="{{ route('admin.rencana-p3d.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            {{-- Filter Kecamatan --}}
            <div>
                <label for="kecamatan_id" class="block text-xs font-bold text-ink mb-2">Filter Kecamatan</label>
                <select name="kecamatan_id" id="kecamatan_id" class="w-full text-sm rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm">
                    <option value="">Semua Kecamatan</option>
                    @foreach($kecamatans as $kec)
                        <option value="{{ $kec->id }}" {{ request('kecamatan_id') == $kec->id ? 'selected' : '' }}>
                            {{ $kec->nama_kecamatan }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Search Desa --}}
            <div>
                <label for="search" class="block text-xs font-bold text-ink mb-2">Cari Nama Desa</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="w-full text-sm rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm"
                    placeholder="Masukkan nama desa...">
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="flex-grow inline-flex items-center justify-center px-4 py-2 bg-primary text-white font-medium rounded-btn hover:bg-primary-light transition-colors text-sm shadow-sm">
                    <span class="material-symbols-outlined mr-1.5 text-[18px]">search</span>
                    Cari & Filter
                </button>
                @if(request()->filled('kecamatan_id') || request()->filled('search'))
                    <a href="{{ route('admin.rencana-p3d.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-border text-sm font-medium rounded-btn text-ink bg-white hover:bg-gray-50 transition-colors" title="Reset Filter">
                        <span class="material-symbols-outlined text-[18px]">refresh</span>
                    </a>
                @endif
                <a href="{{ route('admin.rencana-p3d.export-csv', request()->query()) }}" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white font-medium rounded-btn hover:bg-emerald-700 transition-colors text-sm shadow-sm">
                    <span class="material-symbols-outlined mr-1.5 text-[18px]">download</span>
                    Excel
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-card shadow-sm border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Kecamatan & Desa</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Formasi Kosong</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Jabatan yang Kosong</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Rencana Pelaksanaan</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Rencana Anggaran</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Tahun</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    @forelse ($rencana as $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-ink">Desa {{ $item->desa->nama_desa }}</div>
                                <div class="text-xs text-muted">Kec. {{ $item->kecamatan->nama_kecamatan ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-100">
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-ink font-mono">{{ $item->tahun ?? '-' }}</div>
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
                                <a href="{{ route('admin.rencana-p3d.show', $item->id) }}"
                                    class="inline-flex items-center px-2 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 text-xs font-medium rounded border border-blue-200 transition-all hover:scale-105" title="Detail / Evaluasi">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-symbols-outlined text-[48px] text-gray-300 mb-2">assignment_late</span>
                                    <p class="font-medium text-sm">Belum ada data Rencana P3D.</p>
                                    <p class="text-xs text-slate-400 mt-1">Belum ada desa yang mengirimkan data formasi kosong.</p>
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


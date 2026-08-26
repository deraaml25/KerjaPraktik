<x-app-layout>
    @section('title', 'Data Perangkat Desa')

    <div>
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-ink">Data Perangkat Desa</h3>
                <p class="text-sm text-text-muted mt-1">Data sentral perangkat aktif seluruh desa di kabupaten.</p>
            </div>

            <form action="{{ route('admin.perangkat.index') }}" method="GET" class="w-full md:flex-1">
                <div class="relative mt-2 rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, jabatan..."
                        class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6">
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($perangkats as $p)
                <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] p-6 flex flex-col hover:shadow-[0_8px_25px_-5px_rgba(0,0,0,0.08)] transition-shadow border border-slate-100">
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-[72px] h-[72px] rounded-full bg-slate-200 flex flex-shrink-0 items-center justify-center overflow-hidden">
                            <svg class="w-12 h-12 text-slate-400 mt-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        @if($p->status_aktif)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-200/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2"></span> Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-2"></span> Nonaktif
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex-1 flex flex-col justify-end">
                        <h3 class="text-[17px] font-black text-slate-800 leading-snug uppercase mb-1.5">{{ $p->nama }}</h3>
                        <p class="text-[14px] text-slate-600">{{ $p->jabatan }}</p>
                        <p class="text-[14px] text-slate-600">{{ $p->desa->nama_desa ?? 'Desa' }}, {{ $p->desa->kecamatan->nama_kecamatan ?? 'Kecamatan' }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-card shadow-sm border border-border p-8">
                        <x-empty-state
                            icon="<path stroke-linecap='round' stroke-linejoin='round' d='M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' />"
                            title="Data Perangkat Kosong"
                            message="Belum ada perangkat desa yang terdaftar." />
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $perangkats->links() }}
        </div>
    </div>
</x-app-layout>

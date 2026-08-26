<x-app-layout>
    @section('title', 'Data BPD')

    <div>
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-ink">Data BPD</h3>
                <p class="text-sm text-text-muted mt-1">Data sentral anggota BPD aktif seluruh desa di kabupaten.</p>
            </div>

            <form action="{{ route('admin.bpd.index') }}" method="GET" class="w-full md:flex-1">
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
            @forelse($bpds as $p)
                <div x-data="{}" x-on:click="$dispatch('open-modal', 'detail-{{ $p->id }}')" class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] p-6 flex flex-col hover:shadow-[0_8px_25px_-5px_rgba(0,0,0,0.08)] transition-shadow border border-slate-100 cursor-pointer">
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

                <x-modal name="detail-{{ $p->id }}" :show="false" maxWidth="sm" focusable>
                    <div class="p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Detail BPD</h2>
                        
                        <div class="divide-y divide-gray-100 border-t border-b border-gray-100">
                            <div class="py-3 grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Nama</div>
                                <div class="col-span-2 text-sm font-semibold text-gray-900">{{ $p->nama }}</div>
                            </div>
                            <div class="py-3 grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Jabatan</div>
                                <div class="col-span-2 text-sm font-semibold text-gray-900">{{ $p->jabatan }}</div>
                            </div>
                            <div class="py-3 grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Desa / Kec.</div>
                                <div class="col-span-2 text-sm font-semibold text-gray-900">{{ $p->desa->nama_desa ?? 'Desa' }} / {{ $p->desa->kecamatan->nama_kecamatan ?? 'Kecamatan' }}</div>
                            </div>
                            <div class="py-3 grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Mulai Jabatan</div>
                                <div class="col-span-2 text-sm font-semibold text-gray-900">{{ $p->tgl_mulai_jabatan ? $p->tgl_mulai_jabatan->format('d/m/Y') : '-' }}</div>
                            </div>
                            <div class="py-3 grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">No. SK</div>
                                <div class="col-span-2 text-sm font-semibold text-gray-900">{{ $p->no_sk_terakhir ?? '-' }}</div>
                            </div>
                            <div class="py-3 grid grid-cols-3 gap-4 items-center">
                                <div class="text-sm font-medium text-gray-500">File SK</div>
                                <div class="col-span-2">
                                    @if($p->file_sk)
                                        <a href="{{ asset('storage/' . $p->file_sk) }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-primary font-bold hover:underline bg-blue-50 px-2.5 py-1.5 rounded-lg border border-blue-100 transition-colors hover:bg-blue-100">
                                            <span class="material-symbols-outlined text-[16px]">description</span> Lihat File
                                        </a>
                                    @else
                                        <span class="text-sm font-semibold text-gray-900">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end">
                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">Tutup</button>
                        </div>
                    </div>
                </x-modal>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-card shadow-sm border border-border p-8">
                        <x-empty-state
                            icon="<path stroke-linecap='round' stroke-linejoin='round' d='M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' />"
                            title="Data BPD Kosong"
                            message="Belum ada anggota BPD yang terdaftar." />
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $bpds->links() }}
        </div>
    </div>
</x-app-layout>

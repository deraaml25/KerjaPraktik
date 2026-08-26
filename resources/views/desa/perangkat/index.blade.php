<x-app-layout>
    @section('title', 'Data Kepala dan Perangkat Desa Saya')



    <!-- Toolbar / Pencarian -->
    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <form method="GET" action="{{ route('desa.perangkat.index') }}" class="w-full md:flex-1 relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / jabatan..."
                class="w-full text-sm rounded bg-white border border-border shadow-sm pl-10 h-10 focus:border-primary focus:ring-1 focus:ring-primary transition-all">
        </form>
        <div class="flex items-center gap-2">
            <a href="{{ route('desa.perangkat.create') }}"
                class="inline-flex items-center px-4 h-10 bg-primary text-white text-sm font-bold rounded-btn hover:bg-primary-light hover:-translate-y-0.5 hover:shadow-lg transition-all active:scale-95 shadow-sm whitespace-nowrap">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Perangkat
            </a>
        </div>
    </div>

    <!-- Tabel -->
    <div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($perangkat as $row)
                <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] p-6 flex flex-col transition-all duration-300 hover:-translate-y-1 hover:shadow-lg border border-slate-100 group cursor-pointer" x-data="" x-on:click="$dispatch('open-modal', 'detail-{{ $row->id }}')">
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-[72px] h-[72px] rounded-full bg-slate-200 flex flex-shrink-0 items-center justify-center overflow-hidden">
                            <svg class="w-12 h-12 text-slate-400 mt-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            @if($row->status_aktif)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-2"></span> Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex-1 flex flex-col justify-end">
                        <h3 class="text-[17px] font-black text-slate-800 leading-snug uppercase mb-1.5">{{ $row->nama }}</h3>
                        <p class="text-[14px] text-slate-600">{{ $row->jabatan }}</p>
                        <p class="text-[14px] text-slate-600">{{ $row->desa->nama_desa ?? auth()->user()->desa->nama_desa ?? 'Desa' }}, {{ $row->desa->kecamatan->nama_kecamatan ?? auth()->user()->desa->kecamatan->nama_kecamatan ?? 'Kecamatan' }}</p>
                        @if(str_starts_with($row->status_verifikasi, 'pending'))
                            <div class="mt-2.5">
                                <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200/80 uppercase tracking-wider">
                                    <svg class="w-3 h-3 mr-1.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Menunggu Verifikasi
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-end gap-4" x-on:click.stop>
                        <a href="{{ route('desa.perangkat.edit', $row) }}" class="text-[13px] font-bold text-blue-600 hover:text-blue-800 transition-transform hover:scale-110">Edit</a>
                        @if($row->status_aktif)
                            <form action="{{ route('desa.perangkat.destroy', $row) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan perangkat ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[13px] font-bold text-red-600 hover:text-red-800 transition-transform hover:scale-105">Nonaktifkan</button>
                            </form>
                        @else
                            <form action="{{ route('desa.perangkat.activate', $row) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengaktifkan kembali perangkat ini?');">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-[13px] font-bold text-green-600 hover:text-green-800 transition-transform hover:scale-105">Aktifkan</button>
                            </form>
                        @endif
                    </div>
                </div>

                <x-modal name="detail-{{ $row->id }}" :show="false" maxWidth="sm" focusable>
                    <div class="p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Detail Perangkat Desa</h2>
                        
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Nama</p>
                                <p class="font-medium text-gray-900">{{ $row->nama }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Jabatan</p>
                                <p class="font-medium text-gray-900">{{ $row->jabatan }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tanggal Mulai Jabatan</p>
                                <p class="font-medium text-gray-900">{{ $row->tgl_mulai_jabatan ? $row->tgl_mulai_jabatan->format('d F Y') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">No. SK Terakhir</p>
                                <p class="font-medium text-gray-900">{{ $row->no_sk_terakhir ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">File SK</p>
                                @if($row->file_sk)
                                    <a href="{{ asset('storage/' . $row->file_sk) }}" target="_blank" class="inline-flex items-center gap-1 mt-1 text-sm text-primary font-bold hover:underline bg-blue-50 px-3 py-1.5 rounded-lg">
                                        <span class="material-symbols-outlined text-[16px]">description</span> Lihat File SK
                                    </a>
                                @else
                                    <p class="font-medium text-gray-900">-</p>
                                @endif
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
                            title="Data Perangkat Desa Kosong"
                            message="Belum ada perangkat desa yang terdaftar." />
                    </div>
                </div>
            @endforelse
        </div>
        @if($perangkat->hasPages())
            <div class="mt-6">{{ $perangkat->links() }}</div>
        @endif
    </div>
</x-app-layout>
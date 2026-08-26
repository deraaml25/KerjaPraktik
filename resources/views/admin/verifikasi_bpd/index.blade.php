<x-app-layout>
    @section('title', 'Data BPD')
    <div x-data="{ tab: '{{ request()->has('pending_page') ? 'verifikasi' : 'data' }}' }">
        <!-- Tabs Nav -->
        <div class="border-b border-border mb-6">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button @click="tab = 'data'" 
                        :class="tab === 'data' ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-ink hover:border-gray-300'"
                        class="border-b-2 py-4 px-1 text-sm font-semibold transition-colors">
                    Semua Data
                </button>
                <button @click="tab = 'verifikasi'" 
                        :class="tab === 'verifikasi' ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-ink hover:border-gray-300'"
                        class="border-b-2 py-4 px-1 text-sm font-semibold transition-colors relative flex items-center">
                    Verifikasi Usulan
                    @if($pending->total() > 0)
                        <span class="relative flex h-2.5 w-2.5 ml-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                        </span>
                    @endif
                </button>
            </nav>
        </div>

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

        <!-- Tab Content: Data BPD -->
        <div x-show="tab === 'data'" style="display: none;" x-transition>
            <div class="mb-6 flex justify-end">
                <form action="{{ route('admin.bpd.index') }}" method="GET" class="w-full md:flex-1">
                    <div class="relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="material-symbols-outlined text-gray-400 text-[20px]">search</span>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, jabatan, desa..."
                            class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6">
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                @forelse($bpds as $p)
                    <div x-data="{}" x-on:click="$dispatch('open-modal', 'detail-{{ $p->id }}')" class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] p-6 flex flex-col hover:shadow-[0_8px_25px_-5px_rgba(0,0,0,0.08)] transition-shadow border border-slate-100 cursor-pointer">
                        <div class="flex justify-between items-start mb-8">
                            <div class="w-[72px] h-[72px] rounded-full bg-slate-200 flex flex-shrink-0 items-center justify-center overflow-hidden">
                                <span class="material-symbols-outlined text-slate-400 text-[36px]">person</span>
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
                                <button type="button" x-on:click="show = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">Tutup</button>
                            </div>
                        </div>
                    </x-modal>
                @empty
                    <div class="col-span-full">
                        <div class="bg-white rounded-card shadow-sm border border-border p-8 text-center">
                            <span class="material-symbols-outlined text-slate-300 text-5xl mb-3 block">group_off</span>
                            <h3 class="text-lg font-bold text-slate-900 mb-1">Data BPD Kosong</h3>
                            <p class="text-slate-500">Belum ada BPD yang terdaftar.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $bpds->links() }}
            </div>
        </div>

        <!-- Tab Content: Verifikasi Usulan -->
        <div x-show="tab === 'verifikasi'" style="display: none;" x-transition>
            <div class="bg-white rounded-card shadow-sm border border-border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-50 border-b border-border">
                            <tr>
                                <th class="px-6 py-4 font-bold text-ink">Desa</th>
                                <th class="px-6 py-4 font-bold text-ink">Data Lama / Saat Ini</th>
                                <th class="px-6 py-4 font-bold text-ink">Jenis Usulan</th>
                                <th class="px-6 py-4 font-bold text-ink">Data Baru (Draft)</th>
                                <th class="px-6 py-4 font-bold text-ink text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($pending as $item)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-ink">{{ $item->desa->nama_desa ?? '-' }}</div>
                                        <div class="text-xs text-muted">{{ $item->desa->kecamatan->nama_kecamatan ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($item->status_verifikasi === 'pending_tambah')
                                            <span class="text-muted italic">Data Baru</span>
                                        @else
                                            <div class="font-medium text-ink">{{ $item->nama }}</div>
                                            <div class="text-xs text-muted">{{ $item->jabatan }}</div>
                                            <div class="text-xs text-muted">Mulai: {{ $item->tgl_mulai_jabatan ? $item->tgl_mulai_jabatan->format('d/m/Y') : '-' }}</div>
                                            <div class="text-xs text-muted">SK: {{ $item->no_sk_terakhir ?? '-' }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($item->status_verifikasi === 'pending_tambah')
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">Tambah Data</span>
                                        @elseif($item->status_verifikasi === 'pending_ubah')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold">Ubah Data</span>
                                        @elseif($item->status_verifikasi === 'pending_nonaktif')
                                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">Nonaktifkan</span>
                                        @elseif($item->status_verifikasi === 'pending_aktif')
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Aktifkan Kembali</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($item->status_verifikasi === 'pending_ubah' && $item->draft_perubahan)
                                            <div class="font-medium text-ink">{{ $item->draft_perubahan['nama'] ?? $item->nama }}</div>
                                            <div class="text-xs text-muted">{{ $item->draft_perubahan['jabatan'] ?? $item->jabatan }}</div>
                                            <div class="text-xs text-muted">Mulai: {{ isset($item->draft_perubahan['tgl_mulai_jabatan']) ? date('d/m/Y', strtotime($item->draft_perubahan['tgl_mulai_jabatan'])) : '-' }}</div>
                                            <div class="text-xs text-muted">SK: {{ $item->draft_perubahan['no_sk_terakhir'] ?? '-' }}</div>
                                            @if(isset($item->draft_perubahan['file_sk']) && $item->draft_perubahan['file_sk'] !== $item->file_sk)
                                                <div class="text-xs text-blue-600 font-medium mt-1 flex items-center">
                                                    <span class="material-symbols-outlined text-[14px] mr-1">attach_file</span> File SK Baru
                                                </div>
                                            @endif
                                        @elseif($item->status_verifikasi === 'pending_tambah')
                                            <div class="font-medium text-ink">{{ $item->nama }}</div>
                                            <div class="text-xs text-muted">{{ $item->jabatan }}</div>
                                            <div class="text-xs text-muted">Mulai: {{ $item->tgl_mulai_jabatan ? $item->tgl_mulai_jabatan->format('d/m/Y') : '-' }}</div>
                                            <div class="text-xs text-muted">SK: {{ $item->no_sk_terakhir ?? '-' }}</div>
                                            @if($item->file_sk)
                                                <div class="text-xs text-blue-600 font-medium mt-1 flex items-center">
                                                    <span class="material-symbols-outlined text-[14px] mr-1">attach_file</span> File SK Dilampirkan
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted italic">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('admin.bpd.approve', $item->id) }}" method="POST" onsubmit="return confirm('Setujui usulan ini?');">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-green-100 text-green-800 border border-green-200 text-xs font-bold rounded hover:bg-green-200">Setujui</button>
                                            </form>
                                            <form action="{{ route('admin.bpd.reject', $item->id) }}" method="POST" onsubmit="return confirm('Tolak usulan ini?');">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-red-100 text-red-800 border border-red-200 text-xs font-bold rounded hover:bg-red-200">Tolak</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-muted">
                                        Tidak ada usulan BPD yang menunggu verifikasi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($pending->hasPages())
                    <div class="p-4 border-t border-border bg-gray-50">
                        {{ $pending->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>


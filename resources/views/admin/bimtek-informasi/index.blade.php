<x-app-layout>
    @section('title', 'Berita & Informasi Pembinaan')
    @section('page-description', 'Kelola artikel, dokumentasi, dan pengumuman kegiatan pembinaan Dinpermasdes.')

    <!-- Floating Action Button -->
    <a href="{{ route('admin.bimtek-informasi.create') }}"
        style="position: fixed; bottom: 2rem; right: 2rem; z-index: 50;"
        class="flex items-center px-5 py-3 bg-primary text-white font-bold rounded-full transition-all shadow-lg text-sm">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Informasi
    </a>


    

    <!-- Tabs Nav -->
    <div class="border-b border-border mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">

            <a href="{{ route('admin.bimtek-informasi.index') }}"
               class="border-b-2 py-4 px-1 text-sm font-semibold {{ request()->routeIs('admin.bimtek-informasi.*') ? 'border-blue-700 text-blue-700' : 'border-transparent text-muted hover:text-ink hover:border-gray-300' }}">
                Berita & Informasi Pembinaan
            </a>
            <a href="{{ route('admin.pengajuan-pembinaan.index') }}"
               class="border-b-2 py-4 px-1 text-sm font-semibold {{ request()->routeIs('admin.pengajuan-pembinaan.*') ? 'border-blue-700 text-blue-700' : 'border-transparent text-muted hover:text-ink hover:border-gray-300' }}">
                Pengajuan Pembinaan Desa
            </a>
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @forelse ($informasis as $info)
            <div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                @if($info->foto && is_array($info->foto) && count($info->foto) > 0)
                    <img src="{{ Storage::url($info->foto[0]) }}" alt="{{ $info->judul }}"
                        class="w-full h-48 object-cover">
                @elseif($info->foto && is_string($info->foto))
                    <img src="{{ Storage::url($info->foto) }}" alt="{{ $info->judul }}"
                        class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                        <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                            </path>
                        </svg>
                    </div>
                @endif

                <div class="p-4 flex-1 flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 rounded text-xs font-medium
                            {{ $info->kategori === 'dokumentasi' ? 'bg-purple-100 text-purple-700' : ($info->kategori === 'pengumuman' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ ucfirst($info->kategori) }}
                        </span>
                        @if($info->isPublished())
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Terpublikasi</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Draft</span>
                        @endif
                    </div>

                    <h3 class="font-display font-bold text-ink text-base leading-snug mb-2 flex-1">{{ $info->judul }}</h3>
                    <p class="text-xs text-muted mb-3">{{ $info->published_at ? $info->published_at->format('d/m/y') : 'Belum dijadwalkan' }}</p>

                    <div class="flex items-center justify-between gap-2 border-t border-border pt-3 mt-auto">
                        <a href="{{ route('admin.bimtek-informasi.show', $info) }}"
                            class="text-blue-700 hover:text-blue-900 text-xs hover:underline font-bold">Lihat</a>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.bimtek-informasi.edit', $info) }}"
                                class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-ink text-xs font-medium rounded transition-colors">Edit</a>
                            <form action="{{ route('admin.bimtek-informasi.destroy', $info) }}" method="POST"
                                onsubmit="return confirm('Hapus informasi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium rounded border border-red-200 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <p class="text-muted text-sm">Belum ada informasi pembinaan. Buat yang pertama!</p>
            </div>
        @endforelse
    </div>

    @if($informasis->hasPages())
        <div class="mt-6">{{ $informasis->links() }}</div>
    @endif
</x-app-layout>


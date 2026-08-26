<x-app-layout>
    @section('title', 'Berita & Informasi Pembinaan')
    @section('page-description', 'Berita, artikel, dan informasi kegiatan pembinaan dari Dinpermasdes.')

    <!-- Tabs Nav -->
    <div class="border-b border-slate-200 mb-6 mt-4">
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

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse ($informasis as $info)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
                @if($info->foto && is_array($info->foto) && count($info->foto) > 0)
                    <img src="{{ Storage::url($info->foto[0]) }}" alt="{{ $info->judul }}"
                        class="w-full h-48 object-cover">
                @elseif($info->foto && is_string($info->foto))
                    <img src="{{ Storage::url($info->foto) }}" alt="{{ $info->judul }}"
                        class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-slate-100 flex items-center justify-center text-slate-400">
                        <span class="material-symbols-outlined text-[48px] text-blue-300">feed</span>
                    </div>
                @endif

                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                            {{ $info->kategori === 'dokumentasi' ? 'bg-purple-100 text-purple-700' : ($info->kategori === 'pengumuman' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ ucfirst($info->kategori) }}
                        </span>
                        <span class="text-xs text-slate-500 font-medium">{{ $info->published_at->format('d/m/y') }}</span>
                    </div>

                    <h3 class="font-bold text-slate-900 text-lg leading-snug mb-3 flex-1">{{ $info->judul }}</h3>
                    <p class="text-sm text-slate-600 line-clamp-2 mb-4">{{ strip_tags($info->konten) }}</p>

                    <div class="border-t border-slate-100 pt-4 mt-auto">
                        <a href="{{ route('desa.bimtek-informasi.show', $info) }}"
                            class="text-blue-600 text-sm font-semibold hover:text-blue-800 flex items-center gap-1 transition-all group-hover:translate-x-1">
                            Baca Selengkapnya
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-slate-200">
                <span class="material-symbols-outlined text-[48px] text-slate-300 mb-4">feed</span>
                <p class="text-slate-500 text-sm">Belum ada berita atau informasi pembinaan dari Dinpermasdes.</p>
            </div>
        @endforelse
    </div>

    @if($informasis->hasPages())
        <div class="mt-8">{{ $informasis->links() }}</div>
    @endif
</x-app-layout>

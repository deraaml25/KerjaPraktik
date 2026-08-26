<x-app-layout>
    @section('title', 'Informasi & Pengajuan Pembinaan')

    <div class="mb-4">
        <a href="{{ route('desa.bimtek-informasi.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <span class="material-symbols-outlined text-[18px] mr-1">arrow_back</span>
            Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @if($bimtekInformasi->foto && is_array($bimtekInformasi->foto) && count($bimtekInformasi->foto) > 0)
            <div class="w-full h-64 md:h-96 relative">
                <img src="{{ Storage::url($bimtekInformasi->foto[0]) }}" alt="{{ $bimtekInformasi->judul }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent"></div>
                <div class="absolute bottom-6 left-6 right-6">
                    <span class="inline-block px-2.5 py-1 rounded-md text-xs font-bold mb-3
                        {{ $bimtekInformasi->kategori === 'dokumentasi' ? 'bg-purple-500 text-white' : ($bimtekInformasi->kategori === 'pengumuman' ? 'bg-orange-500 text-white' : 'bg-blue-500 text-white') }}">
                        {{ ucfirst($bimtekInformasi->kategori) }}
                    </span>
                    <h2 class="text-2xl md:text-3xl font-bold text-white">{{ $bimtekInformasi->judul }}</h2>
                </div>
            </div>
        @elseif($bimtekInformasi->foto && is_string($bimtekInformasi->foto))
            <div class="w-full h-64 md:h-96 relative">
                <img src="{{ Storage::url($bimtekInformasi->foto) }}" alt="{{ $bimtekInformasi->judul }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent"></div>
                <div class="absolute bottom-6 left-6 right-6">
                    <span class="inline-block px-2.5 py-1 rounded-md text-xs font-bold mb-3
                        {{ $bimtekInformasi->kategori === 'dokumentasi' ? 'bg-purple-500 text-white' : ($bimtekInformasi->kategori === 'pengumuman' ? 'bg-orange-500 text-white' : 'bg-blue-500 text-white') }}">
                        {{ ucfirst($bimtekInformasi->kategori) }}
                    </span>
                    <h2 class="text-2xl md:text-3xl font-bold text-white">{{ $bimtekInformasi->judul }}</h2>
                </div>
            </div>
        @else
            <div class="px-8 pt-8 pb-4 border-b border-slate-100">
                <span class="inline-block px-2.5 py-1 rounded-md text-xs font-bold mb-3
                    {{ $bimtekInformasi->kategori === 'dokumentasi' ? 'bg-purple-100 text-purple-700' : ($bimtekInformasi->kategori === 'pengumuman' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                    {{ ucfirst($bimtekInformasi->kategori) }}
                </span>
                <h2 class="text-2xl md:text-3xl font-bold text-slate-900">{{ $bimtekInformasi->judul }}</h2>
            </div>
        @endif

        <div class="px-8 py-6 flex flex-col md:flex-row gap-8">
            <div class="flex-1">
                <div class="flex items-center gap-4 text-sm text-slate-500 font-medium mb-6">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                        {{ $bimtekInformasi->published_at->format('d/m/y, H:i') }}
                    </div>
                </div>

                <div class="prose prose-slate max-w-none text-slate-700 mb-8">
                    {!! $bimtekInformasi->konten !!}
                </div>

                @if($bimtekInformasi->foto && is_array($bimtekInformasi->foto) && count($bimtekInformasi->foto) > 1)
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Galeri Foto</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-8">
                        @foreach(array_slice($bimtekInformasi->foto, 1) as $img)
                            <a href="{{ Storage::url($img) }}" target="_blank" class="block aspect-w-1 aspect-h-1 rounded-lg overflow-hidden border border-slate-200 hover:shadow-md transition-shadow">
                                <img src="{{ Storage::url($img) }}" alt="Galeri" class="w-full h-full object-cover">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($bimtekInformasi->file_lampiran)
                <div class="w-full md:w-72 flex-shrink-0">
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                        <h4 class="font-bold text-slate-900 text-sm mb-3">Lampiran Dokumen</h4>
                        <a href="{{ asset('storage/' . $bimtekInformasi->file_lampiran) }}" target="_blank"
                            class="flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-lg hover:border-blue-300 hover:shadow-sm transition-all group">
                            <div class="w-10 h-10 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-900 truncate">Lihat Dokumen</p>
                                <p class="text-xs text-slate-500">Klik untuk membuka</p>
                            </div>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

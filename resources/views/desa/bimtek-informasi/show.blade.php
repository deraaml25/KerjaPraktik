<x-app-layout>
    @section('title', 'Informasi & Pengajuan Pembinaan')

    <div class="mb-5 flex flex-wrap items-center gap-3">
        <a href="{{ route('desa.bimtek-informasi.index') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm group">
            <svg class="w-4 h-4 mr-2 text-slate-500 group-hover:text-slate-700 group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @if($bimtekInformasi->foto && is_array($bimtekInformasi->foto) && count($bimtekInformasi->foto) > 0)
            <div class="w-full h-48 md:h-64 relative">
                <img src="{{ Storage::url($bimtekInformasi->foto[0]) }}" alt="{{ $bimtekInformasi->judul }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 to-transparent"></div>
                <div class="absolute bottom-4 left-5 right-5">
                    <span class="inline-block px-2 py-1 rounded text-[10px] font-bold mb-1.5
                        {{ $bimtekInformasi->kategori === 'dokumentasi' ? 'bg-purple-500 text-white' : ($bimtekInformasi->kategori === 'pengumuman' ? 'bg-orange-500 text-white' : 'bg-blue-500 text-white') }}">
                        {{ ucfirst($bimtekInformasi->kategori) }}
                    </span>
                    <h2 class="text-lg md:text-xl font-bold text-white leading-tight">{{ $bimtekInformasi->judul }}</h2>
                </div>
            </div>
        @elseif($bimtekInformasi->foto && is_string($bimtekInformasi->foto))
            <div class="w-full h-48 md:h-64 relative">
                <img src="{{ Storage::url($bimtekInformasi->foto) }}" alt="{{ $bimtekInformasi->judul }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 to-transparent"></div>
                <div class="absolute bottom-4 left-5 right-5">
                    <span class="inline-block px-2 py-1 rounded text-[10px] font-bold mb-1.5
                        {{ $bimtekInformasi->kategori === 'dokumentasi' ? 'bg-purple-500 text-white' : ($bimtekInformasi->kategori === 'pengumuman' ? 'bg-orange-500 text-white' : 'bg-blue-500 text-white') }}">
                        {{ ucfirst($bimtekInformasi->kategori) }}
                    </span>
                    <h2 class="text-lg md:text-xl font-bold text-white leading-tight">{{ $bimtekInformasi->judul }}</h2>
                </div>
            </div>
        @else
            <div class="px-5 py-4 border-b border-slate-100">
                <span class="inline-block px-2 py-1 rounded text-[10px] font-bold mb-1.5
                    {{ $bimtekInformasi->kategori === 'dokumentasi' ? 'bg-purple-100 text-purple-700' : ($bimtekInformasi->kategori === 'pengumuman' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                    {{ ucfirst($bimtekInformasi->kategori) }}
                </span>
                <h2 class="text-lg font-bold text-slate-900 leading-tight">{{ $bimtekInformasi->judul }}</h2>
            </div>
        @endif

        <div class="px-5 py-4">
            <div class="flex items-center gap-3 text-xs text-slate-500 font-medium mb-3">
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                    {{ $bimtekInformasi->published_at->format('d/m/y, H:i') }}
                </div>
            </div>

            <div class="prose prose-slate max-w-none text-slate-700 mb-4">
                {!! $bimtekInformasi->konten !!}
            </div>

            @if($bimtekInformasi->foto && is_array($bimtekInformasi->foto) && count($bimtekInformasi->foto) > 1)
                <h3 class="text-md font-bold text-slate-900 mb-3">Galeri Foto</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-4">
                    @foreach(array_slice($bimtekInformasi->foto, 1) as $img)
                        <a href="{{ Storage::url($img) }}" target="_blank" class="block aspect-w-1 aspect-h-1 rounded-lg overflow-hidden border border-slate-200 hover:shadow-md transition-shadow">
                            <img src="{{ Storage::url($img) }}" alt="Galeri" class="w-full h-full object-cover">
                        </a>
                    @endforeach
                </div>
            @endif
            
            @if($bimtekInformasi->file_lampiran)
                @php $ext = strtolower(pathinfo($bimtekInformasi->file_lampiran, PATHINFO_EXTENSION)); @endphp
                <div class="mt-4 border-t border-slate-200 pt-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-md font-bold text-slate-900">Lampiran Dokumen</h3>
                        <a href="{{ asset('storage/' . $bimtekInformasi->file_lampiran) }}" target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors">
                            <span class="material-symbols-outlined text-[18px] mr-2">download</span>
                            Unduh
                        </a>
                    </div>
                    
                    @if($ext === 'pdf')
                        <div class="w-full border border-slate-200 rounded-lg overflow-hidden bg-slate-50" style="height: 500px;">
                            <iframe src="{{ asset('storage/' . $bimtekInformasi->file_lampiran) }}" class="w-full h-full border-0"></iframe>
                        </div>
                    @else
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 w-full md:w-96">
                            <a href="{{ asset('storage/' . $bimtekInformasi->file_lampiran) }}" target="_blank"
                                class="flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-lg hover:border-blue-300 hover:shadow-sm transition-all group">
                                <div class="w-10 h-10 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <span class="material-symbols-outlined">description</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate">Unduh Dokumen</p>
                                    <p class="text-xs text-slate-500">File {{ strtoupper($ext) }}</p>
                                </div>
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

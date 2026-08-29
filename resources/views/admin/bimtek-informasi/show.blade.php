<x-app-layout>
    @section('title', $bimtekInformasi->judul)

    <div class="mb-5 flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.bimtek-informasi.index') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm group">
            <svg class="w-4 h-4 mr-2 text-slate-500 group-hover:text-slate-700 group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Pembinaan
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-card shadow-sm border border-border p-8">

            @if($bimtekInformasi->foto && is_array($bimtekInformasi->foto) && count($bimtekInformasi->foto) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    @foreach($bimtekInformasi->foto as $img)
                        <img src="{{ Storage::url($img) }}" alt="{{ $bimtekInformasi->judul }}"
                            class="w-full h-60 object-cover rounded-lg">
                    @endforeach
                </div>
            @elseif($bimtekInformasi->foto && is_string($bimtekInformasi->foto))
                <img src="{{ Storage::url($bimtekInformasi->foto) }}" alt="{{ $bimtekInformasi->judul }}"
                    class="w-full h-60 object-cover rounded-lg mb-6">
            @endif

            <div class="flex items-center gap-3 mb-4">
                <span class="px-2.5 py-1 rounded text-xs font-medium
                    {{ $bimtekInformasi->kategori === 'dokumentasi' ? 'bg-purple-100 text-purple-700' : ($bimtekInformasi->kategori === 'pengumuman' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                    {{ ucfirst($bimtekInformasi->kategori) }}
                </span>
                @if($bimtekInformasi->isPublished())
                    <span class="px-2.5 py-1 rounded text-xs font-medium bg-green-100 text-green-700">Terpublikasi</span>
                @else
                    <span class="px-2.5 py-1 rounded text-xs font-medium bg-gray-100 text-gray-500">Draft</span>
                @endif
                <span class="text-xs text-muted">{{ $bimtekInformasi->published_at?->format('d/m/y, H:i') ?? 'Belum dijadwalkan' }}</span>
            </div>

            <h1 class="text-2xl font-display font-bold text-ink mb-6">{{ $bimtekInformasi->judul }}</h1>

            <div class="prose prose-sm max-w-none text-ink leading-relaxed border-t border-border pt-6">
                {!! $bimtekInformasi->konten !!}
            </div>

            @if($bimtekInformasi->file_lampiran)
                <div class="mt-6 border-t border-border pt-5">
                    <a href="{{ asset('storage/' . $bimtekInformasi->file_lampiran) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-border rounded-btn text-sm font-medium text-ink transition-colors">
                        📎 Unduh Lampiran
                    </a>
                </div>
            @endif

            <div class="flex gap-3 mt-8 border-t border-border pt-5">
                <a href="{{ route('admin.bimtek-informasi.edit', $bimtekInformasi) }}"
                    class="px-4 py-2 bg-primary text-white font-medium rounded-btn hover:bg-primary-light transition-colors text-sm">Edit</a>
                <form action="{{ route('admin.bimtek-informasi.destroy', $bimtekInformasi) }}" method="POST"
                    onsubmit="return confirm('Hapus informasi ini?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 font-medium rounded-btn border border-red-200 transition-colors text-sm">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>


<x-app-layout>
    @section('title', 'Unggah Surat Rekomendasi')

    <div class="mb-6">
        <a href="{{ route('admin.ajuan.show', $ajuan) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm group">
            <svg class="w-4 h-4 mr-2 text-slate-500 group-hover:text-slate-700 group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Detail Ajuan
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 rounded-card bg-red-50 border border-red-200 text-red-700 flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <strong class="font-medium">Terjadi Kesalahan:</strong>
                <ul class="list-disc list-inside mt-1 text-sm">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="bg-surface rounded-card border border-border shadow-sm p-8 max-w-2xl mx-auto">
        <div class="flex items-center gap-3 mb-6 pb-6 border-b border-border">
            <div class="w-12 h-12 bg-success/10 text-success rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-display font-bold text-ink mb-1">Terbitkan Rekomendasi</h2>
                <p class="text-sm text-muted">Ajuan: <span class="font-mono">{{ $ajuan->no_registrasi }}</span> - Desa {{ $ajuan->desa->nama_desa }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.arsip.store', $ajuan) }}" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-5">
                <label for="no_surat_rekom" class="block text-sm font-medium text-ink mb-2">Nomor Surat Rekomendasi Bupati <span class="text-danger">*</span></label>
                <input type="text" name="no_surat_rekom" id="no_surat_rekom" required class="w-full rounded-btn border-border text-sm text-ink focus:ring-primary focus:border-primary shadow-sm" placeholder="Contoh: 141/001/REKOM/2026" value="{{ old('no_surat_rekom') }}">
            </div>

            <div class="mb-8">
                <label for="file_rekom" class="block text-sm font-medium text-ink mb-2">Unggah File Surat Rekomendasi (PDF) <span class="text-danger">*</span></label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-border border-dashed rounded-xl hover:border-primary transition-colors bg-gray-50">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-muted" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-ink justify-center">
                            <label for="file_rekom" class="relative cursor-pointer bg-transparent rounded-md font-medium text-primary hover:text-primary-light focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                <span>Pilih file PDF</span>
                                <input id="file_rekom" name="file_rekom" type="file" class="sr-only" required accept=".pdf">
                            </label>
                        </div>
                        <p class="text-xs text-muted">Hanya file PDF (Maksimal 20MB)</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-border">
                <a href="{{ route('admin.ajuan.show', $ajuan) }}" class="px-5 py-2.5 text-sm font-medium text-ink bg-white border border-border rounded-btn hover:bg-gray-50 transition-colors shadow-sm">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-btn hover:bg-primary-light transition-colors shadow-sm shadow-primary/30">
                    Simpan Arsip & Selesaikan Ajuan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>


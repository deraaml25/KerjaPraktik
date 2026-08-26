<x-app-layout>
    @section('title', 'Ubah Rencana P3D')

    <div class="max-w-4xl mx-auto bg-white rounded-card shadow-sm border border-border p-8 mb-8">
        <div class="mb-6 border-b border-border pb-4">
            <h2 class="text-xl font-display font-bold text-ink">Ubah Data Rencana P3D</h2>
            <p class="text-muted text-sm mt-1">Ubah atau perbarui data formasi jabatan perangkat desa yang kosong beserta rencana pelaksanaan dan anggarannya.</p>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-red-50 text-red-800 rounded-lg border border-red-200 text-sm mb-6">
                <strong class="font-bold">Terjadi kesalahan input:</strong>
                <ul class="list-disc ml-5 mt-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('desa.rencana-p3d.update', $rencana->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Kecamatan --}}
                <div>
                    <label class="block text-sm font-bold text-ink mb-2">Kecamatan</label>
                    <input type="text" value="{{ $desa->kecamatan->nama_kecamatan ?? '-' }}" disabled
                        class="w-full text-sm rounded-md border-border text-slate-500 bg-gray-50 cursor-not-allowed shadow-sm">
                </div>

                {{-- Desa --}}
                <div>
                    <label class="block text-sm font-bold text-ink mb-2">Desa</label>
                    <input type="text" value="{{ $desa->nama_desa ?? '-' }}" disabled
                        class="w-full text-sm rounded-md border-border text-slate-500 bg-gray-50 cursor-not-allowed shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Jumlah Formasi Kosong --}}
                <div>
                    <label for="jumlah_formasi_kosong" class="block text-sm font-bold text-ink mb-2">Jumlah Formasi Jabatan Kosong <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_formasi_kosong" id="jumlah_formasi_kosong" min="1" required
                        value="{{ old('jumlah_formasi_kosong', $rencana->jumlah_formasi_kosong) }}"
                        class="w-full text-sm rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm"
                        placeholder="Contoh: 2">
                    <p class="text-xs text-muted mt-1">Masukkan jumlah jabatan perangkat desa yang saat ini kosong.</p>
                </div>

                {{-- Rencana Pelaksanaan P3D --}}
                <div>
                    <label class="block text-sm font-bold text-ink mb-2">Rencana Pelaksanaan P3D <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-2">
                        <div class="w-full">
                            <input type="date" name="rencana_pelaksanaan_mulai" id="rencana_pelaksanaan_mulai" required
                                value="{{ old('rencana_pelaksanaan_mulai', $rencana->rencana_pelaksanaan_mulai ? $rencana->rencana_pelaksanaan_mulai->format('Y-m-d') : '') }}"
                                class="w-full text-sm rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm">
                        </div>
                        <span class="text-sm font-medium text-slate-500">s/d</span>
                        <div class="w-full">
                            <input type="date" name="rencana_pelaksanaan_selesai" id="rencana_pelaksanaan_selesai" required
                                value="{{ old('rencana_pelaksanaan_selesai', $rencana->rencana_pelaksanaan_selesai ? $rencana->rencana_pelaksanaan_selesai->format('Y-m-d') : '') }}"
                                class="w-full text-sm rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm">
                        </div>
                    </div>
                    <p class="text-xs text-muted mt-1">Pilih perkiraan range tanggal pelaksanaan penjaringan dan penyaringan.</p>
                </div>
            </div>

            {{-- Jabatan yang Kosong --}}
            <div class="mb-6">
                <label for="jabatan_kosong" class="block text-sm font-bold text-ink mb-2">Jabatan yang Kosong <span class="text-red-500">*</span></label>
                <input type="text" name="jabatan_kosong" id="jabatan_kosong" required
                    value="{{ old('jabatan_kosong', $rencana->jabatan_kosong) }}"
                    class="w-full text-sm rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm"
                    placeholder="Contoh: Kasi Pemerintahan, Kaur TU & Umum">
                <p class="text-xs text-muted mt-1">Sebutkan nama-nama formasi jabatan yang kosong, pisahkan dengan koma jika lebih dari satu.</p>
            </div>

            {{-- Rencana Anggaran --}}
            <div class="mb-6" x-data="{ formatRupiah(value) {
                if (!value) return 'Rp 0';
                return 'Rp ' + parseInt(value).toLocaleString('id-ID');
            }, val: '{{ old('rencana_anggaran', (int) $rencana->rencana_anggaran) }}' }">
                <label for="rencana_anggaran" class="block text-sm font-bold text-ink mb-2">Rencana Anggaran (Rp) <span class="text-red-500">*</span></label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-slate-500 sm:text-sm">Rp</span>
                    </div>
                    <input type="number" name="rencana_anggaran" id="rencana_anggaran" min="0" required x-model="val"
                        class="w-full text-sm rounded-md border-border pl-10 text-ink bg-white focus:border-primary focus:ring-primary shadow-sm"
                        placeholder="0">
                </div>
                <p class="text-xs text-primary font-bold mt-1.5" x-text="formatRupiah(val)"></p>
                <p class="text-xs text-muted mt-1">Masukkan estimasi rencana biaya anggaran pelaksanaan P3D.</p>
            </div>

            {{-- Keterangan / Kondisi --}}
            <div class="mb-6">
                <label for="keterangan" class="block text-sm font-bold text-ink mb-2">Keterangan / Kondisi</label>
                <textarea name="keterangan" id="keterangan" rows="4"
                    class="w-full text-sm rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm"
                    placeholder="Tuliskan keterangan mengenai kondisi kekosongan jabatan atau detail rencana lainnya...">{{ old('keterangan', $rencana->keterangan) }}</textarea>
                <p class="text-xs text-muted mt-1">Catatan tambahan opsional untuk menjelaskan kondisi terkini di desa.</p>
            </div>

            {{-- Status --}}
            <input type="hidden" name="status" value="{{ $rencana->status }}">

            {{-- Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-border">
                <a href="{{ route('desa.rencana-p3d.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-border text-sm font-medium rounded-btn text-ink bg-white hover:bg-gray-50 focus:outline-none transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-primary text-white font-medium rounded-btn hover:bg-primary-light transition-colors shadow-sm text-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

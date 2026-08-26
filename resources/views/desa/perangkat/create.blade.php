<x-app-layout>
    @section('title', 'Tambah Perangkat Desa')

    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('desa.perangkat.index') }}"
                    class="text-sm font-medium text-muted hover:text-ink transition-colors inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar
                </a>
                <h2 class="text-2xl font-display font-bold text-ink mt-2">Tambah Data Perangkat Desa</h2>
                <p class="text-muted text-sm mt-1">
                    Silakan isi data administratif perangkat desa. Data akan secara otomatis terkunci pada batas wilayah
                    administrasi desa Anda.
                </p>
            </div>
        </div>

        <div class="bg-white rounded-card shadow-sm border border-border p-6">
            <form action="{{ route('desa.perangkat.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1">Nama Lengkap <span
                                class="text-danger">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" required
                            class="w-full rounded-md border-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                            placeholder="Contoh: Budi Santoso">
                        @error('nama') <span class="text-xs text-danger font-medium mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink mb-1">Jabatan <span
                                class="text-danger">*</span></label>
                        <input type="text" name="jabatan" value="{{ old('jabatan') }}" required
                            class="w-full rounded-md border-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                            placeholder="Contoh: Sekretaris Desa">
                        @error('jabatan') <span class="text-xs text-danger font-medium mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink mb-1">Nomor SK Terakhir</label>
                        <input type="text" name="no_sk_terakhir" value="{{ old('no_sk_terakhir') }}"
                            class="w-full rounded-md border-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                            placeholder="Nomor Surat Keputusan">
                        @error('no_sk_terakhir') <span
                        class="text-xs text-danger font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink mb-1">Tanggal Mulai Menjabat</label>
                        <input type="date" name="tgl_mulai_jabatan" value="{{ old('tgl_mulai_jabatan') }}"
                            class="w-full rounded-md border-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                        @error('tgl_mulai_jabatan') <span
                        class="text-xs text-danger font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink mb-1">Upload File SK</label>
                        <input type="file" name="file_sk" accept=".pdf,.jpg,.jpeg,.png"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-light">
                        <p class="text-xs text-muted mt-1">Format didukung: PDF, JPG, PNG (Maks 10MB)</p>
                        @error('file_sk') <span
                        class="text-xs text-danger font-medium mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit"
                        class="px-6 py-2.5 bg-primary text-white text-sm font-bold rounded-btn hover:bg-primary-light transition-all shadow-sm">
                        Simpan Perangkat Desa
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
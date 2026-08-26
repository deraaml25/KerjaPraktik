<x-app-layout>
    @section('title', 'Tambah BPD')

    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('desa.bpd.index') }}"
                    class="text-sm font-medium text-muted hover:text-ink transition-colors inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar
                </a>
                <h2 class="text-2xl font-display font-bold text-ink mt-2">Tambah Data BPD</h2>
                <p class="text-muted text-sm mt-1">
                    Silakan isi data administratif bpd desa. Data akan secara otomatis terkunci pada batas wilayah
                    administrasi desa Anda.
                </p>
            </div>
        </div>

        <div class="bg-white rounded-card shadow-sm border border-border p-6">
            <form action="{{ route('desa.bpd.store') }}" method="POST" enctype="multipart/form-data">
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
                            placeholder="Contoh: Ketua BPD">
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
                        <label class="block text-sm font-medium text-ink mb-1">File SK (Opsional)</label>
                        <input type="file" name="file_sk" accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all border border-border rounded-md shadow-sm">
                        <p class="mt-1 text-xs text-muted">Format: PDF, JPG, PNG. Maksimal 2MB.</p>
                        @error('file_sk') <span class="text-xs text-danger font-medium mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit"
                        class="px-6 py-2.5 bg-primary text-white text-sm font-bold rounded-btn hover:bg-primary-light transition-all shadow-sm">
                        Simpan BPD
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
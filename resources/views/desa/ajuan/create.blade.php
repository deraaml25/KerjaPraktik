<x-app-layout>
    @section('title', 'Buat Ajuan Rekomendasi Baru')

    <div class="mb-6">
        <a href="{{ route('desa.ajuan.index') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm group">
            <svg class="w-4 h-4 mr-2 text-slate-500 group-hover:text-slate-700 group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Ajuan
        </a>
    </div>

    <!-- Alert Error -->
    @if($errors->any())
        <div class="mb-6 p-4 rounded-card bg-red-50 border border-red-200 text-red-700 flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
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

    <div class="bg-surface rounded-card border border-border shadow-sm p-8 max-w-3xl mx-auto" x-data="ajuanForm()">
        <h2 class="text-2xl font-display font-bold text-ink mb-6">Form Ajuan Layanan</h2>

        <form method="POST" action="{{ route('desa.ajuan.store') }}" @submit="isSubmitting = true">
            @csrf

            <!-- 0. Metode Pengajuan -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-ink mb-2">Metode Pengajuan <span class="text-danger">*</span></label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                        :class="{ 'border-primary ring-1 ring-primary bg-primary-soft/30': metode == 'online', 'border-border': metode != 'online' }">
                        <input type="radio" name="metode" value="online" class="sr-only" x-model="metode">
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium"
                                    :class="{ 'text-primary': metode == 'online', 'text-ink': metode != 'online' }">Online</span>
                                <span class="mt-1 flex items-center text-sm text-muted">Unggah dokumen persyaratan.</span>
                            </span>
                        </span>
                        <svg class="h-5 w-5 text-primary" :class="{ 'invisible': metode != 'online' }"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                        :class="{ 'border-primary ring-1 ring-primary bg-primary-soft/30': metode == 'offline', 'border-border': metode != 'offline' }">
                        <input type="radio" name="metode" value="offline" class="sr-only" x-model="metode">
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium"
                                    :class="{ 'text-primary': metode == 'offline', 'text-ink': metode != 'offline' }">Offline</span>
                                <span class="mt-1 flex items-center text-sm text-muted">Berkas diserahkan langsung, tidak perlu unggah.</span>
                            </span>
                        </span>
                        <svg class="h-5 w-5 text-primary" :class="{ 'invisible': metode != 'offline' }"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </label>
                </div>
            </div>

            <!-- 1. Jenis Layanan -->
            <div class="mb-6">
                <label for="jenis_layanan_id" class="block text-sm font-medium text-ink mb-2">Jenis Layanan <span
                        class="text-danger">*</span></label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($jenisLayanans as $jl)
                        <label
                            class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                            :class="{ 'border-primary ring-1 ring-primary bg-primary-soft/30': jenisLayanan == {{ $jl->id }}, 'border-border': jenisLayanan != {{ $jl->id }} }">
                            <input type="radio" name="jenis_layanan_id" value="{{ $jl->id }}" class="sr-only"
                                x-model="jenisLayanan" @change="checkAlasanRequired('{{ strtolower($jl->nama) }}')">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium"
                                        :class="{ 'text-primary': jenisLayanan == {{ $jl->id }}, 'text-ink': jenisLayanan != {{ $jl->id }} }">{{ $jl->nama }}</span>
                                </span>
                            </span>
                            <svg class="h-5 w-5 text-primary" :class="{ 'invisible': jenisLayanan != {{ $jl->id }} }"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- 2. Alasan Pemberhentian (Hanya jika jenis = Pemberhentian) -->
            <div class="mb-6 p-5 bg-gray-50 rounded-lg border border-border" x-show="showAlasan" style="display: none;"
                x-transition>
                <label for="alasan_pemberhentian_id" class="block text-sm font-medium text-ink mb-2">Alasan
                    Pemberhentian <span class="text-danger">*</span></label>
                <select name="alasan_pemberhentian_id" id="alasan_pemberhentian_id"
                    class="w-full rounded-btn border-border text-sm text-ink focus:ring-primary focus:border-primary shadow-sm"
                    x-bind:required="showAlasan">
                    <option value="">-- Pilih Alasan --</option>
                    @foreach($alasanPemberhentians as $alasan)
                        <option value="{{ $alasan->id }}" {{ old('alasan_pemberhentian_id') == $alasan->id ? 'selected' : '' }}>{{ $alasan->nama }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-muted mt-2"><svg class="w-4 h-4 inline-block mr-1 text-primary" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg> Checklist dokumen pemberhentian akan berbeda tergantung pada alasan yang dipilih (misal:
                    syarat untuk meninggal dunia berbeda dengan purna tugas).</p>
            </div>

            <!-- 3. Perangkat Desa (Dinamis / Kolektif) -->
            <div class="mb-8 p-5 bg-gray-50 rounded-lg border border-border">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-medium text-ink">Daftar Perangkat Desa <span
                            class="text-danger">*</span></label>
                    <button type="button" x-show="showRotasi || (!showRotasi && !showAlasan)" @click="addPeserta()"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-primary bg-primary-soft hover:bg-primary-light hover:text-white rounded transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Peserta
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(peserta, index) in pesertas" :key="peserta.id">
                        <div class="p-4 bg-white rounded border border-gray-200 relative shadow-sm">
                            <button type="button" x-show="pesertas.length > 1" @click="removePeserta(index)"
                                class="absolute top-3 right-3 text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1 rounded-full transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                                <div>
                                    <label class="block text-xs font-medium text-muted mb-1">Nama Perangkat</label>
                                    <select x-model="peserta.perangkat_desa_id"
                                        :name="'pesertas['+index+'][perangkat_desa_id]'"
                                        class="w-full rounded border-border text-sm text-ink focus:ring-primary focus:border-primary shadow-sm"
                                        required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($perangkatDesas as $perangkat)
                                            <option value="{{ $perangkat->id }}">{{ $perangkat->nama }} —
                                                {{ $perangkat->jabatan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div x-show="showRotasi">
                                    <label class="block text-xs font-medium text-muted mb-1">Jabatan Baru</label>
                                    <input type="text" x-model="peserta.jabatan_baru"
                                        :name="'pesertas['+index+'][jabatan_baru]'"
                                        class="w-full rounded border-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                                        placeholder="Contoh: Kasi Kesejahteraan" x-bind:required="showRotasi">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <p class="text-xs text-muted mt-3" x-show="showAlasan"><svg
                        class="w-4 h-4 inline-block mr-1 text-primary" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg> Laporan Pemberhentian bersifat spesifik per individu. Formulir dikunci untuk 1 (satu) orang
                    peserta.</p>
                <p class="text-xs text-muted mt-3" x-show="!showAlasan"><svg
                        class="w-4 h-4 inline-block mr-1 text-primary" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg> Jika Anda tidak menemukan nama perangkat desa, pastikan data telah ditambahkan pada menu Data
                    Kepala dan Perangkat Desa.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-border">
                <a href="{{ route('desa.ajuan.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-ink bg-white border border-border rounded-btn hover:bg-gray-50 transition-colors shadow-sm">Batal</a>
                <button type="submit" name="draft" value="1"
                    :disabled="isSubmitting"
                    :class="{'opacity-50 cursor-not-allowed': isSubmitting}"
                    class="px-5 py-2.5 text-sm font-medium text-primary bg-primary-soft border border-primary-soft rounded-btn hover:bg-primary-light hover:text-white transition-colors shadow-sm">
                    <span x-show="!isSubmitting">Simpan sebagai Draft</span>
                    <span x-show="isSubmitting">Memproses...</span>
                </button>
                <button type="submit"
                    :disabled="isSubmitting"
                    :class="{'opacity-50 cursor-not-allowed': isSubmitting}"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-btn hover:bg-primary-light transition-colors shadow-sm shadow-primary/30 flex items-center">
                    <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="!isSubmitting">Buat Ajuan & Lanjut Upload</span>
                    <span x-show="isSubmitting">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Script for interaction -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('ajuanForm', () => ({
                isSubmitting: false,
                metode: '{{ old('metode', 'online') }}',
                jenisLayanan: '{{ old('jenis_layanan_id') }}',
                showAlasan: false,
                showRotasi: false,

                pesertas: [
                    { id: Date.now(), perangkat_desa_id: '', jabatan_baru: '' }
                ],

                init() {
                    // Cek di awal jika old data terisi
                    @if(old('jenis_layanan_id'))
                        const selectedJl = {!! \App\Models\JenisLayanan::find(old('jenis_layanan_id'))->toJson() ?? 'null' !!};
                        if (selectedJl && selectedJl.nama.toLowerCase() === 'pemberhentian') {
                            this.showAlasan = true;
                        }
                        if (selectedJl && selectedJl.nama.toLowerCase() === 'rotasi') {
                            this.showRotasi = true;
                        }
                    @endif
                },

                addPeserta() {
                    this.pesertas.push({ id: Date.now(), perangkat_desa_id: '', jabatan_baru: '' });
                },

                removePeserta(idx) {
                    if (this.pesertas.length > 1) {
                        this.pesertas.splice(idx, 1);
                    }
                },

                checkAlasanRequired(namaLayanan) {
                    this.showAlasan = namaLayanan === 'pemberhentian';
                    this.showRotasi = namaLayanan === 'rotasi';

                    if (!this.showAlasan) {
                        document.getElementById('alasan_pemberhentian_id').value = '';
                    }
                    if (this.showAlasan) {
                        // Jika pemberhentian, pangkas pesertas jadi 1
                        if (this.pesertas.length > 1) {
                            this.pesertas = [this.pesertas[0]];
                        }
                    }
                }
            }))
        })
    </script>
</x-app-layout>
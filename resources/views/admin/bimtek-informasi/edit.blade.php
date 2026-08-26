<x-app-layout>
    @section('title', 'Edit Informasi Pembinaan')

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-card shadow-sm border border-border p-8">
            <div class="mb-6">
                <a href="{{ route('admin.bimtek-informasi.index') }}"
                    class="text-sm text-primary hover:underline flex items-center gap-1 mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </a>
                <h2 class="text-xl font-display font-bold text-ink">Edit Informasi Pembinaan</h2>
                <p class="text-muted text-sm mt-1">Perbarui konten informasi: <strong>{{ $bimtekInformasi->judul }}</strong></p>
            </div>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.bimtek-informasi.update', $bimtekInformasi) }}" method="POST" enctype="multipart/form-data" class="space-y-5" onsubmit="if(window.tinymce){ tinymce.triggerSave(); }">
                @csrf @method('PUT')

                <div>
                    <label for="judul" class="block text-sm font-medium text-ink mb-1">Judul <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" id="judul" required value="{{ old('judul', $bimtekInformasi->judul) }}"
                        class="w-full rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm">
                </div>

                <div>
                    <label for="kategori" class="block text-sm font-medium text-ink mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori" id="kategori" required
                        class="w-full rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm">
                        <option value="informasi" {{ old('kategori', $bimtekInformasi->kategori) === 'informasi' ? 'selected' : '' }}>📋 Informasi Umum</option>
                        <option value="dokumentasi" {{ old('kategori', $bimtekInformasi->kategori) === 'dokumentasi' ? 'selected' : '' }}>📷 Dokumentasi Kegiatan</option>
                        <option value="pengumuman" {{ old('kategori', $bimtekInformasi->kategori) === 'pengumuman' ? 'selected' : '' }}>📢 Pengumuman</option>
                    </select>
                </div>

                <div>
                    <label for="konten" class="block text-sm font-medium text-ink mb-1">Konten / Isi <span class="text-red-500">*</span></label>
                    <textarea name="konten" id="konten" rows="8"
                        class="w-full rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm">{{ old('konten', $bimtekInformasi->konten) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="foto" class="block text-sm font-medium text-ink mb-1">Foto Baru (Opsional)</label>
                        @if($bimtekInformasi->foto && is_array($bimtekInformasi->foto) && count($bimtekInformasi->foto) > 0)
                            <div class="mb-2 flex flex-wrap gap-2">
                                @foreach($bimtekInformasi->foto as $img)
                                    <img src="{{ Storage::url($img) }}" class="h-20 rounded object-cover" alt="Foto saat ini">
                                @endforeach
                            </div>
                            <p class="text-xs text-muted mb-2">Foto saat ini. Upload baru untuk mengganti semua foto.</p>
                        @elseif($bimtekInformasi->foto && is_string($bimtekInformasi->foto))
                            <div class="mb-2">
                                <img src="{{ Storage::url($bimtekInformasi->foto) }}" class="h-20 rounded object-cover" alt="Foto saat ini">
                                <p class="text-xs text-muted mt-1">Foto saat ini. Upload baru untuk mengganti.</p>
                            </div>
                        @endif
                        <input type="file" name="foto[]" id="foto" accept="image/*" multiple
                            onchange="if(this.files.length > 5){ alert('Maksimal 5 foto!'); this.value=''; return; } for(let f of this.files){ if(f.size > 20971520){ alert('Ukuran per foto maksimal 20MB!'); this.value=''; return; } }"
                            class="w-full rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm text-sm p-1">
                        <p class="text-xs text-muted mt-1">Format: JPG, PNG, WebP. Maks 5 foto, ukuran maks 20MB/foto. Akan menimpa foto lama.</p>
                    </div>

                    <div>
                        <label for="file_lampiran" class="block text-sm font-medium text-ink mb-1">File Lampiran Baru (Opsional)</label>
                        @if($bimtekInformasi->file_lampiran)
                            <a href="{{ asset('storage/' . $bimtekInformasi->file_lampiran) }}" target="_blank"
                                class="text-primary text-xs hover:underline block mb-2">📎 Lihat lampiran saat ini</a>
                        @endif
                        <input type="file" name="file_lampiran" id="file_lampiran" accept=".pdf,.doc,.docx"
                            class="w-full rounded-md border-border text-ink bg-white text-sm p-1">
                    </div>
                </div>

                <div>
                    <label for="published_at" class="block text-sm font-medium text-ink mb-1">Tanggal Publikasi</label>
                    <input type="datetime-local" name="published_at" id="published_at"
                        value="{{ old('published_at', $bimtekInformasi->published_at?->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm text-sm">
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-border pt-5">
                    <a href="{{ route('admin.bimtek-informasi.index') }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-ink font-medium rounded-btn transition-colors text-sm">Batal</a>
                    <button type="submit"
                        class="px-5 py-2 bg-primary text-white font-medium rounded-btn hover:bg-primary-light transition-colors shadow-sm text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '#konten',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            height: 500,
            menubar: false,
            images_upload_handler: function (blobInfo, progress) {
                return new Promise((resolve, reject) => {
                    let xhr = new XMLHttpRequest();
                    xhr.open('POST', '{{ route('admin.bimtek-informasi.upload-image') }}');
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    
                    xhr.upload.onprogress = (e) => {
                        progress(e.loaded / e.total * 100);
                    };
                    
                    xhr.onload = () => {
                        if (xhr.status === 403) {
                            reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                            return;
                        }
                        if (xhr.status < 200 || xhr.status >= 300) {
                            reject('HTTP Error: ' + xhr.status);
                            return;
                        }
                        let json = JSON.parse(xhr.responseText);
                        if (!json || typeof json.location != 'string') {
                            reject('Invalid JSON: ' + xhr.responseText);
                            return;
                        }
                        resolve(json.location);
                    };
                    xhr.onerror = () => { reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status); };
                    let formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    xhr.send(formData);
                });
            }
        });
    </script>
</x-app-layout>

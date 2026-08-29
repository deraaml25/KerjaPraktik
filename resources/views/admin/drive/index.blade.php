<x-app-layout>
    @section('title', 'Arsip Dokumen Digital')

    <div class="mb-4">
        <!-- Breadcrumb -->
        <div class="text-sm mb-6 flex items-center gap-1.5">
            @foreach($breadcrumbs as $idx => $crumb)
                @if($idx > 0)
                    <span class="text-muted">/</span>
                @endif
                <a href="{{ route('admin.drive.index', ['path' => $crumb['path']]) }}" 
                   class="{{ $loop->last ? 'text-ink font-medium' : 'text-muted hover:text-primary transition-colors' }}">
                    {{ $crumb['label'] }}
                </a>
            @endforeach
        </div>

        <!-- Buttons -->
        <div class="flex items-center gap-3 mb-6">
            <form action="{{ route('admin.drive.upload') }}" method="POST" enctype="multipart/form-data" class="inline-flex">
                @csrf
                <input type="hidden" name="path" value="{{ request('path', 'dokumen') }}">
                <input type="file" name="file" id="file_upload" class="hidden" onchange="this.form.submit()">
                <button type="button" onclick="document.getElementById('file_upload').click()" class="inline-flex items-center px-4 py-2.5 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                    Upload
                </button>
            </form>
            @php
                $folderName = basename(request('path', 'dokumen'));
                $label = $folderName === 'dokumen' ? 'Semua_Arsip' : 'Arsip_' . ucwords(str_replace('_', ' ', $folderName));
            @endphp
            <a href="{{ route('admin.drive.download-zip', ['path' => request('path', 'dokumen'), 'label' => $label]) }}" class="inline-flex items-center px-4 py-2.5 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download All
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success') || session('error'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '{{ session('success') ? "success" : "error" }}',
                    title: '{{ session('success') ? "Berhasil!" : "Gagal!" }}',
                    text: '{{ session('success') ?? session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    position: 'top'
                });
            });
        </script>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 rounded-card bg-red-50 border border-red-200 text-red-700 flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <ul class="list-disc pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div>
        @if(empty($folders) && empty($files))
            <div class="flex flex-col items-center justify-center text-muted py-12">
                <svg class="w-16 h-16 mb-4 text-border" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/></svg>
                <p class="font-medium text-lg text-ink">Folder ini kosong</p>
                <p class="text-sm mt-1">Belum ada dokumen atau sub-folder di dalam direktori ini.</p>
            </div>
        @else
            <!-- Folders -->
            @if(count($folders) > 0)
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">FOLDER</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-7 gap-4 mb-10">
                    @php
                        $colors = [
                            'text-blue-400',
                            'text-orange-400',
                            'text-green-400',
                            'text-purple-400',
                            'text-rose-400'
                        ];
                    @endphp
                    @foreach($folders as $idx => $folder)
                        @php
                            $colorClass = $colors[$idx % count($colors)];
                            $isActive = (request('path') == $folder['path'] || ($idx === 0 && !request('path')));
                            
                            $bgClass = $isActive ? 'bg-blue-50 border-blue-200' : 'bg-white border-slate-200';
                        @endphp
                        <a href="{{ route('admin.drive.index', ['path' => $folder['path']]) }}" class="block border rounded-2xl p-5 hover:shadow-sm transition-all {{ $bgClass }} min-h-[140px] flex flex-col justify-between">
                            <div class="mb-4">
                                <svg class="w-12 h-12 {{ $colorClass }}" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M10 4H4C2.89543 4 2 4.89543 2 6V18C2 19.1046 2.89543 20 4 20H20C21.1046 20 22 19.1046 22 18V8C22 6.89543 21.1046 6 20 6H12L10 4Z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-[15px] font-semibold text-slate-800 truncate" title="{{ $folder['name'] }}">{{ $folder['name'] }}</h4>
                                <p class="text-[11px] text-slate-500 mt-1">{{ $folder['count'] }} item</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Files -->
            @if(count($files) > 0)
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">FILE</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($files as $file)
                        @php
                            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                            $isImg = in_array(strtolower($ext), ['jpg', 'jpeg', 'png']);
                        @endphp
                        <div class="relative block bg-white border border-slate-100 rounded-[20px] p-4 hover:shadow-sm transition-all flex flex-col justify-between group">
                            <a href="{{ $file['url'] }}" target="_blank" class="block flex-1">
                                <div class="mb-4 flex items-center justify-center h-16 bg-slate-50 rounded-lg overflow-hidden relative">
                                    @if($isImg)
                                        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $file['url'] }}')"></div>
                                    @else
                                        <svg class="w-8 h-8 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-[13px] font-medium text-ink truncate" title="{{ $file['name'] }}">{{ $file['name'] }}</h4>
                                    <p class="text-[10px] text-slate-400 mt-1">{{ round($file['size'] / 1024) }} KB</p>
                                </div>
                            </a>
                            
                            <form action="{{ route('admin.drive.delete') }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Hapus file ini secara permanen?');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="file_path" value="{{ $file['path'] }}">
                                <button type="submit" class="bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center hover:bg-red-600 transition-colors shadow-sm" title="Hapus File">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</x-app-layout>


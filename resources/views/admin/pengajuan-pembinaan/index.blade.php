<x-app-layout>
    @section('title', 'Pembinaan')
    @section('page-description', 'Daftar permohonan narasumber dan pembinaan yang diajukan oleh desa-desa.')


    <!-- Tabs Nav -->
    <div class="border-b border-border mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('admin.bimtek-informasi.index') }}"
               class="border-b-2 py-4 px-1 text-sm font-semibold {{ request()->routeIs('admin.bimtek-informasi.*') ? 'border-blue-700 text-blue-700' : 'border-transparent text-muted hover:text-ink hover:border-gray-300' }}">
                Berita & Informasi Pembinaan
            </a>
            <a href="{{ route('admin.pengajuan-pembinaan.index') }}"
               class="border-b-2 py-4 px-1 text-sm font-semibold {{ request()->routeIs('admin.pengajuan-pembinaan.*') ? 'border-blue-700 text-blue-700' : 'border-transparent text-muted hover:text-ink hover:border-gray-300' }}">
                Pengajuan Pembinaan Desa
            </a>
        </nav>
    </div>

    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session("success") }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    position: 'top'
                });
            });
        </script>
    @endif

    <div class="bg-white rounded-card shadow-sm border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">Desa</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">Judul Kegiatan</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Tanggal Diajukan</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">Dokumen</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    @forelse ($pengajuans as $p)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-4 text-sm font-medium text-ink">{{ $p->desa->nama_desa ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <div class="text-sm text-ink font-medium">{{ $p->judul_kegiatan }}</div>
                                @if($p->deskripsi)
                                    <div class="text-xs text-muted mt-0.5">{{ Str::limit($p->deskripsi, 80) }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center text-sm text-muted whitespace-nowrap">
                                {{ $p->tanggal_diajukan->format('d/m/y') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    @if($p->file_surat_permohonan)
                                        <a href="{{ asset('storage/' . $p->file_surat_permohonan) }}" target="_blank"
                                            class="text-blue-700 font-medium text-xs hover:underline">Surat Permohonan</a>
                                    @endif
                                    @if($p->file_undangan)
                                        <a href="{{ asset('storage/' . $p->file_undangan) }}" target="_blank"
                                            class="text-blue-700 font-medium text-xs hover:underline">Surat Undangan</a>
                                    @endif
                                    @if(!$p->file_surat_permohonan && !$p->file_undangan)
                                        <span class="text-xs text-muted italic">Tidak ada dokumen</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase {{ $p->status_color }}" style="width: 130px;">
                                    {{ $p->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.pengajuan-pembinaan.show', $p) }}"
                                        class="inline-flex items-center px-2 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 text-xs font-medium rounded border border-blue-200 transition-all hover:scale-105" title="Detail & Balas">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    </a>
                                    <form action="{{ route('admin.pengajuan-pembinaan.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pengajuan pembinaan ini secara permanen?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 text-xs font-medium rounded border border-red-200 transition-all hover:scale-105" title="Hapus">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-muted">
                                Belum ada pengajuan pembinaan dari desa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pengajuans->hasPages())
            <div class="px-5 py-4 border-t border-border">{{ $pengajuans->links() }}</div>
        @endif
    </div>
</x-app-layout>


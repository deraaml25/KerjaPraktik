<x-app-layout>
    @section('title', 'e - Rekomendasi')

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

    <div class="bg-surface rounded-card border border-border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-border">
                        <th class="px-6 py-4 text-center text-xs font-bold text-ink uppercase tracking-wider">No. Registrasi</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-ink uppercase tracking-wider">Desa & Pemohon</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-ink uppercase tracking-wider">Layanan</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-ink uppercase tracking-wider">Posisi Surat</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-ink uppercase tracking-wider">Status & SLA</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-ink uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($ajuans as $ajuan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-mono text-ink">{{ $ajuan->no_registrasi }}</span>
                                <div class="text-xs text-muted mt-1">{{ $ajuan->tgl_diajukan ? $ajuan->tgl_diajukan->format('d/m/y') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-semibold text-ink">{{ $ajuan->desa->nama_desa }}</div>
                                <div class="text-xs text-muted mt-0.5">
                                    {{ $ajuan->pesertas->first() ? $ajuan->pesertas->first()->perangkatDesa->nama : '-' }}
                                    @if($ajuan->pesertas->count() > 1)
                                        <span class="text-primary font-bold ml-1">(+{{ $ajuan->pesertas->count() - 1 }})</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider mb-1 {{ $ajuan->jenisLayanan->nama == 'Pengangkatan' ? 'bg-green-100 text-green-800' : ($ajuan->jenisLayanan->nama == 'Pemberhentian' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}" style="width: 150px;">
                                    {{ $ajuan->jenisLayanan->nama }}
                                </span>
                                <div class="text-xs text-muted mt-1 uppercase font-bold">{{ $ajuan->metode }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-ink">{{ $ajuan->posisi_surat ?? 'Pegawai' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $statusBadge = match($ajuan->status) {
                                        'submitted' => ['label' => 'Menunggu Verifikasi', 'css' => 'bg-yellow-100 text-yellow-800'],
                                        'direvisi'  => ['label' => 'Perlu Perbaikan', 'css' => 'bg-red-100 text-red-800'],
                                        'diproses'  => ['label' => 'Dalam Proses', 'css' => 'bg-green-100 text-green-800'],
                                        'selesai'   => ['label' => 'Selesai', 'css' => 'bg-emerald-100 text-emerald-800'],
                                        'ditolak'   => ['label' => 'Ditolak', 'css' => 'bg-gray-200 text-gray-800'],
                                        default     => ['label' => $ajuan->status, 'css' => 'bg-gray-100 text-gray-800'],
                                    };
                                @endphp
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider mb-1 {{ $statusBadge['css'] }}" style="width: 150px;">
                                    {{ $statusBadge['label'] }}
                                </span>
                                @if($ajuan->tgl_sla_batas)
                                    @php
                                        $sisaHari = now()->startOfDay()->diffInDays($ajuan->tgl_sla_batas, false);
                                    @endphp
                                    <div class="text-xs font-medium text-ink">
                                        SLA: Sisa {{ $sisaHari }} Hari
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.ajuan.show', $ajuan) }}" class="inline-flex items-center px-2 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 text-xs font-medium rounded border border-blue-200 transition-all hover:scale-105" title="Verifikasi">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    </a>
                                    <form action="{{ route('admin.ajuan.destroy', $ajuan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data usulan ini secara permanen? Semua berkas terkait akan ikut terhapus.');" class="inline">
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
                            <td colspan="6" class="px-6 py-10 text-center text-muted">
                                Tidak ada pengajuan e-rekomendasi yang berjalan saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>


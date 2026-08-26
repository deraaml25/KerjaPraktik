<x-app-layout>
    @section('title', 'Verifikasi e-Rekomendasi')

    @if(session('success'))
        <div class="mb-5 p-4 rounded-card bg-green-50 border border-green-200 text-green-800 flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-surface rounded-card border border-border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-border">
                        <th class="px-6 py-4 text-center text-xs font-semibold text-muted uppercase tracking-wider">No. Registrasi</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-muted uppercase tracking-wider">Desa & Pemohon</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-muted uppercase tracking-wider">Layanan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-muted uppercase tracking-wider">Posisi Surat</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-muted uppercase tracking-wider">Status & SLA</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($ajuans as $ajuan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-mono text-ink">{{ $ajuan->no_registrasi }}</span>
                                <div class="text-xs text-muted mt-1">{{ $ajuan->tgl_diajukan ? $ajuan->tgl_diajukan->format('d M Y') : '-' }}</div>
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
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $ajuan->jenisLayanan->nama == 'Pengangkatan' ? 'bg-indigo-100 text-indigo-800' : ($ajuan->jenisLayanan->nama == 'Pemberhentian' ? 'bg-red-100 text-danger' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $ajuan->jenisLayanan->nama }}
                                </span>
                                <div class="text-xs text-muted mt-1 uppercase font-bold">{{ $ajuan->metode }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-ink">{{ $ajuan->posisi_surat ?? 'Front Office (FO)' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $statusBadge = match($ajuan->status) {
                                        'submitted' => ['label' => 'Menunggu Verifikasi', 'css' => 'bg-blue-100 text-blue-800'],
                                        'direvisi'  => ['label' => 'Perlu Perbaikan', 'css' => 'bg-red-100 text-red-800'],
                                        'diproses'  => ['label' => 'Dalam Proses', 'css' => 'bg-yellow-100 text-yellow-800'],
                                        'selesai'   => ['label' => 'Selesai', 'css' => 'bg-green-100 text-green-800'],
                                        'ditolak'   => ['label' => 'Ditolak', 'css' => 'bg-gray-200 text-gray-800'],
                                        default     => ['label' => $ajuan->status, 'css' => 'bg-gray-100 text-gray-800'],
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mb-1 {{ $statusBadge['css'] }}">
                                    {{ $statusBadge['label'] }}
                                </span>
                                @if($ajuan->tgl_sla_batas)
                                    @php
                                        $sisaHari = now()->startOfDay()->diffInDays($ajuan->tgl_sla_batas, false);
                                        $slaClass = $sisaHari < 3 ? 'text-danger' : ($sisaHari <= 7 ? 'text-warning' : 'text-success');
                                    @endphp
                                    <div class="text-xs font-medium {{ $slaClass }}">
                                        SLA: Sisa {{ $sisaHari }} Hari
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.ajuan.show', $ajuan) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium rounded bg-primary text-white hover:bg-primary-light transition-all hover:scale-105 shadow-sm">
                                        Verifikasi
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

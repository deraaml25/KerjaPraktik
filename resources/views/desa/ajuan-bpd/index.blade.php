<x-app-layout>
    @section('title', 'Ajuan BPD')

    <div class="flex items-center justify-between mb-6 mt-1">
        <div>
            <p class="text-muted text-sm mt-1">Kelola pengajuan pemberhentian dan peresmian BPD (PAW) Desa Anda.</p>
        </div>
        <a href="{{ route('desa.ajuan-bpd.create') }}"
            class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-btn hover:bg-primary-light hover:-translate-y-0.5 hover:shadow-lg transition-all active:scale-95 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Ajuan Baru
        </a>
    </div>

    <!-- Tabel -->
    <div>
        <div class="bg-white rounded-card shadow-sm border border-border overflow-hidden transition-shadow duration-300 hover:shadow-md">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">No. Registrasi / Tanggal</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Jenis Ajuan</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Anggota BPD</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Kelengkapan Dokumen</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-border">
                        @forelse ($ajuans as $ajuan)
                            @php
                                $totalChecklist = $ajuan->checklists->count();
                                $uploadedChecklist = $ajuan->checklists->whereNotNull('file_path')->count();
                                $approvedChecklist = $ajuan->checklists->where('status', 'terverifikasi')->count();
                                $percent = $totalChecklist > 0 ? round(($uploadedChecklist / $totalChecklist) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="font-mono text-sm font-semibold text-ink text-center">{{ $ajuan->no_registrasi }}</div>
                                    <div class="text-xs text-muted mt-0.5 text-center">{{ $ajuan->tgl_diajukan ? \Carbon\Carbon::parse($ajuan->tgl_diajukan)->translatedFormat('d/m/y') : ($ajuan->created_at ? $ajuan->created_at->translatedFormat('d/m/y') : '-') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $badgeColor = match($ajuan->jenis_ajuan) {
                                            'pemberhentian' => 'bg-red-100 text-danger',
                                            'peresmian' => 'bg-primary-soft text-primary',
                                            default => 'bg-gray-100 text-muted'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $badgeColor }}">
                                        {{ str_replace('_', ' ', $ajuan->jenis_ajuan) }}
                                    </span>
                                    <div class="text-xs text-muted mt-1 uppercase font-bold">{{ $ajuan->metode }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-ink font-display">
                                        {{ $ajuan->pesertas->first() ? $ajuan->pesertas->first()->bpd->nama : '-' }}
                                        @if($ajuan->pesertas->count() > 1) 
                                            <span class="text-primary font-bold ml-1">(+{{ $ajuan->pesertas->count() - 1 }})</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-muted mt-0.5">{{ $ajuan->pesertas->first() ? $ajuan->pesertas->first()->bpd->jabatan : '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="bg-primary h-2 rounded-full" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-ink">{{ $uploadedChecklist }}/{{ $totalChecklist }}</span>
                                    </div>
                                    <div class="text-xs text-muted mt-1">{{ $approvedChecklist }} disetujui</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusLabel = ['menunggu_verifikasi' => 'Menunggu Verifikasi', 'revisi' => 'Perlu Perbaikan', 'diproses' => 'Sedang Diproses', 'selesai' => 'Selesai', 'draft' => 'Draft'];
                                        $statusWarna = ['menunggu_verifikasi' => 'bg-blue-100 text-blue-700', 'revisi' => 'bg-red-100 text-danger', 'diproses' => 'bg-yellow-100 text-warning', 'selesai' => 'bg-green-100 text-success', 'draft' => 'bg-gray-100 text-muted'];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $statusWarna[$ajuan->status] ?? 'bg-gray-100 text-muted' }}">
                                        {{ $statusLabel[$ajuan->status] ?? $ajuan->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('desa.ajuan-bpd.show', $ajuan) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium rounded bg-primary text-white hover:bg-primary-light transition-all hover:scale-105 shadow-sm">
                                            Lihat & Unggah
                                        </a>
                                        <form action="{{ route('desa.ajuan-bpd.destroy', $ajuan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ajuan ini secara permanen? Semua berkas terkait akan ikut terhapus.');" class="inline">
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
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-muted">
                                    Belum ada ajuan BPD. Klik tombol <strong>+ Buat Ajuan Baru</strong> di atas untuk membuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($ajuans->hasPages())
            <div class="mt-6">{{ $ajuans->links() }}</div>
        @endif
    </div>
</x-app-layout>

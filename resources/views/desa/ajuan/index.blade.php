<x-app-layout>
    @section('title', 'Daftar Ajuan Saya')

    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-muted text-sm mt-1">Kelola seluruh ajuan rekomendasi terkait pengangkatan, rotasi, dan pemberhentian perangkat desa Anda.</p>
        </div>
        
        <a href="{{ route('desa.ajuan.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-btn hover:bg-primary-light hover:-translate-y-0.5 hover:shadow-lg transition-all active:scale-95 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Ajuan Baru
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-card bg-green-50 border border-green-200 text-green-700 flex items-start shadow-sm">
            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- Ajuan Table -->
    <div class="bg-surface rounded-card border border-border shadow-sm overflow-hidden transition-shadow duration-300 hover:shadow-md">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">No. Registrasi / Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Jenis Layanan</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Perangkat Desa</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Kelengkapan Dokumen</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($ajuans as $ajuan)
                        @php
                            $totalChecklist = $ajuan->checklistAjuans->count();
                            $uploadedChecklist = $ajuan->checklistAjuans->whereNotNull('file_path')->count();
                            $approvedChecklist = $ajuan->checklistAjuans->whereIn('status', ['lengkap', 'valid', 'terverifikasi'])->count();
                            $percent = $totalChecklist > 0 ? round(($uploadedChecklist / $totalChecklist) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="font-mono text-sm font-semibold text-ink text-center">{{ $ajuan->no_registrasi }}</div>
                                <div class="text-xs text-muted text-center">{{ \Carbon\Carbon::parse($ajuan->tgl_diajukan)->format('d/m/y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeColor = match($ajuan->jenisLayanan->nama) {
                                        'Pengangkatan' => 'bg-green-100 text-green-800',
                                        'Pemberhentian' => 'bg-red-100 text-red-800',
                                        'Rotasi' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-muted'
                                    };
                                @endphp
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-bold uppercase mb-1 {{ $badgeColor }}" style="width: 140px;">
                                    {{ $ajuan->jenisLayanan->nama }}
                                </span>
                                <div class="text-xs text-muted mt-1 uppercase font-bold">{{ $ajuan->metode }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-ink">
                                    {{ $ajuan->pesertas->first() ? $ajuan->pesertas->first()->perangkatDesa->nama : '-' }}
                                    @if($ajuan->pesertas->count() > 1) 
                                        <span class="text-primary font-bold ml-1">(+{{ $ajuan->pesertas->count() - 1 }})</span>
                                    @endif
                                </div>
                                <div class="text-xs text-muted">{{ $ajuan->pesertas->first() ? $ajuan->pesertas->first()->perangkatDesa->jabatan : '-' }}</div>
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
                                    $statusLabel = ['submitted' => 'Menunggu Verifikasi', 'direvisi' => 'Perlu Perbaikan', 'diproses' => 'Sedang Diproses', 'selesai' => 'Selesai', 'draft' => 'Draft'];
                                    $statusWarna = ['submitted' => 'bg-blue-100 text-blue-700', 'direvisi' => 'bg-red-100 text-danger', 'diproses' => 'bg-yellow-100 text-warning', 'selesai' => 'bg-green-100 text-success', 'draft' => 'bg-gray-100 text-muted'];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $statusWarna[$ajuan->status] ?? 'bg-gray-100 text-muted' }}">
                                    {{ $statusLabel[$ajuan->status] ?? $ajuan->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('desa.ajuan.show', $ajuan) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium rounded bg-primary text-white hover:bg-primary-light transition-all hover:scale-105 shadow-sm">
                                        Lihat & Unggah
                                    </a>
                                    <form action="{{ route('desa.ajuan.destroy', $ajuan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus usulan ini secara permanen? Semua berkas terkait akan ikut terhapus.');" class="inline">
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
                            <td colspan="6" class="p-0">
                                <x-empty-state 
                                    icon="<path stroke-linecap='round' stroke-linejoin='round' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' />"
                                    title="Belum ada ajuan yang dibuat"
                                    message="Mulai dengan membuat ajuan baru untuk memproses rekomendasi perangkat desa."
                                >
                                    <x-slot name="action">
                                        <a href="{{ route('desa.ajuan.create') }}" class="px-6 py-2.5 bg-primary text-white rounded-full text-sm font-bold shadow-md hover:bg-primary-light hover:shadow-lg hover:-translate-y-0.5 transition-all inline-flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Buat Ajuan Sekarang
                                        </a>
                                    </x-slot>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ajuans->hasPages())
            <div class="px-6 py-4 border-t border-border">
                {{ $ajuans->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

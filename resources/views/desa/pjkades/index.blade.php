<x-app-layout>
    @section('title', 'SK Kades')

    <div class="flex items-center justify-between mb-6 mt-1">
        <div>
            <p class="text-muted text-sm mt-1">Kelola usulan pemberhentian Kades dan penunjukan Pj/Plt secara digital.</p>
        </div>
        <a href="{{ route('desa.pjkades.create') }}"
            class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-btn hover:bg-primary-light hover:-translate-y-0.5 hover:shadow-lg transition-all active:scale-95 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Usulan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 text-sm mb-6 font-medium flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 text-red-800 rounded-lg border border-red-200 text-sm mb-6 font-medium">
            {{ session('error') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-card shadow-sm border border-border overflow-hidden transition-shadow duration-300 hover:shadow-md">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">No. Registrasi / Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Jenis Pemberhentian & Alasan</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Pengganti (Pj / Plt Kades)</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Kelengkapan Dokumen</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    @forelse ($pjkades as $pj)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm font-semibold text-ink font-mono text-center">{{ $pj->no_registrasi ?? ('SKK-' . $pj->id) }}</div>
                                <div class="text-xs text-muted mt-0.5 text-center">{{ $pj->tgl_diajukan ? $pj->tgl_diajukan->format('d/m/y') : $pj->created_at->format('d/m/y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($pj->kategori === 'plt_kades')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Pemberhentian Sementara / Cuti
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                        Pemberhentian
                                    </span>
                                @endif
                                <div class="text-xs font-medium text-ink mt-1">Alasan: <strong>{{ $pj->alasan_nama ?? ($pj->alasanPemberhentian->nama ?? '-') }}</strong></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($pj->kategori === 'plt_kades')
                                    <div class="text-sm font-semibold text-ink font-display">{{ $pj->nama_plt ?? '-' }}</div>
                                    <div class="text-xs text-amber-700 font-medium">Plt Kades (Sekretaris Desa)</div>
                                @else
                                    <div class="text-sm font-semibold text-ink font-display">{{ $pj->nama_pns ?? '-' }}</div>
                                    <div class="text-xs text-indigo-700 font-medium">Pj Kades (PNS {{ $pj->pangkat ?? '' }})</div>
                                    <div class="text-xs text-muted font-mono">NIP. {{ $pj->nip ?? '-' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $totalChecklist = $pj->checklists->count();
                                    $uploadedChecklist = $pj->checklists->whereNotNull('file_path')->count();
                                    $approvedChecklist = $pj->checklists->where('status_verifikasi', 'valid')->count();
                                    $percent = $totalChecklist > 0 ? round(($uploadedChecklist / $totalChecklist) * 100) : 0;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="w-24 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-primary h-2 rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-ink">{{ $uploadedChecklist }}/{{ $totalChecklist }}</span>
                                </div>
                                <div class="text-xs text-muted mt-1">{{ $approvedChecklist }} disetujui</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($pj->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Disetujui / SK Terbit
                                    </span>
                                @elseif($pj->status === 'submitted')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($pj->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Ditolak / Dikembalikan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Draft (Lengkapi Berkas)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('desa.pjkades.show', $pj->id) }}"
                                        class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium rounded bg-primary text-white hover:bg-primary-light transition-all hover:scale-105 shadow-sm">
                                        Lihat & Unggah
                                    </a>
                                    <form action="{{ route('desa.pjkades.destroy', $pj->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus usulan ini secara permanen? Semua berkas terkait akan ikut terhapus.');" class="inline">
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
                                Belum ada usulan SK Kades. Klik tombol <strong>+ Buat Usulan SK Pemberhentian & Kades Baru</strong> di atas untuk membuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pjkades->hasPages())
            <div class="px-6 py-4 border-t border-border">
                {{ $pjkades->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
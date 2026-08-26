<x-app-layout>
    @section('title', 'SK Pemberhentian Kades & Pengangkatan Pj/Plt Kades')

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

    {{-- Alert Masa Jabatan Hampir Berakhir --}}
    @php
        $alertPj = $pjkades->filter(fn($p) => $p->status === 'approved' && $p->hampir_berakhir);
    @endphp
    @if($alertPj->count() > 0)
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-lg text-sm mb-6">
            <strong class="font-bold block">⚠️ PERINGATAN DINI — Masa Jabatan Hampir Habis</strong>
            <p class="text-xs mt-1">Terdapat <strong>{{ $alertPj->count() }}</strong> SK Kades yang masa jabatannya akan berakhir dalam 30 hari:</p>
            <ul class="list-disc ml-5 mt-1 text-xs">
                @foreach($alertPj as $a)
                    <li><strong>{{ $a->kategori === 'plt_kades' ? ($a->nama_plt ?? 'Plt Sekdes') : $a->nama_pns }}</strong> (Desa {{ $a->desa->nama_desa }}) — Sisa <strong>{{ $a->sisa_hari }} hari</strong></li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-card shadow-sm border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Kecamatan / Desa</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Jenis Pemberhentian & Alasan</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Pengganti (Pj / Plt Kades)</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Verifikasi Berkas</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-ink uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    @forelse ($pjkades as $pj)
                        <tr class="hover:bg-gray-50/50 {{ $pj->status === 'approved' && $pj->hampir_berakhir ? 'bg-red-50/30' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-left">
                                <div class="text-sm font-bold text-ink">Desa {{ $pj->desa->nama_desa }}</div>
                                <div class="text-xs text-blue-700 font-bold">Kec. {{ $pj->desa->kecamatan->nama_kecamatan ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($pj->kategori === 'plt_kades')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-800">
                                        Pemberhentian Sementara / Cuti
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-800">
                                        Pemberhentian
                                    </span>
                                @endif
                                <div class="text-xs text-ink mt-1 font-medium"><strong>{{ $pj->alasan_nama ?? ($pj->alasanPemberhentian->nama ?? '-') }}</strong></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left">
                                @if($pj->kategori === 'plt_kades')
                                    <div class="text-sm font-bold text-ink">{{ $pj->nama_plt ?? '-' }}</div>
                                    <div class="text-xs text-indigo-700 font-medium">Plt Kades (Sekretaris Desa)</div>
                                @else
                                    <div class="text-sm font-bold text-ink">{{ $pj->nama_pns ?? '-' }}</div>
                                    <div class="text-xs text-indigo-700 font-medium">Pj Kades (PNS {{ $pj->pangkat ?? '' }})</div>
                                    <div class="text-xs text-muted font-mono">NIP. {{ $pj->nip ?? '-' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $totalChecklist = $pj->checklists->count();
                                    $uploadedChecklist = $pj->checklists->whereNotNull('file_path')->count();
                                    $approvedChecklist = $pj->checklists->where('status_verifikasi', 'valid')->count();
                                @endphp
                                <div class="text-xs font-bold text-ink">{{ $approvedChecklist }}/{{ $totalChecklist }} Disetujui</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($pj->status === 'submitted')
                                    <span style="width: 130px;" class="inline-flex justify-center items-center px-3 py-1 rounded-full text-[11px] font-bold bg-yellow-100 text-yellow-800 tracking-wider">
                                        PERLU VERIFIKASI
                                    </span>
                                @elseif($pj->status === 'direvisi')
                                    <span style="width: 130px;" class="inline-flex justify-center items-center px-3 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-800 tracking-wider">
                                        REVISI
                                    </span>
                                @elseif($pj->status === 'approved')
                                    <span style="width: 130px;" class="inline-flex justify-center items-center px-3 py-1 rounded-full text-[11px] font-bold bg-green-100 text-green-800 tracking-wider">
                                        SELESAI
                                    </span>
                                @elseif($pj->status === 'rejected')
                                    <span style="width: 130px;" class="inline-flex justify-center items-center px-3 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-800 tracking-wider">
                                        DITOLAK
                                    </span>
                                @else
                                    <span style="width: 130px;" class="inline-flex justify-center items-center px-3 py-1 rounded-full text-[11px] font-bold bg-green-100 text-green-800 tracking-wider">
                                        DIPROSES
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.pjkades.show', $pj->id) }}"
                                        class="inline-flex items-center px-2 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 text-xs font-medium rounded border border-blue-200 transition-all hover:scale-105" title="Verifikasi & SK">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    </a>
                                    <form action="{{ route('admin.pjkades.destroy', $pj->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus usulan SK Kades ini secara permanen? Semua berkas terkait akan ikut terhapus.');" class="inline">
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
                                Belum ada usulan SK Kades dari desa.
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

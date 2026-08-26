<x-app-layout>
    @section('title', 'Detail Rencana P3D ' . ucwords(strtolower($rencana->desa->nama_desa)))

    @section('back-button')
        <a href="{{ route('admin.rencana-p3d.index') }}" class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border border-border text-muted hover:text-primary hover:border-primary transition-colors shadow-sm" title="Kembali">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
    @endsection

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Detail Informasi --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-card shadow-sm border border-border overflow-hidden">
                <div class="px-6 py-4 border-b border-border bg-gray-50 flex items-center justify-between">
                    <h3 class="text-base font-bold text-ink">Informasi Rencana P3D</h3>
                    <span class="text-xs font-medium text-muted">Dikirim: {{ $rencana->created_at->format('d/m/y H:i') }}</span>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                        <div>
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Kecamatan</label>
                            <p class="text-sm font-medium text-ink">{{ $rencana->kecamatan->nama_kecamatan ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Desa</label>
                            <p class="text-sm font-medium text-ink">{{ ucwords(strtolower($rencana->desa->nama_desa)) }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Tahun Anggaran</label>
                            <p class="text-sm font-medium text-ink">{{ $rencana->tahun ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Rencana Pelaksanaan</label>
                            <p class="text-sm font-medium text-ink flex items-center">
                                <span class="material-symbols-outlined text-muted text-[18px] mr-1.5">calendar_month</span>
                                {{ $rencana->rencana_pelaksanaan ? $rencana->rencana_pelaksanaan->format('F Y') : '-' }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Jumlah Formasi Kosong</label>
                            <p class="text-sm font-bold text-rose-600">{{ $rencana->jumlah_formasi_kosong }} Formasi</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Rencana Anggaran</label>
                            <p class="text-sm font-bold text-emerald-600">Rp {{ number_format($rencana->rencana_anggaran, 0, ',', '.') }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Jabatan yang Kosong</label>
                            <div class="p-4 bg-gray-50 rounded-lg border border-border text-sm font-medium text-ink whitespace-pre-wrap">{{ $rencana->jabatan_kosong }}</div>
                        </div>

                        <div class="md:col-span-2 border-t border-border pt-6">
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Keterangan / Kondisi</label>
                            <p class="text-sm text-ink leading-relaxed whitespace-pre-wrap">{{ $rencana->keterangan ?: 'Tidak ada keterangan khusus.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Evaluasi Panel --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-card shadow-sm border border-border sticky top-6">
                <div class="px-6 py-4 border-b border-border bg-gray-50">
                    <h3 class="text-base font-bold text-ink flex items-center">
                        <span class="material-symbols-outlined text-[20px] mr-2 text-primary">fact_check</span>
                        Tindakan Evaluasi
                    </h3>
                </div>
                
                <div class="p-6">
                    @if($rencana->status === 'dikirim')
                        <div class="mb-4 text-sm text-muted">
                            Rencana ini sedang menunggu persetujuan / evaluasi. Jika data dirasa sudah sesuai, klik setujui.
                        </div>
                        
                        <form action="{{ route('admin.rencana-p3d.update-status', $rencana->id) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-ink mb-2">Ubah Status</label>
                                <select name="status" class="w-full text-sm rounded-md border-border text-ink bg-white focus:border-primary focus:ring-primary shadow-sm">
                                    <option value="disetujui">Setujui Rencana</option>
                                    <option value="draft">Kembalikan ke Draft (Revisi)</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-primary text-white font-semibold rounded-btn hover:bg-primary-light transition-colors text-sm shadow-sm">
                                <span class="material-symbols-outlined text-[18px] mr-1.5">save</span>
                                Simpan Keputusan
                            </button>
                        </form>
                    @elseif($rencana->status === 'disetujui')
                        <div class="flex flex-col items-center justify-center p-4 bg-green-50 rounded-lg border border-green-100 text-center">
                            <span class="material-symbols-outlined text-[40px] text-green-500 mb-2">task_alt</span>
                            <h4 class="font-bold text-green-800 mb-1">Rencana Disetujui</h4>
                            <p class="text-xs text-green-700">Rencana P3D ini telah dievaluasi dan disetujui untuk dilaksanakan pada {{ $rencana->rencana_pelaksanaan ? $rencana->rencana_pelaksanaan->format('F Y') : '-' }}</p>
                            
                            <form action="{{ route('admin.rencana-p3d.update-status', $rencana->id) }}" method="POST" class="mt-4 w-full">
                                @csrf
                                <input type="hidden" name="status" value="dikirim">
                                <button type="submit" class="w-full inline-flex justify-center items-center px-3 py-1.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium rounded-btn transition-colors text-xs shadow-sm">
                                    Batalkan Persetujuan
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                            <span class="material-symbols-outlined text-[40px] text-gray-400 mb-2">edit_document</span>
                            <h4 class="font-bold text-gray-700 mb-1">Masih Draft</h4>
                            <p class="text-xs text-gray-500">Desa belum mengirimkan rencana ini untuk dievaluasi. Masih dalam status draft oleh pihak desa.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


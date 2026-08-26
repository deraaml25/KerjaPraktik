<x-app-layout>
    @section('title', 'Dashboard')

    <!-- Welcome Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 font-display">Halo, Admin Dinpermasdes</h1>
            <p class="text-slate-500 mt-0.5 text-sm">Selamat datang kembali di pusat kendali administrasi desa Anda.</p>
        </div>
    </div>

    <!-- 8 Stat Cards Grid -->
    <div class="grid gap-4 mb-6" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
        <!-- Card 1: Regulasi -->
        <a href="{{ route('admin.regulasi.index') }}" class="bg-white rounded-xl border border-slate-200 hover:border-slate-900 p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)] transition-all hover:shadow-md flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 bg-slate-100 group-hover:bg-[#111827] group-hover:text-white rounded-lg flex flex-shrink-0 items-center justify-center text-slate-500 transition-colors">
                <span class="material-symbols-outlined text-[22px]">gavel</span>
            </div>
            <div class="flex-grow">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Draft Regulasi</p>
                <h3 class="text-2xl font-bold text-slate-900 font-display leading-none">{{ number_format($counts['regulasi'] ?? 0) }}</h3>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 duration-300">
                <span class="material-symbols-outlined text-slate-300">chevron_right</span>
            </div>
        </a>

        <!-- Card 2: Pembinaan -->
        <a href="{{ route('admin.pengajuan-pembinaan.index') }}" class="bg-white rounded-xl border border-slate-200 hover:border-slate-900 p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)] transition-all hover:shadow-md flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 bg-slate-100 group-hover:bg-[#111827] group-hover:text-white rounded-lg flex flex-shrink-0 items-center justify-center text-slate-500 transition-colors">
                <span class="material-symbols-outlined text-[22px]">school</span>
            </div>
            <div class="flex-grow">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Pembinaan</p>
                <h3 class="text-2xl font-bold text-slate-900 font-display leading-none">{{ number_format($counts['pembinaan'] ?? 0) }}</h3>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 duration-300">
                <span class="material-symbols-outlined text-slate-300">chevron_right</span>
            </div>
        </a>

        <!-- Card 3: e-Rekomendasi -->
        <a href="{{ route('admin.ajuan.index') }}" class="bg-white rounded-xl border border-slate-200 hover:border-slate-900 p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)] transition-all hover:shadow-md flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 bg-slate-100 group-hover:bg-[#111827] group-hover:text-white rounded-lg flex flex-shrink-0 items-center justify-center text-slate-500 transition-colors">
                <span class="material-symbols-outlined text-[22px]">approval</span>
            </div>
            <div class="flex-grow">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">e-Rekomendasi</p>
                <h3 class="text-2xl font-bold text-slate-900 font-display leading-none">{{ number_format($counts['ajuan'] ?? 0) }}</h3>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 duration-300">
                <span class="material-symbols-outlined text-slate-300">chevron_right</span>
            </div>
        </a>

        <!-- Card 4: SK Kades -->
        <a href="{{ route('admin.pjkades.index') }}" class="bg-white rounded-xl border border-slate-200 hover:border-slate-900 p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)] transition-all hover:shadow-md flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 bg-slate-100 group-hover:bg-[#111827] group-hover:text-white rounded-lg flex flex-shrink-0 items-center justify-center text-slate-500 transition-colors">
                <span class="material-symbols-outlined text-[22px]">person</span>
            </div>
            <div class="flex-grow">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">SK Kades</p>
                <h3 class="text-2xl font-bold text-slate-900 font-display leading-none">{{ number_format($counts['pjkades'] ?? 0) }}</h3>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 duration-300">
                <span class="material-symbols-outlined text-slate-300">chevron_right</span>
            </div>
        </a>

        <!-- Card 5: Rencana P3D -->
        <a href="{{ route('admin.rencana-p3d.index') }}" class="bg-white rounded-xl border border-slate-200 hover:border-slate-900 p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)] transition-all hover:shadow-md flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 bg-slate-100 group-hover:bg-[#111827] group-hover:text-white rounded-lg flex flex-shrink-0 items-center justify-center text-slate-500 transition-colors">
                <span class="material-symbols-outlined text-[22px]">event_note</span>
            </div>
            <div class="flex-grow">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Rencana P3D</p>
                <h3 class="text-2xl font-bold text-slate-900 font-display leading-none">{{ number_format($counts['rencana_p3d'] ?? 0) }}</h3>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 duration-300">
                <span class="material-symbols-outlined text-slate-300">chevron_right</span>
            </div>
        </a>

        <!-- Card 6: Perangkat Desa -->
        <a href="{{ route('admin.perangkat.index') }}" class="bg-white rounded-xl border border-slate-200 hover:border-slate-900 p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)] transition-all hover:shadow-md flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 bg-slate-100 group-hover:bg-[#111827] group-hover:text-white rounded-lg flex flex-shrink-0 items-center justify-center text-slate-500 transition-colors">
                <span class="material-symbols-outlined text-[22px]">group</span>
            </div>
            <div class="flex-grow">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Perangkat Desa</p>
                <h3 class="text-2xl font-bold text-slate-900 font-display leading-none">{{ number_format($counts['perangkat_desa'] ?? 0) }}</h3>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 duration-300">
                <span class="material-symbols-outlined text-slate-300">chevron_right</span>
            </div>
        </a>

        <!-- Card 7: Data BPD -->
        <a href="{{ route('admin.bpd.index') }}" class="bg-white rounded-xl border border-slate-200 hover:border-slate-900 p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)] transition-all hover:shadow-md flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 bg-slate-100 group-hover:bg-[#111827] group-hover:text-white rounded-lg flex flex-shrink-0 items-center justify-center text-slate-500 transition-colors">
                <span class="material-symbols-outlined text-[22px]">account_balance</span>
            </div>
            <div class="flex-grow">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Data BPD</p>
                <h3 class="text-2xl font-bold text-slate-900 font-display leading-none">{{ number_format($counts['bpd'] ?? 0) }}</h3>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 duration-300">
                <span class="material-symbols-outlined text-slate-300">chevron_right</span>
            </div>
        </a>

        <!-- Card 8: Ajuan BPD -->
        <a href="{{ route('admin.ajuan-bpd.index') }}" class="bg-white rounded-xl border border-slate-200 hover:border-slate-900 p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)] transition-all hover:shadow-md flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 bg-slate-100 group-hover:bg-[#111827] group-hover:text-white rounded-lg flex flex-shrink-0 items-center justify-center text-slate-500 transition-colors">
                <span class="material-symbols-outlined text-[22px]">how_to_reg</span>
            </div>
            <div class="flex-grow">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Ajuan BPD</p>
                <h3 class="text-2xl font-bold text-slate-900 font-display leading-none">{{ number_format($counts['ajuan_bpd'] ?? 0) }}</h3>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 duration-300">
                <span class="material-symbols-outlined text-slate-300">chevron_right</span>
            </div>
        </a>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Aktivitas Terkini -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden md:col-span-2">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-900">Aktivitas Terkini</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 bg-slate-50 text-center">Dokumen</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 bg-slate-50 text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 bg-slate-50 text-center">Asal Desa</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 bg-slate-50 text-center">Tanggal Update</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($aktivitas as $act)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center">
                                    <span class="text-sm font-semibold text-slate-700">{{ $act->title }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="whitespace-nowrap bg-slate-100 text-slate-800 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                                    {{ str_replace('_', ' ', $act->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 text-center">{{ ucwords(strtolower($act->admin)) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 text-center">{{ \Carbon\Carbon::parse($act->date)->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500 text-sm">Belum ada aktivitas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column Widgets -->
        <div class="flex flex-col gap-6 md:col-span-1">
            <!-- Widget 1: Informasi Wilayah -->
            <div class="rounded-2xl overflow-hidden relative shadow-sm" style="background-image: url('https://images.unsplash.com/photo-1542224566-6e85f2e6772f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'); background-size: cover; background-position: center;">
                <div class="absolute inset-0 bg-black/40"></div>
                <div class="relative p-6 h-full flex flex-col justify-end min-h-[180px]">
                    <h3 class="text-xl font-bold text-white mb-2">Informasi Wilayah</h3>
                    <p class="text-white/80 text-sm mb-4">Data spasial dan demografi desa terbaru.</p>
                    <a href="https://maps.app.goo.gl/jFDWxg1pKXNHyZz78" target="_blank" class="bg-white text-slate-900 text-sm font-bold py-2.5 rounded-lg w-full hover:bg-slate-50 transition-colors text-center block">
                        Buka Peta Desa
                    </a>
                </div>
            </div>

            <!-- Widget 2: Berita & Informasi -->
            <div class="rounded-2xl p-5 text-white shadow-md flex-1 flex flex-col relative overflow-hidden {{ (isset($berita) && $berita->count() > 0) ? 'justify-start' : 'justify-center' }}" style="background-color: #0A1A3A;">
                
                <h3 class="text-lg font-bold mb-4 text-white flex items-center justify-between leading-tight relative z-10">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">campaign</span>
                        Informasi Pembinaan
                    </span>
                    <a href="{{ route('admin.bimtek-informasi.index') }}" class="text-[10px] font-medium bg-white/10 hover:bg-white/20 px-2.5 py-1 rounded-md transition-colors border border-white/10">Lihat Semua</a>
                </h3>
                @if(isset($berita) && $berita->count() > 0)
                    <div class="space-y-3 overflow-y-auto max-h-[110px] scrollbar-none pr-1 relative z-10">
                        @foreach($berita as $item)
                            <a href="{{ route('admin.bimtek-informasi.show', $item->id) }}" class="block bg-white/5 hover:bg-white/10 transition-colors rounded-xl p-3" style="border: 1px solid rgba(255, 255, 255, 0.25);">
                                <h4 class="font-bold text-[13px] text-white/90 leading-tight mb-2 line-clamp-2">{{ $item->title }}</h4>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded text-white/80 uppercase tracking-wider" style="background-color: rgba(255,255,255,0.1);">{{ $item->kategori }}</span>
                                    <span class="text-[10px] text-white/50 font-medium flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">schedule</span>
                                        {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center text-center opacity-50 relative z-10">
                        <span class="material-symbols-outlined text-3xl mb-1">inbox</span>
                        <p class="text-xs">Belum ada informasi pembinaan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bottom Circular Charts -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Chart 1 -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 text-center shadow-sm">
            <div class="w-32 h-32 mx-auto relative flex items-center justify-center">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                    <path class="text-slate-100" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    <path class="text-[#111827]" stroke-width="4" stroke-dasharray="{{ $charts['efisiensi'] ?? 0 }}, 100" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-2xl font-bold font-display text-slate-900">{{ $charts['efisiensi'] ?? 0 }}%</span>
                </div>
            </div>
            <h4 class="mt-6 font-bold text-slate-900">Efisiensi Layanan</h4>
            <p class="text-xs text-slate-500 mt-1">Rasio ajuan desa yang disubmit</p>
        </div>
        
        <!-- Chart 2 -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 text-center shadow-sm">
            <div class="w-32 h-32 mx-auto relative flex items-center justify-center">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                    <path class="text-slate-100" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    <path class="text-[#6fa7e9]" stroke-width="4" stroke-dasharray="100, 100" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-2xl font-bold font-display text-slate-900">{{ $charts['pemohon'] ?? 0 }}</span>
                </div>
            </div>
            <h4 class="mt-6 font-bold text-slate-900">Pemohon Aktif</h4>
            <p class="text-xs text-slate-500 mt-1">Total desa pemohon bulan ini</p>
        </div>

        <!-- Chart 3 -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 text-center shadow-sm">
            <div class="w-32 h-32 mx-auto relative flex items-center justify-center">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                    <path class="text-slate-100" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    <path class="text-slate-400" stroke-width="4" stroke-dasharray="{{ $charts['akurasi'] ?? 0 }}, 100" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-2xl font-bold font-display text-slate-900">{{ $charts['akurasi'] ?? 0 }}%</span>
                </div>
            </div>
            <h4 class="mt-6 font-bold text-slate-900">Akurasi Data</h4>
            <p class="text-xs text-slate-500 mt-1">Verifikasi perangkat & BPD sukses</p>
        </div>
    </div>
</x-app-layout>
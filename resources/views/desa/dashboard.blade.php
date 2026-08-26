<x-app-layout>
    @section('title', 'Dashboard Desa')

    <!-- Welcome Section -->
    <div
        class="bg-primary text-white rounded-card p-8 mb-8 relative overflow-hidden shadow-floating border border-primary-light/30">
        <!-- Background decoration -->
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-5 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 right-20 w-32 h-32 bg-primary-light opacity-20 rounded-full blur-xl"></div>

        <div class="relative z-10">
            <h2 class="text-2xl font-display font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}!</h2>
            <p class="text-primary-soft mb-6 max-w-2xl">Kelola data desa dan pantau status ajuan rekomendasi penerbitan SK secara real-time.</p>

            <a href="{{ route('desa.ajuan.create') }}"
                class="inline-flex items-center px-6 py-3 bg-white text-primary font-medium rounded-btn hover:bg-gray-50 transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Ajuan Baru
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('desa.ajuan.index') }}" class="bg-surface rounded-xl border border-border hover:border-ink p-6 shadow-sm transition-all hover:shadow-md flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 bg-gray-100 group-hover:bg-ink group-hover:text-white rounded-lg flex flex-shrink-0 items-center justify-center text-muted transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div class="flex-grow">
                <p class="text-[11px] font-bold text-muted uppercase tracking-wider mb-0.5">Total Ajuan</p>
                <h3 class="text-3xl font-bold text-ink font-display leading-none">{{ $totalAjuan }}</h3>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 duration-300">
                <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        <a href="{{ route('desa.ajuan.index', ['status' => 'proses']) }}" class="bg-surface rounded-xl border border-border hover:border-ink p-6 shadow-sm transition-all hover:shadow-md flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 bg-primary-soft group-hover:bg-ink group-hover:text-white rounded-lg flex flex-shrink-0 items-center justify-center text-primary transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-grow">
                <p class="text-[11px] font-bold text-muted uppercase tracking-wider mb-0.5">Sedang Diproses</p>
                <h3 class="text-3xl font-bold text-ink font-display leading-none">{{ $sedangDiproses }}</h3>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 duration-300">
                <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        <a href="{{ route('desa.ajuan.index', ['status' => 'revisi']) }}" class="bg-surface rounded-xl border border-border hover:border-ink p-6 shadow-sm transition-all hover:shadow-md flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 bg-red-100 group-hover:bg-ink group-hover:text-white rounded-lg flex flex-shrink-0 items-center justify-center text-danger transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="flex-grow">
                <p class="text-[11px] font-bold text-muted uppercase tracking-wider mb-0.5">Perlu Tindakan</p>
                <h3 class="text-3xl font-bold text-danger font-display leading-none">{{ $perluTindakan }}</h3>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 duration-300">
                <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Ajuan Aktif Anda -->
        <div class="bg-surface rounded-card shadow-sm border border-border overflow-hidden md:col-span-2 flex flex-col">
            <div class="px-6 py-5 border-b border-border bg-white flex justify-between items-center">
                <h3 class="text-lg font-display font-semibold text-ink">Ajuan Aktif Anda</h3>
            </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">No.
                            Registrasi</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">Jenis
                            Layanan</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">
                            Perangkat Desa</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-ink uppercase tracking-wider">Tahap /
                            Status</th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-bold text-ink uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    @forelse($ajuans as $ajuan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-ink font-medium">
                                {{ $ajuan->no_registrasi }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeColor = match(optional($ajuan->jenisLayanan)->nama) {
                                        'Pengangkatan' => 'bg-primary-soft text-primary',
                                        'Pemberhentian' => 'bg-red-100 text-danger',
                                        'Rotasi' => 'bg-indigo-100 text-indigo-600',
                                        default => 'bg-gray-100 text-muted'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }}">
                                    {{ optional($ajuan->jenisLayanan)->nama ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-ink font-medium">
                                    {{ $ajuan->pesertas->first() ? $ajuan->pesertas->first()->perangkatDesa->nama : '-' }}
                                    @if($ajuan->pesertas->count() > 1) <span class="text-primary font-bold">(+{{ $ajuan->pesertas->count() - 1 }})</span> @endif
                                </div>
                                <div class="text-xs text-muted">{{ $ajuan->pesertas->first() ? $ajuan->pesertas->first()->perangkatDesa->jabatan : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ajuan->status === 'draft')
                                    <div class="text-sm text-muted font-medium">Bisa Diajukan</div>
                                    <div class="text-xs text-muted">Berkas Disiapkan</div>
                                @elseif($ajuan->status === 'revisi')
                                    <div class="text-sm text-danger font-medium">Perlu Revisi</div>
                                    <div class="text-xs text-danger">Mohon lengkapi berkas kurang</div>
                                @elseif($ajuan->status === 'selesai')
                                    <div class="text-sm text-success font-medium">Selesai</div>
                                    <div class="text-xs text-success">SK Bupati Terbit</div>
                                @else
                                    <div class="text-sm text-ink font-medium">Tahap {{ $ajuan->milestoneTrackings->where('tgl_selesai', null)->min('tahap') ?: 1 }} / 4</div>
                                    <div class="text-xs text-warning">Sedang diproses</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($ajuan->status === 'revisi')
                                    <a href="{{ route('desa.ajuan.show', $ajuan) }}"
                                        class="text-white bg-danger hover:bg-red-600 px-3 py-1.5 rounded-md transition-colors shadow-sm inline-block">Upload Ulang</a>
                                @else
                                    <a href="{{ route('desa.ajuan.show', $ajuan) }}"
                                        class="text-primary hover:text-primary-light bg-primary-soft px-3 py-1.5 rounded-md transition-colors inline-block">Detail</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-0">
                                <x-empty-state 
                                    icon="<path stroke-linecap='round' stroke-linejoin='round' d='M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2' />"
                                    title="Belum Ada Ajuan Aktif"
                                    message="Anda belum memiliki pengajuan rekomendasi yang sedang berproses."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
        
        <!-- Right Column Widgets -->
        <div class="flex flex-col gap-6 md:col-span-1">
            <!-- Widget 1: Informasi Wilayah (GIS) -->
            <div class="bg-surface rounded-2xl border border-border shadow-sm overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-border bg-gray-50 flex justify-between items-center">
                    <h3 class="font-display font-semibold text-ink text-sm">Peta Wilayah</h3>
                    <a href="https://www.google.com/maps/search/?api=1&query=Desa+{{ urlencode(auth()->user()->desa->nama_desa ?? '') }},+Kecamatan+{{ urlencode(auth()->user()->desa->kecamatan->nama_kecamatan ?? '') }}" target="_blank" class="text-xs text-primary font-medium hover:underline">Buka Penuh</a>
                </div>
                <div class="w-full h-48 bg-gray-100 relative">
                    <iframe 
                        width="100%" 
                        height="100%" 
                        frameborder="0" 
                        scrolling="no" 
                        marginheight="0" 
                        marginwidth="0" 
                        src="https://maps.google.com/maps?q=Desa+{{ urlencode(auth()->user()->desa->nama_desa ?? '') }},+Kecamatan+{{ urlencode(auth()->user()->desa->kecamatan->nama_kecamatan ?? '') }}&t=k&z=15&ie=UTF8&iwloc=&output=embed"
                        class="absolute inset-0"
                    ></iframe>
                </div>
                <div class="p-4 bg-white">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-danger mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-ink">Desa {{ auth()->user()->desa->nama_desa ?? '-' }}</p>
                            <p class="text-xs text-muted">Kec. {{ auth()->user()->desa->kecamatan->nama_kecamatan ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Widget 2: Pusat Informasi -->
            <div class="bg-primary rounded-2xl p-6 text-white shadow-sm flex-1 flex flex-col justify-center relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-white opacity-5 rounded-full blur-2xl -mr-10 -mt-10"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5L18.5 8M6 8h4m-4 4h4m-4 4h4m4-12v1m0 4v1m0 4v1" />
                        </svg>
                        <h3 class="text-lg font-bold text-white">Pusat Informasi</h3>
                    </div>
                    <p class="text-white/90 text-sm leading-relaxed mb-4">
                        Cek jadwal pembinaan, bimbingan teknis (Bimtek), dan pengumuman terbaru dari Kecamatan.
                    </p>
                    <a href="{{ route('desa.bimtek-informasi.index') }}" class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-semibold transition-colors backdrop-blur-sm">
                        Lihat Pengumuman
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
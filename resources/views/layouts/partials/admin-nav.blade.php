@php
    $navs = [
        ['route' => 'admin.dashboard', 'icon' => 'grid_view', 'text' => 'Dashboard', 'active' => request()->routeIs('admin.dashboard')],
        ['route' => 'admin.regulasi.index', 'icon' => 'description', 'text' => 'Draft Regulasi', 'active' => request()->routeIs('admin.regulasi.*')],
        ['route' => 'admin.pengajuan-pembinaan.index', 'icon' => 'history_edu', 'text' => 'Pembinaan', 'active' => request()->routeIs('admin.pengajuan-pembinaan.*') || request()->routeIs('admin.bimtek-informasi.*') || request()->routeIs('admin.bimtek.*')],
        ['route' => 'admin.ajuan.index', 'icon' => 'approval', 'text' => 'e-Rekomendasi', 'active' => request()->routeIs('admin.ajuan.*')],
        ['route' => 'admin.pjkades.index', 'icon' => 'admin_panel_settings', 'text' => 'SK Kades', 'active' => request()->routeIs('admin.pjkades.*')],
        ['route' => 'admin.rencana-p3d.index', 'icon' => 'assignment', 'text' => 'Rencana P3D', 'active' => request()->routeIs('admin.rencana-p3d.*')],
        ['route' => 'admin.drive.index', 'icon' => 'cloud_circle', 'text' => 'Arsip Dokumen', 'active' => request()->routeIs('admin.drive.*')],

        ['route' => 'admin.perangkat.index', 'icon' => 'badge', 'text' => 'Data Kepala dan Perangkat Desa', 'active' => request()->routeIs('admin.perangkat.*')],
        ['route' => 'admin.bpd.index', 'icon' => 'groups', 'text' => 'Data BPD', 'active' => request()->routeIs('admin.bpd.*')],
        ['route' => 'admin.ajuan-bpd.index', 'icon' => 'post_add', 'text' => 'Ajuan BPD', 'active' => request()->routeIs('admin.ajuan-bpd.*')],
        ['route' => 'admin.akun_desa.index', 'icon' => 'manage_accounts', 'text' => 'Manajemen Akun', 'active' => request()->routeIs('admin.akun_desa.*')],
    ];
@endphp

@foreach($navs as $nav)
    @if($nav['active'])
        <a href="{{ route($nav['route']) }}" class="group flex items-center h-[44px] w-full relative">
            <!-- WHITE BACKGROUND CURVE ACROSS BOTH COLUMNS -->
            <div class="active-nav-curve absolute inset-y-0 right-0 z-0" style="left: 10px;"></div>

            <div class="w-[60px] flex-shrink-0 flex items-center justify-center text-[#0A1A3A] relative z-10">
                <span class="material-symbols-outlined text-[20px]">{{ $nav['icon'] }}</span>
            </div>
            <div class="flex-grow h-full flex items-center relative z-10 pr-3">
                <span class="font-bold text-[13px] text-[#0A1A3A] pl-4">{{ $nav['text'] }}</span>
            </div>
        </a>
    @else
        <a href="{{ route($nav['route']) }}" class="group flex items-center h-[44px] w-full relative hover:bg-white/5 transition-colors rounded-xl">
            <div class="w-[60px] flex-shrink-0 flex items-center justify-center text-white transition-colors relative z-10">
                <span class="material-symbols-outlined text-[20px]">{{ $nav['icon'] }}</span>
            </div>
            <div class="flex-grow h-full flex items-center pl-4 transition-all duration-300 group-hover:pl-6">
                <span class="font-semibold text-[13px] text-white/80 group-hover:text-white transition-colors">
                    {{ $nav['text'] }}
                </span>
            </div>
        </a>
    @endif
@endforeach
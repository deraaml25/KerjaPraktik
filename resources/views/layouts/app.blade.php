<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SiPerangkat') }} - @yield('title', 'Dashboard')</title>

        <!-- Fonts & Icons -->
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=block" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .scrollbar-none::-webkit-scrollbar { display: none; }
            .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
            body { font-family: 'Inter', sans-serif; }
            h1, h2, h3, h4, h5, h6, .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }
            
            /* Active Nav Curve CSS */
            .active-nav-curve {
                background-color: #f8fafc !important;
                border-radius: 40px 0 0 40px !important;
            }
            .active-nav-curve::before,
            .active-nav-curve::after {
                content: '';
                position: absolute;
                right: 0;
                width: 20px;
                height: 20px;
                background-color: transparent;
                pointer-events: none;
            }
            .active-nav-curve::before {
                top: -20px;
                border-radius: 0 0 20px 0;
                box-shadow: 10px 10px 0 10px #f8fafc;
            }
            .active-nav-curve::after {
                bottom: -20px;
                border-radius: 0 20px 0 0;
                box-shadow: 10px -10px 0 10px #f8fafc;
            }
        </style>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body class="font-sans antialiased bg-[#f8fafc] text-slate-900 flex h-screen overflow-hidden text-sm">
        
        <!-- SIDEBAR -->
        <aside class="flex-shrink-0 flex flex-col z-30 relative w-[260px] bg-[#0A1A3A] h-screen rounded-tr-[32px] rounded-br-[32px] shadow-xl overflow-hidden transition-all duration-300">
            <!-- Background Kiri (Icon Rail) -->
            <div class="absolute left-0 top-0 w-[60px] h-full bg-[#738FB9] z-0 shadow-md"></div>

            <div class="relative z-10 flex flex-col h-full">
                <!-- Logo Header -->
                <div class="flex items-center h-[72px] border-b border-white/10 bg-black/10 relative">
                    <div class="w-[60px] flex-shrink-0 flex justify-center items-center">
                        <img src="{{ asset('logo.png') }}" onerror="this.src='https://lh3.googleusercontent.com/aida-public/AB6AXuDKBYY88kZ13swAXZCwTS6ub06DYmk7LgIvWrsJg4M5Mf764XFIciikJ_cuC39VrLn_VfTYs_HVED5VotHHKbdrPkVC9ZxMCk27gHWU2YHiYe1RguIfp1OfWuNAnoKAFkHh9p2cYiVxg-LNb09DpG2Pndv5ZtWWTy7W5rcPBE5qYyBjBfMS8eLV8wYS0VZ9sXduv8_vi7bSuXA4QGHHnSYxzVjt3Th6UicoO9-auMC89VtIMDJ4YkYG-qvLrLDMaAo85-DohAodwHVQ'" alt="Logo" class="w-8 h-8 object-contain">
                    </div>
                    <div class="flex-grow px-4">
                        <h1 class="text-white font-bold text-lg leading-none font-display">SiPerangkat</h1>
                        <p class="text-slate-400 text-[9px] tracking-[0.1em] mt-1 uppercase font-semibold">Kabupaten Banyumas</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 overflow-y-auto scrollbar-none pt-2 flex flex-col w-full">
                    @if(auth()->user()->role === 'super_admin')
                        @include('layouts.partials.admin-nav')
                    @else
                        @include('layouts.partials.desa-nav')
                    @endif
                </nav>

                <!-- User Profile Footer -->
                <div class="mt-auto flex w-full py-4 border-t border-white/10 bg-black/10 relative">
                    <div class="w-[60px] flex-shrink-0 flex justify-center">
                        <div class="relative h-8 w-8">
                            <div class="w-8 h-8 rounded-full border-2 border-white/30 overflow-hidden shadow-lg bg-slate-200 flex items-center justify-center text-slate-800 font-bold text-[10px]">
                                {{ substr(auth()->user()->name, 0, 2) }}
                            </div>
                            <div class="absolute -right-0.5 -bottom-0.5 w-3 h-3 bg-green-500 border-2 border-[#738FB9] rounded-full"></div>
                        </div>
                    </div>
                    <div class="flex-grow px-4 flex flex-col justify-center overflow-hidden">
                        <h3 class="text-white font-bold text-xs truncate">{{ auth()->user()->name }}</h3>
                        <p class="text-slate-400 text-[9px] uppercase font-semibold tracking-wide truncate mt-0.5">
                            {{ auth()->user()->role == 'super_admin' ? 'Administrator' : 'Operator Desa' }}
                        </p>

                        <div class="flex flex-col gap-2 mt-2.5">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors text-[11px] font-semibold w-full">
                                <span class="material-symbols-outlined text-[14px]">manage_accounts</span>
                                Profil Saya
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="w-full" data-turbo="false">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors text-[11px] font-semibold w-full">
                                    <span class="material-symbols-outlined text-[14px]">logout</span>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col relative z-10 overflow-hidden bg-[#f8fafc]">
            <!-- Top App Bar -->
            <div class="sticky top-0 z-40 px-6 pt-5 pb-2 bg-[#f8fafc]">
                <div class="flex items-center gap-3">
                    @hasSection('back-button')
                        @yield('back-button')
                    @endif
                    <header class="flex-1 flex items-center justify-between px-5 py-3 bg-white rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-slate-200/60 transition-all duration-200">
                        <div class="flex items-center gap-3">
                            <div class="w-1 h-6 bg-[#738FB9] rounded-full"></div>
                            <div class="flex flex-col">
                                @hasSection('page-kicker')
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                    @yield('page-kicker')
                                </span>
                                @endif
                                <h2 class="text-lg text-[#111827] font-bold font-display tracking-tight leading-none">
                                    @yield('title', 'Dashboard')
                                </h2>
                                @hasSection('page-subtitle')
                                <p class="text-[13px] text-slate-500 mt-1 font-medium">@yield('page-subtitle')</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            @yield('page-actions')
                            <div class="w-px h-6 bg-slate-200 hidden md:block"></div>
                            <button class="w-8 h-8 rounded-full bg-slate-50 shadow-sm border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors relative">
                                <span class="material-symbols-outlined text-[18px]">notifications</span>
                                <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                            </button>
                        </div>
                    </header>
                </div>
            </div>

            <main class="flex-1 overflow-y-auto px-6 pb-8 scrollbar-none mt-2">
                <div class="mx-auto max-w-[1100px]">
                    {{ $slot }}
                </div>
            </main>
        </div>
        @stack('scripts')
    </body>
</html>

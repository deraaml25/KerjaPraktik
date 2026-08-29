<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Masuk — SiPerangkat</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-background text-ink">
        <div class="min-h-screen flex">
            <!-- Left Panel: Illustration -->
            <div class="hidden lg:flex lg:w-1/2 bg-primary relative overflow-hidden flex-col justify-between p-12">
                <!-- Background shapes -->
                <div class="absolute top-0 left-0 w-72 h-72 bg-primary-light opacity-30 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-primary-light opacity-20 rounded-full translate-x-1/3 translate-y-1/3"></div>
                <div class="absolute top-1/2 left-1/2 w-48 h-48 bg-white opacity-5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>

                <!-- Logo -->
                <div class="relative z-10">
                    <h1 class="text-3xl font-display font-bold text-white tracking-tight">
                        SID<span class="text-primary-soft">mini</span>
                    </h1>
                    <p class="text-primary-soft text-sm mt-1">Sistem Informasi Pelayanan Rekomendasi</p>
                </div>

                <!-- Center Illustration Area -->
                <div class="relative z-10 flex-1 flex flex-col items-center justify-center text-center px-8">
                    <!-- Icon illustration -->
                    <div class="w-40 h-40 bg-white/10 rounded-3xl flex items-center justify-center mb-8 backdrop-blur-sm border border-white/20">
                        <svg class="w-20 h-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>

                    <!-- Feature cards -->
                    <div class="space-y-3 w-full max-w-xs">
                        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-3 flex items-center gap-3">
                            <div class="w-8 h-8 bg-success/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <p class="text-white text-sm font-medium">Checklist Dokumen Dinamis</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-3 flex items-center gap-3">
                            <div class="w-8 h-8 bg-warning/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-white text-sm font-medium">Tracking Milestone 9 Tahap & SLA</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-3 flex items-center gap-3">
                            <div class="w-8 h-8 bg-primary-light/50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            </div>
                            <p class="text-white text-sm font-medium">Arsip Dokumen Terpusat</p>
                        </div>
                    </div>
                </div>

                <!-- Bottom text -->
                <div class="relative z-10">
                    <p class="text-primary-soft text-xs">Dinas Pemberdayaan Masyarakat dan Desa</p>
                    <p class="text-white/50 text-xs mt-0.5">Bidang Penetapan Desa & Pemerintahan Desa</p>
                </div>
            </div>

            <!-- Right Panel: Login Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 relative overflow-hidden">
                <!-- Decorative blurred blobs for right panel background -->
                <div class="absolute top-20 right-20 w-64 h-64 bg-primary/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
                <div class="absolute bottom-20 left-20 w-72 h-72 bg-white/60 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>

                <div class="w-full max-w-md bg-white/40 backdrop-blur-2xl p-10 rounded-3xl shadow-[0_8px_40px_rgb(0,0,0,0.06)] border border-white/60 relative z-10 transition-all hover:shadow-[0_8px_50px_rgb(0,0,0,0.08)]">
                    <!-- Mobile logo -->
                    <div class="lg:hidden mb-8 text-center">
                        <h1 class="text-3xl font-display font-bold text-primary drop-shadow-sm">SID<span class="text-ink">mini</span></h1>
                    </div>

                    <div class="mb-8 text-center">
                        <h2 class="text-3xl font-display font-bold text-ink drop-shadow-sm">Selamat Datang</h2>
                        <p class="text-muted mt-3 text-sm leading-relaxed">Masuk ke sistem untuk mengelola pelayanan rekomendasi perangkat desa.</p>
                    </div>

                    {{ $slot }}

                    <p class="mt-8 text-center text-xs text-muted/70 font-medium tracking-wide">
                        &copy; {{ date('Y') }} Dinpermasdes — SiPerangkat v1.0
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>


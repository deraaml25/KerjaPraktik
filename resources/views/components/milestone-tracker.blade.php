@props(['tahapAktif' => 1, 'milestones' => [], 'ajuan' => null])

@php
    $tahapList = [
        1 => 'Berkas Diterima',
        2 => 'Verifikasi & Validasi Petugas',
        3 => 'Penyusunan Draft Rekomendasi',
        4 => 'Verifikasi & Validasi Kabid PDPD',
        5 => 'Verifikasi & Validasi Sekretaris Dinas',
        6 => 'Verifikasi & Validasi Kepala Dinas',
        7 => 'Verifikasi & Validasi Asisten Pemerintahan & Kesra',
        8 => 'Verifikasi & Validasi Sekda',
        9 => 'Tanda Tangan Bupati',
        10 => 'Penomoran TU Umum Setda',
        11 => 'Sudah di Dinpermasdes',
        12 => 'Sudah di Desa (Nama Penerima)'
    ];

    if ($ajuan && $ajuan->status === 'selesai') {
        $tahapAktif = 999;
    }
@endphp

<div class="relative py-4">
    <!-- Vertical Line -->
    <div class="absolute left-[23px] top-6 bottom-6 w-0.5 bg-border z-0"></div>

    <div class="space-y-6 relative z-10">
        @foreach($tahapList as $index => $namaTahap)
            @php
                $status = 'belum'; // belum, aktif, selesai
                if ($index < $tahapAktif)
                    $status = 'selesai';
                if ($index == $tahapAktif)
                    $status = 'aktif';

                // Get milestone data if exists
                $milestoneData = collect($milestones)->firstWhere('tahap', $index);

                $dateToDisplay = null;
                if ($milestoneData) {
                    $dateToDisplay = $milestoneData['tgl_selesai'] ?? $milestoneData['tgl_mulai'] ?? $milestoneData['created_at'];
                } elseif ($ajuan && ($status === 'selesai' || $status === 'aktif')) {
                    if ($index === 1) {
                        $dateToDisplay = $ajuan->tgl_diajukan ?? $ajuan->created_at;
                    } else {
                        // For skipped completed steps, find the next available milestone's date
                        $nextMilestone = collect($milestones)->where('tahap', '>', $index)->sortBy('tahap')->first();
                        $dateToDisplay = $nextMilestone ? ($nextMilestone->tgl_mulai ?? $nextMilestone->created_at) : $ajuan->updated_at;
                    }
                }
            @endphp

            <div class="flex items-start">
                <div class="flex-shrink-0 mt-0.5">
                    @if($status === 'selesai')
                        <div
                            class="w-12 h-12 rounded-full bg-primary flex items-center justify-center border-4 border-white shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    @elseif($status === 'aktif')
                        <div
                            class="w-12 h-12 rounded-full bg-white border-4 border-primary shadow-sm flex items-center justify-center relative">
                            <div class="w-3 h-3 bg-primary rounded-full animate-pulse"></div>
                            <!-- Ping animation -->
                            <div class="absolute inset-0 rounded-full border-2 border-primary animate-ping opacity-20"></div>
                        </div>
                    @else
                        <div class="w-12 h-12 rounded-full bg-white border-4 border-border flex items-center justify-center">
                            <span class="text-muted font-medium">{{ $index }}</span>
                        </div>
                    @endif
                </div>

                <div class="ml-4 mt-1.5 flex-1 pb-2">
                    <h4 class="text-sm font-semibold {{ $status === 'belum' ? 'text-muted' : 'text-ink' }}">
                        {{ $namaTahap }}
                    </h4>

                    @if($status === 'aktif')
                        <span
                            class="inline-block mt-1 text-xs font-medium text-primary bg-primary-soft px-2 py-0.5 rounded-full">Sedang
                            Berjalan</span>
                    @endif

                    @if($dateToDisplay)
                        <div class="mt-1 text-xs text-muted flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ \Carbon\Carbon::parse($dateToDisplay)->format('d/m/y') }}
                        </div>
                    @endif


                </div>
            </div>
        @endforeach
    </div>
</div>
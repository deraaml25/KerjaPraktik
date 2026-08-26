<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ajuan;
use App\Models\AjuanBpd;
use App\Models\BimtekInformasi;
use App\Models\Bpd;
use App\Models\PengajuanPembinaan;
use App\Models\PerangkatDesa;
use App\Models\PjKades;
use App\Models\Regulasi;
use App\Models\RencanaP3d;

class DashboardController extends Controller
{
    public function index()
    {
        $counts = [
            'regulasi' => Regulasi::count(),
            'pembinaan' => PengajuanPembinaan::count(),
            'ajuan' => Ajuan::count(),
            'pjkades' => PjKades::count(),
            'rencana_p3d' => RencanaP3d::count(),
            'perangkat_desa' => PerangkatDesa::where('status_aktif', true)->count(),
            'bpd' => Bpd::where('status_aktif', true)->count(),
            'ajuan_bpd' => AjuanBpd::count(),
        ];

        // Chart 1: Efisiensi Layanan (Ajuan Submitted / Total Ajuan)
        $total_ajuan = Ajuan::count();
        $ajuan_submitted = Ajuan::where('status', 'submitted')->count();
        $efisiensi_layanan = $total_ajuan > 0 ? round(($ajuan_submitted / $total_ajuan) * 100) : 0;

        // Chart 2: Pemohon Aktif (Jumlah desa unik yang membuat ajuan bulan ini)
        $pemohon_aktif = Ajuan::whereMonth('created_at', now()->month)->distinct('desa_id')->count('desa_id');
        // Jika 0, setidaknya tampilkan jumlah desa total
        if ($pemohon_aktif == 0) {
            $pemohon_aktif = \App\Models\Desa::count();
        }

        // Chart 3: Akurasi Data (Data Perangkat & BPD terverifikasi / Total Aktif)
        $total_perangkat_bpd = $counts['perangkat_desa'] + $counts['bpd'];
        $perangkat_disetujui = PerangkatDesa::where('status_aktif', true)->where('status_verifikasi', 'disetujui')->count();
        $bpd_disetujui = Bpd::where('status_aktif', true)->where('status_verifikasi', 'disetujui')->count();
        $total_disetujui = $perangkat_disetujui + $bpd_disetujui;
        $akurasi_data = $total_perangkat_bpd > 0 ? round(($total_disetujui / $total_perangkat_bpd) * 100) : 0;

        $charts = [
            'efisiensi' => $efisiensi_layanan,
            'pemohon' => $pemohon_aktif,
            'akurasi' => $akurasi_data,
        ];

        // Aktivitas Terkini (gabungan dari beberapa model yang sering diupdate)
        $aktivitas = collect();

        $recentPjkades = PjKades::withoutGlobalScopes()->latest('updated_at')->take(3)->get()->map(function ($item) {
            return (object) [
                'icon' => 'person',
                'title' => 'Usulan SK Kades',
                'status' => $item->status,
                'admin' => $item->desa->nama_desa ?? 'Admin',
                'date' => $item->updated_at,
            ];
        });

        $recentAjuan = Ajuan::latest('updated_at')->take(3)->get()->map(function ($item) {
            return (object) [
                'icon' => 'approval',
                'title' => 'e-Rekomendasi',
                'status' => $item->status,
                'admin' => $item->desa->nama_desa ?? 'Admin',
                'date' => $item->updated_at,
            ];
        });

        $recentRegulasi = Regulasi::latest('updated_at')->take(3)->get()->map(function ($item) {
            return (object) [
                'icon' => 'gavel',
                'title' => 'Draft Regulasi',
                'status' => $item->status ?? 'draft',
                'admin' => $item->desa->nama_desa ?? 'Admin',
                'date' => $item->updated_at,
            ];
        });

        $aktivitas = $aktivitas->concat($recentPjkades)->concat($recentAjuan)->concat($recentRegulasi)
            ->sortByDesc('date')
            ->take(5);

        $berita = BimtekInformasi::latest('created_at')->take(4)->get();

        return view('admin.dashboard', compact('berita', 'counts', 'aktivitas', 'charts'));
    }
}

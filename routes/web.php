<?php

use App\Http\Controllers\Admin\AdminAjuanController;
use App\Http\Controllers\Admin\AkunDesaController;
use App\Http\Controllers\Admin\AjuanBpdController;
use App\Http\Controllers\Admin\AjuanController;
use App\Http\Controllers\Admin\ArsipRekomController;
use App\Http\Controllers\Admin\BimtekInformasiController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DriveController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\PengajuanPembinaanController;
use App\Http\Controllers\Admin\PerangkatController;
use App\Http\Controllers\Admin\PjKadesController;
use App\Http\Controllers\Admin\RegulasiController;
use App\Http\Controllers\Admin\RencanaP3dController;
use App\Http\Controllers\Admin\VerifikasiBpdController;
use App\Http\Controllers\Admin\VerifikasiPerangkatController;
use App\Http\Controllers\Desa\ArsipController;
use App\Http\Controllers\Desa\BpdController;
use App\Http\Controllers\Desa\DashboardController as DesaDashboardController;
use App\Http\Controllers\ProfileController;
use App\Models\AlasanPemberhentian;
use App\Models\Desa;
use App\Models\JenisLayanan;
use App\Models\Kecamatan;
use App\Models\PerangkatDesa;
use App\Models\TemplateChecklist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'super_admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('desa.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Ajuan
    Route::get('/ajuan', [AjuanController::class, 'index'])->name('ajuan.index');
    Route::get('/ajuan/{ajuan}', [AjuanController::class, 'show'])->name('ajuan.show');
    Route::patch('/ajuan/{ajuan}/checklist/{checklistAjuan}/verifikasi', [AjuanController::class, 'verifikasiChecklist'])->name('ajuan.verifikasi-checklist');
    Route::post('/ajuan/{ajuan}/milestone', [AjuanController::class, 'updateMilestone'])->name('ajuan.update-milestone');
    Route::delete('/ajuan/{ajuan}', [AjuanController::class, 'destroy'])->name('ajuan.destroy');

    // Arsip
    Route::get('/arsip', [ArsipRekomController::class, 'index'])->name('arsip.index');
    Route::get('/arsip/{ajuan}/create', [ArsipRekomController::class, 'create'])->name('arsip.create');
    Route::post('/arsip/{ajuan}', [ArsipRekomController::class, 'store'])->name('arsip.store');
    Route::get('/arsip/{arsipRekom}/download', [ArsipRekomController::class, 'download'])->name('arsip.download');

    // Drive Dokumen
    Route::get('/drive', [DriveController::class, 'index'])->name('drive.index');
    Route::post('/drive/upload', [DriveController::class, 'upload'])->name('drive.upload');
    Route::get('/drive/download-zip', [DriveController::class, 'downloadZip'])->name('drive.download-zip');
    Route::delete('/drive/delete', [DriveController::class, 'delete'])->name('drive.delete');

    // Modul 1: e-Regulasi (Admin)
    Route::get('/regulasi', [RegulasiController::class, 'index'])->name('regulasi.index');
    Route::get('/regulasi/{regulasi}', [RegulasiController::class, 'show'])->name('regulasi.show');
    Route::post('/regulasi/{regulasi}/kembalikan', [RegulasiController::class, 'kembalikanUntukRevisi'])->name('regulasi.kembalikan');
    Route::post('/regulasi/{regulasi}/setujui', [RegulasiController::class, 'setujuiDraft'])->name('regulasi.setujui');
    Route::delete('/regulasi/{regulasi}', [RegulasiController::class, 'destroy'])->name('regulasi.destroy');
    // Modul 2: e-Bimtek (Admin) - Removed by request
    // Modul 2b: Informasi & Berita Pembinaan (Admin CRUD)
    Route::get('/bimtek-informasi', [BimtekInformasiController::class, 'index'])->name('bimtek-informasi.index');
    Route::get('/bimtek-informasi/create', [BimtekInformasiController::class, 'create'])->name('bimtek-informasi.create');
    Route::post('/bimtek-informasi/upload-image', [BimtekInformasiController::class, 'uploadImage'])->name('bimtek-informasi.upload-image');
    Route::post('/bimtek-informasi', [BimtekInformasiController::class, 'store'])->name('bimtek-informasi.store');
    Route::get('/bimtek-informasi/{bimtekInformasi}', [BimtekInformasiController::class, 'show'])->name('bimtek-informasi.show');
    Route::get('/bimtek-informasi/{bimtekInformasi}/edit', [BimtekInformasiController::class, 'edit'])->name('bimtek-informasi.edit');
    Route::put('/bimtek-informasi/{bimtekInformasi}', [BimtekInformasiController::class, 'update'])->name('bimtek-informasi.update');
    Route::delete('/bimtek-informasi/{bimtekInformasi}', [BimtekInformasiController::class, 'destroy'])->name('bimtek-informasi.destroy');
    // Modul 2c: Pengajuan Pembinaan dari Desa (Admin kelola)
    Route::get('/pengajuan-pembinaan', [PengajuanPembinaanController::class, 'index'])->name('pengajuan-pembinaan.index');
    Route::get('/pengajuan-pembinaan/{pengajuanPembinaan}', [PengajuanPembinaanController::class, 'show'])->name('pengajuan-pembinaan.show');
    Route::post('/pengajuan-pembinaan/{pengajuanPembinaan}/balas', [PengajuanPembinaanController::class, 'balas'])->name('pengajuan-pembinaan.balas');
    Route::delete('/pengajuan-pembinaan/{pengajuanPembinaan}', [PengajuanPembinaanController::class, 'destroy'])->name('pengajuan-pembinaan.destroy');
    // Modul 5: e-Pj Kades (Admin)
    Route::get('/pjkades', [PjKadesController::class, 'index'])->name('pjkades.index');
    Route::get('/pjkades/{pjkades}', [PjKadesController::class, 'show'])->name('pjkades.show');
    Route::get('/pjkades/{pjkades}/print-syarat', [PjKadesController::class, 'printSyarat'])->name('pjkades.print-syarat');
    Route::post('/pjkades/{pjkades}/verify/{checklistPjKades}', [PjKadesController::class, 'verifyChecklist'])->name('pjkades.verify-checklist');
    Route::post('/pjkades/{pjkades}/verify-bulk', [PjKadesController::class, 'verifyChecklistBulk'])->name('pjkades.verify-bulk');
    Route::post('/pjkades/{pjkades}/generate-sk', [PjKadesController::class, 'generateSk'])->name('pjkades.generate-sk');
    Route::post('/pjkades/{pjkades}/update-catatan', [PjKadesController::class, 'updateCatatanAdmin'])->name('pjkades.update-catatan');
    Route::post('/pjkades/{pjkades}/disposisi', [PjKadesController::class, 'updateDisposisi'])->name('pjkades.disposisi');
    Route::delete('/pjkades/{pjkades}', [PjKadesController::class, 'destroy'])->name('pjkades.destroy');
    // Modul 9: Rencana P3D (Admin)
    Route::get('/rencana-p3d', [RencanaP3dController::class, 'index'])->name('rencana-p3d.index');
    Route::get('/rencana-p3d/export/csv', [RencanaP3dController::class, 'exportCsv'])->name('rencana-p3d.export-csv');
    Route::get('/rencana-p3d/{id}', [RencanaP3dController::class, 'show'])->name('rencana-p3d.show');
    Route::post('/rencana-p3d/{id}/status', [RencanaP3dController::class, 'updateStatus'])->name('rencana-p3d.update-status');

    // Data Master
    Route::get('/perangkat', [PerangkatController::class, 'index'])->name('perangkat.index');

    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('kecamatan', MasterDataController::class);
    });
});

Route::middleware(['auth', 'role:desa'])->prefix('desa')->name('desa.')->group(function () {
    Route::get('/dashboard', [DesaDashboardController::class, 'index'])->name('dashboard');

    Route::get('/perangkat', [App\Http\Controllers\Desa\PerangkatController::class, 'index'])->name('perangkat.index');
    Route::get('/perangkat/create', [App\Http\Controllers\Desa\PerangkatController::class, 'create'])->name('perangkat.create');
    Route::post('/perangkat', [App\Http\Controllers\Desa\PerangkatController::class, 'store'])->name('perangkat.store');
    Route::get('/perangkat/{perangkat}/edit', [App\Http\Controllers\Desa\PerangkatController::class, 'edit'])->name('perangkat.edit');
    Route::put('/perangkat/{perangkat}', [App\Http\Controllers\Desa\PerangkatController::class, 'update'])->name('perangkat.update');
    Route::delete('/perangkat/{perangkat}', [App\Http\Controllers\Desa\PerangkatController::class, 'destroy'])->name('perangkat.destroy');
    Route::patch('/perangkat/{perangkat}/activate', [App\Http\Controllers\Desa\PerangkatController::class, 'activate'])->name('perangkat.activate');

    // Ajuan — /buat MUST come before /{ajuan} or it gets swallowed
    Route::get('/ajuan/buat', [App\Http\Controllers\Desa\AjuanController::class, 'create'])->name('ajuan.create');
    Route::get('/ajuan', [App\Http\Controllers\Desa\AjuanController::class, 'index'])->name('ajuan.index');
    Route::post('/ajuan', [App\Http\Controllers\Desa\AjuanController::class, 'store'])->name('ajuan.store');
    Route::get('/ajuan/{ajuan}', [App\Http\Controllers\Desa\AjuanController::class, 'show'])->name('ajuan.show');
    Route::post('/ajuan/{ajuan}/upload/{checklistAjuan}', [App\Http\Controllers\Desa\AjuanController::class, 'uploadDokumen'])->name('ajuan.upload');
    Route::post('/ajuan/{ajuan}/bulk-upload', [App\Http\Controllers\Desa\AjuanController::class, 'bulkUpload'])->name('ajuan.bulk-upload');
    Route::delete('/ajuan/{ajuan}', [App\Http\Controllers\Desa\AjuanController::class, 'destroy'])->name('ajuan.destroy');

    Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');
    // Modul 1: e-Regulasi (Desa)
    Route::get('/regulasi', [App\Http\Controllers\Desa\RegulasiController::class, 'index'])->name('regulasi.index');
    Route::get('/regulasi/buat', [App\Http\Controllers\Desa\RegulasiController::class, 'create'])->name('regulasi.create');
    Route::get('/regulasi/{regulasi}', [App\Http\Controllers\Desa\RegulasiController::class, 'show'])->name('regulasi.show');
    Route::post('/regulasi', [App\Http\Controllers\Desa\RegulasiController::class, 'store'])->name('regulasi.store');
    Route::post('/regulasi/{regulasi}/kirim-revisi', [App\Http\Controllers\Desa\RegulasiController::class, 'kirimRevisi'])->name('regulasi.kirim-revisi');
    Route::delete('/regulasi/{regulasi}', [App\Http\Controllers\Desa\RegulasiController::class, 'destroy'])->name('regulasi.destroy');

    // Modul 2: e-Bimtek (Desa) - Removed by request
    // Modul 2b: Informasi & Berita Pembinaan (Desa)
    Route::get('/bimtek-informasi', [App\Http\Controllers\Desa\BimtekInformasiController::class, 'index'])->name('bimtek-informasi.index');
    Route::get('/bimtek-informasi/{bimtekInformasi}', [App\Http\Controllers\Desa\BimtekInformasiController::class, 'show'])->name('bimtek-informasi.show');
    // Modul 2b: Pengajuan Pembinaan Desa ke Dinpermasdes
    Route::get('/pengajuan-pembinaan', [App\Http\Controllers\Desa\PengajuanPembinaanController::class, 'index'])->name('pengajuan-pembinaan.index');
    Route::get('/pengajuan-pembinaan/buat', [App\Http\Controllers\Desa\PengajuanPembinaanController::class, 'create'])->name('pengajuan-pembinaan.create');
    Route::post('/pengajuan-pembinaan', [App\Http\Controllers\Desa\PengajuanPembinaanController::class, 'store'])->name('pengajuan-pembinaan.store');
    Route::get('/pengajuan-pembinaan/{pengajuanPembinaan}', [App\Http\Controllers\Desa\PengajuanPembinaanController::class, 'show'])->name('pengajuan-pembinaan.show');
    Route::delete('/pengajuan-pembinaan/{pengajuanPembinaan}', [App\Http\Controllers\Desa\PengajuanPembinaanController::class, 'destroy'])->name('pengajuan-pembinaan.destroy');
    // Modul 5: e-Pj Kades (Desa)
    Route::get('/pjkades', [App\Http\Controllers\Desa\PjKadesController::class, 'index'])->name('pjkades.index');
    Route::get('/pjkades/buat', [App\Http\Controllers\Desa\PjKadesController::class, 'create'])->name('pjkades.create');
    Route::post('/pjkades', [App\Http\Controllers\Desa\PjKadesController::class, 'store'])->name('pjkades.store');
    Route::get('/pjkades/{pjkades}', [App\Http\Controllers\Desa\PjKadesController::class, 'show'])->name('pjkades.show');
    Route::post('/pjkades/{pjkades}/upload/{checklistPjKades}', [App\Http\Controllers\Desa\PjKadesController::class, 'uploadChecklist'])->name('pjkades.upload');
    Route::post('/pjkades/{pjkades}/bulk-upload', [App\Http\Controllers\Desa\PjKadesController::class, 'bulkUpload'])->name('pjkades.bulkUpload');
    Route::post('/pjkades/{pjkades}/submit', [App\Http\Controllers\Desa\PjKadesController::class, 'submitUsulan'])->name('pjkades.submit');
    Route::delete('/pjkades/{pjkades}', [App\Http\Controllers\Desa\PjKadesController::class, 'destroy'])->name('pjkades.destroy');
    // Modul 9: Rencana P3D (Desa)
    Route::get('/rencana-p3d', [App\Http\Controllers\Desa\RencanaP3dController::class, 'index'])->name('rencana-p3d.index');
    Route::get('/rencana-p3d/buat', [App\Http\Controllers\Desa\RencanaP3dController::class, 'create'])->name('rencana-p3d.create');
    Route::post('/rencana-p3d', [App\Http\Controllers\Desa\RencanaP3dController::class, 'store'])->name('rencana-p3d.store');
    Route::get('/rencana-p3d/{rencanaP3d}/edit', [App\Http\Controllers\Desa\RencanaP3dController::class, 'edit'])->name('rencana-p3d.edit');
    Route::put('/rencana-p3d/{rencanaP3d}', [App\Http\Controllers\Desa\RencanaP3dController::class, 'update'])->name('rencana-p3d.update');
    Route::delete('/rencana-p3d/{rencanaP3d}', [App\Http\Controllers\Desa\RencanaP3dController::class, 'destroy'])->name('rencana-p3d.destroy');

    // Data Master: Perangkat Desa
    Route::get('/perangkat', [App\Http\Controllers\Desa\PerangkatController::class, 'index'])->name('perangkat.index');
    Route::get('/bpd', [BpdController::class, 'index'])->name('bpd.index');
    Route::get('/bpd/create', [BpdController::class, 'create'])->name('bpd.create');
    Route::post('/bpd', [BpdController::class, 'store'])->name('bpd.store');
    Route::get('/bpd/{bpd}/edit', [BpdController::class, 'edit'])->name('bpd.edit');
    Route::put('/bpd/{bpd}', [BpdController::class, 'update'])->name('bpd.update');
    Route::delete('/bpd/{bpd}', [BpdController::class, 'destroy'])->name('bpd.destroy');
    Route::patch('/bpd/{bpd}/activate', [BpdController::class, 'activate'])->name('bpd.activate');

    Route::resource('ajuan-bpd', App\Http\Controllers\Desa\AjuanBpdController::class);
    Route::post('ajuan-bpd/{ajuanBpd}/upload/{checklist}', [App\Http\Controllers\Desa\AjuanBpdController::class, 'uploadDokumen'])->name('ajuan-bpd.upload');
    Route::post('ajuan-bpd/{ajuanBpd}/bulk-upload', [App\Http\Controllers\Desa\AjuanBpdController::class, 'bulkUpload'])->name('ajuan-bpd.bulkUpload');
});

// Admin Dinpermasdes Routes
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Akun Desa
    Route::get('/akun-desa', [AkunDesaController::class, 'index'])->name('akun_desa.index');
    Route::patch('/akun-desa/{user}/password', [AkunDesaController::class, 'updatePassword'])->name('akun_desa.update_password');

    // Modul e-Rekomendasi (Admin Verification)
    Route::get('/ajuan', [AdminAjuanController::class, 'index'])->name('ajuan.index');
    Route::get('/ajuan/{ajuan}', [AdminAjuanController::class, 'show'])->name('ajuan.show');
    Route::get('/ajuan/{ajuan}/print-syarat', [AdminAjuanController::class, 'printSyarat'])->name('ajuan.print-syarat');
    Route::post('/ajuan/{ajuan}/verify/{checklistAjuan}', [AdminAjuanController::class, 'verifyDokumen'])->name('ajuan.verify');
    Route::post('/ajuan/{ajuan}/verify-bulk', [AdminAjuanController::class, 'verifyDokumenBulk'])->name('ajuan.verify-bulk');
    Route::post('/ajuan/{ajuan}/update-catatan', [AdminAjuanController::class, 'updateCatatanAdmin'])->name('ajuan.update-catatan');
    Route::post('/ajuan/{ajuan}/disposisi', [AdminAjuanController::class, 'updateDisposisi'])->name('ajuan.disposisi');

    // Modul Data Master (Admin Verification)
    Route::get('/perangkat', [VerifikasiPerangkatController::class, 'index'])->name('perangkat.index');
    Route::post('/perangkat/{id}/approve', [VerifikasiPerangkatController::class, 'approve'])->name('perangkat.approve');
    Route::post('/perangkat/{id}/reject', [VerifikasiPerangkatController::class, 'reject'])->name('perangkat.reject');

    Route::get('/bpd', [VerifikasiBpdController::class, 'index'])->name('bpd.index');
    Route::post('/bpd/{id}/approve', [VerifikasiBpdController::class, 'approve'])->name('bpd.approve');
    Route::post('/bpd/{id}/reject', [VerifikasiBpdController::class, 'reject'])->name('bpd.reject');

    // Ajuan BPD
    Route::get('/ajuan-bpd', [AjuanBpdController::class, 'index'])->name('ajuan-bpd.index');
    Route::get('/ajuan-bpd/{ajuanBpd}', [AjuanBpdController::class, 'show'])->name('ajuan-bpd.show');
    Route::get('/ajuan-bpd/{ajuanBpd}/print-syarat', [AjuanBpdController::class, 'printSyarat'])->name('ajuan-bpd.print-syarat');
    Route::post('/ajuan-bpd/{ajuanBpd}/verify-checklist/{checklist}', [AjuanBpdController::class, 'verifyChecklist'])->name('ajuan-bpd.verify-checklist');
    Route::post('/ajuan-bpd/{ajuanBpd}/catatan', [AjuanBpdController::class, 'updateCatatanAdmin'])->name('ajuan-bpd.catatan');
    Route::post('/ajuan-bpd/{ajuanBpd}/disposisi', [AjuanBpdController::class, 'updateDisposisi'])->name('ajuan-bpd.disposisi');
    Route::delete('/ajuan-bpd/{ajuanBpd}', [AjuanBpdController::class, 'destroy'])->name('ajuan-bpd.destroy');});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Route for serving images from storage directly (useful for shared hosting without symlinks)
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (file_exists($filePath)) {
        return response()->file($filePath);
    }
    abort(404);
})->where('path', '.*');

Route::get('/fix-storage', function () {
    $storageHtaccess = base_path('storage/.htaccess');
    if (file_exists($storageHtaccess)) {
        @unlink($storageHtaccess);
    }
    
    // Hapus symlink/folder public/storage yang nyangkut di hosting
    $publicStorage = public_path('storage');
    if (file_exists($publicStorage) || is_link($publicStorage)) {
        if (is_link($publicStorage)) {
            @unlink($publicStorage);
        } else {
            // Hapus isi folder jika berupa folder (recursively deleted not fully needed, just use rmdir if empty, but usually it's just an empty folder or symlink)
            if (is_dir($publicStorage)) {
                @rmdir($publicStorage);
            }
        }
    }
    
    $logsHtaccess = base_path('storage/logs/.htaccess');
    if (!file_exists($logsHtaccess)) {
        @file_put_contents($logsHtaccess, "Order deny,allow\nDeny from all\n");
    }
    
    $frameworkHtaccess = base_path('storage/framework/.htaccess');
    if (!file_exists($frameworkHtaccess)) {
        @file_put_contents($frameworkHtaccess, "Order deny,allow\nDeny from all\n");
    }
    
    return "✅ Selesai! Pintu sudah berhasil didobrak. Silakan kembali ke halaman sebelumnya dan Refresh (F5).";
});

Route::get('/parse-excel-diagnose', function () {
    try {
        $filePath = base_path('data_desa.xlsx');
        if (! file_exists($filePath)) {
            return 'File not found at: '.$filePath;
        }

        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            return 'Could not open ZIP file';
        }

        // Read shared strings
        $sharedStrings = [];
        $stringsData = $zip->getFromName('xl/sharedStrings.xml');
        if ($stringsData) {
            $xml = simplexml_load_string($stringsData);
            if ($xml && $xml->si) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // Read sheet1
        $sheetData = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (! $sheetData) {
            return 'Could not read sheet1.xml';
        }

        $xml = simplexml_load_string($sheetData);
        $rows = [];
        if ($xml && $xml->sheetData) {
            foreach ($xml->sheetData->row as $row) {
                $rowData = [];
                foreach ($row->c as $c) {
                    $val = (string) $c->v;
                    $type = (string) $c['t'];
                    if ($type === 's') {
                        $val = $sharedStrings[(int) $val] ?? '';
                    }
                    $ref = (string) $c['r'];
                    preg_match('/^[A-Z]+/', $ref, $matches);
                    $colName = $matches[0] ?? '';
                    $rowData[$colName] = $val;
                }
                $rows[] = $rowData;
            }
        }
        $zip->close();

        file_put_contents(base_path('data_desa.json'), json_encode($rows, JSON_PRETTY_PRINT));

        return response()->json([
            'status' => 'success',
            'row_count' => count($rows),
            'sample_rows' => array_slice($rows, 0, 10),
        ]);

    } catch (Exception $e) {
        return 'Error: '.$e->getMessage()."\n".$e->getTraceAsString();
    }
});

Route::get('/run-import', function () {
    set_time_limit(0);

    $jsonFile = base_path('data_desa.json');

    if (! file_exists($jsonFile)) {
        return 'File data_desa.json tidak ditemukan!';
    }

    $jsonData = json_decode(file_get_contents($jsonFile), true);

    if (! $jsonData) {
        return 'Gagal membaca atau mem-parse JSON.';
    }

    // Row 0 is header, so shift it
    array_shift($jsonData);

    $insertedCount = 0;
    $errors = [];

    DB::transaction(function () use ($jsonData, &$insertedCount, &$errors) {
        foreach ($jsonData as $index => $row) {
            $kecamatanName = trim($row['B'] ?? '');
            $desaName = trim($row['C'] ?? '');

            if (empty($kecamatanName) || empty($desaName)) {
                continue;
            }

            try {
                $kecamatan = Kecamatan::firstOrCreate(['nama_kecamatan' => $kecamatanName]);
                $desa = Desa::firstOrCreate([
                    'nama_desa' => $desaName,
                    'kecamatan_id' => $kecamatan->id,
                ]);

                $perangkatList = [
                    ['jabatan' => 'Kepala Desa', 'nama' => trim($row['D'] ?? '')],
                    ['jabatan' => 'Sekretaris Desa', 'nama' => trim($row['E'] ?? '')],
                    ['jabatan' => 'Kasi Pemerintahan', 'nama' => trim($row['F'] ?? '')],
                    ['jabatan' => 'Kasi Kesejahteraan', 'nama' => trim($row['G'] ?? '')],
                    ['jabatan' => 'Kasi Pelayanan', 'nama' => trim($row['H'] ?? '')],
                    ['jabatan' => 'Kaur Keuangan', 'nama' => trim($row['I'] ?? '')],
                    ['jabatan' => 'Kaur Perencanaan', 'nama' => trim($row['J'] ?? '')],
                    ['jabatan' => 'Kaur TU & Umum', 'nama' => trim($row['K'] ?? '')],
                    ['jabatan' => 'Kadus I', 'nama' => trim($row['L'] ?? '')],
                    ['jabatan' => 'Kadus II', 'nama' => trim($row['M'] ?? '')],
                    ['jabatan' => 'Kadus III', 'nama' => trim($row['N'] ?? '')],
                ];

                if (! empty(trim($row['P'] ?? '')) && stripos(trim($row['O'] ?? ''), 'Tidak Ada') === false) {
                    $perangkatList[] = ['jabatan' => 'Kadus IV', 'nama' => trim($row['P'])];
                }
                if (! empty(trim($row['R'] ?? '')) && stripos(trim($row['Q'] ?? ''), 'Tidak Ada') === false) {
                    $perangkatList[] = ['jabatan' => 'Kadus V', 'nama' => trim($row['R'])];
                }
                if (! empty(trim($row['U'] ?? '')) && stripos(trim($row['S'] ?? ''), 'Tidak Ada') === false) {
                    $perangkatList[] = ['jabatan' => trim($row['V'] ?? 'Staf Perangkat Desa'), 'nama' => trim($row['U'])];
                }
                if (! empty(trim($row['Y'] ?? '')) && stripos(trim($row['W'] ?? ''), 'Tidak Ada') === false) {
                    $perangkatList[] = ['jabatan' => trim($row['Z'] ?? 'Staf Non Perangkat Desa 1'), 'nama' => trim($row['Y'])];
                }
                if (! empty(trim($row['AA'] ?? '')) && trim($row['AA']) !== '-') {
                    $perangkatList[] = ['jabatan' => trim($row['AB'] ?? 'Staf Non Perangkat Desa 2'), 'nama' => trim($row['AA'])];
                }
                if (! empty(trim($row['AC'] ?? '')) && trim($row['AC']) !== '-') {
                    $perangkatList[] = ['jabatan' => trim($row['AD'] ?? 'Staf Non Perangkat Desa 3'), 'nama' => trim($row['AC'])];
                }

                foreach ($perangkatList as $p) {
                    if (! empty($p['nama']) && $p['nama'] !== '-') {
                        PerangkatDesa::updateOrCreate(
                            ['desa_id' => $desa->id, 'jabatan' => $p['jabatan']],
                            ['nama' => $p['nama'], 'status_aktif' => true, 'tgl_mulai_jabatan' => now()]
                        );
                        $insertedCount++;
                    }
                }

                $bpdList = [
                    ['nama' => trim($row['AJ'] ?? ''), 'jabatan' => 'Ketua BPD', 'status' => trim($row['AI'] ?? '')],
                    ['nama' => trim($row['AL'] ?? ''), 'jabatan' => 'Wakil Ketua BPD', 'status' => trim($row['AK'] ?? '')],
                    ['nama' => trim($row['AN'] ?? ''), 'jabatan' => 'Sekretaris BPD', 'status' => trim($row['AM'] ?? '')],
                    ['nama' => trim($row['AP'] ?? ''), 'jabatan' => 'Ketua Bid. Pemerintahan', 'status' => trim($row['AO'] ?? '')],
                    ['nama' => trim($row['AR'] ?? ''), 'jabatan' => 'Ketua Bid. Pembangunan', 'status' => trim($row['AQ'] ?? '')],
                    ['nama' => trim($row['AT'] ?? ''), 'jabatan' => trim($row['AU'] ?? 'Anggota BPD 1'), 'status' => trim($row['AS'] ?? '')],
                    ['nama' => trim($row['AW'] ?? ''), 'jabatan' => trim($row['AX'] ?? 'Anggota BPD 2'), 'status' => trim($row['AV'] ?? '')],
                    ['nama' => trim($row['AZ'] ?? ''), 'jabatan' => trim($row['BA'] ?? 'Anggota BPD 3'), 'status' => trim($row['AY'] ?? '')],
                    ['nama' => trim($row['BC'] ?? ''), 'jabatan' => trim($row['BD'] ?? 'Anggota BPD 4'), 'status' => trim($row['BB'] ?? '')],
                ];

                foreach ($bpdList as $b) {
                    if (! empty($b['nama']) && $b['nama'] !== '-' && stripos($b['status'], 'Ada') !== false) {
                        PerangkatDesa::updateOrCreate(
                            ['desa_id' => $desa->id, 'jabatan' => $b['jabatan']],
                            ['nama' => $b['nama'], 'status_aktif' => true, 'tgl_mulai_jabatan' => now()]
                        );
                        $insertedCount++;
                    }
                }
            } catch (Exception $e) {
                $errors[] = 'Baris '.($index + 2).': '.$e->getMessage();
            }
        }
    });

    $result = "Berhasil mengimpor $insertedCount data perangkat desa/BPD.";
    if (count($errors) > 0) {
        $result .= "\n\nErrors:\n".implode("\n", $errors);
    }

    return nl2br($result);
});

Route::get('/buatakun', function () {
    set_time_limit(0);

    $desas = Desa::with('kecamatan')->get();
    $created = 0;
    $skipped = 0;
    $details = [];
    $hashedPassword = bcrypt('password'); // Hash once, reuse for all

    DB::transaction(function () use ($desas, &$created, &$skipped, &$details, $hashedPassword) {
        foreach ($desas as $desa) {
            $username = strtolower(str_replace([' ', '.', ',', "'"], ['_', '', '', ''], $desa->nama_desa));

            $exists = DB::table('users')->where('username', $username)->exists();
            if ($exists) {
                $skipped++;

                continue;
            }

            DB::table('users')->insert([
                'name' => 'Operator '.$desa->nama_desa,
                'username' => $username,
                'password' => $hashedPassword,
                'role' => 'desa',
                'desa_id' => $desa->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $kec = $desa->kecamatan->nama_kecamatan ?? '-';
            $details[] = "✅ {$desa->nama_desa} (Kec. {$kec}) → username: <b>{$username}</b>";
            $created++;
        }
    });

    $html = '<h2>Hasil Pembuatan Akun Desa</h2>';
    $html .= '<p>Total desa: '.count($desas)." | Dibuat: <b>{$created}</b> | Sudah ada: {$skipped}</p>";
    $html .= '<p>Password semua akun: <b>password</b></p>';
    $html .= '<hr>';
    $html .= '<ol>'.implode('', array_map(fn ($d) => "<li>{$d}</li>", $details)).'</ol>';

    return $html;
});

Route::get('/daftarakun', function () {
    $users = DB::table('users')
        ->where('role', 'desa')
        ->join('desas', 'users.desa_id', '=', 'desas.id')
        ->leftJoin('kecamatans', 'desas.kecamatan_id', '=', 'kecamatans.id')
        ->select('users.username', 'users.name', 'desas.nama_desa', 'kecamatans.nama_kecamatan')
        ->orderBy('kecamatans.nama_kecamatan')
        ->orderBy('desas.nama_desa')
        ->get();

    $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Daftar Akun Desa</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; }
        h2 { color: #0F3C65; }
        table { border-collapse: collapse; width: 100%; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        th { background: #0F3C65; color: white; padding: 10px 14px; text-align: left; font-size: 13px; }
        td { padding: 8px 14px; border-bottom: 1px solid #eee; font-size: 13px; }
        tr:hover { background: #FFF2BA; }
        .pwd { font-family: monospace; color: #666; }
        .usr { font-family: monospace; font-weight: bold; color: #0F3C65; }
        .count { color: #666; margin-bottom: 16px; }
    </style></head><body>';
    $html .= '<h2>📋 Daftar Lengkap Akun Operator Desa</h2>';
    $html .= "<p class='count'>Total akun: <b>".count($users).'</b> | Password semua: <b>password</b></p>';
    $html .= '<table><thead><tr><th>No</th><th>Kecamatan</th><th>Nama Desa</th><th>Username</th><th>Password</th></tr></thead><tbody>';

    foreach ($users as $i => $u) {
        $no = $i + 1;
        $html .= "<tr>
            <td>{$no}</td>
            <td>{$u->nama_kecamatan}</td>
            <td>{$u->nama_desa}</td>
            <td class='usr'>{$u->username}</td>
            <td class='pwd'>password</td>
        </tr>";
    }

    $html .= '</tbody></table></body></html>';

    return $html;
});

Route::get('/parse-docx', function () {
    $file = base_path('Ceklist Dokumen Pengangkatan, Rotasi dan Pemberhentian.docx');
    $zip = new ZipArchive;

    if ($zip->open($file) === true) {
        if (($index = $zip->locateName('word/document.xml')) !== false) {
            $data = $zip->getFromIndex($index);
            $zip->close();

            $dom = new DOMDocument;
            $dom->loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            $text = '';

            // DOCX uses tables heavily for checklists. Let's try to extract text row by row
            $rows = $dom->getElementsByTagName('tr');
            if ($rows->length > 0) {
                foreach ($rows as $row) {
                    $cells = $row->getElementsByTagName('tc');
                    $row_text = [];
                    foreach ($cells as $cell) {
                        $cell_text = '';
                        $paras = $cell->getElementsByTagName('p');
                        foreach ($paras as $p) {
                            $texts = $p->getElementsByTagName('t');
                            foreach ($texts as $t) {
                                $cell_text .= $t->nodeValue.' ';
                            }
                        }
                        $row_text[] = trim($cell_text);
                    }
                    $text .= implode(' | ', $row_text)."\n";
                }
            } else {
                // Fallback to paragraphs if no tables
                $paragraphs = $dom->getElementsByTagName('p');
                foreach ($paragraphs as $p) {
                    $texts = $p->getElementsByTagName('t');
                    $p_text = '';
                    foreach ($texts as $t) {
                        $p_text .= $t->nodeValue;
                    }
                    if ($p_text !== '') {
                        $text .= $p_text."\n";
                    }
                }
            }

            file_put_contents(base_path('ceklist_parsed.txt'), $text);

            return "<pre>Success parsing to ceklist_parsed.txt:\n\n".htmlspecialchars($text).'</pre>';
        } else {
            return 'Could not find word/document.xml';
        }
    } else {
        return "Could not open ZIP archive. Path: $file";
    }
});

Route::get('/seed-checklist', function () {
    DB::transaction(function () {
        // Clear existing template checklists to replace dummy data
        TemplateChecklist::query()->delete();

        $pengangkatan = JenisLayanan::firstOrCreate(['nama' => 'Pengangkatan']);
        $rotasi = JenisLayanan::firstOrCreate(['nama' => 'Rotasi']);
        $pemberhentian = JenisLayanan::firstOrCreate(['nama' => 'Pemberhentian']);

        AlasanPemberhentian::query()->delete();

        $alasanPurnaTugas = AlasanPemberhentian::firstOrCreate(['nama' => 'Purna Tugas']);
        $alasanMundur = AlasanPemberhentian::firstOrCreate(['nama' => 'Permintaan Sendiri']);
        $alasanDiberhentikan = AlasanPemberhentian::firstOrCreate(['nama' => 'Diberhentikan']);

        $pengangkatanItems = [
            'Surat Pengantar dari Kecamatan',
            'Surat Usulan Rekomendasi Pengangkatan Perangkat Desa dari Kepala Desa kepada Bupati lewat Camat',
            'Fc. Rekomendasi Camat terkait pengangkatan Perangkat Desa',
            'Fc. Surat Permohonan Rekomendasi Pengangkatan Perangkat Desa dari Kepala Desa kepada Camat',
            'Surat Pernyataan kebenaran dokumen dari Kepala Desa (bermaterai)',
            'Fc. Berita Acara musyawarah pembentukan Panitia Penjaringan dan Penyaringan',
            'Fc. Keputusan Kepala Desa tentang Panitia Penjaringan dan Penyaringan',
            'Fc. Tata Tertib Panitia Penjaringan dan Penyaringan Perangkat Desa',
            'Fc. Jadwal Pelaksanaan Penjaringan dan Penyaringan',
            'Fc. MoU Perjanjian Kerjasama (apabila tahapan seleksi menggunakan pihak ketiga)',
            'Fc. Berita Acra Penetapan calon Perangkat Desa',
            'Fc. Berita Acara Penelitian keberatan masyarakat oleh Panitia Penjaringan dan Penyaringan (apabila ada)',
            'Fc. Keputusan Kepala Desa tentang calon yang berhak mengikuti ujian',
            'Fc. Berita Acara Ujian penyaringan yang dilengkapi tanda tangan calon yang berhak mengikuti ujian',
            'Fc. Daftar hadir ujian Penyaringan dan uji kemampuan',
            'Fc. Berita Acara uji kemampuan yang dilengkapi tanda tangan calon yang berhak mengikuti uji kemampuan',
            'Fc. Berita Acara penetapan calon yang lulus dan memperoleh peringkat 1,2 dan 3',
            'Fc. Berita Acara ujian penyaringan lanjutan (apabila terdapat lebih dari satu orang calon yang lulus dan memperoleh nilai tertinggi yang sama)',
            'Fc. Rincian perhitungan penilaian penjaringan dan penyaringan',
            'Fc. Persyaratan administrasi calon yang lolos pada seleksi pengangkatan perangkat desa',
            'Fc. Rencana Anggaran Biaya (RAB)',
            'Fc. Laporan pertanggungjawaban penggunaan anggaran',
            'Dokumentasi setiap tahapan penjaringan Perangkat Desa',
        ];

        $rotasiItems = [
            'Surat Pengantar dari Kecamatan',
            'Surat Usulan Rekomendasi Rotasi Perangkat Desa dari Kepala Desa kepada Bupati lewat Camat',
            'Surat Pernyataan kebenaran dokumen dari Kepala Desa (bermaterai)',
            'Fc. Berita Acara Konsultasi Kepala Desa kepada Camat terkait akan dilaksanakannya rotasi Perangkat Desa',
            'Fc. Surat Pemberitahuan dari Kepala desa kepada BPD mengenai pelaksaaan rotasi',
            'Fc. Hasil Rincian perhitungan penilaian kinerja Perangkat desa yang akan dirotasi',
            'Fc. Berita Acara hasil penilaian kinerjaperangkat desa yang akan dirotasi',
            'Fc. Surat Permohonan Rekomendasi Rotasi Perangkat Desa dari Kepala Desa kepada Camat',
            'Fc. Rekomendasi Camat atas Proses Rotasi',
            'Fc. Sk Pengangkatan Pertama Perangkat Desa yang akan di Rotasi',
            'Fc. Ijasah atau Surat Tanda Tamat Belajar (STTB)',
            'Fc. Daftar Hadir Perangkat Desa yang akan dirotasi selama 6 (enam) bulan sebelum pelaksaaan Rotasi',
            'Fc. Peraturan Kepala Desa tentang Tata Tertib Rotasi',
        ];

        $purnaTugasItems = [
            'Surat Pengantar dari Kecamatan',
            'Surat Usulan Rekomendasi Pemberhentian Perangkat Desa dari Kepala Desa kepada Bupati lewat Camat',
            'Fc. Surat Permohonan Rekomendasi Pemberhentian Perangkat Desa dari Kepala Desa kepada Camat',
            'Surat Pernyataan kebenaran dokumen dari Kepala Desa (bermaterai)',
            'Fc. Rekomendasi Camat atas proses pemberhentian Perangkat Desa',
            'Fc. SK Pengangkatan Perangkat Desa',
            'Fc. Kartu Keluarga',
            'Fc. KTP atau Akta Kelahiran',
        ];

        $permintaanSendiriItems = array_merge($purnaTugasItems, [
            'Fc. Surat Pernyataan Pengunduran diri dari Perangkat Desa yang ditujukan kepada Kepala Desa',
        ]);

        $diberhentikanItems = [
            'Surat Pengantar dari Kecamatan',
            'Surat Usulan Rekomendasi Pemberhentian Perangkat Desa dari Kepala Desa kepada Bupati lewat Camat',
            'Fc. Surat Permohonan Rekomendasi Pemberhentian Perangkat Desa dari Kepala Desa kepada Camat',
            'Surat Pernyataan kebenaran dokumen dari Kepala Desa (bermaterai)',
            'Fc. Rekomendasi Camat atas proses pemberhentian Perangkat Desa',
            'Fc. SK Pengangkatan Perangkat Desa',
            'Fc. Kartu Keluarga',
            'Fc. Surat keterangan Kematian (apabila meninggal)',
            'Fc. Surat Keterangan dari Rumah Sakit yang menerangkan bahwa yang bersangkutan tidak dapat melaksanakan tugas dan kewajiban secara berturut-turut selama 6 (enam) bulan (apabila berhalangan tetap)',
            'Fc. Putusan dari pengadilan yang memiliki kekuatan hukum tetap (apabila melakukan Tindak Pidana)',
            // 11. Apabila diberhentikan karena melakukan pelanggaran disiplin maka melampirkan: (kita set (apabila...) supaya jadi opsional di sistem)
            'Fc. Bukti teguran lisan tercatat pertama, Teguran tertulis pertama, Teguran tertulis kedua, Teguran tertulis ketiga (apabila pelanggaran disiplin)',
            'Fc. SK pemberhentian Sementara (apabila pelanggaran disiplin)',
            'Fc. Berita Acara Pemeriksaan (apabila pelanggaran disiplin)',
            'Fc. Berita Acara Hasil Rapat Tim Pemeriksa Pelanggaran Disiplin (apabila pelanggaran disiplin)',
            'Fc. Laporan Hasil Pemeriksaan dari Ketua TIM Pemeriksa Pelanggaran Disiplin (apabila pelanggaran disiplin)',
            'Fc. Surat Pemberitahuan Penjatuhan Disiplin dari Kepala Desa kepada Bupati lewat Camat (apabila pelanggaran disiplin)',
            'Fc. Surat Keputusan Penjatuhan Hukuman Disiplin dari Kepala Desa (apabila pelanggaran disiplin)',
            'Fc. SK penguatan Hukuman Disiplin dari Kepala Desa (apabila menolak keberatan yang diajukan Perangkat desa yang bersangkutan)',
            'Fc. Kartu Hukuman Disiplin (apabila pelanggaran disiplin)',
        ];

        $createTemplates = function ($items, $jenis_layanan_id, $alasan_id = null) {
            foreach ($items as $idx => $item) {
                // (apabila...) will make it wajib = false
                $wajib = (strpos($item, '(apabila') === false && strpos($item, 'apabila') === false);
                TemplateChecklist::create([
                    'jenis_layanan_id' => $jenis_layanan_id,
                    'alasan_pemberhentian_id' => $alasan_id,
                    'nama_dokumen' => $item,
                    'urutan' => $idx + 1,
                    'wajib' => $wajib,
                ]);
            }
        };

        $createTemplates($pengangkatanItems, $pengangkatan->id);
        $createTemplates($rotasiItems, $rotasi->id);
        $createTemplates($purnaTugasItems, $pemberhentian->id, $alasanPurnaTugas->id);
        $createTemplates($permintaanSendiriItems, $pemberhentian->id, $alasanMundur->id);
        $createTemplates($diberhentikanItems, $pemberhentian->id, $alasanDiberhentikan->id);
    });

    return 'Berhasil memperbarui Master Data Checklist';
});

Route::get('/debug-count', function () {
    try {
        $out = "Start DB Fix...<br>\n";
        $dbPath = database_path('database.sqlite');
        $db = new PDO('sqlite:'.$dbPath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $out .= "Connected to: $dbPath<br>\n";

        // 1. ADD COLUMN
        $q = $db->query('PRAGMA table_info(bimtek_pendaftarans)');
        $cols = $q->fetchAll(PDO::FETCH_ASSOC);
        $hasDesaId = false;
        foreach ($cols as $c) {
            if ($c['name'] === 'desa_id') {
                $hasDesaId = true;
            }
        }
        if (! $hasDesaId) {
            $db->exec('ALTER TABLE bimtek_pendaftarans ADD COLUMN desa_id INTEGER');
            $out .= "ADD COLUMN desa_id Sukses.<br>\n";
        }

        // 2. Fix Ajuans Checklists
        for ($i = 4; $i <= 10; $i++) {
            $stmt = $db->prepare('SELECT id, jenis_layanan_id, alasan_pemberhentian_id FROM ajuans WHERE id = ?');
            $stmt->execute([$i]);
            $ajuan = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($ajuan) {
                $cStmt = $db->prepare('SELECT COUNT(*) as c FROM checklist_ajuans WHERE ajuan_id = ?');
                $cStmt->execute([$i]);
                if ($cStmt->fetchColumn() == 0) {
                    if ($ajuan['alasan_pemberhentian_id']) {
                        $tStmt = $db->prepare('SELECT id FROM template_checklists WHERE jenis_layanan_id = ? AND (alasan_pemberhentian_id IS NULL OR alasan_pemberhentian_id = ?)');
                        $tStmt->execute([$ajuan['jenis_layanan_id'], $ajuan['alasan_pemberhentian_id']]);
                    } else {
                        $tStmt = $db->prepare('SELECT id FROM template_checklists WHERE jenis_layanan_id = ? AND alasan_pemberhentian_id IS NULL');
                        $tStmt->execute([$ajuan['jenis_layanan_id']]);
                    }
                    $templates = $tStmt->fetchAll(PDO::FETCH_ASSOC);
                    $ins = 0;
                    foreach ($templates as $t) {
                        $db->prepare("INSERT INTO checklist_ajuans (ajuan_id, template_checklist_id, status, versi, created_at, updated_at) VALUES (?, ?, 'belum_diunggah', 1, datetime('now'), datetime('now'))")->execute([$i, $t['id']]);
                        $ins++;
                    }
                    $out .= "Ajuan $i: $ins checklist.<br>\n";
                }
            }
        }

        // 3. Karangendep Update
        $desa = $db->prepare("SELECT id, nama_desa FROM desas WHERE nama_desa LIKE '%Karangendep%'");
        $desa->execute();
        $d = $desa->fetch(PDO::FETCH_ASSOC);
        if ($d) {
            // Update the first existing Perangkat to KARSINAH to preserve foreign keys!
            $firstPStmt = $db->prepare('SELECT id FROM perangkat_desas WHERE desa_id = ? ORDER BY id ASC LIMIT 1');
            $firstPStmt->execute([$d['id']]);
            $firstP = $firstPStmt->fetch(PDO::FETCH_ASSOC);

            if ($firstP) {
                $db->prepare("UPDATE perangkat_desas SET nama = 'KARSINAH', jabatan = 'Kepala Desa', no_sk_terakhir = '141/001/2020', tgl_mulai_jabatan = '2020-01-01', status_aktif = 1 WHERE id = ?")->execute([$firstP['id']]);
                $out .= "Dummy Budi updated to Karsinah.<br>\n";
            }

            // Delete duplicates only
            if ($firstP) {
                $db->prepare('DELETE FROM perangkat_desas WHERE desa_id = ? AND id != ?')->execute([$d['id'], $firstP['id']]);
            } else {
                $db->prepare('DELETE FROM perangkat_desas WHERE desa_id = ?')->execute([$d['id']]); // fall back
            }

            $realData = [
                ['jabatan' => 'Sekretaris Desa', 'nama' => 'TRIYO WIDODO'],
                ['jabatan' => 'Kasi Pemerintahan', 'nama' => 'KIRTO'],
                ['jabatan' => 'Kasi Kesejahteraan', 'nama' => 'SUTARKO'],
                ['jabatan' => 'Kasi Pelayanan', 'nama' => 'AGUS SUPRIJATNO'],
                ['jabatan' => 'Kaur Keuangan', 'nama' => 'NETY AMI PRABAWATI'],
                ['jabatan' => 'Kaur Perencanaan', 'nama' => 'TRI YUNIA RUBIANTO'],
                ['jabatan' => 'Kaur TU & Umum', 'nama' => 'INAWAN NUR KHOLIQ'],
            ];
            foreach ($realData as $p) {
                $db->prepare("INSERT INTO perangkat_desas (desa_id, nama, jabatan, no_sk_terakhir, tgl_mulai_jabatan, status_aktif, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, datetime('now'), datetime('now'))")
                    ->execute([$d['id'], $p['nama'], $p['jabatan'], '141/00'.rand(1, 9).'/2020', '2020-01-01']);
            }
            $out .= "Karangendep berhasi disuntik sisa baris murni.<br>\n";
        }

        return $out;
    } catch (Exception $e) {
        return 'ERROR: '.$e->getMessage().' line: '.$e->getLine();
    }
});

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ajuan;
use App\Models\AjuanBpd;
use App\Models\ChecklistAjuan;
use App\Models\ChecklistAjuanBpd;
use App\Models\ChecklistPjKades;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\PengajuanPembinaan;
use App\Models\PjKades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriveController extends Controller
{
    public function index(Request $request)
    {
        $path = $request->query('path', '');
        $files = [];
        $folders = [];

        $parts = explode('/', trim($path, '/'));

        if (empty($path) || $path === 'dokumen') {
            // Root level: show all kecamatan folders
            $kecamatans = Kecamatan::all();
            foreach ($kecamatans as $kecamatan) {
                $dirPath = 'dokumen/'.strtolower($kecamatan->nama_kecamatan);
                $folders[] = [
                    'name' => ucwords(strtolower($kecamatan->nama_kecamatan)),
                    'path' => $dirPath,
                    'count' => Storage::disk('public')->exists($dirPath) ? count(Storage::disk('public')->allFiles($dirPath)) : 0,
                ];
            }
        } elseif (count($parts) === 2) {
            // Level 2: {kecamatan} -> show all desas
            $kecamatanName = $parts[1];
            $kecamatan = Kecamatan::where('nama_kecamatan', str_replace('_', ' ', $kecamatanName))->first();
            if ($kecamatan) {
                $desas = Desa::where('kecamatan_id', $kecamatan->id)->get();
                foreach ($desas as $desa) {
                    $dirPath = $path.'/'.strtolower(str_replace(' ', '_', $desa->nama_desa));
                    $folders[] = [
                        'name' => ucwords(strtolower($desa->nama_desa)),
                        'path' => $dirPath,
                        'count' => 4, // 4 static folders
                    ];
                }
            }
            if (Storage::disk('public')->exists($path)) {
                foreach (Storage::disk('public')->files($path) as $file) {
                    $files[] = ['name' => basename($file), 'path' => $file, 'size' => Storage::disk('public')->size($file), 'url' => Storage::disk('public')->url($file)];
                }
            }
        } elseif (count($parts) === 3) {
            // Level 3: {kecamatan}/{desa}
            $kecamatanName = $parts[1];
            $desaName = $parts[2];
            $desa = Desa::where('nama_desa', str_replace('_', ' ', $desaName))
                ->whereHas('kecamatan', function ($q) use ($kecamatanName) {
                    $q->where('nama_kecamatan', str_replace('_', ' ', $kecamatanName));
                })->first();

            if ($desa) {
                $fixedFolders = ['kades' => 'Kades', 'perangkat_desa' => 'Perangkat Desa', 'bpd' => 'BPD', 'pembinaan' => 'Pembinaan'];
                foreach ($fixedFolders as $slug => $label) {
                    $folders[] = ['name' => $label, 'path' => $path.'/'.$slug, 'count' => $this->getVirtualFolderCount($desa->id, $slug)];
                }
            }
            if (Storage::disk('public')->exists($path)) {
                foreach (Storage::disk('public')->files($path) as $file) {
                    $files[] = ['name' => basename($file), 'path' => $file, 'size' => Storage::disk('public')->size($file), 'url' => Storage::disk('public')->url($file)];
                }
            }
        } elseif (count($parts) === 4) {
            // Level 4: {kecamatan}/{desa}/{module}
            $kecamatanName = $parts[1];
            $desaName = $parts[2];
            $module = $parts[3];
            $desa = Desa::where('nama_desa', str_replace('_', ' ', $desaName))
                ->whereHas('kecamatan', function ($q) use ($kecamatanName) {
                    $q->where('nama_kecamatan', str_replace('_', ' ', $kecamatanName));
                })->first();

            if ($module === 'kades') {
                $folders[] = ['name' => 'Pemberhentian', 'path' => $path.'/pemberhentian', 'count' => $this->getVirtualFolderCount($desa->id, 'kades', 'pemberhentian')];
                $folders[] = ['name' => 'Penunjukan', 'path' => $path.'/penunjukan', 'count' => $this->getVirtualFolderCount($desa->id, 'kades', 'penunjukan')];
            } elseif ($module === 'perangkat_desa') {
                $folders[] = ['name' => 'Pemberhentian', 'path' => $path.'/pemberhentian', 'count' => $this->getVirtualFolderCount($desa->id, 'perangkat_desa', 'pemberhentian')];
                $folders[] = ['name' => 'Rotasi', 'path' => $path.'/rotasi', 'count' => $this->getVirtualFolderCount($desa->id, 'perangkat_desa', 'rotasi')];
                $folders[] = ['name' => 'Pengangkatan', 'path' => $path.'/pengangkatan', 'count' => $this->getVirtualFolderCount($desa->id, 'perangkat_desa', 'pengangkatan')];
            } elseif ($module === 'bpd') {
                $folders[] = ['name' => 'Pemberhentian', 'path' => $path.'/pemberhentian', 'count' => $this->getVirtualFolderCount($desa->id, 'bpd', 'pemberhentian')];
                $folders[] = ['name' => 'Peresmian', 'path' => $path.'/peresmian', 'count' => $this->getVirtualFolderCount($desa->id, 'bpd', 'peresmian')];
            } elseif ($module === 'pembinaan' && $desa) {
                $pembinaans = PengajuanPembinaan::where('desa_id', $desa->id)->where('status', '!=', 'draft')->get();
                foreach ($pembinaans as $pem) {
                    if ($pem->file_surat_permohonan) {
                        $files[] = ['name' => '[Pembinaan] '.basename($pem->file_surat_permohonan), 'path' => $pem->file_surat_permohonan, 'size' => Storage::disk('public')->exists($pem->file_surat_permohonan) ? Storage::disk('public')->size($pem->file_surat_permohonan) : 0, 'url' => Storage::disk('public')->url($pem->file_surat_permohonan)];
                    }
                    if ($pem->file_undangan) {
                        $files[] = ['name' => '[Pembinaan_Undangan] '.basename($pem->file_undangan), 'path' => $pem->file_undangan, 'size' => Storage::disk('public')->exists($pem->file_undangan) ? Storage::disk('public')->size($pem->file_undangan) : 0, 'url' => Storage::disk('public')->url($pem->file_undangan)];
                    }
                }
            }
            if (Storage::disk('public')->exists($path)) {
                foreach (Storage::disk('public')->files($path) as $file) {
                    $files[] = ['name' => basename($file), 'path' => $file, 'size' => Storage::disk('public')->size($file), 'url' => Storage::disk('public')->url($file)];
                }
                foreach (Storage::disk('public')->directories($path) as $dir) {
                    $base = basename($dir);
                    if (! in_array(strtolower($base), ['pemberhentian', 'penunjukan', 'rotasi', 'pengangkatan', 'peresmian'])) {
                        $folders[] = ['name' => $base, 'path' => $dir, 'count' => count(Storage::disk('public')->allFiles($dir))];
                    }
                }
            }
        } elseif (count($parts) === 5) {
            // Level 5: {kecamatan}/{desa}/{module}/{jenis}
            $kecamatanName = $parts[1];
            $desaName = $parts[2];
            $module = $parts[3];
            $jenis = $parts[4];

            $desa = Desa::where('nama_desa', str_replace('_', ' ', $desaName))
                ->whereHas('kecamatan', function ($q) use ($kecamatanName) {
                    $q->where('nama_kecamatan', str_replace('_', ' ', $kecamatanName));
                })->first();
            if ($desa) {
                if ($module === 'kades') {
                    if ($jenis === 'pemberhentian') {
                        $ajuanChecklists = ChecklistAjuan::whereNotNull('file_path')->whereHas('ajuan', function ($q) use ($desa) {
                            $q->where('desa_id', $desa->id)
                                ->where('status', '!=', 'draft')
                                ->whereIn('jenis_layanan_id', [4, 5])
                                ->whereHas('milestoneTrackings', function ($sq) {
                                    $sq->where('tahap', '>=', 3);
                                });
                        })->get();
                        foreach ($ajuanChecklists as $chk) {
                            $files[] = ['name' => '[e-Rekom] '.basename($chk->file_path), 'path' => $chk->file_path, 'size' => Storage::disk('public')->exists($chk->file_path) ? Storage::disk('public')->size($chk->file_path) : 0, 'url' => Storage::disk('public')->url($chk->file_path)];
                        }

                        $ajuans = Ajuan::whereNotNull('berkas_zip')->where('desa_id', $desa->id)->where('status', '!=', 'draft')->whereIn('jenis_layanan_id', [4, 5])
                            ->whereHas('milestoneTrackings', function ($sq) {
                                $sq->where('tahap', '>=', 3);
                            })->get();
                        foreach ($ajuans as $aju) {
                            $files[] = ['name' => '[e-Rekom_ZIP] '.basename($aju->berkas_zip), 'path' => $aju->berkas_zip, 'size' => Storage::disk('public')->exists($aju->berkas_zip) ? Storage::disk('public')->size($aju->berkas_zip) : 0, 'url' => Storage::disk('public')->url($aju->berkas_zip)];
                        }
                    } elseif ($jenis === 'penunjukan') {
                        $pjKadesChecklists = ChecklistPjKades::whereNotNull('file_path')->whereHas('pjKades', function ($q) use ($desa) {
                            $q->where('desa_id', $desa->id)
                                ->where('status', '!=', 'draft')
                                ->whereNotIn('posisi_surat', ['Front Office (FO)', 'Berkas Diterima', 'Verifikasi & Validasi Petugas'])
                                ->whereNotNull('posisi_surat');
                        })->get();
                        foreach ($pjKadesChecklists as $chk) {
                            $files[] = ['name' => '[SK-Kades] '.basename($chk->file_path), 'path' => $chk->file_path, 'size' => Storage::disk('public')->exists($chk->file_path) ? Storage::disk('public')->size($chk->file_path) : 0, 'url' => Storage::disk('public')->url($chk->file_path)];
                        }

                        $pjs = PjKades::whereNotNull('berkas_zip')->where('desa_id', $desa->id)->where('status', '!=', 'draft')
                            ->whereNotIn('posisi_surat', ['Front Office (FO)', 'Berkas Diterima', 'Verifikasi & Validasi Petugas'])->whereNotNull('posisi_surat')->get();
                        foreach ($pjs as $pj) {
                            $files[] = ['name' => '[SK-Kades_ZIP] '.basename($pj->berkas_zip), 'path' => $pj->berkas_zip, 'size' => Storage::disk('public')->exists($pj->berkas_zip) ? Storage::disk('public')->size($pj->berkas_zip) : 0, 'url' => Storage::disk('public')->url($pj->berkas_zip)];
                        }
                    }
                } elseif ($module === 'perangkat_desa') {
                    $mapJenis = ['pengangkatan' => 1, 'rotasi' => 2, 'pemberhentian' => 3];
                    if (isset($mapJenis[$jenis])) {
                        $ajuanChecklists = ChecklistAjuan::whereNotNull('file_path')->whereHas('ajuan', function ($q) use ($desa, $mapJenis, $jenis) {
                            $q->where('desa_id', $desa->id)
                                ->where('status', '!=', 'draft')
                                ->where('jenis_layanan_id', $mapJenis[$jenis])
                                ->whereHas('milestoneTrackings', function ($sq) {
                                    $sq->where('tahap', '>=', 3);
                                });
                        })->get();
                        foreach ($ajuanChecklists as $chk) {
                            $files[] = ['name' => '[e-Rekom] '.basename($chk->file_path), 'path' => $chk->file_path, 'size' => Storage::disk('public')->exists($chk->file_path) ? Storage::disk('public')->size($chk->file_path) : 0, 'url' => Storage::disk('public')->url($chk->file_path)];
                        }

                        $ajuans = Ajuan::whereNotNull('berkas_zip')->where('desa_id', $desa->id)->where('status', '!=', 'draft')->where('jenis_layanan_id', $mapJenis[$jenis])
                            ->whereHas('milestoneTrackings', function ($sq) {
                                $sq->where('tahap', '>=', 3);
                            })->get();
                        foreach ($ajuans as $aju) {
                            $files[] = ['name' => '[e-Rekom_ZIP] '.basename($aju->berkas_zip), 'path' => $aju->berkas_zip, 'size' => Storage::disk('public')->exists($aju->berkas_zip) ? Storage::disk('public')->size($aju->berkas_zip) : 0, 'url' => Storage::disk('public')->url($aju->berkas_zip)];
                        }
                    }
                } elseif ($module === 'bpd') {
                    if (in_array($jenis, ['pemberhentian', 'peresmian'])) {
                        $bpdChecklists = ChecklistAjuanBpd::whereNotNull('file_path')->whereHas('ajuanBpd', function ($q) use ($desa, $jenis) {
                            $q->where('desa_id', $desa->id)
                                ->where('status', '!=', 'draft')
                                ->where('jenis_ajuan', $jenis)
                                ->whereHas('milestones', function ($sq) {
                                    $sq->where('tahapan', 'like', '%Draft%')->orWhere('tahapan', 'like', '%Validasi%')->orWhere('tahapan', 'like', '%Bupati%');
                                });
                        })->get();
                        foreach ($bpdChecklists as $chk) {
                            $files[] = ['name' => '[BPD] '.basename($chk->file_path), 'path' => $chk->file_path, 'size' => Storage::disk('public')->exists($chk->file_path) ? Storage::disk('public')->size($chk->file_path) : 0, 'url' => Storage::disk('public')->url($chk->file_path)];
                        }

                        $bpds = AjuanBpd::where('desa_id', $desa->id)->where('status', '!=', 'draft')->where('jenis_ajuan', $jenis)->whereHas('milestones', function ($sq) {
                            $sq->where('tahapan', 'like', '%Draft%')->orWhere('tahapan', 'like', '%Validasi%')->orWhere('tahapan', 'like', '%Bupati%');
                        })->get();
                        foreach ($bpds as $bpd) {
                            if ($bpd->berkas_zip) {
                                $files[] = ['name' => '[BPD_ZIP] '.basename($bpd->berkas_zip), 'path' => $bpd->berkas_zip, 'size' => Storage::disk('public')->exists($bpd->berkas_zip) ? Storage::disk('public')->size($bpd->berkas_zip) : 0, 'url' => Storage::disk('public')->url($bpd->berkas_zip)];
                            }
                        }
                    }
                }
            }
            if (Storage::disk('public')->exists($path)) {
                foreach (Storage::disk('public')->directories($path) as $dir) {
                    $folders[] = ['name' => basename($dir), 'path' => $dir, 'count' => count(Storage::disk('public')->allFiles($dir))];
                }
                foreach (Storage::disk('public')->files($path) as $file) {
                    $files[] = ['name' => basename($file), 'path' => $file, 'size' => Storage::disk('public')->size($file), 'url' => Storage::disk('public')->url($file)];
                }
            }
        } else {
            // Beyond level 5
            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path);
            }
            foreach (Storage::disk('public')->directories($path) as $dir) {
                $folders[] = ['name' => basename($dir), 'path' => $dir, 'count' => count(Storage::disk('public')->allFiles($dir))];
            }
            foreach (Storage::disk('public')->files($path) as $file) {
                $files[] = ['name' => basename($file), 'path' => $file, 'size' => Storage::disk('public')->size($file), 'url' => Storage::disk('public')->url($file)];
            }
        }

        $breadcrumbs = $this->buildBreadcrumbs($path);

        return view('admin.drive.index', compact('folders', 'files', 'breadcrumbs', 'path'));
    }

    public function upload(Request $request)
    {
        $request->validate(['file' => 'required|file', 'path' => 'required|string']);
        $path = $request->input('path', 'dokumen');
        $file = $request->file('file');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->storeAs($path, $filename, 'public');

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function downloadZip(Request $request)
    {
        $path = $request->query('path', 'dokumen');
        $label = $request->query('label', 'drive-dokumen');
        $parts = explode('/', trim($path, '/'));

        $tempDir = storage_path('app/temp');
        if (! file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $cleanLabel = preg_replace('/[^A-Za-z0-9_\-]/', '_', $label);
        $zipName = $tempDir.'/'.$cleanLabel.'_'.now()->format('Ymd_His').'.zip';
        $zip = new \ZipArchive;

        if ($zip->open($zipName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat ZIP.');
        }

        $hasFiles = false;

        // Add physical files
        if (Storage::disk('public')->exists($path)) {
            $allPhysical = Storage::disk('public')->allFiles($path);
            foreach ($allPhysical as $file) {
                $realPath = Storage::disk('public')->path($file);
                if (file_exists($realPath)) {
                    $relativePath = str_replace($path.'/', '', $file);
                    $zip->addFile($realPath, $relativePath);
                    $hasFiles = true;
                }
            }
        }

        // Add virtual files based on level
        $virtualFiles = [];
        if (count($parts) >= 3) {
            $kecamatanName = $parts[1];
            $desaName = $parts[2];
            $desa = Desa::where('nama_desa', str_replace('_', ' ', $desaName))
                ->whereHas('kecamatan', function ($q) use ($kecamatanName) {
                    $q->where('nama_kecamatan', str_replace('_', ' ', $kecamatanName));
                })->first();
            if ($desa) {
                if (count($parts) === 3) {
                    $virtualFiles = $this->getVirtualFilesArray($desa->id);
                } elseif (count($parts) === 4) {
                    $virtualFiles = $this->getVirtualFilesArray($desa->id, $parts[3]);
                } elseif (count($parts) === 5) {
                    $virtualFiles = $this->getVirtualFilesArray($desa->id, $parts[3], $parts[4]);
                }
            }
        }

        foreach ($virtualFiles as $vf) {
            if (file_exists($vf['real_path'])) {
                $zip->addFile($vf['real_path'], $vf['relative_name']);
                $hasFiles = true;
            }
        }

        if (! $hasFiles) {
            $zip->close();

            return back()->with('error', 'Tidak ada file untuk diunduh.');
        }
        $zip->close();

        return response()->download($zipName, $label.'.zip')->deleteFileAfterSend(true);
    }

    public function delete(Request $request)
    {
        $path = $request->input('file_path');
        if (empty($path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return back()->with('success', 'File berhasil dihapus.');
        }

        return back()->with('error', 'File tidak ditemukan di server.');
    }

    private function buildBreadcrumbs($path)
    {
        if (empty($path)) {
            return [];
        }

        $parts = explode('/', trim($path, '/'));
        $breadcrumbs = [];
        $currentPath = '';

        foreach ($parts as $index => $part) {
            $currentPath .= ($index === 0 ? '' : '/').$part;

            $label = ucwords(str_replace('_', ' ', $part));
            if ($index === 0 && strtolower($part) === 'dokumen') {
                $label = 'Arsip Dokumen';
            }

            $breadcrumbs[] = [
                'label' => $label,
                'path' => $currentPath,
            ];
        }

        return $breadcrumbs;
    }

    private function getVirtualFolderCount($desaId, $module, $jenis = null)
    {
        $count = 0;
        if ($module === 'kades') {
            if ($jenis === 'pemberhentian' || $jenis === null) {
                $count += ChecklistAjuan::whereNotNull('file_path')->whereHas('ajuan', function ($q) use ($desaId) {
                    $q->where('desa_id', $desaId)->where('status', '!=', 'draft')->whereIn('jenis_layanan_id', [4, 5])
                        ->whereHas('milestoneTrackings', function ($sq) {
                            $sq->where('tahap', '>=', 3);
                        });
                })->count();
                $count += Ajuan::whereNotNull('berkas_zip')->where('desa_id', $desaId)->where('status', '!=', 'draft')->whereIn('jenis_layanan_id', [4, 5])
                    ->whereHas('milestoneTrackings', function ($sq) {
                        $sq->where('tahap', '>=', 3);
                    })->count();
            }
            if ($jenis === 'penunjukan' || $jenis === null) {
                $count += ChecklistPjKades::whereNotNull('file_path')->whereHas('pjKades', function ($q) use ($desaId) {
                    $q->where('desa_id', $desaId)->where('status', '!=', 'draft')
                        ->whereNotIn('posisi_surat', ['Front Office (FO)', 'Berkas Diterima', 'Verifikasi & Validasi Petugas'])
                        ->whereNotNull('posisi_surat');
                })->count();
                $count += PjKades::whereNotNull('berkas_zip')->where('desa_id', $desaId)->where('status', '!=', 'draft')
                    ->whereNotIn('posisi_surat', ['Front Office (FO)', 'Berkas Diterima', 'Verifikasi & Validasi Petugas'])->whereNotNull('posisi_surat')->count();
            }
        } elseif ($module === 'perangkat_desa') {
            $mapJenis = ['pengangkatan' => 1, 'rotasi' => 2, 'pemberhentian' => 3];
            $jenisList = $jenis ? [$jenis => $mapJenis[$jenis]] : $mapJenis;
            foreach ($jenisList as $j => $layananId) {
                $count += ChecklistAjuan::whereNotNull('file_path')->whereHas('ajuan', function ($q) use ($desaId, $layananId) {
                    $q->where('desa_id', $desaId)->where('status', '!=', 'draft')->where('jenis_layanan_id', $layananId)
                        ->whereHas('milestoneTrackings', function ($sq) {
                            $sq->where('tahap', '>=', 3);
                        });
                })->count();
                $count += Ajuan::whereNotNull('berkas_zip')->where('desa_id', $desaId)->where('status', '!=', 'draft')->where('jenis_layanan_id', $layananId)
                    ->whereHas('milestoneTrackings', function ($sq) {
                        $sq->where('tahap', '>=', 3);
                    })->count();
            }
        } elseif ($module === 'bpd') {
            $jenisList = $jenis ? [$jenis] : ['pemberhentian', 'peresmian'];
            foreach ($jenisList as $j) {
                $count += ChecklistAjuanBpd::whereNotNull('file_path')->whereHas('ajuanBpd', function ($q) use ($desaId, $j) {
                    $q->where('desa_id', $desaId)->where('status', '!=', 'draft')->where('jenis_ajuan', $j)
                        ->whereHas('milestones', function ($sq) {
                            $sq->where('tahapan', 'like', '%Draft%')->orWhere('tahapan', 'like', '%Validasi%')->orWhere('tahapan', 'like', '%Bupati%');
                        });
                })->count();
                $count += AjuanBpd::whereNotNull('berkas_zip')->where('desa_id', $desaId)->where('status', '!=', 'draft')->where('jenis_ajuan', $j)
                    ->whereHas('milestones', function ($sq) {
                        $sq->where('tahapan', 'like', '%Draft%')->orWhere('tahapan', 'like', '%Validasi%')->orWhere('tahapan', 'like', '%Bupati%');
                    })->count();
            }
        } elseif ($module === 'pembinaan') {
            $pembinaans = PengajuanPembinaan::where('desa_id', $desaId)->where('status', '!=', 'draft')->get();
            foreach ($pembinaans as $pem) {
                if ($pem->file_surat_permohonan) {
                    $count++;
                }
                if ($pem->file_undangan) {
                    $count++;
                }
            }
        }

        return $count;
    }

    private function getVirtualFilesArray($desaId, $module = null, $jenis = null)
    {
        $files = [];
        $modules = $module ? [$module] : ['kades', 'perangkat_desa', 'bpd', 'pembinaan'];

        foreach ($modules as $m) {
            if ($m === 'kades') {
                if ($jenis === 'pemberhentian' || $jenis === null) {
                    $ajuans = Ajuan::where('desa_id', $desaId)->where('status', '!=', 'draft')->whereIn('jenis_layanan_id', [4, 5])
                        ->whereHas('milestoneTrackings', function ($sq) {
                            $sq->where('tahap', '>=', 3);
                        })->get();
                    foreach ($ajuans as $aju) {
                        if ($aju->berkas_zip) {
                            $files[] = ['real_path' => Storage::disk('public')->path($aju->berkas_zip), 'relative_name' => 'Kades/Pemberhentian/[e-Rekom_ZIP] '.basename($aju->berkas_zip)];
                        }
                        $checklists = ChecklistAjuan::where('ajuan_id', $aju->id)->whereNotNull('file_path')->get();
                        foreach ($checklists as $chk) {
                            $files[] = ['real_path' => Storage::disk('public')->path($chk->file_path), 'relative_name' => 'Kades/Pemberhentian/[e-Rekom] '.basename($chk->file_path)];
                        }
                    }
                }
                if ($jenis === 'penunjukan' || $jenis === null) {
                    $pjs = PjKades::where('desa_id', $desaId)->where('status', '!=', 'draft')
                        ->whereNotIn('posisi_surat', ['Front Office (FO)', 'Berkas Diterima', 'Verifikasi & Validasi Petugas'])->whereNotNull('posisi_surat')->get();
                    foreach ($pjs as $pj) {
                        if ($pj->berkas_zip) {
                            $files[] = ['real_path' => Storage::disk('public')->path($pj->berkas_zip), 'relative_name' => 'Kades/Penunjukan/[SK-Kades_ZIP] '.basename($pj->berkas_zip)];
                        }
                        $checklists = ChecklistPjKades::where('pj_kades_id', $pj->id)->whereNotNull('file_path')->get();
                        foreach ($checklists as $chk) {
                            $files[] = ['real_path' => Storage::disk('public')->path($chk->file_path), 'relative_name' => 'Kades/Penunjukan/[SK-Kades] '.basename($chk->file_path)];
                        }
                    }
                }
            } elseif ($m === 'perangkat_desa') {
                $mapJenis = ['pengangkatan' => 1, 'rotasi' => 2, 'pemberhentian' => 3];
                $jenisList = $jenis ? [$jenis => $mapJenis[$jenis]] : $mapJenis;
                foreach ($jenisList as $j => $layananId) {
                    $ajuans = Ajuan::where('desa_id', $desaId)->where('status', '!=', 'draft')->where('jenis_layanan_id', $layananId)
                        ->whereHas('milestoneTrackings', function ($sq) {
                            $sq->where('tahap', '>=', 3);
                        })->get();
                    foreach ($ajuans as $aju) {
                        $fName = ucwords($j);
                        if ($aju->berkas_zip) {
                            $files[] = ['real_path' => Storage::disk('public')->path($aju->berkas_zip), 'relative_name' => 'Perangkat Desa/'.$fName.'/[e-Rekom_ZIP] '.basename($aju->berkas_zip)];
                        }
                        $checklists = ChecklistAjuan::where('ajuan_id', $aju->id)->whereNotNull('file_path')->get();
                        foreach ($checklists as $chk) {
                            $files[] = ['real_path' => Storage::disk('public')->path($chk->file_path), 'relative_name' => 'Perangkat Desa/'.$fName.'/[e-Rekom] '.basename($chk->file_path)];
                        }
                    }
                }
            } elseif ($m === 'bpd') {
                $jenisList = $jenis ? [$jenis] : ['pemberhentian', 'peresmian'];
                foreach ($jenisList as $j) {
                    $bpds = AjuanBpd::where('desa_id', $desaId)->where('status', '!=', 'draft')->where('jenis_ajuan', $j)
                        ->whereHas('milestones', function ($sq) {
                            $sq->where('tahapan', 'like', '%Draft%')->orWhere('tahapan', 'like', '%Validasi%')->orWhere('tahapan', 'like', '%Bupati%');
                        })->get();
                    foreach ($bpds as $bpd) {
                        $fName = ucwords($j);
                        if ($bpd->berkas_zip) {
                            $files[] = ['real_path' => Storage::disk('public')->path($bpd->berkas_zip), 'relative_name' => 'BPD/'.$fName.'/[BPD_ZIP] '.basename($bpd->berkas_zip)];
                        }
                        $checklists = ChecklistAjuanBpd::where('ajuan_bpd_id', $bpd->id)->whereNotNull('file_path')->get();
                        foreach ($checklists as $chk) {
                            $files[] = ['real_path' => Storage::disk('public')->path($chk->file_path), 'relative_name' => 'BPD/'.$fName.'/[BPD] '.basename($chk->file_path)];
                        }
                    }
                }
            } elseif ($m === 'pembinaan') {
                $pembinaans = PengajuanPembinaan::where('desa_id', $desaId)->where('status', '!=', 'draft')->get();
                foreach ($pembinaans as $pem) {
                    if ($pem->file_surat_permohonan) {
                        $files[] = ['real_path' => Storage::disk('public')->path($pem->file_surat_permohonan), 'relative_name' => 'Pembinaan/[Pembinaan] '.basename($pem->file_surat_permohonan)];
                    }
                    if ($pem->file_undangan) {
                        $files[] = ['real_path' => Storage::disk('public')->path($pem->file_undangan), 'relative_name' => 'Pembinaan/[Pembinaan_Undangan] '.basename($pem->file_undangan)];
                    }
                }
            }
        }

        return $files;
    }
}

<?php

namespace Database\Seeders;

use App\Models\Ajuan;
use App\Models\ChecklistAjuan;
use App\Models\Desa;
use App\Models\JenisLayanan;
use App\Models\PerangkatDesa;
use App\Models\TemplateChecklist;
use Illuminate\Database\Seeder;

class DummyAjuanSeeder extends Seeder
{
    public function run()
    {
        $desa = Desa::first();
        $layanan = JenisLayanan::where('nama', 'Pengangkatan')->first();
        $perangkat = PerangkatDesa::where('desa_id', $desa->id)->first();

        if (! $perangkat) {
            $perangkat = PerangkatDesa::create([
                'desa_id' => $desa->id,
                'nama' => 'Dummy Perangkat',
                'jabatan' => 'Kasi Pelayanan',
                'status_aktif' => true,
            ]);
        }

        $ajuan = Ajuan::create([
            'no_registrasi' => 'PGKT/2026/07/0099',
            'desa_id' => $desa->id,
            'jenis_layanan_id' => $layanan->id,
            'perangkat_desa_id' => $perangkat->id,
            'status' => 'submitted',
            'posisi_surat' => 'Pegawai',
            'tgl_diajukan' => now(),
            'tgl_sla_batas' => now()->addDays(28),
        ]);

        $templates = TemplateChecklist::where('jenis_layanan_id', $layanan->id)->get();

        foreach ($templates as $idx => $t) {
            ChecklistAjuan::create([
                'ajuan_id' => $ajuan->id,
                'template_checklist_id' => $t->id,
                // Simulasi file terunggah di beberapa checklist awal
                'file_path' => $idx < 3 ? 'arsip/dummy.pdf' : null,
                'status' => 'menunggu',
            ]);
        }
    }
}

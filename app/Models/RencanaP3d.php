<?php

namespace App\Models;

use App\Models\Scopes\TenantDesaScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy(TenantDesaScope::class)]
class RencanaP3d extends Model
{
    protected $table = 'rencana_p3ds';

    protected $fillable = [
        'desa_id',
        'kecamatan_id',
        'jumlah_formasi_kosong',
        'jabatan_kosong',
        'rencana_pelaksanaan_mulai',
        'rencana_pelaksanaan_selesai',
        'rencana_anggaran',
        'keterangan',
        'status',
        'tahun',
    ];

    protected $casts = [
        'rencana_pelaksanaan_mulai' => 'date',
        'rencana_pelaksanaan_selesai' => 'date',
        'rencana_anggaran' => 'decimal:2',
        'jumlah_formasi_kosong' => 'integer',
        'tahun' => 'integer',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class, 'desa_id');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }
}

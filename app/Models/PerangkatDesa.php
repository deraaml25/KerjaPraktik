<?php

namespace App\Models;

use App\Models\Scopes\TenantDesaScope;
use Illuminate\Database\Eloquent\Model;

class PerangkatDesa extends Model
{
    protected $fillable = [
        'desa_id',
        'nama',
        'jabatan',
        'no_sk_terakhir',
        'file_sk',
        'tgl_mulai_jabatan',
        'status_aktif',
        'status_verifikasi',
        'draft_perubahan',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantDesaScope);
    }

    protected $casts = [
        'status_aktif' => 'boolean',
        'tgl_mulai_jabatan' => 'date',
        'draft_perubahan' => 'array',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function ajuans()
    {
        return $this->hasMany(Ajuan::class);
    }
}

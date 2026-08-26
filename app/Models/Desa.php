<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $fillable = ['nama_desa', 'kecamatan_id'];

    public function getNamaDesaAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function perangkatDesas()
    {
        return $this->hasMany(PerangkatDesa::class);
    }

    public function ajuans()
    {
        return $this->hasMany(Ajuan::class);
    }
}

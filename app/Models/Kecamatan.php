<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $fillable = ['nama_kecamatan'];

    public function getNamaKecamatanAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    public function desas()
    {
        return $this->hasMany(Desa::class);
    }
}

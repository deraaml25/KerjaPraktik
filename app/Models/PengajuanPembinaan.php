<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanPembinaan extends Model
{
    protected $table = 'pengajuan_pembinaans';

    protected $fillable = [
        'desa_id',
        'user_id',
        'judul_kegiatan',
        'deskripsi',
        'tanggal_diajukan',
        'file_surat_permohonan',
        'file_undangan',
        'status',
        'catatan_admin',
        'dibalas_at',
    ];

    protected $casts = [
        'tanggal_diajukan' => 'date',
        'dibalas_at' => 'datetime',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label warna status pengajuan.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'selesai' => 'Selesai',
            default => $this->status,
        };
    }

    /**
     * Warna badge status.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'bg-yellow-100 text-yellow-800',
            'disetujui' => 'bg-green-100 text-green-800',
            'ditolak' => 'bg-red-100 text-red-800',
            'selesai' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-600',
        };
    }
}

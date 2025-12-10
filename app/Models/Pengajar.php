<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajar extends Model
{
    use HasFactory;

    // Pastikan nama tabel sesuai migration. Kalau migration pakai 'pengajars' ganti di sini.
    protected $table = 'pengajar';

    protected $fillable = [
        'user_id',
        'mata_pelajaran',
        'pendidikan_terakhir',
        'pengalaman_tahun',
        'deskripsi',
        'tarif_per_jam',
        'status_verifikasi',
        'keahlian_khusus',
        'sertifikat',
    ];

    protected $casts = [
        'pengalaman_tahun' => 'integer',
        'tarif_per_jam' => 'decimal:2',
        'status_verifikasi' => 'boolean',
    ];

    // Append supaya attribute accessor muncul di JSON / toArray()
    protected $appends = ['average_rating','total_ulasan'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rekomendasi()
    {
        return $this->hasMany(Rekomendasi::class, 'pengajar_id');
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'pengajar_id');
    }

    public function permintaan()
    {
        return $this->hasMany(Permintaan::class, 'pengajar_id');
    }

    // Accessor: average rating (float)
    public function getAverageRatingAttribute()
    {
        $avg = $this->ulasan()->avg('rating');
        return $avg ? round((float)$avg, 2) : 0.0;
    }

    // Accessor: total ulasan (int)
    public function getTotalUlasanAttribute()
    {
        return (int) $this->ulasan()->count();
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('status_verifikasi', true);
    }

    public function scopeByMataPelajaran($query, $mataPelajaran)
    {
        return $query->where('mata_pelajaran', 'LIKE', "%{$mataPelajaran}%");
    }
}

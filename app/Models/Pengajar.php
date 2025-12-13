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

    // KRITIS: Tambahkan 'latitude' dan 'longitude' agar muncul saat model di-load
    // dan agar bisa dipanggil dengan $pengajar->latitude (memanggil Accessor di bawah)
    protected $appends = ['average_rating','total_ulasan', 'latitude', 'longitude'];

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

    // ACCESSOR KRITIS 1: Mengambil Latitude dari relasi User
    public function getLatitudeAttribute()
    {
        // Menggunakan optional() untuk keamanan jika relasi user belum dimuat
        return optional($this->user)->latitude;
    }

    // ACCESSOR KRITIS 2: Mengambil Longitude dari relasi User
    public function getLongitudeAttribute()
    {
        // Mengambil data lokasi dari tabel users melalui relasi
        return optional($this->user)->longitude;
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekomendasi extends Model
{
    use HasFactory;

    protected $table = 'rekomendasi';

    protected $fillable = [
        'user_id',
        'pengajar_id',
        'nilai_kemiripan',
        'jarak_km',
        'tanggal_rekomendasi',
    ];

    protected $casts = [
        'nilai_kemiripan' => 'decimal:4',
        'jarak_km' => 'decimal:2',
        'tanggal_rekomendasi' => 'datetime',
    ];

    /**
     * Relationship: Rekomendasi belongs to User (pelajar)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Rekomendasi belongs to Pengajar
     */
    public function pengajar()
    {
        return $this->belongsTo(Pengajar::class);
    }

    /**
     * Scope: Order by nilai kemiripan (highest first)
     */
    public function scopeTopRecommendations($query, $limit = 5)
    {
        return $query->orderBy('nilai_kemiripan', 'desc')->limit($limit);
    }

    /**
     * Scope: Recent recommendations
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('tanggal_rekomendasi', '>=', now()->subDays($days));
    }
}

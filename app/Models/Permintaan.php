<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    use HasFactory;

    protected $table = 'permintaan';

    protected $fillable = [
        'user_id',
        'pengajar_id',
        'mata_pelajaran',
        'deskripsi',
        'jadwal_diinginkan',
        'status',
        'catatan_pengajar',
    ];

    protected $casts = [
        'jadwal_diinginkan' => 'datetime',
    ];

    /**
     * Relationship: Permintaan belongs to User (pelajar)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Permintaan belongs to Pengajar
     */
    public function pengajar()
    {
        return $this->belongsTo(Pengajar::class);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Accepted requests
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'diterima');
    }

    /**
     * Scope: Rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'ditolak');
    }
}

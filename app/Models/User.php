<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'umur',
        'alamat',
        'latitude',
        'longitude',
        'no_telepon',
        'foto_profil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Role helpers
    public function isAdmin() { return $this->role === 'admin'; }
    public function isPengajar() { return $this->role === 'pengajar'; }
    public function isPelajar() { return $this->role === 'pelajar'; }

    // Relationships
    public function pengajar()
    {
        return $this->hasOne(Pengajar::class, 'user_id');
    }

    public function rekomendasi()
    {
        return $this->hasMany(Rekomendasi::class, 'user_id');
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'user_id');
    }

    public function permintaan()
    {
        return $this->hasMany(Permintaan::class, 'user_id');
    }
}

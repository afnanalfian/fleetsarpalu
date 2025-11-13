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
        'phone',
        'NIP',
        'password',
        'team_id',
        'role', // admin, sumda, ketua_tim, pegawai
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /* =======================
       🔗 RELASI ANTAR MODEL
       ======================= */

    /**
     * Relasi ke tim (jika user adalah anggota tim)
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Relasi ke peminjaman kendaraan (pegawai)
     */
    public function borrowRequests()
    {
        return $this->hasMany(BorrowRequest::class);
    }

    /**
     * Relasi ke laporan kehadiran pengecekan (ketua/anggota tim)
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Jika user adalah ketua tim, maka relasi ke tim yang dipimpinnya
     */
    public function ledTeam()
    {
        return $this->hasOne(Team::class, 'leader_id');
    }

    /* =======================
       ⚙️ HELPER DAN ROLE
       ======================= */

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah Sumda (pimpinan sumber daya)
     */
    public function isSumda()
    {
        return $this->role === 'sumda';
    }

    /**
     * Cek apakah user adalah Ketua Tim
     */
    public function isKetuaTim()
    {
        return $this->role === 'ketua_tim';
    }

    /**
     * Cek apakah user adalah Pegawai biasa
     */
    public function isPegawai()
    {
        return $this->role === 'pegawai';
    }

    /**
     * Dapatkan label peran dalam bahasa Indonesia untuk UI
     */
    public function getRoleLabelAttribute()
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'sumda' => 'Pimpinan Sumber Daya',
            'ketua_tim' => 'Ketua Tim',
            'pegawai' => 'Pegawai',
            default => ucfirst($this->role),
        };
    }

    /**
     * Dapatkan nama tim user (kalau ada)
     */
    public function getTeamNameAttribute()
    {
        return $this->team ? $this->team->name : '-';
    }
}

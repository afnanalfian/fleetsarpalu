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
    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }

    /* =======================
       ⚙️ HELPER DAN ROLE
       ======================= */

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin()
    {
        return $this->role === 'Admin';
    }

    /**
     * Cek apakah user adalah Sumda (pimpinan sumber daya)
     */
    public function isSumda()
    {
        return $this->role === 'Kepala Sumber Daya';
    }

    /**
     * Cek apakah user adalah Ketua Tim
     */
    public function isKetuaTim()
    {
        return $this->role === 'Ketua Tim';
    }

    /**
     * Cek apakah user adalah Pegawai biasa
     */
    public function isPegawai()
    {
        return $this->role === 'Pegawai';
    }

    /**
     * Dapatkan label peran dalam bahasa Indonesia untuk UI
     */
    public function getRoleLabelAttribute()
    {
        return match ($this->role) {
            'Admin' => 'Administrator',
            'Kepala Sumber Daya' => 'Pimpinan Sumber Daya',
            'Ketua Tim' => 'Ketua Tim',
            'Pegawai' => 'Pegawai',
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
    public function hasRole($roles)
    {
        $roles = is_array($roles) ? $roles : explode(',', $roles);
        return in_array(strtolower($this->role), array_map('strtolower', $roles));
    }
    public function isSameTeam($teamId)
    {
        // Jika admin atau kepala sumber daya → boleh semua
        if (in_array(strtolower($this->role), ['admin', 'kepala sumber daya'])) {
            return true;
        }

        // Selain itu (pegawai & ketua tim) → hanya tim sendiri
        return $this->team_id == $teamId;
    }
}

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method bool hasVerifiedEmail()
 * @method void markEmailAsVerified()
 * @method void sendEmailVerificationNotification()
 */


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'NIP',
        'password',
        'team_id',
        'role',
        'institution',
        'verification_token',
        'email_verified_at',
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

    public function isAdmin() { return strtolower($this->role) === 'admin'; }
    public function isSumda() { return strtolower($this->role) === 'kepala sumber daya'; }
    public function isKetuaTim() { return strtolower($this->role) === 'ketua tim'; }
    public function isPegawai() { return strtolower($this->role) === 'pegawai'; }
    public function isExternal() { return strtolower($this->role) === 'external'; }

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
            'External' => 'External',
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

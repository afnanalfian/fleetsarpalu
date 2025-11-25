<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'leader_id',
    ];

    /**
     * 🔗 Relasi ke ketua tim (user yang jadi leader)
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * 🔗 Relasi ke semua anggota tim
     */
    public function members()
    {
        return $this->hasMany(User::class);
    }

    /**
     * 🔗 Relasi ke jadwal pengecekan (check records)
     */
    public function checks()
    {
        return $this->hasMany(Check::class);
    }

    /**
     * Helper: nama ketua tim atau placeholder
     */
    public function getLeaderNameAttribute()
    {
        return $this->leader ? $this->leader->name : '-';
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }

}

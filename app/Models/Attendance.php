<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_id',
        'user_id',
        'present',
        'reason',
    ];

    /**
     * 🔗 Relasi ke pengecekan
     */
    public function check()
    {
        return $this->belongsTo(Check::class);
    }

    /**
     * 🔗 Relasi ke user (anggota tim)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

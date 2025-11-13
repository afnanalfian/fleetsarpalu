<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'scheduled_date',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $dates = ['date'];

    /**
     * 🔗 Relasi ke tim yang melakukan pengecekan
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * 🔗 Relasi ke daftar anggota (attendance)
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * 🔗 Relasi ke hasil pengecekan per kendaraan
     */
    public function checkItems()
    {
        return $this->hasMany(CheckItem::class);
    }

    /**
     * Helper: apakah pengecekan sudah selesai
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

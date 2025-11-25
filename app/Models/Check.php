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
        'shift',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $dates = ['date'];
    protected $casts = [
        'scheduled_date' => 'date',
        'started_at'     => 'datetime',
        'completed_at'   => 'datetime',
    ];

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
    public function updateStatus()
    {
        // Pending → In Progress
        if ($this->status === 'pending') {

            $hasAttendance = $this->attendances()->exists();
            $hasItem = $this->items()->exists();

            if ($hasAttendance || $hasItem) {
                $this->update([
                    'status' => 'in_progress',
                    'started_at' => now(),
                ]);
            }
        }

        // In Progress → Completed
        if ($this->status === 'in_progress') {

            $teamMembers = $this->team->users()->count();
            $attendanceCount = $this->attendances()->count();

            $vehicleCount = $this->team->vehicles()->count();
            $itemsCount = $this->items()->count();

            if ($attendanceCount == $teamMembers && $itemsCount == $vehicleCount) {
                $this->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }
        }
    }

}

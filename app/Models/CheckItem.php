<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_id',
        'vehicle_id',
        'fuel',
        'km',
        'lampu_hazard',
        'lampu_hazard_note',
        'radiator_ok',
        'radiator_note',
        'air_filter_ok',
        'air_filter_note',
        'wiper_ok',
        'wiper_note',
        'lights_ok',
        'lights_note',
        'leaks_ok',
        'leaks_note',
        'glass_celanliness_ok',
        'glass_cleanliness_note',
        'body_cleanliness_ok',
        'body_cleanliness_note',
        'klakson',
        'klakson_note',
        'sirine',
        'sirine_note',
        'ban',
        'ban_note',
        'rem',
        'rem_note',
        'aki',
        'aki_note',
        'start_engine',
        'start_engine_note',
        'status', // Baik / Rusak
    ];

    /**
     * 🔗 Relasi ke laporan pengecekan utama
     */
    public function check()
    {
        return $this->belongsTo(Check::class);
    }

    /**
     * 🔗 Relasi ke kendaraan yang dicek
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Helper: menentukan status kondisi kendaraan
     */
    public function evaluateCondition()
    {
        $items = [
            'lampu_hazard', 'klakson', 'sirine',
            'ban', 'rem', 'aki', 'start_engine'
        ];

        foreach ($items as $item) {
            if ($this->{$item} === 'tidak_aman') {
                $this->status = 'Rusak';
                return;
            }
        }

        $this->status = 'Baik';
    }
}

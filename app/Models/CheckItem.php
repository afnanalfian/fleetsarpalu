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
        'fuel_percent',
        'km',
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
        'hazards_ok',
        'hazards_note',
        'horn_ok',
        'horn_note',
        'siren_ok',
        'siren_note',
        'tires_ok',
        'tires_note',
        'brakes_ok',
        'brakes_note',
        'battery_ok',
        'battery_note',
        'start_engine_ok',
        'start_engine_note',
        'glass_cleanliness_ok',
        'glass_cleanliness_note',
        'body_cleanliness_ok',
        'body_cleanliness_note',
        'interior_cleanliness_ok',
        'interior_cleanliness_note',
        'photos',
        'condition'
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
        // Ambil semua kolom yang diakhiri dengan _ok
        $okColumns = [
            'radiator_ok',
            'air_filter_ok',
            'wiper_ok',
            'lights_ok',
            'leaks_ok',
            'hazards_ok',
            'horn_ok',
            'siren_ok',
            'tires_ok',
            'brakes_ok',
            'battery_ok',
            'start_engine_ok',
            'glass_cleanliness_ok',
            'body_cleanliness_ok',
            'interior_cleanliness_ok',
        ];

        // Cek apakah ada salah satu kolom yang != 1
        foreach ($okColumns as $col) {
            if ($this->{$col} != 1) {
                $this->condition = 'Rusak';
                return;
            }
        }

        // Jika lolos semua
        $this->condition = 'Baik';
    }
}

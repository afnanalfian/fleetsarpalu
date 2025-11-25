<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UseReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrow_request_id',
        'user_id',
        'fuel_before',
        'fuel_after',
        'km_before',
        'km_after',
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
        'indicator_before_photos_path',
        'indicator_after_photos_path',
        'location_photos_path',
    ];

    /**
     * Relasi ke tabel borrow_requests
     */
    public function borrowRequest()
    {
        return $this->belongsTo(BorrowRequest::class);
    }

    /**
     * Relasi ke user (yang mengisi laporan)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper untuk menghitung selisih bahan bakar (%)
     */
    public function fuelDifference()
    {
        return is_numeric($this->fuel_before) && is_numeric($this->fuel_after)
            ? $this->fuel_before - $this->fuel_after
            : null;
    }

    /**
     * Helper untuk menentukan kondisi kendaraan
     * Jika semua *_ok bernilai 1 → "Aman"
     * Jika ada yang 0 → "Tidak Aman"
     */
    public function conditionSummary()
    {
        $checks = [
            'hazards_ok',
            'horn_ok',
            'siren_ok',
            'tires_ok',
            'brakes_ok',
            'battery_ok',
            'start_engine_ok',
        ];

        foreach ($checks as $check) {
            if ($this->{$check} == 0 || $this->{$check} === false) {
                return 'Tidak Aman';
            }
        }

        return 'Aman';
    }
}

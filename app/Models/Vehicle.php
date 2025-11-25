<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_bmn',
        'name',
        'distance',
        'year',
        'merk',
        'plat_nomor',
        'tipe',
        'factory',
        'load_capacity',
        'weight',
        'bahan_bakar',
        'lokasi',
        'warna',
        'status',
        'last_km_for_oil',
        'oil_change_interval',
        'fuel_percent',
        'notes',
        'photo_path',
    ];

    /**
     * 🔗 Relasi ke peminjaman kendaraan
     */
    public function borrowRequests()
    {
        return $this->hasMany(BorrowRequest::class);
    }

    /**
     * 🔗 Relasi ke pengecekan rutin
     */
    public function checkItems()
    {
        return $this->hasMany(CheckItem::class);
    }

    /**
     * ✅ Helper: apakah kendaraan sedang dipinjam
     */
    public function isBorrowed()
    {
        return $this->status === 'in_use';
    }

    /**
     * 🚫 Helper: apakah kendaraan rusak
     */
    public function isBroken()
    {
        return $this->condition === 'Rusak';
    }

    /**
     * 🔄 Helper: ubah kondisi kendaraan setelah laporan pemakaian
     */
    public function updateConditionFromReport(UseReport $report)
    {
        $this->condition = $report->conditionSummary();
        $this->status = $this->isBroken() ? 'unavailable' : 'available';
        $this->save();
    }

    /**
     * Label status kendaraan untuk UI
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'available' => 'Tersedia',
            'in_use' => 'Digunakan',
            'unavailable' => 'Tidak Tersedia',
            default => ucfirst($this->status),
        };
    }
}

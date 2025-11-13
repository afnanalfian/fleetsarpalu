<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowRequest extends Model
{
    use HasFactory;

    protected $table = 'borrow_requests';

    protected $fillable = [
        'kode_pinjam',
        'user_id',
        'team_id',
        'vehicle_id',
        'purpose_text',
        'destination_address',
        'start_at',
        'end_at',
        'start_time',
        'end_time',
        'surat_tugas_path',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // RELATIONS
    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function vehicle() { return $this->belongsTo(\App\Models\Vehicle::class); }
    public function team() { return $this->belongsTo(\App\Models\Team::class); }
    public function useReport() { return $this->hasOne(\App\Models\UseReport::class, 'borrow_request_id'); }

    // Helpers
    public function isPending() { return strcasecmp($this->status, 'Pending') === 0; }
    public function isApproved() { return strcasecmp($this->status, 'Approved') === 0; }
    public function isInUse() { return strcasecmp($this->status, 'In Use') === 0; }
    public function isCompleted() { return strcasecmp($this->status, 'Completed') === 0; }

    public function updateStatusAutomatically()
    {
        $now = now();

        // Ubah status ke "In Use" hanya ketika waktunya tiba
        if ($this->status === 'Approved' && $now->gte($this->start_at) && $now->lte($this->end_at)) {
            $this->update(['status' => 'In Use']);
        }
    }
    public function syncVehicleStatus()
    {
        if (!$this->vehicle) return;

        // Kalau kendaraan sedang rusak, jangan ubah status apapun
        if ($this->vehicle->status === 'unavailable') {
            return;
        }

        // Jika sedang digunakan
        if ($this->status === 'In Use') {
            $this->vehicle->update(['status' => 'is_use']);
        }

        // Jika sudah selesai atau status lain, kembalikan ke available
        elseif (in_array($this->status, ['Completed', 'Approved', 'Rejected', 'Cancelled', 'Pending'])) {
            $this->vehicle->update(['status' => 'available']);
        }
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'start_at'    => 'datetime:Y-m-d',
        'end_at'      => 'datetime:Y-m-d',
        'start_time'  => 'string',
        'end_time'    => 'string',
        'approved_at' => 'datetime',
    ];

    // RELATIONS
    public function user() { return $this->belongsTo(User::class); }
    public function vehicle() { return $this->belongsTo(Vehicle::class); }
    public function team() { return $this->belongsTo(Team::class); }
    public function useReport() { return $this->hasOne(UseReport::class, 'borrow_request_id'); }
    public function approvedBy(){ return $this->belongsTo(User::class, 'approved_by'); }

    // Helpers
    public function isPending() { return strcasecmp($this->status, 'Pending') === 0; }
    public function isApproved() { return strcasecmp($this->status, 'Approved') === 0; }
    public function isInUse() { return strcasecmp($this->status, 'In Use') === 0; }
    public function isCompleted() { return strcasecmp($this->status, 'Completed') === 0; }

    public function updateStatusAutomatically()
    {
        if (!$this->start_at || !$this->start_time || !$this->end_at || !$this->end_time) {
            return;
        }

        $start = Carbon::parse($this->start_at->format('Y-m-d').' '.$this->start_time);
        $end   = Carbon::parse($this->end_at->format('Y-m-d').' '.$this->end_time);
        $now   = now();

        // === Approved → In Use ===
        if ($this->status === 'Approved' && $now->between($start, $end)) {
            $this->update(['status' => 'In Use']);
        }

        if ($this->status === 'In Use' && $now->greaterThan($end)) {

            // Kirim notif ke PEMINJAM
            notify(
                $this->user_id,
                "Waktu Peminjaman Telah Lewat",
                "Peminjaman kendaraan {$this->vehicle->name} telah melebihi waktu pulang. Jangan lupa membuat laporan penggunaan.",
                route('borrowings.show', $this->id)
            );
        }
    }

    public function syncVehicleStatus()
    {
        if (!$this->vehicle) return;

        // Jika kendaraan sedang rusak, jangan diubah
        if ($this->vehicle->status === 'unavailable') {
            return;
        }

        // Jika kendaraan sedang dipakai
        if ($this->status === 'In Use') {
            $this->vehicle->update([
                'status' => 'is_use',
                'lokasi' => $this->destination_address ?? $this->vehicle->lokasi
            ]);
        }

        // Jika peminjaman selesai / tidak aktif / batal
        elseif (in_array($this->status, ['Completed', 'Approved', 'Rejected', 'Cancelled', 'Pending'])) {
            $this->vehicle->update([
                'status' => 'available',
                'lokasi' => 'Markas Utama'
            ]);
        }
    }

}

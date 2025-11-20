<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'date',
        'shift',
    ];

    // Relasi ke tim
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // Scope untuk hari ini
    public function scopeToday($query)
    {
        return $query->where('date', now()->toDateString());
    }
}

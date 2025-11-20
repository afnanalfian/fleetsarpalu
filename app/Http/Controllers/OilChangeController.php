<?php

namespace App\Http\Controllers;

use App\Models\OilChange;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class OilChangeController extends Controller
{
    public function store(Request $request, $vehicle_id)
    {
        $request->validate([
            'new_distance' => 'required|integer|min:0',
        ]);

        $vehicle = Vehicle::findOrFail($vehicle_id);

        // Buat record pergantian oli
        OilChange::create([
            'vehicle_id'   => $vehicle_id,
            'new_distance' => $request->new_distance,
            'date'         => now()->toDateString(),
        ]);

        // Update last_km_for_oil
        $vehicle->update([
            'last_km_for_oil' => $request->new_distance
        ]);

        return back()->with('success', 'Pergantian oli berhasil dicatat.');
    }
}

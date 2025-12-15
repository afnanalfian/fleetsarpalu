<?php

use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;

function notifyOilChangeVehicles()
{
    $vehicles = Vehicle::with([
        'oilChanges' => function ($q) {
            $q->latest('date')->limit(1);
        }
    ])->get();

    $users = User::pluck('id'); // SEMUA USER

    foreach ($vehicles as $vehicle) {

        $distanceSinceOilChange = $vehicle->distance - $vehicle->last_km_for_oil;
        $overKmInterval = $distanceSinceOilChange >= $vehicle->oil_change_interval;

        $lastOilChangeDate = $vehicle->oilChanges->first()?->date;
        $overDateInterval = false;

        if ($lastOilChangeDate) {
            $overDateInterval = Carbon::now()
                ->greaterThanOrEqualTo(
                    Carbon::parse($lastOilChangeDate)->addMonth()
                );
        }

        if ($overKmInterval || $overDateInterval) {

            $message = "Kendaraan {$vehicle->name} perlu ganti oli.";

            if ($overKmInterval) {
                $message .= " Jarak tempuh sejak ganti oli terakhir: {$distanceSinceOilChange} km.";
            }

            if ($overDateInterval) {
                $message .= " Sudah lebih dari 1 bulan sejak ganti oli terakhir (" .
                    Carbon::parse($lastOilChangeDate)->format('d M Y') . ").";
            }

            foreach ($users as $userId) {
                notify(
                    $userId,
                    'Peringatan Ganti Oli Kendaraan',
                    $message,
                    route('vehicles.show', $vehicle->id)
                );
            }
        }
    }
}

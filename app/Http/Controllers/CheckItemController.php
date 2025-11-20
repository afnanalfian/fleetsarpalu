<?php

namespace App\Http\Controllers;

use App\Models\Check;
use App\Models\CheckItem;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class CheckItemController extends Controller
{
    public function edit($id)
    {
        $item = CheckItem::findOrFail($id);
        $check = $item->check;
        $vehicle = $item->vehicle;

        // Daftar checklist
        $items = [
            'radiator' => 'Radiator',
            'air_filter' => 'Filter Udara',
            'wiper' => 'Wiper',
            'lights' => 'Lampu Kendaraan',
            'leaks' => 'Kebocoran Fluida',
            'hazards' => 'Lampu Hazard',
            'horn' => 'Klakson',
            'siren' => 'Sirine',
            'tires' => 'Ban',
            'brakes' => 'Rem',
            'battery' => 'Aki',
            'start_engine' => 'Starter Mesin',
            'glass_cleanliness' => 'Kebersihan Kaca',
            'body_cleanliness' => 'Kebersihan Body',
            'interior_cleanliness'=>'Kebersihan Interior'
        ];

        return view('checkitems.edit', compact('item', 'check', 'vehicle', 'items'));
    }


    /**
     * UPDATE — perbarui hasil cek kendaraan
     */
    public function update(Request $request, $id)
    {
        $item = CheckItem::findOrFail($id);

        $request->validate([
            'fuel_percent' => 'required|numeric|min:0|max:100',
            'km' => 'required|numeric|min:0',
            'photos.*' => 'nullable|image|max:4096'
        ]);

        $data = $request->only(['fuel_percent', 'km']);

        $keys = [
            'radiator','air_filter','wiper','lights','leaks','hazards','horn','siren',
            'tires','brakes','battery','start_engine','glass_cleanliness','body_cleanliness','interior_cleanliness'
        ];

        foreach ($keys as $k) {
            $data[$k . '_ok'] = $request->input($k . '_ok');
            $data[$k . '_note'] = $request->input($k . '_note');
        }

        // Foto lama
        $photoPaths = $item->photos ? json_decode($item->photos, true) : [];

        // Tambahkan foto baru
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $p) {
                $photoPaths[] = $p->store('checkitems', 'public');
            }
        }

        $data['photos'] = json_encode($photoPaths);

        // Tentukan kondisi
        $data['condition'] = collect($keys)->every(fn($k) => $data[$k . '_ok'] == 1)
            ? 'Baik'
            : 'Rusak';

        $item->update($data);

        /** --------------------------------------------------
         *  UPDATE OTOMATIS DATA VEHICLE (distance & fuel)
         * -------------------------------------------------- */
        $vehicle = $item->vehicle; // pastikan relasi checkItem->vehicle sudah benar

        if ($vehicle) {
            $vehicle->update([
                'distance' => $item->km,              // update dari check_items.ke km
                'fuel_percent' => $item->fuel_percent // update dari check_items.fuel_percent
            ]);
        }

        // Cek apakah pengecekan boleh selesai
        $this->tryFinishChecking($item->check);

        return redirect()->route('checkings.show', $item->check_id)
            ->with('success', 'Laporan pengecekan berhasil diperbarui.');
    }


    /**
     * SHOW — detail laporan kendaraan
     */
    public function show($id)
    {
        $item = CheckItem::findOrFail($id);
        $check = $item->check;
        $vehicle = $item->vehicle;

        return view('checkitems.show', compact('item', 'check', 'vehicle'));
    }

    /**
     * Helper → cek apakah pengecekan sudah bisa dianggap selesai
     */
    private function tryFinishChecking(Check $check)
    {
        $items = CheckItem::where('check_id', $check->id)->get();

        $vehicles = Vehicle::all();

        // ambil hanya kendaraan available saat pengecekan dibuat
        $availableVehicles = $vehicles->filter(fn($v) => $v->status === 'available');

        $doneCount = $items->whereNotNull('condition')->count();

        if ($doneCount >= $availableVehicles->count() && $availableVehicles->count() > 0) {
            $check->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
        }
        $sumdas = User::where('role', 'Kepala Sumber Daya')->get();

        foreach ($sumdas as $sd) {
            notify(
                $sd->id,
                "Pengecekan Selesai",
                "{$check->team->name} telah melakukan pengecekan.",
                route('checkings.show', $check->id)
            );
        }
    }
}

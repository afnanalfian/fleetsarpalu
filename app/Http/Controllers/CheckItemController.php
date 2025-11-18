<?php

namespace App\Http\Controllers;

use App\Models\Check;
use App\Models\CheckItem;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CheckItemController extends Controller
{
    /**
     * CREATE — halaman form cek kendaraan
     */
    public function create($check_id, $vehicle_id)
    {
        $check = Check::findOrFail($check_id);
        $vehicle = Vehicle::findOrFail($vehicle_id);

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
            'body_cleanliness' => 'Kebersihan Body'
        ];

        return view('checkitems.create', compact('check', 'vehicle', 'items'));
    }

    /**
     * STORE — simpan hasil cek kendaraan
     */
    public function store(Request $request)
    {
        $request->validate([
            'check_id' => 'required|exists:checks,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'fuel_percent' => 'required|numeric|min:0|max:100',
            'km' => 'required|numeric|min:0',
            'photos.*' => 'nullable|image|max:4096'
        ]);

        $check = Check::findOrFail($request->check_id);

        $data = $request->only([
            'check_id', 'vehicle_id', 'fuel_percent', 'km'
        ]);

        // Checklist
        $keys = [
            'radiator','air_filter','wiper','lights','leaks','hazards','horn','siren',
            'tires','brakes','battery','start_engine','glass_cleanliness','body_cleanliness'
        ];

        foreach ($keys as $k) {
            $data[$k . '_ok'] = $request->input($k . '_ok');
            $data[$k . '_note'] = $request->input($k . '_note');
        }

        // Upload foto
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $p) {
                $photoPaths[] = $p->store('checkitems', 'public');
            }
        }
        $data['photos'] = json_encode($photoPaths);

        // Tentukan kondisi kendaraan
        $data['condition'] = collect($keys)->every(fn($k) => $data[$k . '_ok'] == 1)
            ? 'Baik'
            : 'Rusak';

        CheckItem::create($data);

        // Update pengecekan menjadi in_progress jika belum
        if ($check->status === 'pending') {
            $check->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        // Periksa apakah semua item selesai → set completed
        $this->tryFinishChecking($check);

        return redirect()->route('checkings.show', $check->id)
            ->with('success', 'Pengecekan kendaraan berhasil disimpan.');
    }

    /**
     * EDIT — halaman edit hasil cek kendaraan
     */
    public function edit($id)
    {
        $item = CheckItem::findOrFail($id);
        $check = $item->check;
        $vehicle = $item->vehicle;

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
            'body_cleanliness' => 'Kebersihan Body'
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
            'tires','brakes','battery','start_engine','glass_cleanliness','body_cleanliness'
        ];

        foreach ($keys as $k) {
            $data[$k . '_ok'] = $request->input($k . '_ok');
            $data[$k . '_note'] = $request->input($k . '_note');
        }

        // Tambahkan foto baru
        $photoPaths = $item->photos ? json_decode($item->photos, true) : [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $p) {
                $photoPaths[] = $p->store('checkitems', 'public');
            }
        }

        $data['photos'] = json_encode($photoPaths);

        // Status kondisi
        $data['condition'] = collect($keys)->every(fn($k) => $data[$k . '_ok'] == 1)
            ? 'Baik'
            : 'Rusak';

        $item->update($data);

        // Cek apakah pengecekan boleh selesai
        $this->tryFinishChecking($item->check);

        return redirect()->route('checkings.show', $item->check_id)
            ->with('success', 'Laporan pengecekan berhasil diperbarui.');
    }

    /**
     * SHOW — tampilkan detail hasil pengecekan kendaraan
     */
    public function show($id)
    {
        $item = CheckItem::findOrFail($id);

        return view('checkitems.show', [
            'item' => $item,
            'check' => $item->check,
            'vehicle' => $item->vehicle
        ]);
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
    }
}

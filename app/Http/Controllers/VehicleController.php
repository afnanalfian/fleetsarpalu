<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::orderBy('name')->paginate(12);
        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_bmn' => 'required',
            'name' => 'required',
            'year' => 'required',
            'merk' => 'required',
            'plat_nomor' => 'required',
            'photo_path' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('photo_path');

        // Upload foto bila ada
        if ($request->hasFile('photo_path')) {
            $data['photo_path'] = $request->file('photo_path')->store('vehicles', 'public');
        }

        Vehicle::create($data);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'kode_bmn' => 'required',
            'name' => 'required',
            'year' => 'required',
            'merk' => 'required',
            'plat_nomor' => 'required',
            'photo_path' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('photo_path');

        // Update foto jika ada file baru
        if ($request->hasFile('photo_path')) {
            $data['photo_path'] = $request->file('photo_path')->store('vehicles', 'public');
        }

        $vehicle->update($data);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil diupdate.');
    }

    public function destroy($id)
    {
        Vehicle::findOrFail($id)->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }
    public function disable($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->update(['status' => 'unavailable']);

        return back()->with('success', 'Kendaraan berhasil dinonaktifkan.');
    }
    public function enable($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->update(['status' => 'available']);

        return back()->with('success', 'Kendaraan berhasil diaktifkan kembali.');
    }
}

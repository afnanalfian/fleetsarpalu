<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BorrowRequest;
use App\Models\UseReport;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UseReportController extends Controller
{
    /**
     * Tampilkan form laporan baru
     */

    public function index()
    {
        $reports = UseReport::with(['borrowRequest.vehicle', 'borrowRequest.user'])
            ->latest()
            ->paginate(10);

        return view('usereports.index', compact('reports'));
    }
    public function create($borrow_id)
    {
        $borrow = BorrowRequest::findOrFail($borrow_id);
        return view('usereports.create', compact('borrow'));
    }

    /**
     * Simpan laporan baru
     */
    public function store(Request $request, $borrow_id)
    {
        $request->validate([
            'fuel_before' => 'required|numeric|min:0|max:100',
            'fuel_after' => 'required|numeric|min:0|max:100',
            'km_before' => 'required|numeric|min:0',
            'km_after' => 'required|numeric|min:' . $request->km_before,
            'indicator_before_photos_path' => 'nullable|image|max:2048',
            'indicator_after_photos_path' => 'nullable|image|max:2048',
            'location_photos_path' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'fuel_before', 'fuel_after', 'km_before', 'km_after',
            'hazards_ok', 'hazards_note', 'horn_ok', 'horn_note',
            'siren_ok', 'siren_note', 'tires_ok', 'tires_note',
            'brakes_ok', 'brakes_note', 'battery_ok', 'battery_note',
            'start_engine_ok', 'start_engine_note'
        ]);

        // Upload foto jika ada
        foreach (['indicator_before_photos_path', 'indicator_after_photos_path', 'location_photos_path'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('usereports', 'public');
            }
        }

        $data['borrow_request_id'] = $borrow_id;

        // Simpan laporan
        UseReport::create($data);

        // Update status peminjaman menjadi Completed
        $borrow = BorrowRequest::with('vehicle')->findOrFail($borrow_id);
        $borrow->update(['status' => 'Completed']);

        // Update data kendaraan
        if ($borrow->vehicle) {
            $borrow->vehicle->update([
                'distance' => $request->km_after,
                'fuel_percent' => $request->fuel_after,
                'status' => 'available',
            ]);
        }
        $sumdas = User::where('role', 'Kepala Sumber Daya')->get();

        foreach ($sumdas as $sd) {
            notify(
                $sd->id,
                "Peminjaman Selesai",
                "Mobil {$borrow->vehicle->name} telah selesai dipinjam.",
                route('borrowings.show', $borrow->id)
            );
        }


        return redirect()->route('borrowings.show', $borrow_id)
            ->with('success', 'Laporan penggunaan berhasil dikirim.');
    }

    /**
     * Form edit laporan
     */
    public function edit($id)
    {
        $report = UseReport::with('borrowRequest.vehicle')->findOrFail($id);
        return view('usereports.edit', compact('report'));
    }

    /**
     * Update laporan penggunaan
     */
    public function update(Request $request, $id)
    {
        $report = UseReport::with('borrowRequest.vehicle')->findOrFail($id);

        $request->validate([
            'fuel_before' => 'required|numeric|min:0|max:100',
            'fuel_after' => 'required|numeric|min:0|max:100',
            'km_before' => 'required|numeric|min:0',
            'km_after' => 'required|numeric|min:' . $request->km_before,
            'indicator_before_photos_path' => 'nullable|image|max:2048',
            'indicator_after_photos_path' => 'nullable|image|max:2048',
            'location_photos_path' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'fuel_before', 'fuel_after', 'km_before', 'km_after',
            'hazards_ok', 'hazards_note', 'horn_ok', 'horn_note',
            'siren_ok', 'siren_note', 'tires_ok', 'tires_note',
            'brakes_ok', 'brakes_note', 'battery_ok', 'battery_note',
            'start_engine_ok', 'start_engine_note'
        ]);

        // Update foto bila ada file baru
        foreach (['indicator_before_photos_path', 'indicator_after_photos_path', 'location_photos_path'] as $field) {
            if ($request->hasFile($field)) {
                // Hapus foto lama bila ada
                if ($report->$field && Storage::disk('public')->exists($report->$field)) {
                    Storage::disk('public')->delete($report->$field);
                }
                $data[$field] = $request->file($field)->store('usereports', 'public');
            }
        }

        //  Update laporan
        $report->update($data);

        // Update kendaraan terkait (hanya jika bukan "unavailable")
        if ($report->borrowRequest && $report->borrowRequest->vehicle) {
            $vehicle = $report->borrowRequest->vehicle;

            if ($vehicle->status !== 'unavailable') {
                $vehicle->update([
                    'distance' => $request->km_after,
                    'fuel_percent' => $request->fuel_after,
                ]);
            }
        }

        // ✅ Pastikan status peminjaman tetap "Completed"
        $report->borrowRequest->update(['status' => 'Completed']);

        return redirect()->route('borrowings.show', $report->borrow_request_id)
            ->with('success', 'Laporan penggunaan berhasil diperbarui dan data kendaraan telah disinkronkan.');
    }

    public function show($id)
    {
        $report = UseReport::with('borrowRequest.vehicle')->findOrFail($id);
        $borrow = $report->borrowRequest; // ambil peminjaman terkait
        return view('usereports.show', compact('report', 'borrow'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BorrowRequest;
use App\Models\CheckItem;
use Illuminate\Support\Facades\Auth;

class CheckingController extends Controller
{
    /**
     * List pengecekan (SUMDA & Rescue monitor)
     */
    public function index()
    {
        $checks = CheckItem::latest()->get();
        return view('checking.index', compact('checks'));
    }

    /**
     * Form pengecekan setelah kendaraan kembali
     */
    public function create($borrow_id)
    {
        $borrow = BorrowRequest::findOrFail($borrow_id);

        return view('checking.create', compact('borrow'));
    }

    /**
     * Simpan hasil pengecekan
     */
    public function store(Request $request, $borrow_id)
    {
        $request->validate([
            'fuel_level'        => 'required|string',
            'physical_condition'=> 'required|string',
            'cleanliness'       => 'required|string',
            'tire_condition'    => 'required|string',
            'notes'             => 'nullable|string',
        ]);

        CheckItem::create([
            'borrow_request_id' => $borrow_id,
            'checked_by'        => Auth::id(),
            'fuel_level'        => $request->fuel_level,
            'physical_condition'=> $request->physical_condition,
            'cleanliness'       => $request->cleanliness,
            'tire_condition'    => $request->tire_condition,
            'notes'             => $request->notes
        ]);

        // Update status kendaraan tersedia kembali
        $borrow = BorrowRequest::find($borrow_id);
        $borrow->vehicle->update(['status' => 'Tersedia']);

        return redirect()->route('checking.index')->with('success', 'Pengecekan selesai.');
    }

    /**
     * Detail
     */
    public function show($id)
    {
        $check = CheckItem::findOrFail($id);
        return view('checking.show', compact('check'));
    }
}

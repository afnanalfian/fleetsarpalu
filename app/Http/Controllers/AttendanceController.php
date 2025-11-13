<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $data = Attendance::with(['user', 'check'])->get();
        return view('attendance.index', compact('data'));
    }

    public function store(Request $request)
    {
        foreach ($request->user_ids as $index => $userId) {
            Attendance::create([
                'check_id' => $request->check_id,
                'user_id' => $userId,
                'present' => $request->present[$index],
                'reason' => $request->reason[$index] ?? null,
            ]);
        }

        return back()->with('success', 'Absensi berhasil disimpan.');
    }
}

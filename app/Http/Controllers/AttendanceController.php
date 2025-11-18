<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Check;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $data = Attendance::with(['user', 'check'])->get();
        return view('attendance.index', compact('data'));
    }

    public function create($check_id)
    {
        $checking = Check::with('team.users')->findOrFail($check_id);

        // Semua anggota tim yang harus absen
        $members = $checking->team->users;

        return view('attendances.create', compact('checking', 'members'));
    }

    public function store(Request $request, $check_id)
    {
        $checking = Check::with('team.users')->findOrFail($check_id);
        $members  = $checking->team->users;

        // CEGAH ABSENSI GANDA
        if (Attendance::where('check_id', $check_id)->exists()) {
            return back()->with('error', 'Absensi untuk pengecekan ini sudah dibuat sebelumnya.');
        }

        // LOOP SEMUA ANGGOTA TIM
        foreach ($members as $member) {

            $presentField = "present_{$member->id}";
            $reasonField  = "reason_{$member->id}";
            $buktiField   = "bukti_{$member->id}";

            $present = $request->input($presentField) == 1;
            $reason  = $present ? null : $request->input($reasonField);

            // Upload bukti jika ada
            $buktiPath = null;
            if ($request->hasFile($buktiField)) {
                $buktiPath = $request->file($buktiField)
                                    ->store('attendance_bukti', 'public');
            }

            Attendance::create([
                'check_id'   => $check_id,
                'user_id'    => $member->id,
                'present'    => $present,
                'reason'     => $reason,
                'bukti_path' => $buktiPath,
            ]);
        }

        // UPDATE STATUS CHECK → IN PROGRESS
        if ($checking->status === 'pending') {
            $checking->update([
                'status'     => 'in_progress',
                'started_at' => now()
            ]);
        }

        return redirect()->route('checkings.show', $check_id)
            ->with('success', 'Absensi pengecekan berhasil disimpan.');
    }



}

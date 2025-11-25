<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Check;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{

    public function create($check_id)
    {
        $checking = Check::with('team.users')->findOrFail($check_id);

        // Semua anggota tim yang harus absen
        $members = $checking->team->users;

        // Kandidat pengganti = user dari tim lain + role pegawai / ketua tim
        $candidates = User::where('team_id', '!=', $checking->team_id)
            ->whereIn('role', ['Pegawai', 'Ketua Tim'])
            ->orderBy('name')
            ->get();

        return view('attendances.create', compact('checking', 'members', 'candidates'));
    }

    public function store(Request $request, $check_id)
    {
        $checking = Check::with('team.users')->findOrFail($check_id);
        $members  = $checking->team->users;

        // CEGAH ABSENSI GANDA
        if (Attendance::where('check_id', $check_id)->exists()) {
            return back()->with('error', 'Absensi untuk pengecekan ini sudah dibuat sebelumnya.');
        }

        $replacements = [];

        foreach ($members as $member) {
            $replacementField = "replacement_{$member->id}";
            $rep = $request->input($replacementField);

            if ($rep) {
                if (in_array($rep, $replacements)) {
                    return back()->with('error', 'Pengganti yang sama tidak boleh dipilih untuk lebih dari satu anggota.')
                                ->withInput();
                }
                $replacements[] = $rep;
            }
        }

        // LOOP SEMUA ANGGOTA TIM
        foreach ($members as $member) {

            $statusField      = "status_{$member->id}";
            $notesField       = "notes_{$member->id}";
            $replacementField = "replacement_{$member->id}";
            $buktiField       = "bukti_{$member->id}";

            $status = $request->input($statusField) ?? 'Tanpa Keterangan';
            $isPresent = $status === 'Hadir';

            // Upload bukti jika ada
            $buktiPath = null;
            if ($request->hasFile($buktiField)) {
                $buktiPath = $request->file($buktiField)
                                    ->store('attendance_bukti', 'public');
            }

            Attendance::create([
                'check_id'            => $check_id,
                'user_id'             => $member->id,
                'status'              => $status,
                'notes'               => $isPresent ? null : $request->input($notesField),
                'bukti_path'          => $buktiPath,
                'replacement_user_id' => $request->input($replacementField),
                'is_replacement'      => false,
            ]);
            // ======== JIKA ADA PENGGANTI ========
            if (!$isPresent && $request->filled($replacementField)) {

                $replacementUser = User::find($request->$replacementField);

                Attendance::create([
                    'check_id'       => $check_id,
                    'user_id'        => $replacementUser->id,
                    'status'         => 'Hadir',
                    'notes'          => "Pengganti untuk {$member->name}",
                    'is_replacement' => true,
                    'bukti_path'     => null, // tidak perlu bukti untuk pengganti
                ]);

                // KIRIM NOTIF KE PENGGANTI
                notify(
                    $replacementUser->id,
                    "Penugasan Sebagai Pengganti",
                    "{$replacementUser->name}, Anda ditunjuk sebagai pengganti untuk pengecekan {$checking->title}",
                    route('checkings.show', $checking->id)
                );
            }
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

    public function edit($check_id)
    {
        $checking = Check::with(['team.users', 'attendances'])->findOrFail($check_id);

        // Ambil attendance lama dan mapping per user
        $attendanceMap = $checking->attendances->keyBy('user_id');

        return view('attendances.edit', [
            'checking' => $checking,
            'members' => $checking->team->users,
            'attendanceMap' => $attendanceMap
        ]);
    }

    public function update(Request $request, $check_id)
    {
        $checking = Check::with(['team.users', 'attendances'])->findOrFail($check_id);
        $replacements = [];

        foreach ($checking->team->users as $member) {
            $replacementField = "replacement_{$member->id}";
            $rep = $request->input($replacementField);

            if ($rep) {
                if (in_array($rep, $replacements)) {
                    return back()->with('error', 'Pengganti yang sama tidak boleh dipilih untuk lebih dari satu anggota.')
                                ->withInput();
                }
                $replacements[] = $rep;
            }
        }

        foreach ($checking->team->users as $member) {

            $statusField      = "status_{$member->id}";
            $notesField       = "notes_{$member->id}";
            $replacementField = "replacement_{$member->id}";
            $buktiField       = "bukti_{$member->id}";

            $status = $request->input($statusField) ?? 'Tanpa Keterangan';
            $isPresent = $status === 'Hadir';

            // Ambil absensi lama
            $attendance = $checking->attendances->firstWhere('user_id', $member->id);

            if (!$attendance) {
                $attendance = Attendance::create([
                    'check_id' => $check_id,
                    'user_id'  => $member->id,
                ]);
            }

            // Upload bukti baru
            $buktiPath = $attendance->bukti_path;

            if (!$isPresent && $request->hasFile($buktiField)) {

                if ($buktiPath && Storage::disk('public')->exists($buktiPath)) {
                    Storage::disk('public')->delete($buktiPath);
                }

                $buktiPath = $request->file($buktiField)
                                    ->store('attendance_bukti', 'public');
            }

            // Update absensi
            $attendance->update([
                'status'     => $status,
                'notes'      => $isPresent ? null : $request->input($notesField),
                'bukti_path' => $buktiPath,
            ]);

            // ==========================
            //   HANDLE PENGGANTI
            // ==========================
            $chosenReplacement = $request->input($replacementField);

            if ($isPresent) {
                // Jika hadir → hapus pengganti lama jika ada
                Attendance::where('check_id', $check_id)
                    ->where('is_replacement', true)
                    ->where('notes', "Pengganti untuk {$member->name}")
                    ->delete();

                $attendance->update([
                    'replacement_user_id' => null,
                ]);

                continue;
            }

            // Jika tidak hadir:
            // Ambil pengganti lama
            $oldReplacement = $attendance->replacement_user_id;

            // Jika ada perubahan pengganti
            if ($chosenReplacement != $oldReplacement) {

                // Hapus pengganti lama
                Attendance::where('check_id', $check_id)
                    ->where('user_id', $oldReplacement)
                    ->where('is_replacement', true)
                    ->delete();

                $attendance->update([
                    'replacement_user_id' => $chosenReplacement,
                ]);

                // Jika ada pengganti baru
                if ($chosenReplacement) {

                    $replacementUser = User::find($chosenReplacement);

                    // Buat record pengganti baru
                    Attendance::create([
                        'check_id'       => $check_id,
                        'user_id'        => $chosenReplacement,
                        'status'         => 'Hadir',
                        'notes'          => "Pengganti untuk {$member->name}",
                        'is_replacement' => true,
                        'bukti_path'     => null
                    ]);

                    // Kirim notif
                    notify(
                        $replacementUser->id,
                        "Penugasan Pengganti",
                        "Anda ditunjuk sebagai pengganti untuk pengecekan {$checking->title}.",
                        route('checkings.show', $checking->id)
                    );
                }
            }
        }

        return redirect()->route('checkings.show', $check_id)
            ->with('success', 'Absensi berhasil diperbarui.');
    }

    public function searchReplacement(Request $request)
    {
        $keyword   = $request->input('q');
        $checkId   = $request->input('check_id');

        $checking = Check::with('team.users')->findOrFail($checkId);
        $excludedIds = $checking->team->users->pluck('id')->toArray();

        $users = User::query()
            ->whereIn('role', ['Pegawai', 'Ketua Tim'])
            ->whereNotIn('id', $excludedIds)
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function($sub) use ($keyword) {
                    $sub->where('name', 'LIKE', "%{$keyword}%")
                        ->orWhere('NIP', 'LIKE', "%{$keyword}%");
                });
            })
            ->limit(20)
            ->get();

        return view('attendances.partials.replacement-list', compact('users'));
    }

}

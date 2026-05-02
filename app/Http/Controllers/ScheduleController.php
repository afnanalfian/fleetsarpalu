<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Team;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Tampilkan jadwal siaga per bulan
     */
    public function index(Request $request)
    {
        // Bulan & tahun yang dipilih
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $start = Carbon::create($year, $month, 1);
        $end   = $start->copy()->endOfMonth();

        // Semua tim
        $teams = Team::orderBy('name')->get();

        // Ambil seluruh jadwal sebulan
        $schedules = Schedule::whereBetween('date', [$start, $end])
            ->with('team')
            ->get()
            ->groupBy('team_id');

        // List bulan untuk dropdown
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('schedules.index', compact(
            'teams', 'month', 'year', 'months', 'start', 'end', 'schedules'
        ));
    }

    /**
     * Generate jadwal otomatis untuk satu bulan
     */
    // public function generate(Request $request)
    // {
    //     $data = $request->validate([
    //         'month' => 'required|integer|min:1|max:12',
    //         'year'  => 'required|integer|min:2000|max:2100',
    //     ]);

    //     $month = $data['month'];
    //     $year  = $data['year'];

    //     $start = Carbon::create($year, $month, 1);
    //     $end   = $start->copy()->endOfMonth();

    //     $teams = Team::orderBy('id')->get();

    //     // SEED SESUAI NAMA TEAM DI DATABASE
    //     $seed = [
    //         'ALFA'   => ['S1' => 3,  'S2' => 4],
    //         'BRAVO'   => ['S1' => 2,  'S2' => 3],
    //         'CHARLIE' => ['S1' => 1,  'S2' => 2],
    //         'DELTA'   => ['S1' => 31, 'S2' => 1], // S1 ada di 31 Okt
    //         'ECHO'    => ['S1' => 4,  'S2' => 5],
    //     ];

    //     // Delete existing schedule for this month
    //     Schedule::whereBetween('date', [$start, $end])->delete();

    //     foreach ($teams as $team) {

    //         // =============== SEED UNTUK BULAN PERTAMA ===============
    //         if ($month == 11 && $year == 2025) {

    //             $s1 = $seed[$team->name]['S1'];
    //             $s2 = $seed[$team->name]['S2'];

    //             // S1 bisa di bulan sebelumnya (contoh Delta: 31)
    //             if ($s1 > $end->day) {
    //                 $nextS1 = Carbon::create($year, $month, 1)->subMonth()->day($s1); // Oktober
    //             } else {
    //                 $nextS1 = Carbon::create($year, $month, $s1);
    //             }

    //             $nextS2 = Carbon::create($year, $month, $s2);

    //         } else {

    //             // =============== LANJUTKAN DARI S2 TERAKHIR ===============
    //             $lastS2 = Schedule::where('team_id', $team->id)
    //                 ->where('shift', 'S2')
    //                 ->orderBy('date', 'desc')
    //                 ->first();

    //             if (!$lastS2) {
    //                 // fallback jika belum ada data
    //                 $nextS1 = $start->copy();
    //                 $nextS2 = $nextS1->copy()->addDay();
    //             } else {
    //                 $last = Carbon::parse($lastS2->date);

    //                 // Pola: S1,S2 lalu jeda 3 hari, lalu S1 baru
    //                 // Example: S2=4 → jeda 3 hari: 5,6,7 → next S1 = 8
    //                 $nextS1 = $last->copy()->addDays(4);
    //                 $nextS2 = $nextS1->copy()->addDay();
    //             }
    //         }

    //         // =============== GENERATE BULAN INI ===============
    //         foreach (CarbonPeriod::create($start, $end) as $day) {

    //             // default R / LB
    //             $shift = $day->isWeekend() ? 'LB' : 'R';

    //             if ($day->isSameDay($nextS1)) {
    //                 $shift = 'S1';
    //             }

    //             if ($day->isSameDay($nextS2)) {
    //                 $shift = 'S2';

    //                 // Setelah S2, majukan ke pasangan berikutnya
    //                 $nextS1 = $nextS1->copy()->addDays(5);
    //                 $nextS2 = $nextS2->copy()->addDays(5);
    //             }

    //             Schedule::updateOrCreate(
    //                 [
    //                     'team_id' => $team->id,
    //                     'date'    => $day->toDateString(),
    //                 ],
    //                 [
    //                     'shift' => $shift,
    //                 ]
    //             );
    //         }
    //     }

    //     return back()->with('success', "Jadwal bulan {$month}/{$year} berhasil digenerate.");
    // }

    // public function generate(Request $request)
    // {
    //     $data = $request->validate([
    //         'month' => 'required|integer|min:1|max:12',
    //         'year'  => 'required|integer|min:2000|max:2100',
    //     ]);

    //     $month = $data['month'];
    //     $year  = $data['year'];

    //     $start = Carbon::create($year, $month, 1);
    //     $end   = $start->copy()->endOfMonth();

    //     $teams = Team::orderBy('id')->get();

    //     // ============================================================
    //     // POLA BARU: Siklus 28 hari per regu, patokan mulai 1 April 2026
    //     // Diextract dari jadwal referensi gambar April 2026
    //     // ============================================================
    //     $cycles = [
    //         'ALFA'    => ['R','S1','R','S2','LB','R','S1','R','S2','R','LB','S1','R','S2','R','R','S1','LB','S2','R','R','S1','R','S2','LB','LB','S1','R','S2','R'],
    //         'BRAVO'   => ['S1','R','S2','LB','LB','S1','R','S2','R','R','S1','LB','S2','R','R','S1','R','S2','LB','R','S1','R','S2','R','LB','S1','R','S2','R','R'],
    //         'CHARLIE' => ['R','S2','R','LB','S1','R','S2','R','R','S1','LB','S2','R','R','S1','R','S2','LB','LB','S1','R','S2','R','R','S1','LB','S2','R','R','S1'],
    //         'DELTA'   => ['S2','R','R','S1','LB','S2','R','R','S1','R','S2','LB','R','S1','R','S2','R','LB','S1','R','S2','R','R','S1','LB','S2','R','R','S1','R'],
    //         'ECHO'    => ['R','R','S1','LB','S2','R','R','S1','R','S2','LB','LB','S1','R','S2','R','R','S1','LB','S2','R','R','S1','R','S2','LB','R','S1','R','S2'],
    //     ];

    //     // Tanggal patokan awal (1 April 2026 = index 0 dari siklus)
    //     $anchor = Carbon::create(2026, 4, 1);

    //     // Delete existing schedule for this month
    //     Schedule::whereBetween('date', [$start, $end])->delete();

    //     foreach ($teams as $team) {
    //         $cycle = $cycles[$team->name] ?? null;

    //         if (!$cycle) {
    //             continue; // skip jika nama team tidak dikenal
    //         }

    //         $cycleLength = count($cycle); // 28 hari (kecuali April yg 30, sisanya di-modulo)

    //         foreach (CarbonPeriod::create($start, $end) as $day) {
    //             // Hitung selisih hari dari anchor (1 April 2026)
    //             $daysDiff = $anchor->diffInDays($day, false);

    //             // Modulo siklus 28 hari, pastikan positif
    //             // Untuk bulan sebelum April 2026, daysDiff bisa negatif
    //             $index = (($daysDiff % 28) + 28) % 28;

    //             $shift = $cycle[$index];

    //             Schedule::updateOrCreate(
    //                 [
    //                     'team_id' => $team->id,
    //                     'date'    => $day->toDateString(),
    //                 ],
    //                 [
    //                     'shift' => $shift,
    //                 ]
    //             );
    //         }
    //     }

    //     return back()->with('success', "Jadwal bulan {$month}/{$year} berhasil digenerate.");
    // }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2000|max:2100',
        ]);

        $month = $data['month'];
        $year  = $data['year'];

        $start = Carbon::create($year, $month, 1);
        $end   = $start->copy()->endOfMonth();

        $teams = Team::orderBy('id')->get();

        // ============================================================
        // POLA BARU: Patokan 1 Mei 2026
        // Siklus 30 hari per regu (diextract dari jadwal Mei 2026)
        // Hari ke-31 dst loop kembali ke index 0
        // ============================================================
        $cycles = [
            'ALFA'    => ['R','S1','S2','R','R','R','S1','S2','LB','LB','R','S1','S2','R','R','LB','S1','S2','R','R','R','S1','S2','LB','R','R','S1','S2','R','LB'],
            'BRAVO'   => ['S1','S2','LB','R','R','S1','S2','R','LB','LB','S1','S2','R','R','R','S1','S2','R','R','R','S1','S2','LB','LB','R','S1','S2','R','R','LB'],
            'CHARLIE' => ['S2','LB','LB','R','S1','S2','R','R','LB','S1','S2','R','R','R','S1','S2','LB','R','R','S1','S2','R','LB','LB','S1','S2','R','R','R','S1'],
            'DELTA'   => ['R','LB','LB','S1','S2','R','R','R','S1','S2','R','R','R','S1','S2','LB','LB','R','S1','S2','R','R','LB','S1','S2','R','R','R','S1','S2'],
            'ECHO'    => ['R','LB','S1','S2','R','R','R','S1','S2','LB','R','R','S1','S2','R','LB','LB','S1','S2','R','R','R','S1','S2','R','R','R','S1','S2','LB'],
        ];

        // Anchor: 1 Mei 2026 = index 0
        $anchor = Carbon::create(2026, 5, 1);

        // Delete existing schedule for this month
        Schedule::whereBetween('date', [$start, $end])->delete();

        foreach ($teams as $team) {
            $cycle = $cycles[$team->name] ?? null;

            if (!$cycle) {
                continue;
            }

            $cycleLength = count($cycle); // 30

            foreach (CarbonPeriod::create($start, $end) as $day) {
                // Selisih hari dari anchor (bisa negatif untuk bulan sebelum Mei 2026)
                $daysDiff = $anchor->diffInDays($day, false);

                // Modulo aman (positif) untuk siklus 30 hari
                $index = (($daysDiff % $cycleLength) + $cycleLength) % $cycleLength;

                $shift = $cycle[$index];

                Schedule::updateOrCreate(
                    [
                        'team_id' => $team->id,
                        'date'    => $day->toDateString(),
                    ],
                    [
                        'shift' => $shift,
                    ]
                );
            }
        }

        return back()->with('success', "Jadwal bulan {$month}/{$year} berhasil digenerate.");
    }

    /**
     * Ambil shift hari ini untuk dashboard
     */
    public function today()
    {
        $shift1 = Schedule::today()->where('shift', 'S1')->with('team')->first();
        $shift2 = Schedule::today()->where('shift', 'S2')->with('team')->first();

        return [
            'shift1_team' => $shift1?->team?->name,
            'shift2_team' => $shift2?->team?->name,
            'date'        => now()->toFormattedDateString(),
        ];
    }
}

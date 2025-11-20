<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\BorrowRequest;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin(Request $request)
    {
        // ==== FILTER GRAFIK: bulan & tahun ====
        $filterMonth = (int) $request->input('chart_month', now()->month);
        $filterYear  = (int) $request->input('chart_year', now()->year);

        // ==== 1. Statistik Pegawai ====
        $pegawaiAktif = User::whereIn('role', ['Pegawai', 'Ketua Tim'])->count();

        // ==== 2. Statistik Kendaraan ====
        $kendaraanTersedia  = Vehicle::where('status', 'available')->count();
        $kendaraanOperasi   = Vehicle::where('status', 'is_use')->count();
        $kendaraanPerbaikan = Vehicle::where('status', 'unavailable')->count();
        $totalKendaraan     = Vehicle::count();

        // ==== 3. Grafik Peminjaman per Tanggal ====
        $borrowChart = BorrowRequest::selectRaw('DAY(start_at) AS day, COUNT(*) AS total')
            ->whereMonth('start_at', $filterMonth)
            ->whereYear('start_at', $filterYear)
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->pluck('total', 'day');

        // siapkan array tanggal 1–31
        $chartDaily = [];
        for ($i = 1; $i <= 31; $i++) {
            $chartDaily[$i] = $borrowChart[$i] ?? 0;
        }

        // ==== 4. Tabel Peminjaman (Pending & In Use) ====
        $peminjam = BorrowRequest::with(['user', 'vehicle'])
            ->whereIn('status', ['Pending', 'In Use'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // ==== 5. Jadwal Piket Hari Ini ====
        $today = Carbon::today()->format('Y-m-d');

        $piketShift1 = Schedule::where('date', $today)
            ->where('shift', 'S1')
            ->first();

        $piketShift2 = Schedule::where('date', $today)
            ->where('shift', 'S2')
            ->first();

        return view('admin.dashboard', [
            // statistic card
            'pegawaiAktif'        => $pegawaiAktif,
            'kendaraanTersedia'   => $kendaraanTersedia,
            'kendaraanOperasi'    => $kendaraanOperasi,
            'kendaraanPerbaikan'  => $kendaraanPerbaikan,
            'totalKendaraan'      => $totalKendaraan,

            // grafik
            'chartDaily'          => $chartDaily,
            'filterMonth'         => $filterMonth,
            'filterYear'          => $filterYear,

            // tabel peminjam
            'peminjam'            => $peminjam,

            // piket
            'piketShift1'         => $piketShift1,
            'piketShift2'         => $piketShift2,
        ]);
    }

    public function pegawai()
    {
        $user = auth()->user();

        // Ambil team_id user
        $teamId = $user->team_id;
        $todaySchedule = Schedule::where('team_id', $user->team_id)
            ->where('date', today())
            ->whereIn('shift', ['S1', 'S2'])
            ->first();

        if ($todaySchedule) {
            $shift = $todaySchedule->shift === 'S1' ? '1' : '2';

            notify(
                $user->id,
                "Shift Hari Ini",
                "Hari ini anda Shift {$shift}, jangan lupa melakukan pengecekan rutin kendaraan operasional",
                route('checkings.index')
            );
        }

        // Tanggal hari ini
        $today = now()->format('Y-m-d');

        // Ambil jadwal tim user hari ini
        $schedule = Schedule::where('team_id', $teamId)
            ->where('date', $today)
            ->first();

        // Tentukan pesan shift
        $shiftMessage = "Tidak ada jadwal hari ini. Ciee Libur. Ditemenin siapa nih liburannya? :)";

        if ($schedule) {
            switch ($schedule->shift) {
                case 'LB':
                    $shiftMessage = "LIBUR (LB)";
                    break;

                case 'R':
                    $shiftMessage = "Reguler (R) — 07.00 - 16.00";
                    break;

                case 'S1':
                    $shiftMessage = "Shift 1 (S1) — 08.00 - 20.00";
                    break;

                case 'S2':
                    $shiftMessage = "Shift 2 (S2) — 20.00 - 08.00";
                    break;
            }
        }

        // Kendaraan statistik
        $kendaraanTersedia  = Vehicle::where('status', 'available')->count();
        $kendaraanOperasi   = Vehicle::where('status', 'is_use')->count();
        $kendaraanPerbaikan = Vehicle::where('status', 'unavailable')->count();

        // Peminjaman user sendiri
        $borrowings = BorrowRequest::with(['vehicle'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['Pending', 'Approved', 'In Use'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pegawai.dashboard', [
            'user' => $user,
            'shiftMessage' => $shiftMessage,
            'kendaraanTersedia' => $kendaraanTersedia,
            'kendaraanOperasi' => $kendaraanOperasi,
            'kendaraanPerbaikan' => $kendaraanPerbaikan,
            'borrowings' => $borrowings,
        ]);
    }

    public function kepalasumberdaya(Request $request)
    {
        // ==== FILTER GRAFIK: bulan & tahun ====
        $filterMonth = (int) $request->input('chart_month', now()->month);
        $filterYear  = (int) $request->input('chart_year', now()->year);

        // ==== 1. Statistik Pegawai ====
        $pegawaiAktif = User::whereIn('role', ['Pegawai', 'Ketua Tim'])->count();

        // ==== 2. Statistik Kendaraan ====
        $kendaraanTersedia  = Vehicle::where('status', 'available')->count();
        $kendaraanOperasi   = Vehicle::where('status', 'is_use')->count();
        $kendaraanPerbaikan = Vehicle::where('status', 'unavailable')->count();
        $totalKendaraan     = Vehicle::count();

        // ==== 3. Grafik Peminjaman per Tanggal ====
        $borrowChart = BorrowRequest::selectRaw('DAY(start_at) AS day, COUNT(*) AS total')
            ->whereMonth('start_at', $filterMonth)
            ->whereYear('start_at', $filterYear)
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->pluck('total', 'day');

        // siapkan array tanggal 1–31
        $chartDaily = [];
        for ($i = 1; $i <= 31; $i++) {
            $chartDaily[$i] = $borrowChart[$i] ?? 0;
        }

        // ==== 4. Tabel Peminjaman (Pending & In Use) ====
        $peminjam = BorrowRequest::with(['user', 'vehicle'])
            ->whereIn('status', ['Pending', 'In Use'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // ==== 5. Jadwal Piket Hari Ini ====
        $today = Carbon::today()->format('Y-m-d');

        $piketShift1 = Schedule::where('date', $today)
            ->where('shift', 'S1')
            ->first();

        $piketShift2 = Schedule::where('date', $today)
            ->where('shift', 'S2')
            ->first();

        return view('kepalasumberdaya.dashboard', [
            // statistic card
            'pegawaiAktif'        => $pegawaiAktif,
            'kendaraanTersedia'   => $kendaraanTersedia,
            'kendaraanOperasi'    => $kendaraanOperasi,
            'kendaraanPerbaikan'  => $kendaraanPerbaikan,
            'totalKendaraan'      => $totalKendaraan,

            // grafik
            'chartDaily'          => $chartDaily,
            'filterMonth'         => $filterMonth,
            'filterYear'          => $filterYear,

            // tabel peminjam
            'peminjam'            => $peminjam,

            // piket
            'piketShift1'         => $piketShift1,
            'piketShift2'         => $piketShift2,
        ]);
    }

    public function ketuatim()
    {
        $user = auth()->user();

        // Ambil team_id user
        $teamId = $user->team_id;

        $todaySchedule = Schedule::where('team_id', $user->team_id)
            ->where('date', today())
            ->whereIn('shift', ['S1', 'S2'])
            ->first();

        if ($todaySchedule) {
            $shift = $todaySchedule->shift === 'S1' ? '1' : '2';

            notify(
                $user->id,
                "Shift Hari Ini",
                "Hari ini anda Shift {$shift}, jangan lupa melakukan pengecekan rutin kendaraan operasional",
                route('checkings.index')
            );
        }

        // Tanggal hari ini
        $today = now()->format('Y-m-d');

        // Ambil jadwal tim user hari ini
        $schedule = Schedule::where('team_id', $teamId)
            ->where('date', $today)
            ->first();

        // Tentukan pesan shift
        $shiftMessage = "Tidak ada jadwal hari ini";

        if ($schedule) {
            switch ($schedule->shift) {
                case 'LB':
                    $shiftMessage = "LIBUR (LB)";
                    break;

                case 'R':
                    $shiftMessage = "Reguler (R) — 07.00 - 16.00";
                    break;

                case 'S1':
                    $shiftMessage = "Shift 1 (S1) — 08.00 - 20.00";
                    break;

                case 'S2':
                    $shiftMessage = "Shift 2 (S2) — 20.00 - 08.00";
                    break;
            }
        }

        // Kendaraan statistik
        $kendaraanTersedia  = Vehicle::where('status', 'available')->count();
        $kendaraanOperasi   = Vehicle::where('status', 'is_use')->count();
        $kendaraanPerbaikan = Vehicle::where('status', 'unavailable')->count();

        // Peminjaman user sendiri
        $borrowings = BorrowRequest::with(['vehicle'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['Pending', 'Approved', 'In Use'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ketuatim.dashboard', [
            'user' => $user,
            'shiftMessage' => $shiftMessage,
            'kendaraanTersedia' => $kendaraanTersedia,
            'kendaraanOperasi' => $kendaraanOperasi,
            'kendaraanPerbaikan' => $kendaraanPerbaikan,
            'borrowings' => $borrowings,
        ]);
    }
}

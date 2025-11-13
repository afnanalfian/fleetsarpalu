<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\BorrowRequest;
use App\Models\CheckItem;

class DashboardController extends Controller
{
    // ===========================
    // Statistik umum (dipakai admin / sumda / ketua tim)
    // ===========================
    // private function getStats()
    // {
    //     return [
    //         'totalVehicles'     => Vehicle::count(),
    //         'availableVehicles' => Vehicle::where('status', 'Tersedia')->count(),
    //         'inUseVehicles'     => Vehicle::where('status', 'Digunakan')->count(),
    //         'brokenVehicles'    => Vehicle::where('status', 'Rusak')->count(),
    //         'activeBorrow'      => BorrowRequest::where('status', 'disetujui')->count(),
    //         'recentBorrow'      => BorrowRequest::latest()->take(5)->get(),
    //     ];
    // }

    public function admin()
    {
        // $stats = $this->getStats();
        return view('admin.dashboard');
    }

    public function pegawai()
    {
        return view('pegawai.dashboard');
    }

    public function sumda()
    {
        // $stats = $this->getStats();
        return view('sumda.dashboard');
    }

    public function ketuatim()
    {
        // $stats = $this->getStats();
        return view('ketua_tim.dashboard');
    }
}

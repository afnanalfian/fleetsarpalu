<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BorrowRequest;
use App\Models\CheckItem;
use App\Models\Check;
use App\Models\Vehicle;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class CheckingController extends Controller
{
    /**
     * List pengecekan (SUMDA & Rescue monitor)
     */
    public function index(Request $request)
    {
        $query = Check::with('team')->orderBy('scheduled_date', 'desc');
        $user = auth()->user();
        if (in_array(strtolower($user->role), ['pegawai', 'ketua tim'])) {
            $query->where('team_id', $user->team_id);
        }

        // Filter berdasarkan bulan & tahun
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('scheduled_date', $request->bulan)
                ->whereYear('scheduled_date', $request->tahun);
        }

        $checkings = $query->paginate(10);

        return view('checkings.index', compact('checkings'));
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
    public function store(Request $request)
    {
        $user = auth()->user();

        // Pastikan user punya tim
        if (!$user || !$user->team_id) {
            return back()->with('error', 'Anda belum tergabung dalam tim.');
        }

        // Ambil waktu sekarang
        $now = now();
        $hour = (int) $now->format('H');

        // Tentukan shift dan tanggal pengecekan
        if ($hour >= 8 && $hour < 20) {
            // Shift 1 (08:00 - 19:59)
            $shift = 'Shift 1';
            $scheduledDate = $now->toDateString();
        } else {
            // Shift 2 (20:00 - 07:59)
            $shift = 'Shift 2';
            $scheduledDate = $hour < 8
                ? $now->subDay()->toDateString()  // jam 00:00 - 07:59 → hari sebelumnya
                : $now->toDateString();           // jam 20:00 - 23:59 → hari ini
        }

        // Simpan pengecekan
        $check = Check::create([
            'scheduled_date' => $scheduledDate,
            'team_id'        => $user->team_id,
            'status'         => 'pending',
            'started_at'     => null,
            'completed_at'   => null,
            'shift'          => $shift,  // <= SET SHIFT OTOMATIS
        ]);

        // Ambil kendaraan yang AVAILABLE saat pengecekan dibuat
        $availableVehicles = Vehicle::where('status', 'available')->get();

        foreach ($availableVehicles as $v) {
            CheckItem::create([
                'check_id'   => $check->id,
                'vehicle_id' => $v->id,
            ]);
        }

        return redirect()->route('checkings.index')
            ->with('success', 'Pengecekan berhasil dibuat.');
    }

    /**
     * Detail
     */
    public function show($id)
    {
        // Ambil data pengecekan
        $check = Check::findOrFail($id);

        if (!auth()->user()->isSameTeam($check->team_id)) {
            abort(403, 'Tidak boleh mengakses pengecekan milik tim lain.');
        }

        // Ambil absensi pengecekan
        $attendance = Attendance::where('check_id', $id)->get();

        // Ambil semua kendaraan
        $vehicles = Vehicle::all();

        // Ambil check items yang sudah dibuat untuk pengecekan ini
        $checkItems = CheckItem::where('check_id', $id)->get()->keyBy('vehicle_id');

        return view('checkings.show', compact(
            'check',
            'attendance',
            'vehicles',
            'checkItems'
        ));
    }

    public function destroy($id)
    {
        $check = Check::findOrFail($id);

        // Hanya pengecekan pending yang boleh dihapus
        if ($check->status !== 'pending') {
            return back()->with('error', 'Pengecekan yang sudah berlangsung atau selesai tidak dapat dihapus.');
        }
        // Hapus seluruh check_items jika ada
        foreach (($check->items ?? collect([])) as $item) {
            $item->delete();
        }
        // Hapus seluruh attendance jika ada
        foreach (($check->attendances ?? collect([])) as $att) {
            $att->delete();
        }
        // Hapus record pengecekannya
        $check->delete();

        return back()->with('success', 'Pengecekan berhasil dihapus.');
    }


}

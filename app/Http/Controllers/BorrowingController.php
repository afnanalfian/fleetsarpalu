<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BorrowRequest;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BorrowingController extends Controller
{
    /**
     * List peminjaman
     */
    public function index(Request $request)
    {
        $borrowings = BorrowRequest::with('vehicle')->get();
        // Loop semua peminjaman & update otomatis jika waktunya sudah tiba
        foreach ($borrowings as $borrow) {
            $borrow->updateStatusAutomatically();
            $borrow->syncVehicleStatus();
        }
        $user = Auth::user();

        $query = BorrowRequest::with(['user', 'vehicle', 'team']);

        // Filter role: Pegawai hanya lihat pengajuan sendiri
        if ($user->role === 'Pegawai') {
            $query->where('user_id', $user->id);
        }

        // Filter status (opsional)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Urut terbaru dulu
        $borrowings = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();


        // NOTE: blade expects $borrowings
        return view('borrowings.index', compact('borrowings'));
    }

    /**
     * Form ajukan
     */
    public function create(Request $request)
    {
        // Ambil kendaraan yang tersedia (sesuaikan value status di DB)
        $vehicles = Vehicle::where('status', 'available')->get();

        // Jika ada param vehicle_id (dari tombol Pinjam di detail vehicle), bawa ke view
        $selectedVehicleId = $request->query('vehicle_id');

        return view('borrowings.create', compact('vehicles', 'selectedVehicleId'));
    }

    /**
     * Simpan pengajuan
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_at'            => 'required|date',
            'end_at'              => 'required|date|after_or_equal:start_at',
            'start_time'          => 'nullable|string',
            'end_time'            => 'nullable|string',
            'purpose_text'        => 'required|string',
            'destination_address' => 'required|string',
            'vehicle_id'          => 'required|exists:vehicles,id',
            'surat_tugas'         => 'nullable|file|mimes:pdf|max:2048'
        ]);

        // Simpan file surat tugas (jika ada)
        $filePath = null;
        if ($request->hasFile('surat_tugas')) {
            $filePath = $request->file('surat_tugas')->store('surat_tugas', 'public');
        }

        // Generate kode pinjam otomatis: BR-YYYYMMDD-XXX
        $today = now()->format('Ymd');
        $countToday = BorrowRequest::whereDate('created_at', now())->count() + 1;
        $sequence = str_pad($countToday, 3, '0', STR_PAD_LEFT);
        $kodePinjam = "BR-{$today}-{$sequence}";

        // Cek apakah kendaraan sedang atau akan dipinjam di rentang waktu yang sama
        $overlap = BorrowRequest::where('vehicle_id', $request->vehicle_id)
            ->whereIn('status', ['Approved', 'In Use']) // hanya cek status aktif
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->whereDate('start_at', '<=', $request->end_at)
                    ->whereDate('end_at', '>=', $request->start_at);
                })
                ->where(function ($q) use ($request) {
                    $q->where(function ($inner) use ($request) {
                        $inner->where('start_time', '<=', $request->end_time)
                            ->where('end_time', '>=', $request->start_time);
                    });
                });
            })
            ->first();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'vehicle_id' => "❌ TIDAK DAPAT MEMINJAM - Seseorang meminjam kendaraan tersebut pada
                tanggal " . \Carbon\Carbon::parse($overlap->start_at)->format('d M Y') . " pukul " . $overlap->start_time .
                " hingga " . \Carbon\Carbon::parse($overlap->end_at)->format('d M Y') . " pukul " . $overlap->end_time .
                ". Silakan pilih waktu lain."
            ]);
        }

        $borrow = BorrowRequest::create([
            'kode_pinjam'         => $kodePinjam,
            'user_id'             => Auth::id(),
            'team_id'             => Auth::user()->team_id,
            'vehicle_id'          => $request->vehicle_id,
            'purpose_text'        => $request->purpose_text,
            'destination_address' => $request->destination_address,
            'start_at'            => $request->start_at,
            'end_at'              => $request->end_at,
            'start_time'          => $request->start_time,
            'end_time'            => $request->end_time,
            'surat_tugas_path'    => $filePath,
            'status'              => 'Pending', // gunakan kapital seperti view kamu
        ]);

        return redirect()->route('borrowings.index')->with('success', 'Pengajuan berhasil dikirim.');
    }

    public function edit($id)
    {
        $borrow = BorrowRequest::findOrFail($id);

        // Tampilkan kendaraan yang tersedia atau yang sedang dipakai di peminjaman ini
        $vehicles = Vehicle::where('status', 'available')
            ->orWhere('id', $borrow->vehicle_id)
            ->orderBy('name')
            ->get();

        return view('borrowings.edit', compact('borrow', 'vehicles'));
    }
    public function update(Request $request, $id)
    {
        $borrow = BorrowRequest::findOrFail($id);

        $request->validate([
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'start_time' => 'required',
            'end_time' => 'required',
            'purpose_text' => 'required|string',
            'destination_address' => 'required|string',
            'vehicle_id' => 'required|exists:vehicles,id',
            'surat_tugas' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $data = $request->only([
            'start_at', 'end_at', 'start_time', 'end_time',
            'purpose_text', 'destination_address', 'vehicle_id'
        ]);

        // Upload surat tugas baru kalau ada
        if ($request->hasFile('surat_tugas')) {
            $data['surat_tugas_path'] = $request->file('surat_tugas')->store('surat_tugas', 'public');
        }

        $overlap = BorrowRequest::where('vehicle_id', $request->vehicle_id)
        ->where('id', '!=', $id) // abaikan dirinya sendiri
        ->whereIn('status', ['Approved', 'In Use'])
        ->where(function ($query) use ($request) {
            $query->whereDate('start_at', '<=', $request->end_at)
                ->whereDate('end_at', '>=', $request->start_at)
                ->where(function ($q) use ($request) {
                    $q->where('start_time', '<=', $request->end_time)
                        ->where('end_time', '>=', $request->start_time);
                });
        })
        ->first();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'vehicle_id' => "❌ TIDAK DAPAT MEMINJAM - Seseorang meminjam kendaraan tersebut pada
                tanggal " . \Carbon\Carbon::parse($overlap->start_at)->format('d M Y') . " pukul " . $overlap->start_time .
                " hingga " . \Carbon\Carbon::parse($overlap->end_at)->format('d M Y') . " pukul " . $overlap->end_time .
                ". Silakan pilih waktu lain."
            ]);
        }

        $borrow->update($data);

        return redirect()->route('borrowings.index')->with('success', 'Data peminjaman berhasil diperbarui.');
    }


    /**
     * Detail
     */
    public function show($id)
    {
        $borrow = BorrowRequest::with(['user', 'vehicle', 'team', 'useReport'])->findOrFail($id);
        return view('borrowings.show', compact('borrow'));
    }

    /**
     * SUMDA Approve
     */
    public function approve($id)
    {
        $borrow = BorrowRequest::findOrFail($id);

        $borrow->update([
            'status'      => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan disetujui.');
    }

    /**
     * SUMDA Reject
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $borrow = BorrowRequest::findOrFail($id);

        $borrow->update([
            'status'           => 'Rejected',
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('error', 'Pengajuan ditolak dengan alasan: ' . $request->rejection_reason);
    }

    /**
     * Action: set selesai (dipakai untuk tombol "Selesaikan" ketika status In Use)
     * Biasanya akan diarahkan ke pembuatan use_report, tapi saya sertakan helper singkat:
     */
    public function markCompleted($id)
    {
        $borrow = BorrowRequest::findOrFail($id);

        // set status completed
        $borrow->update([
            'status' => 'Completed',
            'approved_at' => $borrow->approved_at ?? now(),
        ]);

        return redirect()->route('borrowings.index')->with('success', 'Peminjaman ditandai selesai.');
    }

    /**
     * Riwayat
     */
    public function history()
    {
        $user = Auth::user();

        $query = BorrowRequest::with(['user', 'vehicle', 'team']);
        if ($user->role === 'Pegawai') {
            $query->where('user_id', $user->id);
        }
        $borrows = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('history.borrowing', compact('borrows'));
    }
    public function cancel($id)
    {
        $borrow = BorrowRequest::findOrFail($id);

        // Hanya bisa dibatalkan jika belum selesai atau dibatalkan sebelumnya
        if (!in_array(strtolower($borrow->status), ['completed', 'cancelled'])) {
            $borrow->update(['status' => 'cancelled']);
        }

        return back()->with('success', 'Peminjaman berhasil dibatalkan.');
    }
}

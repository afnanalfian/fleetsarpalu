<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BorrowRequest;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BorrowingController extends Controller
{
    /**
     * List peminjaman
     */
    public function index(Request $request)
    {
        // Update otomatis status
        BorrowRequest::with('vehicle')->get()->each(function ($b) {
            $b->updateStatusAutomatically();
            $b->syncVehicleStatus();
        });

        $user = Auth::user();

        $query = BorrowRequest::with(['user', 'vehicle', 'team']);

        // Ketua Tim / Pegawai → hanya miliknya
        if (!$user->hasRole(['admin', 'kepala sumber daya'])) {
            $query->where('user_id', $user->id);
        }

        // Filter status biasa
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ======== FILTER BARU ========
        if ($request->filled('filter_by')) {

            switch ($request->filter_by) {

                case 'nama':
                    if ($request->filled('nama')) {
                        $query->whereHas('user', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->nama . '%');
                        });
                    }
                    break;

                case 'tanggal':
                    if ($request->filled('tanggal')) {
                        $query->whereDate('start_at', $request->tanggal);
                    }
                    break;

                case 'bulan':
                    if ($request->filled('bulan') && $request->filled('tahun')) {
                        $query->whereYear('start_at', $request->tahun)
                            ->whereMonth('start_at', $request->bulan);
                    }
                    break;

                case 'kendaraan':
                    if ($request->filled('kendaraan')) {
                        $query->where('vehicle_id', $request->kendaraan);
                    }
                    break;
            }
        }

        // ORDER + PAGINATION
        $borrowings = $query->orderBy('created_at', 'desc')
                            ->paginate(10)
                            ->withQueryString();

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
        $sumdas = User::where('role', 'Kepala Sumber Daya')->get();
        $user = Auth::user();

        foreach ($sumdas as $sd) {
            notify(
                $sd->id,
                "Pengajuan Peminjaman Baru",
                "Peminjaman dibuat oleh {$user->name}, menunggu konfirmasi",
                route('borrowings.index')
            );
        }

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
        if (!auth()->user()->hasRole(['kepala sumber daya'])) {
            abort(403, 'Hanya Kepala Sumber Daya yang boleh menyetujui.');
        }

        $borrow->update([
            'status'      => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        notify(
            $borrow->user_id,
            "Peminjaman Disetujui",
            "Peminjaman anda untuk kendaraan {$borrow->vehicle->name} pada tanggal "
                . $borrow->start_at->format('d M Y') . " telah disetujui. Jangan lupa memotret indikator sebelum berangkat, dan mengambil gambar saat sudah di lokasi",
            route('borrowings.show', $borrow->id)
        );

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
        if (!auth()->user()->hasRole(['kepala sumber daya'])) {
            abort(403, 'Hanya Kepala Sumber Daya yang boleh menolak.');
        }

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
        // ❗ Semua role → hanya pemilik boleh cancel
        if ($borrow->user_id !== auth()->id()) {
            abort(403, 'Tidak boleh membatalkan peminjaman orang lain.');
        }

        // Hanya bisa dibatalkan jika belum selesai atau dibatalkan sebelumnya
        if (!in_array(strtolower($borrow->status), ['completed', 'cancelled'])) {
            $borrow->update(['status' => 'cancelled']);
        }

        return back()->with('success', 'Peminjaman berhasil dibatalkan.');
    }
}

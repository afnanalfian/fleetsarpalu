@extends('layouts.app')

@section('title', 'Detail Peminjaman Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-10">
            <div class="bg-light rounded p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                    <h5 class="mb-0 fw-bold">Detail Peminjaman Kendaraan</h5>
                </div>

                {{-- Informasi utama --}}
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tr>
                            <th class="text-start" style="width: 25%">Kode Pinjam</th>
                            <td>{{ $borrow->kode_pinjam }}</td>
                        </tr>
                        <tr>
                            <th class="text-start">Nama Peminjam</th>
                            <td>{{ $borrow->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-start">NIP Peminjam</th>
                            <td>{{ $borrow->user->NIP ?? '-' }}</td>
                        </tr>
                        {{-- Supir (khusus external) --}}
                        @if($borrow->user->isExternal())
                        <tr>
                            <th class="text-start">Supir</th>
                            <td>
                                @if($borrow->driver)
                                    {{ $borrow->driver->name }}
                                @else
                                    <span class="text-muted">Belum dipilih</span>
                                @endif
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th class="text-start">Kendaraan</th>
                            <td>{{ $borrow->vehicle->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-start">Tujuan</th>
                            <td>{{ $borrow->destination_address }}</td>
                        </tr>
                        <tr>
                            <th class="text-start">Keperluan</th>
                            <td>{{ $borrow->purpose_text }}</td>
                        </tr>
                        <tr>
                            <th class="text-start">Tanggal Pergi</th>
                            <td>{{ \Carbon\Carbon::parse($borrow->start_at)->format('d/m/Y') }} - {{ $borrow->start_time }}</td>
                        </tr>
                        <tr>
                            <th class="text-start">Tanggal Pulang</th>
                            <td>{{ \Carbon\Carbon::parse($borrow->end_at)->format('d/m/Y') }} - {{ $borrow->end_time }}</td>
                        </tr>
                        <tr>
                            <th class="text-start">Status</th>
                            <td>
                                <span class="badge
                                    @switch(strtolower($borrow->status))
                                        @case('pending') bg-secondary @break
                                        @case('approved') bg-success @break
                                        @case('rejected') bg-danger @break
                                        @case('in use') bg-warning text-dark @break
                                        @case('completed') bg-primary @break
                                        @case('cancelled') bg-dark @break
                                        @default bg-light text-dark
                                    @endswitch
                                ">
                                    {{ ucfirst($borrow->status) }}
                                </span>
                            </td>
                        </tr>

                        {{-- Kondisi --}}
                        <tr>
                            <th class="text-start">Kondisi</th>
                            <td>
                                @php
                                    $condition = 'Not Yet';

                                    if ($borrow->status === 'Completed' && $borrow->useReport) {
                                        $report = $borrow->useReport;

                                        // Komponen mekanik saja (kebersihan tidak dihitung)
                                        $mechanicalItems = [
                                            $report->hazards_ok,
                                            $report->horn_ok,
                                            $report->siren_ok,
                                            $report->tires_ok,
                                            $report->brakes_ok,
                                            $report->battery_ok,
                                            $report->start_engine_ok,
                                        ];

                                        if (in_array('Rusak Berat', $mechanicalItems)) {
                                            $condition = 'Rusak Berat';
                                        } elseif (in_array('Rusak Ringan', $mechanicalItems)) {
                                            $condition = 'Rusak Ringan';
                                        } else {
                                            $condition = 'Baik';
                                        }
                                    }

                                    // Badge warna
                                    $badge = [
                                        'Baik'         => 'bg-success',
                                        'Rusak Ringan' => 'bg-warning text-dark',
                                        'Rusak Berat'  => 'bg-danger',
                                        'Not Yet'      => 'bg-secondary'
                                    ];
                                @endphp

                                <span class="badge {{ $badge[$condition] ?? 'bg-secondary' }}">
                                    {{ $condition }}
                                </span>
                            </td>
                        </tr>

                        {{-- Alasan Penolakan --}}
                        @if($borrow->status === 'Rejected' && !empty($borrow->rejection_reason))
                        <tr>
                            <th class="text-start text-danger">Alasan Penolakan</th>
                            <td>
                                <div class="text-start text-danger fw-semibold">
                                    {{ $borrow->rejection_reason }}
                                </div>
                            </td>
                        </tr>
                        @endif

                        {{-- Surat Tugas --}}
                        @if($borrow->surat_tugas_path)
                        <tr>
                            <th class="text-start">Surat Tugas</th>
                            <td>
                                <a href="{{ asset('storage/' . $borrow->surat_tugas_path) }}" target="_blank" class="text-decoration-none">
                                    <i class="fa-solid fa-file-pdf text-danger me-1"></i> Lihat Surat
                                </a>
                            </td>
                        </tr>
                        @endif
                        {{-- Lampiran --}}
                        @if($borrow->lampiran_path)
                        <tr>
                            <th class="text-start">Lampiran</th>
                            <td>
                                <a href="{{ asset('storage/' . $borrow->lampiran_path) }}" target="_blank" class="text-decoration-none">
                                    <i class="fa-solid fa-file-pdf text-danger me-1"></i> Lihat Lamppiran
                                </a>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
                {{-- Tombol Aksi --}}
                <div class="mt-4 d-flex justify-content-start gap-2">

                    {{-- ======= EDIT PEMINJAMAN — hanya pemilik & status pending ======= --}}
                    @if(auth()->id() === $borrow->user_id && strtolower($borrow->status) === 'pending')
                        <a href="{{ route('borrowings.edit', $borrow->id) }}" class="btn btn-primary">
                            <i class="fa-solid fa-pen me-1"></i> Edit Peminjaman
                        </a>
                    @endif

                    {{-- === PILIH / GANTI SUPIR — hanya untuk External + Kepala SD === --}}
                    @if($borrow->user->isExternal() && auth()->user()->role === 'Kepala Sumber Daya')

                        {{-- Jika sudah disetujui dan supir belum ada --}}
                        @if($borrow->status === 'Approved' && !$borrow->driver_id)
                            <button class="btn btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalPilihSupir">
                                Pilih Supir
                            </button>
                        @endif

                        {{-- Jika supir sudah dipilih --}}
                        @if($borrow->driver_id)
                            <button class="btn btn-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalPilihSupir">
                                Ganti Supir
                            </button>
                        @endif

                    @endif


                    {{-- ======= LIHAT LAPORAN KONDISI ======= --}}
                    @if(!$borrow->user->isExternal() && strtolower($borrow->status) === 'completed' && $borrow->useReport)
                        <a href="{{ route('usereports.show', ['id' => $borrow->useReport->id]) }}"
                        class="btn btn-info">
                            <i class="fa-solid fa-file-alt me-1"></i> Lihat Laporan Kondisi
                        </a>
                    @endif


                    {{-- ======= SETUJUI / TOLAK — hanya Kepala Sumber Daya ======= --}}
                    @if(auth()->user()->role === 'Kepala Sumber Daya' && $borrow->status === 'Pending')

                        <form action="{{ route('borrowings.approve', $borrow->id) }}"
                            method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success"
                                    onclick="return confirm('Setujui peminjaman ini?')">
                                Setujui Peminjaman
                            </button>
                        </form>

                        <button type="button" class="btn btn-danger"
                                data-bs-toggle="modal" data-bs-target="#RejectModal">
                            Tolak Peminjaman
                        </button>

                    @elseif($borrow->status === 'Pending')

                        {{-- Jika bukan Kepala SD, tampilkan keterangan saja --}}
                        <span class="badge bg-warning text-dark align-self-center">
                            Sedang Menunggu Persetujuan
                        </span>

                    @endif

                </div>

                {{-- Modal Penolakan --}}
                <div class="modal fade" id="RejectModal" tabindex="-1" aria-labelledby="RejectModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="RejectModalLabel">Tolak Peminjaman</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('borrowings.reject', $borrow->id) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <p>Apakah Anda yakin ingin menolak peminjaman ini?</p>
                                    <div class="mb-3">
                                        <label for="rejection_reason" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger">Tolak Sekarang</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- Modal Pilih / Ganti Supir --}}
                <div class="modal fade" id="modalPilihSupir" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Pilih Supir</h5>
                                <button type="button" class="btn-close btn-close-white"
                                        data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <input type="text" id="searchDriver" class="form-control mb-3"
                                    placeholder="Cari supir berdasarkan nama...">

                                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                                    <table class="table table-hover" id="driverTable">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>NIP</th>
                                                <th>Pilih</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(\App\Models\User::whereIn('role', ['Pegawai', 'Ketua Tim'])->get() as $driver)
                                                <tr>
                                                    <td>{{ $driver->name }}</td>
                                                    <td>{{ $driver->NIP }}</td>
                                                    <td>
                                                        <form method="POST" action="{{ route('borrowings.assign-driver', $borrow->id) }}">
                                                            @csrf
                                                            <input type="hidden" name="driver_id" value="{{ $driver->id }}">
                                                            <button class="btn btn-success btn-sm">
                                                                Pilih
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <script>
                            document.getElementById("searchDriver").addEventListener("keyup", function () {
                                let keyword = this.value.toLowerCase();
                                let rows = document.querySelectorAll("#driverTable tbody tr");

                                rows.forEach(row => {
                                    let name = row.cells[0].innerText.toLowerCase();
                                    let nip = row.cells[1].innerText.toLowerCase();

                                    row.style.display = (name.includes(keyword) || nip.includes(keyword))
                                        ? "" : "none";
                                });
                            });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

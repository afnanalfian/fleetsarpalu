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
                                        $checklist = [
                                            $report->hazards_ok,
                                            $report->horn_ok,
                                            $report->siren_ok,
                                            $report->tires_ok,
                                            $report->brakes_ok,
                                            $report->battery_ok,
                                            $report->start_engine_ok,
                                        ];
                                        $condition = in_array(0, $checklist) ? 'Tidak Aman' : 'Aman';
                                    }
                                @endphp

                                <span class="badge
                                    @if($condition == 'Aman') bg-success
                                    @elseif($condition == 'Tidak Aman') bg-danger
                                    @else bg-secondary @endif">
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
                    </table>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-4 d-flex justify-content-start gap-2">
                    @if(strtolower($borrow->status) === 'pending' && $borrow->useReport)
                    <a href="{{ route('borrowings.edit', $borrow->id) }}" class="btn btn-primary">
                        <i class="fa-solid fa-pen me-1"></i> Edit Peminjaman
                    </a>
                    @endif

                    @if(strtolower($borrow->status) === 'completed' && $borrow->useReport)
                        <a href="{{ route('usereports.show', ['id' => $borrow->useReport->id]) }}" class="btn btn-info">
                            <i class="fa-solid fa-file-alt me-1"></i> Lihat Laporan Kondisi
                        </a>
                    @endif
                    @if($borrow->status === 'Pending')
                        <form action="{{ route('borrowings.approve', $borrow->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success"
                                    onclick="return confirm('Setujui peminjaman ini?')">
                                Setujui Peminjaman
                            </button>
                        </form>

                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#RejectModal">
                            Tolak Peminjaman
                        </button>
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
            </div>
        </div>
    </div>
</div>
@endsection

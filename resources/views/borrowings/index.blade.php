@extends('layouts.app')

@section('title', 'Data Peminjaman Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">

    {{-- Header & Filter --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h5 class="mb-3 mb-md-0">Data Peminjaman Kendaraan</h5>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="{{ route('borrowings.create') }}" class="btn btn-warning rounded">
                Pinjam Kendaraan <i class="fa-solid fa-key ms-1"></i>
            </a>

            {{-- Filter Status --}}
            <form method="GET" action="{{ route('borrowings.index') }}" class="d-flex align-items-center">
                <label class="me-2 text-nowrap">Filter Status:</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach(['Pending', 'Approved', 'Rejected', 'In Use', 'Completed','Cancelled'] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </form>

            {{-- Pilih Kolom --}}
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    Pilih Kolom
                </button>
                <div class="dropdown-menu p-3" style="min-width: 200px;">
                    @foreach([
                        'nip' => 'NIP Peminjam',
                        'nama' => 'Nama Peminjam',
                        'kendaraan' => 'Kendaraan',
                        'tujuan' => 'Tujuan',
                        'tgl_pergi' => 'Tanggal Pergi',
                        'jam_pergi' => 'Jam Pergi',
                        'tgl_pulang' => 'Tanggal Pulang',
                        'jam_pulang' => 'Jam Pulang',
                    ] as $col => $label)
                        <div class="form-check">
                            <input class="form-check-input column-toggle" type="checkbox" value="{{ $col }}" id="col_{{ $col }}" checked>
                            <label class="form-check-label" for="col_{{ $col }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-hover align-middle text-center" id="borrowingsTable">
            <thead class="table-light">
                <tr>
                    <th style="white-space: nowrap;">Kode Pinjam</th>
                    <th style="white-space: nowrap;">NIP Peminjam</th>
                    <th style="white-space: nowrap;">Nama Peminjam</th>
                    <th style="white-space: nowrap;">Kendaraan</th>
                    <th style="white-space: nowrap;">Tujuan</th>
                    <th style="white-space: nowrap;">Tanggal Pergi</th>
                    <th style="white-space: nowrap;">Jam Pergi</th>
                    <th style="white-space: nowrap;">Tanggal Pulang</th>
                    <th style="white-space: nowrap;">Jam Pulang</th>
                    <th style="white-space: nowrap;">Status</th>
                    <th style="white-space: nowrap; position: sticky; right: 0; background: #f8f9fa; z-index: 2;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($borrowings as $borrow)
                    <tr>
                        <td style="white-space: nowrap;">{{ $borrow->kode_pinjam }}</td>
                        <td style="white-space: nowrap;">{{ $borrow->user->NIP ?? '-' }}</td>
                        <td style="white-space: nowrap;">{{ $borrow->user->name ?? '-' }}</td>
                        <td style="white-space: nowrap;">{{ $borrow->vehicle->name ?? '-' }}</td>
                        <td style="white-space: nowrap;">{{ $borrow->destination_address }}</td>
                        <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($borrow->start_at)->format('d/m/Y') }}</td>
                        <td style="white-space: nowrap;">{{ $borrow->start_time }}</td>
                        <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($borrow->end_at)->format('d/m/Y') }}</td>
                        <td style="white-space: nowrap;">{{ $borrow->end_time }}</td>

                        {{-- Status --}}
                        <td style="white-space: nowrap;">
                            <span class="
                                badge
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

                        {{-- AKSI --}}
                        <td class="position-sticky end-0 bg-white">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('borrowings.show', $borrow->id) }}" class="btn btn-info text-white">
                                    Detail
                                </a>

                                @if($borrow->status === 'In Use')
                                    <a href="{{ route('reports.create', $borrow->id) }}" class="btn btn-sm btn-warning">
                                        Selesaikan
                                    </a>
                                @endif

                                @if(!in_array(strtolower($borrow->status), ['cancelled', 'completed', 'rejected']))
                                    <button type="button" class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#Cancel{{ $borrow->id }}">
                                        Cancel
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    {{-- Modal Cancel --}}
                    <div class="modal fade" id="Cancel{{ $borrow->id }}" tabindex="-1" aria-labelledby="CancelLabel{{ $borrow->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="CancelLabel{{ $borrow->id }}">Batalkan Peminjaman</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin membatalkan peminjaman <strong>{{ $borrow->kode_pinjam }}</strong>?
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('borrowings.cancel', $borrow->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                                    </form>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="11" class="py-4 text-center text-muted">
                            Tidak ada data peminjaman kendaraan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {!! $borrowings->links() !!}
    </div>
</div>

{{-- Script toggle kolom --}}
<script>
    document.querySelectorAll('.column-toggle').forEach(cb => {
        cb.addEventListener('change', function() {
            const col = this.value;
            const visible = this.checked;
            document.querySelectorAll('[data-col="' + col + '"]').forEach(cell => {
                cell.style.display = visible ? '' : 'none';
            });
        });
    });
</script>
@endsection

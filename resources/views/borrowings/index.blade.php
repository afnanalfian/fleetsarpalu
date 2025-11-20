@extends('layouts.app')

@section('title', 'Data Peminjaman Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">

    {{-- Header & Filter --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h5 class="mb-3 mb-md-0">Data Peminjaman Kendaraan</h5>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="{{ route('borrowings.create') }}" class="btn btn-warning btn-sm rounded">
                Pinjam Kendaraan <i class="fa-solid fa-key ms-1"></i>
            </a>
            <a href="{{ route('reports.borrow.form') }}" class="btn btn-success btn-sm rounded">
                Generate Laporan <i class="fa-solid fa-file-export ms-1"></i>
            </a>

            {{-- Filter Pencarian Lanjutan --}}
            <form method="GET" action="{{ route('borrowings.index') }}" class="d-flex flex-wrap gap-2 align-items-end bg-light p-3 rounded shadow-sm">

                {{-- PILIH KATEGORI FILTER --}}
                <div>
                    <label class="form-label mb-1 small">Filter Berdasarkan</label>
                    <select name="filter_by" id="filter_by" class="form-select-sm">
                        <option value="">-- Pilih --</option>
                        <option value="nama"  {{ request('filter_by')==='nama' ? 'selected' : '' }}>Nama Peminjam</option>
                        <option value="tanggal" {{ request('filter_by')==='tanggal' ? 'selected' : '' }}>Tanggal</option>
                        <option value="bulan" {{ request('filter_by')==='bulan' ? 'selected' : '' }}>Bulan</option>
                        <option value="kendaraan" {{ request('filter_by')==='kendaraan' ? 'selected' : '' }}>Kendaraan</option>
                    </select>
                </div>

                {{-- INPUT NAMA --}}
                <div id="filter_nama" style="display:none;">
                    <input type="text" name="nama" class="form-control form-control-sm" placeholder="Masukkan nama..."
                        value="{{ request('nama') }}">
                </div>

                {{-- INPUT TANGGAL --}}
                <div id="filter_tanggal" style="display:none;">
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ request('tanggal') }}">
                </div>

                {{-- INPUT BULAN + TAHUN --}}
                <div id="filter_bulan" style="display:none;">
                    <select name="bulan" class="form-select-sm">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ request('bulan')==$m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="filter_tahun" style="display:none;">
                    <select name="tahun" class="form-select-sm">
                        @for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++)
                            <option value="{{ $y }}" {{ request('tahun')==$y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- INPUT KENDARAAN --}}
                <div id="filter_kendaraan" style="display:none;">
                    <select name="kendaraan" class="form-select-sm">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach(\App\Models\Vehicle::orderBy('name')->get() as $veh)
                            <option value="{{ $veh->id }}" {{ request('kendaraan')==$veh->id ? 'selected' : '' }}>
                                {{ $veh->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TOMBOL --}}
                <div>
                    <button class="btn btn-primary btn-sm">Cari</button>
                    <a href="{{ route('borrowings.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                </div>

            </form>

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
                        'kode' => 'Kode Pinjam',
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
                    <th data-col="kode" style="white-space: nowrap;">Kode Pinjam</th>
                    <th data-col="nip" style="white-space: nowrap;">NIP Peminjam</th>
                    <th data-col="nama" style="white-space: nowrap;">Nama Peminjam</th>
                    <th data-col="kendaraan" style="white-space: nowrap;">Kendaraan</th>
                    <th data-col="tujuan" style="white-space: nowrap;">Tujuan</th>
                    <th data-col="tgl_pergi" style="white-space: nowrap;">Tanggal Pergi</th>
                    <th data-col="jam_pergi" style="white-space: nowrap;">Jam Pergi</th>
                    <th data-col="tgl_pulang" style="white-space: nowrap;">Tanggal Pulang</th>
                    <th data-col="jam_pulang" style="white-space: nowrap;">Jam Pulang</th>
                    <th data-col="nip" style="white-space: nowrap;">Status</th>
                    <th data-col="nip" style="white-space: nowrap; position: sticky; right: 0; background: #f8f9fa; z-index: 2;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($borrowings as $borrow)
                    <tr>
                        <td data-col="kode" style="white-space: nowrap;">{{ $borrow->kode_pinjam }}</td>
                        <td data-col="nip" style="white-space: nowrap;">{{ $borrow->user->NIP ?? '-' }}</td>
                        <td data-col="nama" style="white-space: nowrap;">{{ $borrow->user->name ?? '-' }}</td>
                        <td data-col="kendaraan" style="white-space: nowrap;">{{ $borrow->vehicle->name ?? '-' }}</td>
                        <td data-col="tujuan" style="white-space: nowrap;">{{ $borrow->destination_address }}</td>
                        <td data-col="tgl_pergi" style="white-space: nowrap;">{{ \Carbon\Carbon::parse($borrow->start_at)->format('d/m/Y') }}</td>
                        <td data-col="jam_pergi" style="white-space: nowrap;">{{ $borrow->start_time }}</td>
                        <td data-col="tgl_pulang" style="white-space: nowrap;">{{ \Carbon\Carbon::parse($borrow->end_at)->format('d/m/Y') }}</td>
                        <td data-col="jam_pulang" style="white-space: nowrap;">{{ $borrow->end_time }}</td>

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

                                {{-- Detail selalu tampil --}}
                                <a href="{{ route('borrowings.show', $borrow->id) }}" class="btn btn-info text-white">
                                    Detail
                                </a>

                                {{-- Tombol untuk PEMILIK PEMINJAMAN SAJA --}}
                                @if(auth()->id() === $borrow->user_id)

                                    {{-- Selesaikan (hanya jika status In Use) --}}
                                    @if($borrow->status === 'In Use')
                                        <a href="{{ route('reports.create', $borrow->id) }}" class="btn btn-warning">
                                            Selesaikan
                                        </a>
                                    @endif

                                    {{-- Cancel (hanya jika pending/approved/in use) --}}
                                    @if(!in_array(strtolower($borrow->status), ['cancelled', 'completed', 'rejected']))
                                        <button type="button"
                                            class="btn btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#Cancel{{ $borrow->id }}">
                                            Cancel
                                        </button>
                                    @endif

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

function toggleFilters() {
    const val = document.getElementById('filter_by').value;

    document.getElementById('filter_nama').style.display = (val === 'nama') ? 'block' : 'none';
    document.getElementById('filter_tanggal').style.display = (val === 'tanggal') ? 'block' : 'none';
    document.getElementById('filter_bulan').style.display = (val === 'bulan') ? 'block' : 'none';
    document.getElementById('filter_tahun').style.display = (val === 'bulan') ? 'block' : 'none';
    document.getElementById('filter_kendaraan').style.display = (val === 'kendaraan') ? 'block' : 'none';
}

document.getElementById('filter_by').addEventListener('change', toggleFilters);
toggleFilters(); // load awal
</script>

@endsection

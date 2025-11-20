@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="container-fluid px-4 pt-3">

    {{-- ========================== --}}
    {{-- STATISTIC CARD --}}
    {{-- ========================== --}}
    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" style="width:50px; height:50px;">
                        <i class="fa-solid fa-users fa-lg"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0">Pegawai Aktif</h6>
                        <h2 class="fw-bold mb-0">{{ $pegawaiAktif }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon bg-success text-white rounded-circle d-flex justify-content-center align-items-center" style="width:50px; height:50px;">
                        <i class="fa-solid fa-car-side fa-lg"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0">Kendaraan Tersedia</h6>
                        <h2 class="fw-bold mb-0">{{ $kendaraanTersedia }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon bg-warning text-dark rounded-circle d-flex justify-content-center align-items-center" style="width:50px; height:50px;">
                        <i class="fa-solid fa-location-arrow fa-lg"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0">Sedang Operasi</h6>
                        <h2 class="fw-bold mb-0">{{ $kendaraanOperasi }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon bg-danger text-white rounded-circle d-flex justify-content-center align-items-center" style="width:50px; height:50px;">
                        <i class="fa-solid fa-wrench fa-lg"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0">Diperbaiki</h6>
                        <h2 class="fw-bold mb-0">{{ $kendaraanPerbaikan }}</h2>
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- ========================== --}}
    {{-- GRAFIK --}}
    {{-- ========================== --}}
    <div class="card shadow-sm border-0 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">

            <h5 class="fw-bold mb-0">Grafik Peminjaman per Tanggal</h5>

            <form method="GET" class="d-flex gap-2">

                <select name="chart_month" class="form-select form-select-sm">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $m == $filterMonth ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endforeach
                </select>

                <select name="chart_year" class="form-select form-select-sm">
                    @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
                        <option value="{{ $y }}" {{ $y == $filterYear ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>

                <button class="btn btn-primary btn-sm">Tampilkan</button>
            </form>

        </div>

        <canvas id="borrowChart" height="110"></canvas>
    </div>


    {{-- ========================== --}}
    {{-- TABEL PEMINJAM --}}
    {{-- ========================== --}}
    <div class="card shadow-sm border-0 p-4 mb-4">
        <h5 class="fw-bold mb-3">Peminjaman Aktif</h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Kendaraan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($peminjam as $p)
                        <tr>
                            <td>{{ $p->user->NIP ?? '-' }}</td>
                            <td>{{ $p->user->name ?? '-' }}</td>
                            <td>{{ $p->vehicle->name ?? '-' }}</td>
                            <td>
                                <span class="badge
                                    @if($p->status == 'Pending') bg-secondary
                                    @elseif($p->status == 'In Use') bg-warning text-dark
                                    @endif">
                                    {{ $p->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>


    {{-- ========================== --}}
    {{-- JADWAL PIKET --}}
    {{-- ========================== --}}
    <div class="row g-4 pb-4">

        <div class="col-md-6">
            <div class="card p-3 shadow-sm border-0">
                <h6 class="text-muted">Piket Shift 1</h6>
                <h4 class="fw-bold">{{ $piketShift1?->team->name ?? '-' }}</h4>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3 shadow-sm border-0">
                <h6 class="text-muted">Piket Shift 2</h6>
                <h4 class="fw-bold">{{ $piketShift2?->team->name ?? '-' }}</h4>
            </div>
        </div>

    </div>

</div>

{{-- ========================== --}}
{{-- CHART SCRIPT --}}
{{-- ========================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('borrowChart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($chartDaily)) !!},
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: {!! json_encode(array_values($chartDaily)) !!},
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

@endsection

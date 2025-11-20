@extends('layouts.app')

@section('title', 'Dashboard Pegawai')

@section('content')
<div class="container-fluid pt-4 px-4">

    <h3 class="fw-bold">Hai {{ $user->name }}, Selamat Datang di FleetSAR Palu 👋</h3>

    {{-- INFO SHIFT --}}
    <div class="alert alert-info mt-3">
        <strong>Jadwal Hari Ini:</strong> {{ $shiftMessage }}
        <br>
        @if(str_contains($shiftMessage, 'Shift'))
            Jangan lupa melakukan pengecekan rutin kendaraan hari ini.
        @endif
    </div>

    {{-- KENDARAAN --}}
    <div class="row g-4 mt-2">

        {{-- Kendaraan Tersedia --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon bg-success text-white rounded-circle d-flex justify-content-center align-items-center"
                        style="width:50px; height:50px;">
                        <i class="fa-solid fa-car-side fa-lg"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0">Kendaraan Tersedia</h6>
                        <h2 class="fw-bold mb-0">{{ $kendaraanTersedia }}</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kendaraan Sedang Operasi --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon bg-warning text-dark rounded-circle d-flex justify-content-center align-items-center"
                        style="width:50px; height:50px;">
                        <i class="fa-solid fa-location-arrow fa-lg"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0">Sedang Operasi</h6>
                        <h2 class="fw-bold mb-0">{{ $kendaraanOperasi }}</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kendaraan Diperbaiki --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon bg-danger text-white rounded-circle d-flex justify-content-center align-items-center"
                        style="width:50px; height:50px;">
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


    {{-- PEMINJAMAN AKTIF --}}
    <div class="bg-light mt-4 p-3 rounded shadow-sm">
        <h5>Peminjaman Anda Saat Ini</h5>

        <table class="table table-striped mt-2">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Kendaraan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($borrowings as $b)
                    <tr>
                        <td>{{ $b->kode_pinjam }}</td>
                        <td>{{ $b->vehicle->name }}</td>
                        <td>{{ $b->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">Tidak ada peminjaman aktif</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

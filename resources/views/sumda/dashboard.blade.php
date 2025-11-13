@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- Data --}}
<div class="container-fluid text-center p-4">
    <div class="row g-4">
        <div class="col-sm-6 col-xxl-3">
            <div class="bg-light p-4 d-flex flex-row align-items-center justify-content-between rounded">
                <div>
                    <b class="text-start fs-1 d-inline-block w-100">{{ $jumlah_pegawai_aktif }}/{{ $jumlah_pegawai }}</b>
                    <p class="mb-1 fs-4">Jumlah Pegawai</p>
                </div>
                <i class="fa-solid fa-users fa-3x text-primary w-25"></i>
            </div>
        </div>

        {{-- ...lanjutkan semua card di sini --}}
    </div>
</div>

{{-- Chart & Table --}}
<div class="container-fluid pt-4 px-4 mb-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-6">
            <div class="bg-light text-center rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Peminjaman Bulanan</h6>
                    <form action="{{ route('admin.dashboard') }}" method="GET">
                        @csrf
                        <select name="tahun" class="form-select" onchange="form.submit()">
                            <option value="{{ $tahun }}" selected hidden>{{ $tahun }}</option>
                            @forelse($data_tahun_peminjaman as $tahun_peminjaman)
                                <option value="{{ $tahun_peminjaman }}">{{ $tahun_peminjaman }}</option>
                            @empty
                                <option value="{{ $tahun }}">{{ $tahun }}</option>
                            @endforelse
                        </select>
                    </form>
                </div>
                <div>{!! $chart->render() !!}</div>
            </div>
        </div>

        {{-- Table --}}
        <div class="col-sm-12 col-xl-6">
            <div class="bg-light text-center rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Pengajuan Peminjaman</h6>
                    <a href="{{ route('admin.data.peminjaman') }}" class="text-decoration-none">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table-hover table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>NIP Peminjam</th>
                                <th>Jumlah Kendaraan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data_peminjaman as $peminjaman)
                                <tr>
                                    <th>{{ ($data_peminjaman->currentPage()-1) * $data_peminjaman->perPage() + $loop->iteration }}</th>
                                    <td>{{ $peminjaman->nip_peminjam }}</td>
                                    <td>{{ $peminjaman->jumlah }}</td>
                                    <td>{{ $peminjaman->status }}</td>
                                </tr>
                            @empty
                                <h2>Data Kosong</h2>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

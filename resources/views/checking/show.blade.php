@extends('layouts.app')

@section('title', 'Detail Pengecekan Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-light rounded p-4 shadow-sm">
        {{-- Tombol kembali --}}
        <div class="mb-3">
            <a href="{{ route('checking.index') }}" class="text-decoration-none text-dark">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali
            </a>
        </div>

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Detail Pengecekan</h5>
            <div>
                @if(auth()->user()->id == $check->team?->leader_id)
                    <a href="{{ route('attendance.index', ['check_id' => $check->id]) }}" class="btn btn-sm btn-success me-2">
                        <i class="fas fa-user-check"></i> Kehadiran
                    </a>
                @endif

                @if($check->status == 'pending')
                    <a href="{{ route('checking.start', $check->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-play"></i> Mulai
                    </a>
                @endif
            </div>
        </div>

        {{-- Informasi Umum --}}
        <table class="table table-bordered mb-4">
            <tr>
                <th style="width: 200px;">Tanggal</th>
                <td>{{ \Carbon\Carbon::parse($check->scheduled_date)->format('d M Y') }}</td>
            </tr>
            <tr>
                <th>Tim</th>
                <td>{{ $check->team?->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Ketua Tim</th>
                <td>{{ $check->team?->leader?->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge
                        @if($check->status == 'completed') bg-success
                        @elseif($check->status == 'in_progress') bg-warning
                        @else bg-secondary @endif">
                        {{ ucfirst(str_replace('_', ' ', $check->status)) }}
                    </span>
                </td>
            </tr>
        </table>

        {{-- Daftar Kendaraan --}}
        <h6 class="fw-bold mb-3">Daftar Kendaraan yang Diperiksa</h6>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Kode BMN</th>
                        <th>Nama Kendaraan</th>
                        <th>Plat Nomor</th>
                        <th>Status Kendaraan</th>
                        <th>Status Cek</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($check->checkItems as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->vehicle->kode_bmn ?? '-' }}</td>
                            <td>{{ $item->vehicle->name ?? '-' }}</td>
                            <td>{{ $item->vehicle->plat_nomor ?? '-' }}</td>

                            {{-- Status kendaraan operasional --}}
                            <td>
                                @if($item->vehicle->status == 'digunakan' || $item->vehicle->status == 'Sedang Operasi')
                                    <span class="badge bg-info">Sedang Operasi</span>
                                @elseif($item->vehicle->status == 'perbaikan' || $item->vehicle->status == 'Sedang Dalam Perbaikan')
                                    <span class="badge bg-warning text-dark">Dalam Perbaikan</span>
                                @else
                                    <span class="badge bg-success">Siap Diperiksa</span>
                                @endif
                            </td>

                            {{-- Status cek kendaraan --}}
                            <td>
                                @if($item->status == 'skip')
                                    <span class="badge bg-secondary">Dilewati</span>
                                @elseif($item->status == 'checked')
                                    <span class="badge bg-success">Selesai Dicek</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum Dicek</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td>
                                @if($item->vehicle->status == 'digunakan' || $item->vehicle->status == 'Sedang Operasi' || $item->vehicle->status == 'perbaikan' || $item->vehicle->status == 'Sedang Dalam Perbaikan')
                                    <span class="text-muted small">Tidak bisa dicek</span>
                                @else
                                    @if($item->status == 'checked')
                                        <a href="{{ route('checkitem.show', $item->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                    @else
                                        <a href="{{ route('checkitem.edit', $item->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-check-circle"></i> Cek Sekarang
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">Belum ada kendaraan untuk dicek.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tombol selesaikan pengecekan --}}
        @php
            $total = $check->checkItems->count();
            $completed = $check->checkItems->where('status', 'checked')->count();
        @endphp

        @if($total > 0 && $completed == $total && $check->status != 'completed')
            <div class="text-end mt-3">
                <form action="{{ route('checking.complete', $check->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-flag-checkered me-2"></i> Tandai Selesai
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Rekap Kehadiran Tim')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-light rounded p-4 shadow-sm">

        {{-- Tombol kembali --}}
        <div class="mb-3">
            <a href="{{ route('checking.show', $check->id) }}" class="text-decoration-none text-dark">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Detail Pengecekan
            </a>
        </div>

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Rekap Kehadiran Tim</h5>
            <span class="text-muted">Tanggal: {{ \Carbon\Carbon::parse($check->scheduled_date)->format('d M Y') }}</span>
        </div>

        {{-- Info Tim --}}
        <table class="table table-bordered mb-4">
            <tr>
                <th style="width: 200px;">Nama Tim</th>
                <td>{{ $check->team->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Ketua Tim</th>
                <td>{{ $check->team->leader?->name ?? '-' }}</td>
            </tr>
        </table>

        {{-- Rekap Kehadiran --}}
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama Anggota</th>
                        <th>Status Kehadiran</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $attendance)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $attendance->user?->name ?? '-' }}</td>
                            <td>
                                @if ($attendance->status == 'hadir')
                                    <span class="badge bg-success">Hadir</span>
                                @elseif ($attendance->status == 'izin')
                                    <span class="badge bg-warning text-dark">Izin</span>
                                @else
                                    <span class="badge bg-danger">Alpha</span>
                                @endif
                            </td>
                            <td>{{ $attendance->note ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Belum ada data kehadiran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="text-end">
            <a href="{{ route('checking.show', $check->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Pengecekan
            </a>
        </div>
    </div>
</div>
@endsection

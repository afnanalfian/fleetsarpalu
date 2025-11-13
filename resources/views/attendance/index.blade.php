@extends('layouts.app')

@section('title', 'Kehadiran Anggota Tim')

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
            <h5 class="mb-0">Kehadiran Anggota Tim</h5>
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

        {{-- Form Kehadiran --}}
        <form action="{{ route('attendance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="check_id" value="{{ $check->id }}">

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Anggota</th>
                            <th>Status Kehadiran</th>
                            <th>Alasan (Jika tidak hadir)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($check->team->members as $member)
                            @php
                                $attendance = $attendances->firstWhere('user_id', $member->id);
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $member->name }}</td>
                                <td>
                                    <select name="attendances[{{ $member->id }}][status]" class="form-select">
                                        <option value="hadir" {{ $attendance?->status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                        <option value="izin" {{ $attendance?->status == 'izin' ? 'selected' : '' }}>Izin</option>
                                        <option value="alpha" {{ $attendance?->status == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="attendances[{{ $member->id }}][note]"
                                           value="{{ $attendance?->note }}" class="form-control" placeholder="Catatan opsional">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada anggota di tim ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save me-2"></i> Simpan Kehadiran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

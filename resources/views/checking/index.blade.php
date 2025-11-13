@extends('layouts.app')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Daftar Pengecekan Kendaraan</h4>
        <a href="{{ route('checking.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Buat Pengecekan Baru
        </a>
    </div>

    <div class="bg-light rounded p-4 shadow-sm">
        {{-- Filter --}}
        <form method="GET" action="{{ route('checking.index') }}" class="mb-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="date" name="date" value="{{ request('date') }}" class="form-control" placeholder="Tanggal">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Berjalan</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Tim</th>
                        <th>Ketua Tim</th>
                        <th>Jumlah Kendaraan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($checks as $check)
                        @php
                            $total = $check->checkItems->count();
                            $done = $check->checkItems->whereNotNull('condition')->count();
                            $skipped = $check->checkItems->where('status', 'skip')->count();
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($check->scheduled_date)->format('d M Y') }}</td>
                            <td>{{ $check->team?->name ?? '-' }}</td>
                            <td>{{ $check->team?->leader?->name ?? '-' }}</td>
                            <td>
                                {{ $total }} kendaraan
                                @if($skipped > 0)
                                    <small class="text-muted d-block">({{ $skipped }} dilewati)</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge
                                    @if($check->status == 'completed') bg-success
                                    @elseif($check->status == 'in_progress') bg-warning
                                    @else bg-secondary @endif">
                                    {{ ucfirst(str_replace('_', ' ', $check->status)) }}
                                </span>
                            </td>
                            <td>
                                {{-- Detail --}}
                                <a href="{{ route('checking.show', $check->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>

                                {{-- Kehadiran (hanya untuk ketua tim) --}}
                                @if(auth()->user()->id == $check->team?->leader_id)
                                    <a href="{{ route('attendance.index', ['check_id' => $check->id]) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-user-check"></i> Kehadiran
                                    </a>
                                @endif

                                {{-- Mulai (hanya kalau status pending) --}}
                                @if($check->status == 'pending')
                                    <a href="{{ route('checking.start', $check->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-play"></i> Mulai
                                    </a>
                                @endif

                                {{-- Progres --}}
                                @if($check->status == 'in_progress')
                                    <span class="text-muted small">
                                        {{ $done }}/{{ $total }} selesai
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">Belum ada data pengecekan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {!! $checks->links() !!}
        </div>
    </div>
</div>
@endsection

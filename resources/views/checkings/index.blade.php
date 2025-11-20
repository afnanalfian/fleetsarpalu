@extends('layouts.app')

@section('title', 'Daftar Pengecekan Tim')

@section('content')

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">

            {{-- CARD WRAPPER --}}
            <div class="bg-light rounded p-4">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Daftar Pengecekan Kendaraan</h5>

                    <div class="d-flex gap-2">
                        {{-- Filter Bulan & Tahun --}}
                        <form method="GET" action="{{ route('checkings.index') }}" class="d-flex gap-2">
                            <select name="bulan" class="form-select form-select-sm" required>
                                <option value="">Bulan</option>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="tahun" class="form-select form-select-sm" required>
                                <option value="">Tahun</option>
                                @for($y = date('Y')-5; $y <= date('Y')+5; $y++)
                                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>

                            <button class="btn btn-secondary btn-sm">Filter</button>
                            <a href="{{ route('checkings.index') }}" class="btn btn-outline-dark btn-sm">Reset</a>
                        </form>

                        {{-- Buat Pengecekan --}}
                        @if(auth()->user()->role === 'Ketua Tim')
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCheckingModal">
                                + Buat Pengecekan
                            </button>
                        @endif
                    </div>
                </div>

                {{-- TABEL --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tim</th>
                                <th>Shift</th>
                                <th>Status</th>
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($checkings as $checking)
                                <tr>
                                    {{-- Tanggal --}}
                                    <td>{{ \Carbon\Carbon::parse($checking->scheduled_date)->format('d M Y') }}</td>

                                    {{-- Tim --}}
                                    <td>{{ $checking->team->name ?? '-' }}</td>

                                    {{-- Shift --}}
                                    <td>
                                        @php
                                            $shiftClass = [
                                                'Shift 1' => 'primary',
                                                'Shift 2' => 'info',
                                            ][$checking->shift] ?? 'secondary';
                                        @endphp

                                        <span class="badge bg-{{ $shiftClass }}">
                                            {{ $checking->shift ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        @php
                                            $statusClass = [
                                                'pending'     => 'secondary',
                                                'in_progress' => 'warning',
                                                'completed'   => 'success',
                                            ][$checking->status] ?? 'dark';
                                        @endphp

                                        <span class="badge bg-{{ $statusClass }} text-uppercase">
                                            {{ $checking->status }}
                                        </span>
                                    </td>

                                    {{-- Jam Mulai --}}
                                    <td>
                                        {{ $checking->started_at
                                            ? \Carbon\Carbon::parse($checking->started_at)->format('H:i')
                                            : '-' }}
                                    </td>

                                    {{-- Jam Selesai --}}
                                    <td>
                                        {{ $checking->completed_at
                                            ? \Carbon\Carbon::parse($checking->completed_at)->format('H:i')
                                            : '-' }}
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="text-center">
                                        <div class="btn-group">

                                            {{-- ABSENSI (hanya tampil jika attendances belum ada) --}}
                                            @if(auth()->user()->role === 'Ketua Tim' && $checking->attendances->count() == 0)
                                                <a href="{{ route('attendances.create', $checking->id) }}"
                                                class="btn btn-warning btn-sm">
                                                    Absensi
                                                </a>
                                            @endif

                                            {{-- DETAIL (in_progress & completed) --}}
                                            @if(in_array($checking->status, ['pending','in_progress', 'completed']))
                                                <a href="{{ route('checkings.show', $checking->id) }}"
                                                class="btn btn-secondary btn-sm">
                                                    Detail
                                                </a>
                                            @endif

                                            {{-- HAPUS (hanya pending) --}}
                                            @if(auth()->user()->role === 'Ketua Tim' && $checking->status === 'pending')
                                                <button class="btn btn-danger btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#hapus{{ $checking->id }}">
                                                    Hapus
                                                </button>
                                            @endif

                                        </div>
                                    </td>

                                        </div>
                                    </td>
                                </tr>

                                {{-- MODAL DELETE --}}
                                <div class="modal fade" id="hapus{{ $checking->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">Hapus Pengecekan</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus pengecekan pada tanggal
                                                <strong>{{ \Carbon\Carbon::parse($checking->scheduled_date)->format('d M Y') }}</strong>?
                                            </div>

                                            <div class="modal-footer">
                                                <form action="{{ route('checkings.destroy', $checking->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger">Hapus</button>
                                                </form>
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <h5 class="text-muted">Belum ada pengecekan</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="mt-3">
                    {{ $checkings->links() }}
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ========================= --}}
{{-- MODAL CREATE CHECKING --}}
{{-- ========================= --}}
@php
    use Carbon\Carbon;

    $teamName = auth()->user()->team->name ?? '-';

    $now = Carbon::now();
    $hour = (int) $now->format('H');

    // Tentukan shift & tanggal
    if ($hour >= 8 && $hour < 20) {
        $shift = 'Shift 1';
        $displayDate = $now->format('d M Y');
    } else {
        $shift = 'Shift 2';
        // Jika jam 00:00–07:59 → mundurkan tanggal
        $shiftDate = $hour < 8 ? $now->copy()->subDay() : $now;
        $displayDate = $shiftDate->format('d M Y');
    }
@endphp

<div class="modal fade" id="createCheckingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Buat Pengecekan</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>
                    Apakah benar <strong>{{ $teamName }}</strong> akan melakukan pengecekan pada
                    <strong>{{ $displayDate }}</strong>?
                </p>

                <p class="mt-2">
                    <strong>Shift:</strong> {{ $shift }}
                </p>
            </div>

            <div class="modal-footer">
                <form action="{{ route('checkings.store') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Buat</button>
                </form>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>

        </div>
    </div>
</div>


@endsection

@extends('layouts.app')

@section('title', 'Jadwal Siaga Bulanan')

@section('content')
<div class="container-fluid pt-4 px-4">

    <h3 class="text-center fw-bold mb-4">Jadwal Siaga Bulan {{ $months[$month] }} {{ $year }}</h3>

    {{-- Filter Bulan & Tahun --}}
    <form class="row g-3 mb-4" method="GET" action="{{ route('schedules.index') }}">
        <div class="col-md-3 offset-md-3">
            <select name="month" class="form-select">
                @foreach ($months as $m => $label)
                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="year" class="form-select">
                @for ($y = now()->year - 3; $y <= now()->year + 3; $y++)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-secondary w-100">Tampilkan</button>
        </div>
    </form>

    {{-- Generate (Admin only) --}}
    @if(auth()->user()->role === 'Admin'||auth()->user()->role === 'Kepala Sumber Daya')
        <div class="text-center mb-4">
            <form method="POST" action="{{ route('schedules.generate') }}">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <button class="btn btn-warning">Generate Jadwal Bulan Ini</button>
            </form>
        </div>
    @endif

    {{-- TABEL --}}
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle" style="min-width: 1400px;">

            {{-- Header tanggal --}}
            <thead>
                <tr class="bg-warning fw-bold">
                    <th class="freeze-col-header" style="width:150px;">REGU</th>
                    @for ($d = $start->copy(); $d <= $end; $d->addDay())
                        <th>{{ $d->format('d') }}</th>
                    @endfor
                </tr>
            </thead>

            <tbody>
            @foreach ($teams as $team)
                <tr>
                    <th class="bg-warning fw-bold freeze-col">{{ $team->name }}</th>

                    @for ($d = $start->copy(); $d <= $end; $d->addDay())
                        @php
                            $teamSchedule = $schedules[$team->id] ?? collect();
                            $item = $teamSchedule->firstWhere('date', $d->toDateString());
                            $shift = $item->shift ?? '';

                            $bg = match ($shift) {
                                'LB' => '#ff6d5a',
                                'S1' => '#ffb84d',
                                'S2' => '#ffd27f',
                                'R'  => '#ffffff',
                                default => '#ffffff'
                            };
                        @endphp
                        <td style="background: {{ $bg }}; font-weight:600;">
                            {{ $shift }}
                        </td>
                    @endfor

                </tr>
            @endforeach
            </tbody>

        </table>
    </div>

</div>

<style>
    /* Freeze kolom pertama */
    .freeze-col {
        position: sticky;
        left: 0;
        z-index: 10;
        background: #f8c146 !important; /* warna bg-warning */
    }

    .freeze-col-header {
        position: sticky;
        left: 0;
        z-index: 20;
        background: #f8c146 !important;
    }
</style>
@endsection

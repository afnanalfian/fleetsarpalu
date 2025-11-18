@extends('layouts.app')

@section('title', 'Detail Pengecekan')

@section('content')

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">

            <div class="bg-light rounded p-4">

                {{-- Tombol Kembali --}}
                <div class="text-start mb-4">
                    <a href="{{ route('checkings.index') }}" class="text-decoration-none text-black">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                {{-- DETAIL PENGECEKAN --}}
                <h4 class="fw-bold mb-3">Detail Pengecekan</h4>

                <table class="table table-bordered">
                    <tr>
                        <th class="w-25">Tanggal</th>
                        <td>{{ \Carbon\Carbon::parse($check->scheduled_date)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Tim</th>
                        <td>{{ $check->team->name }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge
                                @if($check->status == 'pending') bg-secondary
                                @elseif($check->status == 'in_progress') bg-info
                                @else bg-success @endif">
                                {{ ucfirst(str_replace('_',' ', $check->status)) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Mulai</th>
                        <td>{{ $check->started_at ? $check->started_at->format('H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Selesai</th>
                        <td>{{ $check->completed_at ? $check->completed_at->format('H:i') : '-' }}</td>
                    </tr>
                </table>


                {{-- ========================== --}}
                {{--        SECTION: ABSENSI     --}}
                {{-- ========================== --}}

                <h4 class="fw-bold mt-4">Absensi</h4>

                @php
                    $attendanceList = $check->attendances;   // hasMany
                    $teamMembers    = $check->team->users;   // anggota tim
                @endphp

                {{-- Jika belum ada absensi --}}
                @if($attendanceList->isEmpty())
                    <p class="text-danger fst-italic">Absensi belum dibuat.</p>

                    <a href="{{ route('attendances.create', $check->id) }}"
                    class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-user-check me-1"></i> Buat Absensi
                    </a>

                @else
                    @php
                        $presentCount = $attendanceList->where('present', 1)->count();
                        $totalMembers = $teamMembers->count();
                    @endphp

                    <p class="fw-semibold">Hadir: {{ $presentCount }}/{{ $totalMembers }}</p>

                    <table class="table table-sm table-bordered mt-2">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Hadir?</th>
                                <th>Alasan</th>
                                <th>Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceList as $a)
                                <tr>
                                    <td>{{ $a->user->name }}</td>

                                    <td>
                                        @if($a->present)
                                            <span class="badge bg-success">Hadir</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Hadir</span>
                                        @endif
                                    </td>

                                    <td>{{ $a->reason ?: '-' }}</td>

                                    <td>
                                        @if($a->bukti_path)
                                            <a href="{{ asset('storage/' . $a->bukti_path) }}"
                                            target="_blank" class="btn btn-info btn-sm">
                                                Lihat
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <a href="{{ route('attendances.edit', $check->id) }}"
                    class="btn btn-warning btn-sm mt-2">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Absensi
                    </a>
                @endif



                {{-- ========================== --}}
                {{--    SECTION: KENDARAAN     --}}
                {{-- ========================== --}}

                <h4 class="fw-bold mt-4">Daftar Kendaraan</h4>

                @foreach($vehicles as $vehicle)
                    @php
                        // Cari apakah kendaraan ini punya checkitem pada pengecekan ini
                        $item = $checkItems->get($vehicle->id);
                    @endphp

                    <div class="border rounded p-3 mb-3 d-flex align-items-start gap-3 bg-white">

                        {{-- FOTO --}}
                        <img src="{{ asset('storage/'.$vehicle->photo_path) }}"
                            class="rounded"
                            style="width: 120px; height: 90px; object-fit: cover;"
                            alt="Foto">

                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">{{ $vehicle->name }}</h5>

                            {{-- Jika kendaraan sedang digunakan --}}
                            @if($vehicle->status === 'is_use')
                                <span class="badge bg-warning text-dark">Sedang Digunakan — Tidak dapat dicek</span>

                            {{-- Jika kendaraan rusak --}}
                            @elseif($vehicle->status === 'unavailable')
                                <span class="badge bg-danger">Sedang Dalam Perbaikan — Tidak dapat dicek</span>

                            {{-- Jika kendaraan available tetapi TIDAK dibuat checkitem --}}
                            @elseif(!$item)
                                <span class="badge bg-secondary">
                                    Tidak berada di lokasi saat pengecekan dibuat — Tidak dapat dicek
                                </span>

                            {{-- Jika KENDARAAN ADA CHECKITEMNYA --}}
                            @else
                                {{-- Jika sudah dicek --}}
                                @if($item->fuel_percent !== null)
                                    <p class="mb-1">Fuel: {{ $item->fuel_percent }}%</p>
                                    <p class="mb-1">KM: {{ $item->km }}</p>

                                    {{-- Tentukan kondisi --}}
                                    @php
                                        $condition = $item->condition === 'Baik' ? 'bg-success' : 'bg-danger';
                                    @endphp

                                    <span class="badge {{ $condition }}">
                                        {{ $item->condition }}
                                    </span>

                                    <div class="mt-2">
                                        <a href="{{ route('checkitems.show', $item->id) }}" class="btn btn-info btn-sm">Detail</a>
                                        <a href="{{ route('checkitems.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    </div>

                                {{-- Belum dicek --}}
                                @else
                                    <span class="badge bg-primary">Belum dicek</span>

                                    <div class="mt-2">
                                        <a href="{{ route('checkitems.create', [$check->id, $vehicle->id]) }}"
                                        class="btn btn-primary btn-sm">
                                            Lakukan Pengecekan
                                        </a>
                                    </div>
                                @endif
                            @endif

                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>

@endsection

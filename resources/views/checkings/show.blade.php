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

                <div class="mb-3">
                    <a href="{{ asset('sop/SOP_Pengecekan_Rutin.pdf') }}"
                    class="btn btn-outline-primary btn-sm"
                    target="_blank">
                        <i class="fa-solid fa-file-pdf me-1"></i> Lihat SOP Pengecekan Rutin
                    </a>
                </div>

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
                    $attendanceList = $check->attendances;
                    $teamMembers    = $check->team->users;
                @endphp

                @if($attendanceList->isEmpty())
                    <p class="text-danger fst-italic">Absensi belum dibuat.</p>

                    @if(auth()->user()->role === 'Ketua Tim')
                        <a href="{{ route('attendances.create', $check->id) }}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-user-check me-1"></i> Buat Absensi
                        </a>
                    @endif

                @else

                    <table class="table table-sm table-bordered mt-2 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>Bukti</th>
                                <th>Pengganti</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceList as $a)
                                <tr>

                                    {{-- Nama anggota tim / atau nama pengganti --}}
                                    <td>
                                        {{ $a->user->name }}
                                        @if($a->is_replacement)
                                            <span class="badge bg-info ms-1">Pengganti</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        <span class="badge
                                            @if($a->status === 'Hadir') bg-success
                                            @elseif(in_array($a->status, ['Sakit','Izin','Cuti'])) bg-warning
                                            @else bg-danger @endif">
                                            {{ $a->status }}
                                        </span>
                                    </td>

                                    {{-- Bukti --}}
                                    <td>
                                        @if($a->bukti_path)
                                            <a href="{{ asset('storage/'.$a->bukti_path) }}"
                                            target="_blank"
                                            class="btn btn-info btn-sm">
                                                Lihat Bukti
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- Pengganti --}}
                                    <td>
                                        @if($a->replacement_user_id)
                                            <span class="text-primary fw-semibold">
                                                {{ $a->replacement->name }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- Catatan --}}
                                    <td>
                                        {{ $a->notes ?: '-' }}
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if(auth()->user()->role === 'Ketua Tim' && auth()->user()->team_id == $check->team_id)
                        <a href="{{ route('attendances.edit', $check->id) }}"
                        class="btn btn-warning btn-sm mt-2">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit Absensi
                        </a>
                    @endif

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
                                @if($item && !is_null($item->fuel_percent))
                                    <p class="mb-1">Fuel: {{ $item->fuel_percent }}%</p>
                                    <p class="mb-1">KM: {{ $item->km }}</p>

                                    {{-- Tentukan kondisi --}}
                                    @php
                                        $value = $item->condition ?? null;

                                        $badge = [
                                            'Baik'         => 'bg-success',
                                            'Rusak Ringan' => 'bg-warning text-dark',
                                            'Rusak Berat'  => 'bg-danger',
                                        ];

                                        $badgeClass = $badge[$value] ?? 'bg-secondary';
                                    @endphp

                                    <span class="badge {{ $badgeClass }}">
                                        {{ $value ?? '-' }}
                                    </span>

                                    <div class="mt-2">
                                        <a href="{{ route('checkitems.show', $item->id) }}" class="btn btn-info btn-sm">Detail</a>
                                        @if (in_array(strtolower(auth()->user()->role), ['pegawai', 'ketua tim']))
                                            <a href="{{ route('checkitems.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        @endif
                                    </div>

                                {{-- Belum dicek --}}
                                @else
                                    <span class="badge bg-primary">Belum dicek</span>
                                    @if (in_array(strtolower(auth()->user()->role), ['pegawai', 'ketua tim']))
                                        <div class="mt-2">
                                            <a href="{{ route('checkitems.edit', $item->id) }}"
                                            class="btn btn-primary btn-sm">
                                                Lakukan Pengecekan
                                            </a>
                                        </div>
                                    @endif
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

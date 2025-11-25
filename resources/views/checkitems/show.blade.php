@extends('layouts.app')

@section('title', 'Detail Pemeriksaan Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">

    <div class="row g-4">
        <div class="col-12">

            <div class="bg-light rounded p-4">

                <div class="text-start mb-3">
                    <a href="{{ route('checkings.show', $check->id) }}" class="text-decoration-none text-black">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <h4 class="fw-bold mb-3">Detail Pemeriksaan Kendaraan</h4>

                {{-- Kendaraan --}}
                <div class="border p-3 rounded mb-4 d-flex gap-3 bg-white">
                    <img src="{{ $vehicle->photo_path ? asset('storage/'.$vehicle->photo_path) : asset('img/no-image.png') }}"
                         class="rounded"
                         style="width: 150px;height:110px;object-fit:cover;">

                    <div>
                        <h5 class="fw-bold">{{ $vehicle->name }}</h5>
                        <p class="mb-0"><strong>Plat:</strong> {{ $vehicle->plat_nomor }}</p>
                        <p class="mb-0"><strong>Kondisi:</strong>
                            <span class="badge @if($item->condition=='Baik') bg-success @else bg-danger @endif">
                                {{ $item->condition }}
                            </span>
                        </p>
                    </div>
                </div>

                {{-- Data Utama --}}
                <table class="table table-bordered bg-white">
                    <tr>
                        <th>Persentase BBM</th>
                        <td>{{ $item->fuel_percent }}%</td>
                    </tr>
                    <tr>
                        <th>Kilometer</th>
                        <td>{{ $item->km }} KM</td>
                    </tr>
                </table>

                {{-- Checklist --}}
                <h5 class="fw-bold mt-4">Checklist Kendaraan</h5>
                <table class="table table-striped bg-white">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Kondisi</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                            $items = [
                                // Mekanikal
                                'radiator' => 'Radiator',
                                'air_filter' => 'Filter Udara',
                                'wiper' => 'Wiper',
                                'lights' => 'Lampu Kendaraan',
                                'leaks' => 'Kebocoran Fluida',
                                'hazards' => 'Lampu Hazard',
                                'horn' => 'Klakson',
                                'siren' => 'Sirine',
                                'tires' => 'Ban',
                                'brakes' => 'Rem',
                                'battery' => 'Aki',
                                'start_engine' => 'Mesin',

                                // Kebersihan
                                'glass_cleanliness' => 'Kaca',
                                'body_cleanliness' => 'Body',
                                'interior_cleanliness' => 'Interior'
                            ];

                            $mechanical = [
                                'Baik' => 'bg-success',
                                'Rusak Ringan' => 'bg-warning text-dark',
                                'Rusak Berat' => 'bg-danger',
                            ];

                            $cleanliness = [
                                'Bersih' => 'bg-success',
                                'Tidak Bersih' => 'bg-danger',
                            ];
                        @endphp

                        @foreach($items as $key => $label)
                            @php
                                $value = $item->{$key.'_ok'} ?? null;
                                $note  = $item->{$key.'_note'} ?: '-';

                                // Tentukan apakah ini kelompok kebersihan
                                $isCleanliness = str_contains($key, 'cleanliness');

                                // Tentukan badge
                                if ($isCleanliness) {
                                    $badgeClass = $cleanliness[$value] ?? 'bg-secondary';
                                } else {
                                    $badgeClass = $mechanical[$value] ?? 'bg-secondary';
                                }
                            @endphp

                            <tr>
                                <td>{{ $label }}</td>

                                {{-- Kondisi --}}
                                <td>
                                    @if($value)
                                        <span class="badge {{ $badgeClass }}">{{ $value }}</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>

                                {{-- Catatan --}}
                                <td>{{ $note }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

                {{-- Foto --}}
                <h5 class="fw-bold mt-4">Foto Pemeriksaan</h5>

                @if($item->photos)
                    @foreach(json_decode($item->photos, true) as $photo)
                        <img src="{{ asset('storage/'.$photo) }}"
                             class="rounded border me-2 mb-2"
                             style="width:150px;height:120px;object-fit:cover;">
                    @endforeach
                @else
                    <p class="text-muted">Tidak ada foto.</p>
                @endif

            </div>
        </div>
    </div>

</div>
@endsection

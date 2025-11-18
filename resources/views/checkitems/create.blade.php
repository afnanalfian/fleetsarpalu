@extends('layouts.app')

@section('title', 'Cek Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">

    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded p-4">

                {{-- Tombol Kembali --}}
                <div class="text-start mb-3">
                    <a href="{{ route('checkings.show', $check->id) }}" class="text-decoration-none text-black">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                {{-- Header Kendaraan --}}
                <div class="border p-3 rounded bg-white mb-4 d-flex gap-3">

                    <img src="{{ $vehicle->photo_path ? asset('storage/'.$vehicle->photo_path) : asset('img/no-image.png') }}"
                         class="rounded"
                         style="width: 140px; height: 100px; object-fit: cover">

                    <div>
                        <h5 class="fw-bold mb-1">{{ $vehicle->name }}</h5>
                        <p class="mb-0"><strong>Plat:</strong> {{ $vehicle->plat_nomor }}</p>
                        <p class="mb-0"><strong>Status:</strong>
                            <span class="badge
                                @if($vehicle->status=='available') bg-success
                                @elseif($vehicle->status=='is_use') bg-primary
                                @else bg-danger @endif">
                                {{ strtoupper($vehicle->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                {{-- FORM CEKLIST --}}
                <form action="{{ route('checkitems.store', ['check_id' => $check->id, 'vehicle_id' => $vehicle->id]) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    <input type="hidden" name="check_id" value="{{ $check->id }}">
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                    {{-- Fuel & KM --}}
                    <div class="col-md-6">
                        <label class="form-label text-start w-100">Persentase BBM (%) <span class="text-danger">*</span></label>
                        <input type="number" name="fuel_percent" class="form-control" value="{{ old('fuel_percent') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-start w-100">Kilometer <span class="text-danger">*</span></label>
                        <input type="number" name="km" class="form-control" value="{{ old('km') }}" required>
                    </div>

                    {{-- Checklist --}}
                    <div class="col-12 mt-3">
                        <h5 class="fw-bold text-start">Checklist Kendaraan</h5>
                    </div>

                    @php
                        $items = [
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
                            'start_engine' => 'Starter Mesin',
                            'glass_cleanliness' => 'Kebersihan Kaca',
                            'body_cleanliness' => 'Kebersihan Body'
                        ];
                    @endphp

                    @foreach($items as $key => $label)
                        <div class="col-md-12">
                            <label class="form-label w-100 fw-bold text-uppercase">{{ $label }} <span class="text-danger">*</span></label>

                            <div class="d-flex flex-wrap align-items-center gap-3">

                                {{-- Aman --}}
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                        name="{{ $key }}_ok" id="{{ $key }}_ok1" value="1" required>
                                    <label class="form-check-label text-success fw-semibold" for="{{ $key }}_ok1">
                                        Aman
                                    </label>
                                </div>

                                {{-- Tidak Aman --}}
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                        name="{{ $key }}_ok" id="{{ $key }}_ok0" value="0">
                                    <label class="form-check-label text-danger fw-semibold" for="{{ $key }}_ok0">
                                        Tidak Aman
                                    </label>
                                </div>

                                {{-- Note --}}
                                <div class="flex-grow-1">
                                    <input type="text"
                                        class="form-control"
                                        name="{{ $key }}_note"
                                        placeholder="Catatan (opsional)">
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Foto --}}
                    <div class="col-12 mt-3">
                        <label class="form-label fw-bold">Foto Bukti (opsional)</label>
                        <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
                    </div>

                    <div class="col-12 mt-3 text-start">
                        <button class="btn btn-primary">Simpan Hasil Cek</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection

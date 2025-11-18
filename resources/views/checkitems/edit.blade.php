@extends('layouts.app')

@section('title', 'Edit Hasil Cek Kendaraan')

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
                    </div>
                </div>

                {{-- FORM EDIT --}}
                <form action="{{ route('checkitems.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    @method('PUT')

                    {{-- Fuel & KM --}}
                    <div class="col-md-6">
                        <label class="form-label text-start w-100">Persentase BBM (%)</label>
                        <input type="number" name="fuel_percent" class="form-control"
                            value="{{ old('fuel_percent', $item->fuel_percent) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-start w-100">Kilometer</label>
                        <input type="number" name="km" class="form-control"
                            value="{{ old('km', $item->km) }}" required>
                    </div>

                    {{-- Checklist --}}
                    <div class="col-12 mt-3">
                        <h5 class="fw-bold text-start">Checklist Kendaraan</h5>
                    </div>

                    @foreach($items as $key => $label)
                        <div class="col-md-12">
                            <label class="form-label w-100 fw-bold text-uppercase">
                                {{ $label }} <span class="text-danger">*</span>
                            </label>

                            <div class="d-flex flex-wrap align-items-center gap-3">
                                {{-- Aman --}}
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="{{ $key }}_ok"
                                           value="1"
                                           {{ $item->{$key.'_ok'} == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label text-success fw-semibold">Aman</label>
                                </div>

                                {{-- Tidak Aman --}}
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="{{ $key }}_ok"
                                           value="0"
                                           {{ $item->{$key.'_ok'} == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label text-danger fw-semibold">Tidak Aman</label>
                                </div>

                                {{-- Note --}}
                                <div class="flex-grow-1">
                                    <input type="text"
                                           class="form-control"
                                           name="{{ $key }}_note"
                                           value="{{ $item->{$key.'_note'} }}"
                                           placeholder="Catatan (opsional)">
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Foto Baru --}}
                    <div class="col-12 mt-3">
                        <label class="form-label fw-bold">Tambahkan Foto Baru</label>
                        <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
                    </div>

                    {{-- Foto Lama --}}
                    @if($item->photos)
                        <div class="col-12 mt-3">
                            <label class="form-label fw-bold">Foto Sebelumnya</label><br>
                            @foreach(json_decode($item->photos, true) as $p)
                                <img src="{{ asset('storage/'.$p) }}"
                                     class="rounded border me-2 mb-2"
                                     style="width: 120px; height: 100px; object-fit: cover;">
                            @endforeach
                        </div>
                    @endif

                    <div class="col-12 mt-4 text-start">
                        <button class="btn btn-success">Update Hasil Cek</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>
@endsection

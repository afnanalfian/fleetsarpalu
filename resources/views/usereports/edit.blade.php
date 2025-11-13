@extends('layouts.app')

@section('title', 'Edit Laporan Penggunaan Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded p-4">
                <div class="text-start mb-4">
                    <a href="{{ route('usereports.show',$report->id) }}" class="mb-0 text-decoration-none text-black">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                {{-- Detail Peminjaman --}}
                <div class="border rounded p-3 mb-4 bg-white">
                    <h5 class="fw-bold mb-2">Detail Peminjaman</h5>
                    <p class="mb-0"><strong>Kode Pinjam:</strong> {{ $report->borrowRequest->kode_pinjam }}</p>
                    <p class="mb-0"><strong>Kendaraan:</strong> {{ $report->borrowRequest->vehicle->name ?? '-' }}</p>
                    <p class="mb-0"><strong>Tujuan:</strong> {{ $report->borrowRequest->destination_address }}</p>
                    <p class="mb-0"><strong>Tanggal:</strong>
                        {{ \Carbon\Carbon::parse($report->borrowRequest->start_at)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($report->borrowRequest->end_at)->format('d M Y') }}
                    </p>
                </div>

                {{-- Form Edit --}}
                <form action="{{ route('usereports.update', $report->id) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    @method('PUT')

                    {{-- Fuel & Kilometer --}}
                    <div class="col-md-6">
                        <label class="form-label text-start w-100">Persentase Bahan Bakar Sebelum<span class="text-danger">*</span></label>
                        <input type="number" name="fuel_before" class="form-control @error('fuel_before') is-invalid @enderror"
                               value="{{ old('fuel_before', $report->fuel_before) }}" placeholder="0-100" required>
                        @error('fuel_before')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-start w-100">Persentase Bahan Bakar Setelah<span class="text-danger">*</span></label>
                        <input type="number" name="fuel_after" class="form-control @error('fuel_after') is-invalid @enderror"
                               value="{{ old('fuel_after', $report->fuel_after) }}" placeholder="0-100" required>
                        @error('fuel_after')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-start w-100">Kilometer Sebelum<span class="text-danger">*</span></label>
                        <input type="number" name="km_before" class="form-control @error('km_before') is-invalid @enderror"
                               value="{{ old('km_before', $report->km_before) }}" required>
                        @error('km_before')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-start w-100">Kilometer Setelah<span class="text-danger">*</span></label>
                        <input type="number" name="km_after" class="form-control @error('km_after') is-invalid @enderror"
                               value="{{ old('km_after', $report->km_after) }}" required>
                        @error('km_after')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    {{-- Checklist --}}
                    <div class="col-12 mt-3">
                        <h5 class="fw-bold text-start mb-3">Pemeriksaan Kondisi Kendaraan</h5>
                    </div>

                    @php
                        $items = [
                            'hazards' => 'Lampu Hazard',
                            'horn' => 'Klakson',
                            'siren' => 'Sirine',
                            'tires' => 'Ban',
                            'brakes' => 'Rem',
                            'battery' => 'Aki',
                            'start_engine' => 'Starter Mesin',
                        ];
                    @endphp

                    @foreach($items as $key => $label)
                        <div class="col-md-12">
                            <label class="form-label w-100 text-start fw-bold text-uppercase">
                                {{ $label }} <span class="text-danger">*</span>
                            </label>

                            <div class="d-flex flex-wrap align-items-center gap-3">
                                {{-- Aman --}}
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="{{ $key }}_ok" id="{{ $key }}_ok1" value="1"
                                           {{ old($key.'_ok', $report->{$key.'_ok'}) == '1' ? 'checked' : '' }} required>
                                    <label class="form-check-label fw-semibold text-success" for="{{ $key }}_ok1">AMAN</label>
                                </div>

                                {{-- Tidak Aman --}}
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="{{ $key }}_ok" id="{{ $key }}_ok0" value="0"
                                           {{ old($key.'_ok', $report->{$key.'_ok'}) == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-danger" for="{{ $key }}_ok0">TIDAK AMAN</label>
                                </div>

                                {{-- Catatan --}}
                                <div class="flex-grow-1">
                                    <input type="text" name="{{ $key }}_note"
                                           class="form-control @error($key.'_note') is-invalid @enderror"
                                           placeholder="Catatan (opsional)"
                                           value="{{ old($key.'_note', $report->{$key.'_note'}) }}">
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Foto --}}
                    @php
                        $photoFields = [
                            'indicator_before_photos_path' => 'Foto Indikator Sebelum',
                            'indicator_after_photos_path' => 'Foto Indikator Setelah',
                            'location_photos_path' => 'Foto Lokasi',
                        ];
                    @endphp

                    @foreach($photoFields as $field => $label)
                        <div class="col-md-4">
                            <label class="form-label w-100 text-start">{{ $label }}</label>
                            <input type="file" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror" accept="image/*">
                            @if($report->$field)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $report->$field) }}" alt="Foto" class="img-fluid rounded border" style="max-height: 150px;">
                                </div>
                            @endif
                            @error($field)<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    @endforeach

                    <div class="col-12 text-start mt-4">
                        <button type="submit" class="btn btn-success">Update Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

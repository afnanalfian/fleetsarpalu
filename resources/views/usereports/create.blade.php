@extends('layouts.app')

@section('title', 'Laporan Penggunaan Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded p-4">
                <div class="text-start mb-4">
                    <a href="{{ route('borrowings.index') }}" class="mb-0 text-decoration-none text-black">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                {{-- Detail Peminjaman --}}
                <div class="border rounded p-3 mb-4 bg-white">
                    <h5 class="fw-bold mb-2">Detail Peminjaman</h5>
                    <p class="mb-0"><strong>Kode Pinjam:</strong> {{ $borrow->kode_pinjam }}</p>
                    <p class="mb-0"><strong>Kendaraan:</strong> {{ $borrow->vehicle->name ?? '-' }}</p>
                    <p class="mb-0"><strong>Tujuan:</strong> {{ $borrow->destination_address }}</p>
                    <p class="mb-0"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($borrow->start_at)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($borrow->end_at)->format('d M Y') }}</p>
                </div>

                {{-- Form Laporan --}}
                <form action="{{ route('usereports.store', $borrow->id) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf

                    {{-- Fuel & Kilometer --}}
                    <div class="col-md-6">
                        <label class="form-label text-start w-100">Persentase Bahan Bakar Sebelum<span class="text-danger">*</span></label>
                        <input type="number" name="fuel_before" class="form-control @error('fuel_before') is-invalid @enderror" value="{{ old('fuel_before') }}" placeholder="0-100" required>
                        @error('fuel_before')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-start w-100">Persentase Bahan Bakar Setelah<span class="text-danger">*</span></label>
                        <input type="number" name="fuel_after" class="form-control @error('fuel_after') is-invalid @enderror" value="{{ old('fuel_after') }}" placeholder="0-100" required>
                        @error('fuel_after')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-start w-100">Kilometer Sebelum<span class="text-danger">*</span></label>
                        <input type="number" name="km_before" class="form-control @error('km_before') is-invalid @enderror" value="{{ old('km_before') }}" required>
                        @error('km_before')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-start w-100">Kilometer Setelah<span class="text-danger">*</span></label>
                        <input type="number" name="km_after" class="form-control @error('km_after') is-invalid @enderror" value="{{ old('km_after') }}" required>
                        @error('km_after')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    {{-- Checklist Kondisi --}}
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
                            {{-- Label bagian atas --}}
                            <label class="form-label w-100 text-start fw-bold text-uppercase">
                                {{ $label }} <span class="text-danger">*</span>
                            </label>

                            <div class="d-flex flex-wrap align-items-center gap-3">
                                {{-- Radio Aman --}}
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="{{ $key }}_ok" id="{{ $key }}_ok1" value="1"
                                        {{ old($key.'_ok') == '1' ? 'checked' : '' }} required>
                                    <label class="form-check-label fw-semibold text-success" for="{{ $key }}_ok1">Aman</label>
                                </div>

                                {{-- Radio Tidak Aman --}}
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="{{ $key }}_ok" id="{{ $key }}_ok0" value="0"
                                        {{ old($key.'_ok') == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-danger" for="{{ $key }}_ok0">Tidak Aman</label>
                                </div>

                                {{-- Catatan --}}
                                <div class="flex-grow-1">
                                    <input type="text" name="{{ $key }}_note"
                                        class="form-control @error($key.'_note') is-invalid @enderror"
                                        placeholder="Catatan (opsional)" value="{{ old($key.'_note') }}">
                                </div>
                            </div>
                        </div>
                        <script>
                        document.addEventListener("DOMContentLoaded", function () {

                            @foreach($items as $key => $label)
                                const radioAman{{ $key }} = document.getElementById("{{ $key }}_ok1");
                                const radioTidakAman{{ $key }} = document.getElementById("{{ $key }}_ok0");
                                const noteField{{ $key }} = document.querySelector("input[name='{{ $key }}_note']");

                                function updateRequirement{{ $key }}() {
                                    if (radioTidakAman{{ $key }}.checked) {
                                        noteField{{ $key }}.setAttribute("required", "required");
                                        noteField{{ $key }}.placeholder = "Wajib diisi jika tidak aman";
                                    } else {
                                        noteField{{ $key }}.removeAttribute("required");
                                        noteField{{ $key }}.placeholder = "Catatan (opsional)";
                                    }
                                }

                                radioAman{{ $key }}.addEventListener("change", updateRequirement{{ $key }});
                                radioTidakAman{{ $key }}.addEventListener("change", updateRequirement{{ $key }});

                                // Inisialisasi default
                                updateRequirement{{ $key }}();
                            @endforeach

                        });
                        </script>
                    @endforeach

                    {{-- Foto --}}
                    <div class="col-md-4">
                        <label class="form-label w-100 text-start">Foto Indikator Sebelum</label>
                        <input type="file" name="indicator_before_photos_path" class="form-control @error('indicator_before_photos_path') is-invalid @enderror" accept="image/*">
                        @error('indicator_before_photos_path')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label w-100 text-start">Foto Indikator Setelah</label>
                        <input type="file" name="indicator_after_photos_path" class="form-control @error('indicator_after_photos_path') is-invalid @enderror" accept="image/*">
                        @error('indicator_after_photos_path')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label w-100 text-start">Foto Lokasi</label>
                        <input type="file" name="location_photos_path" class="form-control @error('location_photos_path') is-invalid @enderror" accept="image/*">
                        @error('location_photos_path')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 text-start mt-4">
                        <button type="submit" class="btn btn-primary">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

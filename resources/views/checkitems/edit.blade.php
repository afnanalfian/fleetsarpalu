@extends('layouts.app')

@section('title', 'Edit Pengecekan Kendaraan')

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

                {{-- FORM EDIT --}}
                <form action="{{ route('checkitems.update', $item->id) }}"
                      method="POST" enctype="multipart/form-data" class="row g-3">

                    @csrf
                    @method('PUT')

                    {{-- Fuel & KM --}}
                    <div class="col-md-6">
                        <label class="form-label w-100">Persentase BBM (%) <span class="text-danger">*</span></label>

                        <input  type="number"
                                name="fuel_percent"
                                class="form-control @error('fuel_percent') is-invalid @enderror"
                                value="{{ old('fuel_percent', $item->fuel_percent) }}"
                                min="0" max="100"
                                required>

                        {{-- Pesan error dari backend --}}
                        @error('fuel_percent')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        {{-- Pesan error dari frontend --}}
                        <div class="invalid-feedback" id="fuelPercentError">
                            Persentase BBM harus antara 0 hingga 100.
                        </div>
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const input = document.querySelector('input[name="fuel_percent"]');

                        input.addEventListener('input', function () {
                            if (this.value < 0 || this.value > 100) {
                                this.classList.add('is-invalid');
                            } else {
                                this.classList.remove('is-invalid');
                            }
                        });
                    });
                    </script>

                    <div class="col-md-6">
                        <label class="form-label w-100">Kilometer <span class="text-danger">*</span></label>
                        <input type="number" name="km"
                               class="form-control"
                               value="{{ old('km', $item->km) }}"
                               required>
                    </div>

                    {{-- Checklist --}}
                    <div class="col-12 mt-3">
                        <h5 class="fw-bold">Checklist Kendaraan</h5>
                    </div>

                    @foreach($items as $key => $label)

                        @php
                            $okField = $key . '_ok';
                            $noteField = $key . '_note';
                        @endphp

                        <div class="col-md-12">
                            <label class="form-label w-100 fw-bold text-uppercase">{{ $label }} <span class="text-danger">*</span></label>

                            <div class="d-flex flex-wrap align-items-center gap-3">

                                {{-- Aman --}}
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                           name="{{ $okField }}"
                                           value="1"
                                           id="{{ $key }}_ok1"
                                           {{ old($okField, $item->$okField) == 1 ? 'checked' : '' }}
                                           required>
                                    <label class="form-check-label text-success fw-semibold" for="{{ $key }}_ok1">
                                        Aman
                                    </label>
                                </div>

                                {{-- Tidak Aman --}}
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                           name="{{ $okField }}"
                                           value="0"
                                           id="{{ $key }}_ok0"
                                           {{ old($okField, $item->$okField) == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label text-danger fw-semibold" for="{{ $key }}_ok0">
                                        Tidak Aman
                                    </label>
                                </div>

                                {{-- Catatan --}}
                                <div class="flex-grow-1">
                                    <input type="text"
                                           class="form-control"
                                           name="{{ $noteField }}"
                                           value="{{ old($noteField, $item->$noteField) }}"
                                           placeholder="Catatan (opsional)">
                                </div>
                            </div>
                        </div>
                        <script>
                        document.addEventListener("DOMContentLoaded", function () {

                            @foreach($items as $key => $label)
                                const okField{{ $key }}1 = document.getElementById("{{ $key }}_ok1"); // Aman
                                const okField{{ $key }}0 = document.getElementById("{{ $key }}_ok0"); // Tidak Aman
                                const noteField{{ $key }} = document.querySelector("input[name='{{ $key }}_note']");

                                function updateRequirement{{ $key }}() {
                                    if (okField{{ $key }}0.checked) {
                                        noteField{{ $key }}.setAttribute("required", "required");
                                        noteField{{ $key }}.placeholder = "Wajib diisi jika tidak aman";
                                    } else {
                                        noteField{{ $key }}.removeAttribute("required");
                                        noteField{{ $key }}.placeholder = "Catatan (opsional)";
                                    }
                                }

                                okField{{ $key }}1.addEventListener('change', updateRequirement{{ $key }});
                                okField{{ $key }}0.addEventListener('change', updateRequirement{{ $key }});

                                // Set initial state (penting saat edit data dan saat error validation)
                                updateRequirement{{ $key }}();
                            @endforeach

                        });
                        </script>

                    @endforeach

                    {{-- Foto Lama --}}
                    @if($item->photos)
                        <div class="col-12">
                            <label class="form-label fw-bold">Foto Sebelumnya:</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(json_decode($item->photos, true) as $photo)
                                    <img src="{{ asset('storage/'.$photo) }}"
                                         style="width:120px; height:90px; object-fit:cover;"
                                         class="rounded border">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Upload Foto Baru --}}
                    <div class="col-12">
                        <label class="form-label fw-bold">Tambah Foto Baru (opsional)</label>
                        <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
                    </div>

                    <div class="col-12 mt-3 text-start">
                        <button class="btn btn-primary">SIMPAN HASIL CEK</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection

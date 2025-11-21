@extends('layouts.app')

@section('title', 'Edit Peminjaman')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light text-center rounded p-4">

                {{-- Tombol kembali --}}
                <div class="text-start mb-4">
                    <a href="{{ route('borrowings.index') }}" class="mb-0 text-decoration-none text-black">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                {{-- Form Edit --}}
                <form action="{{ route('borrowings.update', $borrow->id) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    @method('PUT')

                    {{-- Kode Pinjam (readonly) --}}
                    <div class="col-md-6">
                        <label for="kode_pinjam" class="form-label w-100 text-start">Kode Pinjam</label>
                        <input
                            type="text"
                            name="kode_pinjam"
                            id="kode_pinjam"
                            class="form-control bg-light"
                            value="{{ $borrow->kode_pinjam }}"
                            readonly
                        >
                    </div>

                    {{-- Kendaraan --}}
                    <div class="col-md-6">
                        <label for="vehicle_id" class="form-label w-100 text-start">Kendaraan<span class="text-danger">*</span></label>
                        <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                            <option value="" hidden>Pilih Kendaraan</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ $borrow->vehicle_id == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->name }} ({{ $vehicle->plat_nomor }})
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Tujuan --}}
                    <div class="col-12">
                        <label for="destination_address" class="form-label w-100 text-start">Tujuan<span class="text-danger">*</span></label>
                        <input type="text" name="destination_address" value="{{ old('destination_address', $borrow->destination_address) }}"
                               class="form-control @error('destination_address') is-invalid @enderror" id="destination_address" required>
                        @error('destination_address')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Keperluan --}}
                    <div class="col-12">
                        <label for="purpose_text" class="form-label w-100 text-start">Keperluan<span class="text-danger">*</span></label>
                        <textarea name="purpose_text" id="purpose_text" class="form-control @error('purpose_text') is-invalid @enderror" rows="3" required>{{ old('purpose_text', $borrow->purpose_text) }}</textarea>
                        @error('purpose_text')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Tanggal Pergi & Pulang --}}
                    <div class="col-md-6">
                        <label for="start_at" class="form-label w-100 text-start">Tanggal Pergi <span class="text-danger">*</span></label>
                        <input type="date" name="start_at" id="start_at"
                            class="form-control @error('start_at') is-invalid @enderror"
                            value="{{ old('start_at', optional($borrow->start_at)->format('Y-m-d')) }}" required>
                        @error('start_at')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="start_time" class="form-label w-100 text-start">Jam Pergi<span class="text-danger">*</span></label>
                        <input type="time" lang="id" name="start_time" value="{{ old('start_time', $borrow->start_time) }}"
                               class="form-control @error('start_time') is-invalid @enderror" id="start_time" required>
                        @error('start_time')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="end_at" class="form-label w-100 text-start">Tanggal Pulang <span class="text-danger">*</span></label>
                        <input type="date" lang="id" name="end_at" id="end_at"
                            class="form-control @error('end_at') is-invalid @enderror"
                            value="{{ old('end_at', optional($borrow->end_at)->format('Y-m-d')) }}" required>
                        @error('end_at')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="end_time" class="form-label w-100 text-start">Jam Pulang<span class="text-danger">*</span></label>
                        <input type="time" name="end_time" value="{{ old('end_time', $borrow->end_time) }}"
                               class="form-control @error('end_time') is-invalid @enderror" id="end_time" required>
                        @error('end_time')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Surat Tugas (opsional) --}}
                    <div class="col-md-6">
                        <label for="surat_tugas" class="form-label w-100 text-start">Surat Tugas (PDF, opsional)</label>
                        <input type="file" name="surat_tugas" class="form-control @error('surat_tugas') is-invalid @enderror" id="surat_tugas" accept=".pdf">
                        @error('surat_tugas')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror

                        @if($borrow->surat_tugas_path)
                            <div class="mt-2 text-start">
                                <a href="{{ asset('storage/' . $borrow->surat_tugas_path) }}" target="_blank">Lihat file saat ini</a>
                            </div>
                        @endif
                    </div>

                    {{-- Status (readonly) --}}
                    <div class="col-md-6">
                        <label for="status" class="form-label w-100 text-start">Status</label>
                        <input type="text" value="{{ ucfirst($borrow->status) }}" class="form-control" id="status" readonly>
                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="col-md-12 w-100 text-start">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                    </div>
                    @push('scripts')
                        <script>
                        flatpickr("#start_time", {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: "H:i",
                            time_24hr: true
                        });

                        flatpickr("#end_time", {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: "H:i",
                            time_24hr: true
                        });
                        </script>
                    @endpush

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

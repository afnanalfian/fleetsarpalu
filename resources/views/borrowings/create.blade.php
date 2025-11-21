@extends('layouts.app')

@section('title', 'Ajukan Peminjaman Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light text-center rounded p-4 shadow-sm">
                {{-- Tombol kembali --}}
                <div class="text-start mb-4">
                    <a href="{{ route('borrowings.index') }}" class="mb-0 text-decoration-none text-black">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                {{-- Form pengajuan --}}
                <form action="{{ route('borrowings.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 text-start">
                    @csrf

                    {{-- Kendaraan --}}
                    <div class="col-md-12">
                        <label for="vehicle_id" class="form-label">Pilih Kendaraan <span class="text-danger">*</span></label>
                        <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                            <option value="" hidden selected>Pilih Kendaraan</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->name }} ({{ $vehicle->plat_nomor }})
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <div class="text-danger"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Tanggal Pergi --}}
                    <div class="col-md-6">
                        <label for="start_at" class="form-label">Tanggal Pergi <span class="text-danger">*</span></label>
                        <input type="date" name="start_at" value="{{ old('start_at') }}" class="form-control @error('start_at') is-invalid @enderror" required>
                        @error('start_at')
                            <div class="text-danger"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Tanggal Pulang --}}
                    <div class="col-md-6">
                        <label for="end_at" class="form-label">Tanggal Pulang <span class="text-danger">*</span></label>
                        <input type="date" name="end_at" value="{{ old('end_at') }}" class="form-control @error('end_at') is-invalid @enderror" required>
                        @error('end_at')
                            <div class="text-danger"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Jam Pergi --}}
                    <div class="col-md-6">
                        <label for="start_time" class="form-label">Jam Pergi <span class="text-danger">*</span></label>
                        <input type="time" lang="id" name="start_time" value="{{ old('start_time') }}" class="form-control @error('start_time') is-invalid @enderror" required>
                        @error('start_time')
                            <div class="text-danger"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Jam Pulang --}}
                    <div class="col-md-6">
                        <label for="end_time" class="form-label">Jam Pulang <span class="text-danger">*</span></label>
                        <input type="time" lang="id" name="end_time" value="{{ old('end_time') }}" class="form-control @error('end_time') is-invalid @enderror" required>
                        @error('end_time')
                            <div class="text-danger"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Tujuan --}}
                    <div class="col-md-12">
                        <label for="destination_address" class="form-label">Alamat Tujuan <span class="text-danger">*</span></label>
                        <textarea name="destination_address" id="destination_address" rows="2" class="form-control @error('destination_address') is-invalid @enderror" required>{{ old('destination_address') }}</textarea>
                        @error('destination_address')
                            <div class="text-danger"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Keperluan --}}
                    <div class="col-md-12">
                        <label for="purpose_text" class="form-label">Keperluan <span class="text-danger">*</span></label>
                        <textarea name="purpose_text" id="purpose_text" rows="2" class="form-control @error('purpose_text') is-invalid @enderror" required>{{ old('purpose_text') }}</textarea>
                        @error('purpose_text')
                            <div class="text-danger"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Surat Tugas (opsional) --}}
                    <div class="col-md-6">
                        <label for="surat_tugas" class="form-label">Upload Surat Tugas (PDF)</label>
                        <input type="file" name="surat_tugas" id="surat_tugas" class="form-control @error('surat_tugas') is-invalid @enderror" accept=".pdf">
                        @error('surat_tugas')
                            <div class="text-danger"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="col-md-12 text-start mt-3">
                        <button type="submit" class="btn btn-primary">
                            Ajukan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

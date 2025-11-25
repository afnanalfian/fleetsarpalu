@extends('layouts.app')

@section('title', 'Tambah Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">

    {{-- Tombol Kembali --}}
    <div class="text-start mb-3">
        <a href="{{ route('vehicles.index') }}" class="text-decoration-none text-black">
            <i class="fa-solid fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="bg-light rounded p-4 shadow-sm">

                <h4 class="mb-4 fw-bold text-center">Tambah Kendaraan</h4>

                <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf

                    {{-- Kode BMN --}}
                    <div class="col-md-6">
                        <label class="form-label">Kode BMN <span class="text-danger">*</span></label>
                        <input type="text" name="kode_bmn" class="form-control @error('kode_bmn') is-invalid @enderror"
                               value="{{ old('kode_bmn') }}" required>
                        @error('kode_bmn')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nama --}}
                    <div class="col-md-6">
                        <label class="form-label">Nama Kendaraan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Merk --}}
                    <div class="col-md-6">
                        <label class="form-label">Merk <span class="text-danger">*</span></label>
                        <input type="text" name="merk" class="form-control @error('merk') is-invalid @enderror"
                               value="{{ old('merk') }}" required>
                        @error('merk')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Plat Nomor --}}
                    <div class="col-md-6">
                        <label class="form-label">Plat Nomor <span class="text-danger">*</span></label>
                        <input type="text" name="plat_nomor" class="form-control @error('plat_nomor') is-invalid @enderror"
                               value="{{ old('plat_nomor') }}" required>
                        @error('plat_nomor')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tahun --}}
                    <div class="col-md-6">
                        <label class="form-label">Tahun <span class="text-danger">*</span></label>
                        <input type="number" name="year" class="form-control @error('year') is-invalid @enderror"
                               value="{{ old('year') }}" required>
                        @error('year')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Field opsional --}}
                    <div class="col-md-6">
                        <label class="form-label">Warna</label>
                        <input type="text" name="warna" class="form-control" value="{{ old('warna') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tipe</label>
                        <input type="text" name="tipe" class="form-control" value="{{ old('tipe') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Pabrik</label>
                        <input type="text" name="factory" class="form-control" value="{{ old('factory') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Kapasitas Muatan (kg)</label>
                        <input type="number" name="load_capacity" class="form-control" value="{{ old('load_capacity') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Berat (kg)</label>
                        <input type="number" name="weight" class="form-control" value="{{ old('weight') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Bahan Bakar</label>
                        <input type="text" name="bahan_bakar" class="form-control" value="{{ old('bahan_bakar') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Jarak Tempuh (km)</label>
                        <input type="number" name="distance" class="form-control" value="{{ old('distance') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">KM Terakhir Ganti Oli (km)</label>
                        <input type="number" name="last_km_for_oil" class="form-control" value="{{ old('last_km_for_oil') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Interval Ganti Oli (km)</label>
                        <input type="number" name="oil_change_interval" class="form-control" value="{{ old('oil_change_interval') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Persentase BBM (%)</label>
                        <input type="number" name="fuel_percent" class="form-control" value="{{ old('fuel_percent') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Foto Kendaraan</label>
                        <input type="file" name="photo_path" class="form-control">
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="col-12 text-end">
                        <button class="btn btn-primary px-4">Simpan</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</div>
@endsection

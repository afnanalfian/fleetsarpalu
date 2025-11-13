@extends('layouts.app')

@section('title', 'Cek Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-light rounded p-4 shadow-sm">
        {{-- Tombol kembali --}}
        <div class="mb-3">
            <a href="{{ route('checking.show', $checkItem->check_id) }}" class="text-decoration-none text-dark">
                <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Daftar Kendaraan
            </a>
        </div>

        <h5 class="mb-4">Form Pengecekan Kendaraan</h5>

        {{-- Informasi kendaraan --}}
        <div class="mb-4">
            <table class="table table-bordered">
                <tr>
                    <th>Kode BMN</th>
                    <td>{{ $checkItem->vehicle->kode_bmn ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Nama Kendaraan</th>
                    <td>{{ $checkItem->vehicle->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Plat Nomor</th>
                    <td>{{ $checkItem->vehicle->plat_nomor ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- Form pengecekan --}}
        <form action="{{ route('checkitem.update', $checkItem->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="text-start">
                <h6 class="fw-bold mb-3">Ceklis Kondisi Komponen</h6>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Komponen</th>
                                <th>OK?</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Lampu Hazard --}}
                            <tr>
                                <td>Lampu Hazard</td>
                                <td class="text-center">
                                    <input type="checkbox" name="lampu_hazard" value="1"
                                        {{ old('lampu_hazard', $checkItem->lampu_hazard) ? 'checked' : '' }}>
                                </td>
                                <td><input type="text" name="lampu_hazard_note" class="form-control form-control-sm"
                                    value="{{ old('lampu_hazard_note', $checkItem->lampu_hazard_note) }}"></td>
                            </tr>

                            {{-- Radiator --}}
                            <tr>
                                <td>Radiator</td>
                                <td class="text-center">
                                    <input type="checkbox" name="radiator_ok" value="1"
                                        {{ old('radiator_ok', $checkItem->radiator_ok) ? 'checked' : '' }}>
                                </td>
                                <td><input type="text" name="radiator_note" class="form-control form-control-sm"
                                    value="{{ old('radiator_note', $checkItem->radiator_note) }}"></td>
                            </tr>

                            {{-- Filter Udara --}}
                            <tr>
                                <td>Filter Udara</td>
                                <td class="text-center">
                                    <input type="checkbox" name="air_filter_ok" value="1"
                                        {{ old('air_filter_ok', $checkItem->air_filter_ok) ? 'checked' : '' }}>
                                </td>
                                <td><input type="text" name="air_filter_note" class="form-control form-control-sm"
                                    value="{{ old('air_filter_note', $checkItem->air_filter_note) }}"></td>
                            </tr>

                            {{-- Wiper --}}
                            <tr>
                                <td>Wiper</td>
                                <td class="text-center">
                                    <input type="checkbox" name="wiper_ok" value="1"
                                        {{ old('wiper_ok', $checkItem->wiper_ok) ? 'checked' : '' }}>
                                </td>
                                <td><input type="text" name="wiper_note" class="form-control form-control-sm"
                                    value="{{ old('wiper_note', $checkItem->wiper_note) }}"></td>
                            </tr>

                            {{-- Lampu --}}
                            <tr>
                                <td>Lampu</td>
                                <td class="text-center">
                                    <input type="checkbox" name="lights_ok" value="1"
                                        {{ old('lights_ok', $checkItem->lights_ok) ? 'checked' : '' }}>
                                </td>
                                <td><input type="text" name="lights_note" class="form-control form-control-sm"
                                    value="{{ old('lights_note', $checkItem->lights_note) }}"></td>
                            </tr>

                            {{-- Kebocoran --}}
                            <tr>
                                <td>Kebocoran</td>
                                <td class="text-center">
                                    <input type="checkbox" name="leaks_ok" value="1"
                                        {{ old('leaks_ok', $checkItem->leaks_ok) ? 'checked' : '' }}>
                                </td>
                                <td><input type="text" name="leaks_note" class="form-control form-control-sm"
                                    value="{{ old('leaks_note', $checkItem->leaks_note) }}"></td>
                            </tr>

                            {{-- Kebersihan Kaca --}}
                            <tr>
                                <td>Kebersihan Kaca</td>
                                <td class="text-center">
                                    <input type="checkbox" name="glass_celanliness_ok" value="1"
                                        {{ old('glass_celanliness_ok', $checkItem->glass_celanliness_ok) ? 'checked' : '' }}>
                                </td>
                                <td><input type="text" name="glass_cleanliness_note" class="form-control form-control-sm"
                                    value="{{ old('glass_cleanliness_note', $checkItem->glass_cleanliness_note) }}"></td>
                            </tr>

                            {{-- Kebersihan Body --}}
                            <tr>
                                <td>Kebersihan Body</td>
                                <td class="text-center">
                                    <input type="checkbox" name="body_cleanliness_ok" value="1"
                                        {{ old('body_cleanliness_ok', $checkItem->body_cleanliness_ok) ? 'checked' : '' }}>
                                </td>
                                <td><input type="text" name="body_cleanliness_note" class="form-control form-control-sm"
                                    value="{{ old('body_cleanliness_note', $checkItem->body_cleanliness_note) }}"></td>
                            </tr>

                            {{-- Klakson --}}
                            <tr>
                                <td>Klakson</td>
                                <td class="text-center">
                                    <input type="checkbox" name="klakson" value="1"
                                        {{ old('klakson', $checkItem->klakson) ? 'checked' : '' }}>
                                </td>
                                <td><input type="text" name="klakson_note" class="form-control form-control-sm"
                                    value="{{ old('klakson_note', $checkItem->klakson_note) }}"></td>
                            </tr>

                            {{-- Sirine --}}
                            <tr>
                                <td>Sirine</td>
                                <td class="text-center">
                                    <input type="checkbox" name="sirine" value="1"
                                        {{ old('sirine', $checkItem->sirine) ? 'checked' : '' }}>
                                </td>
                                <td><input type="text" name="sirine_note" class="form-control form-control-sm"
                                    value="{{ old('sirine_note', $checkItem->sirine_note) }}"></td>
                            </tr>

                            {{-- Ban --}}
                            <tr>
                                <td>Ban</td>
                                <td class="text-center">
                                    <input type="checkbox" name="ban" value="1"
                                        {{ old('ban', $checkItem->ban) ? 'checked' : '' }}>
                                </td>
                                <td><input type="text" name="ban_note" class="form-control form-control-sm"
                                    value="{{ old('ban_note', $checkItem->ban_note) }}"></td>
                            </tr>

                            {{-- Rem --}}
                            <tr>
                                <td>Rem</td>
                                <td class="text-center">
                                    <input type="checkbox" name="rem" value="1"
                                        {{ old('rem', $checkItem->rem) ? 'checked' : '' }}>
                                </td>
                                <td><input type="text" name="rem_note" class="form-control form-control-sm"
                                    value="{{ old('rem_note', $checkItem->rem_note) }}"></td>
                            </tr>

                            {{-- Aki --}}
                            <tr>
                                <td>Aki</td>
                                <td class="text-center">
                                    <input type="checkbox" name="aki" value="1"
                                        {{ old('aki', $checkItem->aki) ? 'checked' : '' }}>
                                </td>
                                <td><input type="text" name="aki_note" class="form-control form-control-sm"
                                    value="{{ old('aki_note', $checkItem->aki_note) }}"></td>
                            </tr>

                            {{-- Start Engine --}}
                            <tr>
                                <td>Start Engine</td>
                                <td class="text-center">
                                    <input type="checkbox" name="start_engine" value="1"
                                        {{ old('start_engine', $checkItem->start_engine) ? 'checked' : '' }}>
                                </td>
                                <td><input type="text" name="start_engine_note" class="form-control form-control-sm"
                                    value="{{ old('start_engine_note', $checkItem->start_engine_note) }}"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Input tambahan --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">KM Saat Ini</label>
                    <input type="number" name="km" class="form-control" value="{{ old('km', $checkItem->km) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Bahan Bakar (Fuel)</label>
                    <input type="text" name="fuel" class="form-control" value="{{ old('fuel', $checkItem->fuel) }}">
                </div>

                {{-- Tombol simpan --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-2"></i> Simpan Pengecekan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

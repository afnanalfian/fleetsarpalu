@extends('layouts.app')

@section('title', 'Detail Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">

    {{-- Tombol Kembali --}}
    <div class="text-start mb-3">
        <a href="{{ route('vehicles.index') }}" class="text-decoration-none text-black">
            <i class="fa-solid fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-8 mx-auto">

            <div class="bg-light rounded p-4 shadow-sm">

                {{-- Judul --}}
                <h3 class="text-center fw-bold mb-4">{{ $vehicle->name }}</h3>

                {{-- Foto Kendaraan --}}
                <div class="ratio ratio-16x9 mb-4">
                    @if ($vehicle->photo_path)
                        <img src="{{ asset('storage/'.$vehicle->photo_path) }}" class="rounded w-100" style="object-fit: cover;">
                    @else
                        <img src="{{ asset('img/no-image.png') }}" class="rounded w-100" style="object-fit: cover;">
                    @endif
                </div>

                {{-- Detail Kendaraan --}}
                <div class="bg-white p-3 rounded shadow-sm">

                    @php
                        $needOilChange = ($vehicle->distance - $vehicle->last_km_for_oil) >= $vehicle->oil_change_interval;
                    @endphp

                    @if ($needOilChange)
                        <div class="alert alert-danger fw-bold">
                            ⚠️ Perlu Ganti Oli! Kendaraan sudah menempuh {{ $vehicle->distance - $vehicle->last_km_for_oil }} km
                            sejak ganti oli terakhir. Batas interval: {{ $vehicle->oil_change_interval }} km.
                        </div>
                    @endif

                    <h5 class="fw-bold mb-3">Detail Kendaraan</h5>

                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th class="w-25">Kode BMN</th>
                                <td>{{ $vehicle->kode_bmn }}</td>
                            </tr>
                            <tr>
                                <th>Nama</th>
                                <td>{{ $vehicle->name }}</td>
                            </tr>
                            <tr>
                                <th>Merk</th>
                                <td>{{ $vehicle->merk }}</td>
                            </tr>
                            <tr>
                                <th>Plat Nomor</th>
                                <td>{{ $vehicle->plat_nomor }}</td>
                            </tr>
                            <tr>
                                <th>Tipe</th>
                                <td>{{ $vehicle->tipe }}</td>
                            </tr>
                            <tr>
                                <th>Pabrik</th>
                                <td>{{ $vehicle->factory }}</td>
                            </tr>
                            <tr>
                                <th>Tahun</th>
                                <td>{{ $vehicle->year }}</td>
                            </tr>
                            <tr>
                                <th>Jarak Tempuh</th>
                                <td>{{ $vehicle->distance }} km</td>
                            </tr>
                            <tr>
                                <th>Kapasitas Muatan</th>
                                <td>{{ $vehicle->load_capacity }} kg</td>
                            </tr>
                            <tr>
                                <th>Berat</th>
                                <td>{{ $vehicle->weight }} kg</td>
                            </tr>
                            <tr>
                                <th>Bahan Bakar</th>
                                <td>{{ $vehicle->bahan_bakar }}</td>
                            </tr>
                            <tr>
                                <th>Lokasi</th>
                                <td>{{ $vehicle->lokasi }}</td>
                            </tr>
                            <tr>
                                <th>Warna</th>
                                <td>{{ $vehicle->warna }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if ($vehicle->status == 'available')
                                        <span class="badge bg-success">Tersedia</span>
                                    @elseif ($vehicle->status == 'in_use')
                                        <span class="badge bg-secondary">Digunakan</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Tersedia</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>KM Terakhir Ganti Oli</th>
                                <td>{{ $vehicle->last_km_for_oil }} km</td>
                            </tr>
                            <tr>
                                <th>Interval Ganti Oli</th>
                                <td>{{ $vehicle->oil_change_interval }} km</td>
                            </tr>
                            <tr>
                                <th>Persentase BBM</th>
                                <td>{{ $vehicle->fuel_percent }}%</td>
                            </tr>
                            <tr>
                                <th>Catatan</th>
                                <td>{{ $vehicle->notes ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>


                <div class="text-end mt-3">
                    {{-- Tombol Ganti Oli --}}
                    <button class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#oilChangeModal">
                        Ganti Oli
                    </button>
                    @if(in_array(strtolower(auth()->user()->role), ['admin', 'kepala sumber daya']))
                        {{-- Edit --}}
                        <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-primary">
                            Edit Kendaraan
                        </a>
                    @endif
                </div>
                {{-- ======================= --}}
                {{-- MODAL GANTI OLI --}}
                {{-- ======================= --}}
                <div class="modal fade" id="oilChangeModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">Catat Pergantian Oli</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <p>
                                    Kendaraan: <strong>{{ $vehicle->name }}</strong><br>
                                    Tanggal: <strong>{{ now()->format('d M Y') }}</strong>
                                </p>

                                <form action="{{ route('oilchange.store', $vehicle->id) }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label">KM Saat Ganti Oli</label>
                                        <input type="number" name="new_distance" class="form-control" required>
                                    </div>

                                    <button class="btn btn-primary w-100">Simpan</button>

                                </form>

                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
@endsection

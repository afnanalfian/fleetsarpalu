@extends('layouts.app')

@section('title', 'Detail Pengecekan Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-light rounded p-4 shadow-sm">

        {{-- Tombol kembali --}}
        <div class="mb-3">
            <a href="{{ route('checking.show', $checkItem->check_id) }}" class="text-decoration-none text-dark">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Daftar Kendaraan
            </a>
        </div>

        <h5 class="mb-4">Detail Pengecekan Kendaraan</h5>

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
                <tr>
                    <th>KM</th>
                    <td>{{ $checkItem->km ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Bahan Bakar</th>
                    <td>{{ $checkItem->fuel ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Status Kondisi</th>
                    <td>
                        @if($checkItem->status == 'Baik')
                            <span class="badge bg-success">{{ $checkItem->status }}</span>
                        @elseif($checkItem->status == 'Rusak')
                            <span class="badge bg-danger">{{ $checkItem->status }}</span>
                        @else
                            <span class="badge bg-secondary">Belum Dicek</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- Daftar hasil pengecekan --}}
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Komponen</th>
                        <th>Kondisi</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Lampu Hazard</td>
                        <td>{!! $checkItem->lampu_hazard ? '✅' : '❌' !!}</td>
                        <td>{{ $checkItem->lampu_hazard_note }}</td>
                    </tr>
                    <tr>
                        <td>Radiator</td>
                        <td>{!! $checkItem->radiator_ok ? '✅' : '❌' !!}</td>
                        <td>{{ $checkItem->radiator_note }}</td>
                    </tr>
                    <tr>
                        <td>Filter Udara</td>
                        <td>{!! $checkItem->air_filter_ok ? '✅' : '❌' !!}</td>
                        <td>{{ $checkItem->air_filter_note }}</td>
                    </tr>
                    <tr>
                        <td>Wiper</td>
                        <td>{!! $checkItem->wiper_ok ? '✅' : '❌' !!}</td>
                        <td>{{ $checkItem->wiper_note }}</td>
                    </tr>
                    <tr>
                        <td>Lampu</td>
                        <td>{!! $checkItem->lights_ok ? '✅' : '❌' !!}</td>
                        <td>{{ $checkItem->lights_note }}</td>
                    </tr>
                    <tr>
                        <td>Kebocoran</td>
                        <td>{!! $checkItem->leaks_ok ? '✅' : '❌' !!}</td>
                        <td>{{ $checkItem->leaks_note }}</td>
                    </tr>
                    <tr>
                        <td>Kebersihan Kaca</td>
                        <td>{!! $checkItem->glass_celanliness_ok ? '✅' : '❌' !!}</td>
                        <td>{{ $checkItem->glass_cleanliness_note }}</td>
                    </tr>
                    <tr>
                        <td>Kebersihan Body</td>
                        <td>{!! $checkItem->body_cleanliness_ok ? '✅' : '❌' !!}</td>
                        <td>{{ $checkItem->body_cleanliness_note }}</td>
                    </tr>
                    <tr>
                        <td>Klakson</td>
                        <td>{!! $checkItem->klakson ? '✅' : '❌' !!}</td>
                        <td>{{ $checkItem->klakson_note }}</td>
                    </tr>
                    <tr>
                        <td>Sirine</td>
                        <td>{!! $checkItem->sirine ? '✅' : '❌' !!}</td>
                        <td>{{ $checkItem->sirine_note }}</td>
                    </tr>
                    <tr>
                        <td>Ban</td>
                        <td>{!! $checkItem->ban ? '✅' : '❌' !!}</td>
                        <td>{{ $checkItem->ban_note }}</td>
                    </tr>
                    <tr>
                        <td>Rem</td>
                        <td>{!! $checkItem->rem ? '✅' : '❌' !!}</td>
                        <td>{{ $checkItem->rem_note }}</td>
                    </tr>
                    <tr>
                        <td>Aki</td>
                        <td>{!! $checkItem->aki ? '✅' : '❌' !!}</td>
                        <td>{{ $checkItem->aki_note }}</td>
                    </tr>
                    <tr>
                        <td>Start Engine</td>
                        <td>{!! $checkItem->start_engine ? '✅' : '❌' !!}</td>
                        <td>{{ $checkItem->start_engine_note }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-end mt-3">
            <a href="{{ route('checkitem.edit', $checkItem->id) }}" class="btn btn-warning">
                <i class="fa-solid fa-pen-to-square me-2"></i> Edit Pengecekan
            </a>
        </div>
    </div>
</div>
@endsection

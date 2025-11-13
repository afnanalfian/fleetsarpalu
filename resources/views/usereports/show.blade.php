@extends('layouts.app')

@section('title', 'Detail Laporan Penggunaan Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded p-4">

                {{-- Tombol Kembali --}}
                <div class="text-start mb-4">
                    <a href="{{ route('borrowings.show', $borrow->id) }}"  class="mb-0 text-decoration-none text-black">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                {{-- Header --}}
                <div class="mb-4 text-center">
                    <h4 class="fw-bold text-uppercase mb-2">Laporan Penggunaan Kendaraan</h4>
                    <p class="text-muted mb-0">Kode Pinjam: <strong>{{ $report->borrowRequest->kode_pinjam ?? '-' }}</strong></p>
                </div>

                {{-- Informasi Peminjaman --}}
                <div class="border rounded p-3 bg-white mb-4">
                    <h5 class="fw-bold mb-3 text-start">Informasi Peminjaman</h5>
                    <div class="row">
                        <div class="col-md-6 text-start">
                            <p><strong>Kendaraan:</strong> {{ $report->borrowRequest->vehicle->name ?? '-' }}</p>
                            <p><strong>Tujuan:</strong> {{ $report->borrowRequest->destination_address ?? '-' }}</p>
                            <p><strong>Tanggal:</strong>
                                {{ \Carbon\Carbon::parse($report->borrowRequest->start_at)->format('d M Y') }} -
                                {{ \Carbon\Carbon::parse($report->borrowRequest->end_at)->format('d M Y') }}
                            </p>
                        </div>
                        <div class="col-md-6 text-start">
                            <p><strong>Peminjam:</strong> {{ $report->borrowRequest->user->name ?? '-' }}</p>
                            <p><strong>NIP:</strong> {{ $report->borrowRequest->user->NIP ?? '-' }}</p>
                            <p><strong>Status:</strong>
                                <span class="badge
                                    @if($report->borrowRequest->status == 'Completed') bg-success
                                    @elseif($report->borrowRequest->status == 'Rejected') bg-danger
                                    @else bg-secondary @endif">
                                    {{ ucfirst($report->borrowRequest->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Detail Laporan --}}
                <div class="border rounded p-3 bg-white mb-4">
                    <h5 class="fw-bold mb-3 text-start">Detail Laporan</h5>
                    <div class="row text-start">
                        <div class="col-md-6">
                            <p><strong>Persentase Bahan Bakar:</strong> {{ $report->fuel_before }}% → {{ $report->fuel_after }}%</p>
                            <p><strong>Kilometer:</strong> {{ $report->km_before }} km → {{ $report->km_after }} km</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Selisih Bahan Bakar:</strong> {{ $report->fuel_before - $report->fuel_after }}%</p>
                            <p><strong>Jarak Tempuh:</strong> {{ $report->km_after - $report->km_before }} km</p>
                        </div>
                    </div>
                </div>

                {{-- Pemeriksaan --}}
                <div class="border rounded p-3 bg-white mb-4">
                    <h5 class="fw-bold mb-3 text-start">Pemeriksaan Kendaraan</h5>

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

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr class="text-center">
                                    <th>Komponen</th>
                                    <th>Kondisi</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $key => $label)
                                    @php
                                        $ok = $report[$key.'_ok'] ?? null;
                                        $note = $report[$key.'_note'] ?? '-';
                                    @endphp
                                    <tr>
                                        <td><strong>{{ strtoupper($label) }}</strong></td>
                                        <td class="text-center">
                                            @if($ok === 1)
                                                <span class="badge bg-success">Aman</span>
                                            @elseif($ok === 0)
                                                <span class="badge bg-danger">Tidak Aman</span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $note ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Foto --}}
                <div class="border rounded p-3 bg-white mb-4">
                    <h5 class="fw-bold mb-3 text-start">Dokumentasi Foto</h5>
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <h6>Indikator Sebelum</h6>
                            @if($report->indicator_before_photos_path)
                                <img src="{{ asset('storage/' . $report->indicator_before_photos_path) }}" class="img-fluid rounded shadow-sm" alt="Foto Sebelum">
                            @else
                                <img src="{{ asset('img/no-image.png') }}" class="img-fluid rounded shadow-sm" alt="No Image">
                            @endif
                        </div>

                        <div class="col-md-4 mb-3">
                            <h6>Indikator Setelah</h6>
                            @if($report->indicator_after_photos_path)
                                <img src="{{ asset('storage/' . $report->indicator_after_photos_path) }}" class="img-fluid rounded shadow-sm" alt="Foto Setelah">
                            @else
                                <img src="{{ asset('img/no-image.png') }}" class="img-fluid rounded shadow-sm" alt="No Image">
                            @endif
                        </div>

                        <div class="col-md-4 mb-3">
                            <h6>Foto Lokasi</h6>
                            @if($report->location_photos_path)
                                <img src="{{ asset('storage/' . $report->location_photos_path) }}" class="img-fluid rounded shadow-sm" alt="Foto Lokasi">
                            @else
                                <img src="{{ asset('img/no-image.png') }}" class="img-fluid rounded shadow-sm" alt="No Image">
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="text-start">
                    <a href="{{ route('usereports.edit', $report->id) }}" class="btn btn-success me-2">
                        <i class="fa-solid fa-pen me-1"></i> Edit Laporan
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

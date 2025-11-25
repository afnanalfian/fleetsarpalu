@extends('layouts.app')

@section('title', 'Generate Laporan Peminjaman')

@section('content')
<div class="container py-4">

    <h3 class="fw-bold mb-3">Generate Laporan Peminjaman Kendaraan</h3>

    <div class="card shadow-sm p-4">
        <form action="{{ route('reports.borrow.generate') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="fw-bold">Bulan</label>
                    <select name="month" class="form-select">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="fw-bold">Tahun</label>
                    <select name="year" class="form-select">
                        @for ($y = now()->year - 5; $y <= now()->year + 2; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="fw-bold">Format File</label>
                    <select name="format" class="form-select">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel (.xlsx)</option>
                        <option value="csv">CSV</option>
                        <option value="json">JSON</option>
                    </select>
                </div>
            </div>

            <hr>

            <h5 class="fw-bold">Pilih Kolom</h5>

            <div class="row">
                @foreach([
                    'nip' => 'NIP Peminjam',
                    'nama' => 'Nama Peminjam',
                    'kendaraan' => 'Nama Kendaraan',
                    'start' => 'Tanggal & Jam Pergi',
                    'end' => 'Tanggal & Jam Pulang',
                    'tujuan' => 'Tujuan',
                    'keperluan' => 'Keperluan',
                    'status' => 'Status',
                    'fuel_before' => 'Bensin Sebelum',
                    'fuel_after' => 'Bensin Setelah',
                    'km_before' => 'Kilometer Sebelum',
                    'km_after' => 'Kilometer Setelah',
                    'kondisi' => 'Kondisi',
                    'catatan' => 'Catatan'
                ] as $col => $label)
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="columns[]" value="{{ $col }}" checked>
                        <label class="form-check-label">{{ $label }}</label>
                    </div>
                </div>
                @endforeach
            </div>

            <button class="btn btn-primary mt-4 px-4">Download Laporan</button>

        </form>
    </div>
</div>
@endsection

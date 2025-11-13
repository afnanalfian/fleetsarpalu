@extends('layouts.app')

@section('title', 'Daftar Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">

    <div class="d-flex justify-content-end gap-2 mb-3">

        {{-- Tombol Pinjam Kendaraan --}}
        <a href="{{ route('borrowings.create') }}" class="btn btn-warning rounded">
            Pinjam Kendaraan <i class="fa-solid fa-key ms-1"></i>
        </a>

        {{-- Tombol Tambah Kendaraan --}}
        <a href="{{ route('vehicles.create') }}" class="btn btn-primary rounded">
            Tambah Kendaraan <i class="fa-solid fa-car ms-1"></i>
        </a>

    </div>

    <div class="row g-4">
        @forelse($vehicles as $vehicle)
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="card shadow-sm">

                {{-- Foto Kendaraan --}}
            <div class="ratio ratio-16x9">
                @if ($vehicle->photo_path)
                    <img src="{{ asset('storage/' . $vehicle->photo_path) }}"
                        class="w-100 rounded"
                        style="object-fit: cover;"
                        alt="Foto Kendaraan">
                @else
                    <img src="{{ asset('img/no-image.png') }}"
                        class="w-100 rounded"
                        style="object-fit: cover;"
                        alt="No Image">
                @endif
            </div>

                <ul class="list-group list-group-flush text-center">

                    <li class="list-group-item">
                        <strong>{{ $vehicle->name }}</strong>
                        <br>
                        <small class="text-muted">{{ $vehicle->merk }}</small>
                    </li>

                    <li class="list-group-item">
                        Plat Nomor:<br>
                        {{ $vehicle->plat_nomor }}
                    </li>

                    <li class="list-group-item">
                        Tahun: <br>{{ $vehicle->year ?? '-' }}
                    </li>

                    <li class="list-group-item">
                        Warna: <br>{{ $vehicle->warna }}
                    </li>

                    {{-- Status --}}
                    <li class="list-group-item">
                        Status: <br>

                        @if ($vehicle->status == 'available')
                            <span class="badge bg-success">Tersedia</span>
                        @elseif ($vehicle->status == 'is_use')
                            <span class="badge bg-secondary">Digunakan</span>
                        @else
                            <span class="badge bg-danger">Tidak Tersedia</span>
                        @endif
                    </li>

                    {{-- Tombol Aksi --}}
                    <li class="list-group-item d-flex gap-2">

                        {{-- Detail --}}
                        <a href="{{ route('vehicles.show', $vehicle->id) }}" class="btn btn-info w-33">
                            Detail
                        </a>

                        {{-- Edit --}}
                        <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-success w-33">
                            Ubah
                        </a>

                        {{-- Hapus --}}
                        <button type="button" class="btn btn-danger w-33"
                                data-bs-toggle="modal"
                                data-bs-target="#Hapus{{ $vehicle->id }}">
                            Hapus
                        </button>

                    </li>
                </ul>
            </div>
        </div>

        {{-- Modal Hapus --}}
        <div class="modal fade" id="Hapus{{ $vehicle->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST" class="modal-content">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Kendaraan</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        Apakah anda yakin ingin menghapus kendaraan ini?
                        <br><b>{{ $vehicle->name }} - {{ $vehicle->plat_nomor }}</b>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-danger">Hapus</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                    </div>
                </form>
            </div>
        </div>

        @empty
        <h3 class="text-center py-5">Data Kendaraan Kosong</h3>
        @endforelse
    </div>

</div>
@endsection


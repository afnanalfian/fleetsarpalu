@extends('layouts.app')

@section('title', 'Absensi Pengecekan Kendaraan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded p-4">

                <div class="text-start mb-4">
                    <a href="{{ route('checkings.index') }}" class="text-decoration-none text-black">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <h4 class="fw-bold">Absensi Pengecekan Kendaraan</h4>
                <p class="mb-3">Tim: <strong>{{ $checking->team->name }}</strong></p>
                <p class="mb-3">Tanggal: <strong>{{ now()->format('d M Y') }}</strong></p>

                <form action="{{ route('attendances.store', $checking->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Nama Anggota</th>
                                    <th class="text-center">Hadir?</th>
                                    <th>Alasan (Jika Tidak Hadir)</th>
                                    <th>Bukti (Opsional)</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>

                                        {{-- Hadir --}}
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       name="present_{{ $member->id }}"
                                                       value="1"
                                                       checked>
                                            </div>
                                        </td>

                                        {{-- Alasan --}}
                                        <td>
                                            <input type="text"
                                                   name="reason_{{ $member->id }}"
                                                   class="form-control"
                                                   placeholder="Isi alasan jika tidak hadir">
                                        </td>

                                        {{-- Bukti --}}
                                        <td>
                                            <input type="file"
                                                   name="bukti_{{ $member->id }}"
                                                   class="form-control"
                                                   accept="image/*,application/pdf">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-start mt-3">
                        <button type="submit" class="btn btn-primary">
                            Simpan Absensi
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection

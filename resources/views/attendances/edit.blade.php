@extends('layouts.app')

@section('title', 'Edit Absensi Pengecekan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded p-4">

                <div class="text-start mb-4">
                    <a href="{{ route('checkings.show', $checking->id) }}" class="text-decoration-none text-black">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <h4 class="fw-bold">Edit Absensi Pengecekan Kendaraan</h4>
                <p class="mb-3">Tim: <strong>{{ $checking->team->name }}</strong></p>
                <p class="mb-3">Tanggal: <strong>{{ $checking->created_at->format('d M Y') }}</strong></p>

                <form action="{{ route('attendances.update', $checking->id) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Nama Anggota</th>
                                    <th class="text-center">Hadir?</th>
                                    <th>Alasan</th>
                                    <th>Bukti Sebelumnya</th>
                                    <th>Upload Bukti Baru</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($members as $member)
                                    @php
                                        $att = $attendanceMap[$member->id] ?? null;
                                    @endphp

                                    <tr>
                                        <td>{{ $member->name }}</td>

                                        {{-- Hadir --}}
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       name="present_{{ $member->id }}"
                                                       value="1"
                                                       {{ $att && $att->present ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        {{-- Alasan --}}
                                        <td>
                                            <input type="text"
                                                   name="reason_{{ $member->id }}"
                                                   class="form-control"
                                                   value="{{ $att->reason ?? '' }}"
                                                   placeholder="Isi alasan jika tidak hadir">
                                        </td>

                                        {{-- Bukti Lama --}}
                                        <td>
                                            @if($att && $att->bukti_path)
                                                <a href="{{ asset('storage/'.$att->bukti_path) }}" target="_blank"
                                                   class="btn btn-sm btn-outline-primary">
                                                    Lihat Bukti
                                                </a>
                                            @else
                                                <span class="text-muted">Tidak ada</span>
                                            @endif
                                        </td>

                                        {{-- Upload Baru --}}
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
                            Perbarui Absensi
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection

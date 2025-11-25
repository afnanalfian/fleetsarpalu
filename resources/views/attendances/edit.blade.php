@extends('layouts.app')

@section('title', 'Edit Absensi')

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

                <form action="{{ route('attendances.update', $checking->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Nama</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                    <th>Bukti Lama</th>
                                    <th>Bukti Baru</th>
                                    <th>Pengganti</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($members as $member)
                                    @php $att = $attendanceMap[$member->id] ?? null; @endphp

                                    <tr>
                                        <td>{{ $member->name }}</td>

                                        {{-- Status --}}
                                        <td>
                                            <select name="status_{{ $member->id }}" class="form-select status-select" data-id="{{ $member->id }}">
                                                @foreach(['Hadir','Sakit','Izin','Cuti','Tanpa Keterangan'] as $opt)
                                                    <option value="{{ $opt }}" {{ $att && $att->status == $opt ? 'selected' : '' }}>
                                                        {{ $opt }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        {{-- Catatan --}}
                                        <td>
                                            <input type="text" name="notes_{{ $member->id }}"
                                                   class="form-control"
                                                   value="{{ $att->notes ?? '' }}">
                                        </td>

                                        {{-- Bukti Lama --}}
                                        <td>
                                            @if($att && $att->bukti_path)
                                                <a href="{{ asset('storage/'.$att->bukti_path) }}" target="_blank"
                                                   class="btn btn-sm btn-outline-primary">Lihat</a>
                                            @else
                                                <span class="text-muted">Tidak ada</span>
                                            @endif
                                        </td>

                                        {{-- Upload Baru --}}
                                        <td>
                                            <input type="file" class="form-control"
                                                   name="bukti_{{ $member->id }}"
                                                   accept="image/*,application/pdf">
                                        </td>

                                        {{-- Pengganti --}}
                                        <td class="text-center">

                                            <input type="hidden"
                                                name="replacement_{{ $member->id }}"
                                                id="replacement_{{ $member->id }}"
                                                value="{{ $att->replacement_user_id ?? '' }}">

                                            <button type="button"
                                                    class="btn btn-sm btn-warning open-replacement"
                                                    data-member="{{ $member->id }}"
                                                    data-replace-btn="{{ $member->id }}"
                                                    onclick="openReplacementModal({{ $member->id }})"
                                                    {{ ($att && $att->status === 'Hadir') ? 'disabled' : '' }}>
                                                Pilih
                                            </button>

                                            <div id="replacement_wrap_{{ $member->id }}" class="small mt-1">
                                                <span id="replacement_name_{{ $member->id }}" class="text-success">
                                                    @if($att && $att->replacement)
                                                        Pengganti: <strong>{{ $att->replacement->name }}</strong>
                                                    @endif
                                                </span>

                                                <button type="button"
                                                        class="btn btn-sm btn-danger ms-2 remove-replacement
                                                            {{ ($att && $att->replacement_id) ? '' : 'd-none' }}"
                                                        data-member="{{ $member->id }}">
                                                    x
                                                </button>
                                            </div>

                                        </td>
                                    </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Perbarui Absensi</button>
                    <script>
                    document.addEventListener("DOMContentLoaded", () => {

                        // === HANDLE STATUS SELECT ===
                        document.querySelectorAll('.status-select').forEach(select => {
                            select.addEventListener('change', function () {
                                const id = this.dataset.id;

                                // tombol pilih pengganti
                                const btn = document.querySelector(`[data-replace-btn="${id}"]`);
                                const removeBtn = document.querySelector(`.remove-replacement[data-member="${id}"]`);

                                // disable/enable tombol pilih
                                if (btn) {
                                    btn.disabled = (this.value === "Hadir");
                                }

                                // kalau status jadi Hadir → hapus pengganti
                                if (this.value === "Hadir") {
                                    document.getElementById("replacement_" + id).value = "";
                                    document.getElementById("replacement_name_" + id).innerHTML = "";
                                    if (removeBtn) removeBtn.classList.add("d-none");
                                }
                            });
                        });

                        // === HANDLE TOMBOL HAPUS ===
                        document.querySelectorAll('.remove-replacement').forEach(btn => {
                            btn.addEventListener('click', function () {
                                const id = this.dataset.member;

                                document.getElementById("replacement_" + id).value = "";
                                document.getElementById("replacement_name_" + id).innerHTML = "";

                                this.classList.add("d-none");
                            });
                        });

                    });
                    </script>

                </form>

            </div>
        </div>
    </div>
</div>

@include('attendances.modal-replacement')

@endsection

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

                <form action="{{ route('attendances.store', $checking->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Nama</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                    <th>Bukti</th>
                                    <th>Pengganti</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>

                                        {{-- Status --}}
                                        <td>
                                            <select name="status_{{ $member->id }}" class="form-select status-select" data-id="{{ $member->id }}">
                                                <option value="Hadir">Hadir</option>
                                                <option value="Sakit">Sakit</option>
                                                <option value="Izin">Izin</option>
                                                <option value="Cuti">Cuti</option>
                                                <option value="Tanpa Keterangan">Tanpa Keterangan</option>
                                            </select>
                                        </td>

                                        {{-- Catatan --}}
                                        <td>
                                            <input type="text" name="notes_{{ $member->id }}" class="form-control">
                                        </td>

                                        {{-- Upload Bukti --}}
                                        <td>
                                            <input type="file" name="bukti_{{ $member->id }}" class="form-control"
                                                   accept="image/*,application/pdf">
                                        </td>

                                        {{-- Pengganti --}}
                                        <td class="text-center">

                                            <input type="hidden" name="replacement_{{ $member->id }}" id="replacement_{{ $member->id }}">

                                            <button type="button"
                                                    class="btn btn-sm btn-warning open-replacement"
                                                    data-member="{{ $member->id }}"
                                                    data-replace-btn="{{ $member->id }}"
                                                    onclick="openReplacementModal({{ $member->id }})"
                                                    disabled>
                                                Pilih
                                            </button>

                                            <div id="replacement_wrap_{{ $member->id }}" class="small mt-1">
                                                <span id="replacement_name_{{ $member->id }}" class="text-success"></span>

                                                <button type="button"
                                                        class="btn btn-sm btn-danger ms-2 d-none remove-replacement"
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

                    <button type="submit" class="btn btn-primary mt-3">Simpan Absensi</button>
                    <script>
                    document.querySelectorAll('.status-select').forEach(select => {
                        select.addEventListener('change', function () {
                            const id = this.dataset.id;

                            // cari tombol dengan data-replace-btn="id"
                            const btn = document.querySelector(`[data-replace-btn="${id}"]`);

                            if (btn) {
                                btn.disabled = (this.value === "Hadir");
                            }
                        });
                    });
                    document.querySelectorAll('.remove-replacement').forEach(btn => {
                        btn.addEventListener('click', function () {
                            const id = this.dataset.member;

                            // kosongkan input hidden
                            document.getElementById("replacement_" + id).value = "";

                            // kosongkan teks nama
                            document.getElementById("replacement_name_" + id).innerHTML = "";

                            // sembunyikan tombol hapus
                            this.classList.add('d-none');
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

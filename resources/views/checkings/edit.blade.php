@extends('layouts.app')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-light rounded p-4 shadow-sm">
        {{-- Tombol kembali --}}
        <div class="mb-3">
            <a href="{{ route('checking.index') }}" class="text-decoration-none text-dark">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali
            </a>
        </div>

        <h5 class="mb-4">Edit Data Pengecekan</h5>

        <form action="{{ route('checking.update', $check->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                {{-- Tim --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tim</label>
                    <input type="text" class="form-control"
                           value="{{ $check->team->name ?? '-' }}" readonly>
                    <input type="hidden" name="team_id"
                           value="{{ $check->team_id }}">
                </div>

                {{-- Ketua Tim --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">Ketua Tim</label>
                    <input type="text" class="form-control"
                           value="{{ $check->team->leader->name ?? '-' }}" readonly>
                </div>

                {{-- Tanggal --}}
                <div class="col-md-6">
                    <label for="scheduled_date" class="form-label fw-bold">Tanggal Pengecekan</label>
                    <input type="date" name="scheduled_date" id="scheduled_date"
                           class="form-control" value="{{ $check->scheduled_date }}" required>
                    @error('scheduled_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="col-md-6">
                    <label for="status" class="form-label fw-bold">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="pending" {{ $check->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ $check->status == 'in_progress' ? 'selected' : '' }}>Sedang Berjalan</option>
                        <option value="completed" {{ $check->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div class="col-12 text-start">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-2"></i> Update
                    </button>
                </div>
            </div>
        </form>

        <div class="mt-4 alert alert-info small mb-0">
            <i class="fa-solid fa-circle-info me-2"></i>
            Mengubah tanggal atau status pengecekan tidak akan mempengaruhi data hasil cek kendaraan yang sudah ada.
        </div>
    </div>
</div>
@endsection

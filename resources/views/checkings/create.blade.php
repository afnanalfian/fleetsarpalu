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

        <h5 class="mb-4">Buat Pengecekan Baru</h5>

        <form action="{{ route('checking.store') }}" method="POST">
            @csrf
            <div class="row g-3">

                {{-- Tim otomatis dari user login --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tim</label>
                    <input type="text" class="form-control"
                           value="{{ auth()->user()->team->name ?? '-' }}" readonly>
                    <input type="hidden" name="team_id"
                           value="{{ auth()->user()->team_id }}">
                </div>

                {{-- Ketua tim --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">Ketua Tim</label>
                    <input type="text" class="form-control"
                           value="{{ auth()->user()->name }}" readonly>
                </div>

                {{-- Tanggal pengecekan --}}
                <div class="col-md-6">
                    <label for="date" class="form-label fw-bold">Tanggal Pengecekan</label>
                    <input type="date" name="scheduled_date" id="date"
                           class="form-control" value="{{ date('Y-m-d') }}" required>
                    @error('scheduled_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-12 text-start">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-2"></i> Simpan
                    </button>
                </div>
            </div>
        </form>

        {{-- Informasi tambahan --}}
        <div class="mt-4 alert alert-info small mb-0">
            <i class="fa-solid fa-circle-info me-2"></i>
            Setelah pengecekan dibuat, sistem akan otomatis menambahkan seluruh kendaraan aktif
            ke dalam daftar pengecekan. Kendaraan dengan status
            <strong>"Sedang Operasi/Digunakan"</strong> atau
            <strong>"Sedang Dalam Perbaikan"</strong> akan tetap muncul tetapi tidak dapat dicek.
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')

<style>
    .profile-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        transition: 0.2s;
    }

    .profile-card:hover {
        box-shadow: 0 12px 26px rgba(0,0,0,0.15);
    }

    .profile-title {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        border-bottom: 2px solid #ddd;
        padding-bottom: 10px;
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: #444;
    }

    .btn-modern {
        background: #0d6efd;
        border-radius: 8px;
        padding: 10px 24px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .btn-modern:hover {
        background: #085bd1;
    }
</style>


<div class="container py-4" style="max-width: 650px;">

    <div class="profile-card">

        <div class="profile-title">Profil Pengguna</div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Informasi User (tidak bisa diedit) --}}
        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" value="{{ $user->name }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">NIP</label>
            <input type="text" class="form-control" value="{{ $user->NIP }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="text" class="form-control" value="{{ $user->email }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Tim</label>
            <input type="text" class="form-control" value="{{ $user->team_name }}" disabled>
        </div>

        <div class="mb-4">
            <label class="form-label">Peran</label>
            <input type="text" class="form-control" value="{{ $user->role_label }}" disabled>
        </div>

        {{-- FORM UPDATE --}}
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf

            <div class="mb-4">
                <label class="form-label">Nomor Telepon</label>
                <input type="text" name="phone" class="form-control"
                       value="{{ old('phone', $user->phone) }}">
            </div>

            <hr class="my-4">

            <h5 class="fw-bold mb-3">Ganti Password</h5>

            <div class="mb-3">
                <label class="form-label">Password Lama</label>
                <input type="password" name="old_password" class="form-control">
                @error('old_password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control">
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>

            <button class="btn btn-primary btn-modern">Simpan Perubahan</button>
        </form>

    </div>
</div>

@endsection

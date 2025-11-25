@extends('layouts.app')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light text-center rounded p-4">

                <div class="text-start mb-4">
                    <a href="{{ route('users.index') }}" class="mb-0 text-decoration-none text-black">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <form action="{{ route('users.update', $user->id) }}" method="POST" class="row g-3">
                    @csrf
                    @method('PUT')

                    {{-- NIP --}}
                    <div class="col-md-6">
                        <label class="form-label w-100 text-start">NIP<span class="text-danger">*</span></label>
                        <input type="number" name="NIP" id="NIP"
                               value="{{ old('NIP', $user->NIP) }}"
                               class="form-control @error('NIP') is-invalid @enderror"
                               min="1" @required(true)>
                        @error('NIP')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Nama --}}
                    <div class="col-md-6">
                        <label class="form-label w-100 text-start">Nama<span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                               value="{{ old('name', $user->name) }}"
                               class="form-control @error('name') is-invalid @enderror"
                               autocomplete="off" @required(true)>
                        @error('name')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label class="form-label w-100 text-start">Email<span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $user->email) }}"
                               class="form-control @error('email') is-invalid @enderror"
                               autocomplete="off" @required(true)>
                        @error('email')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Phone (Optional) --}}
                    <div class="col-md-6">
                        <label class="form-label w-100 text-start">Phone Number</label>
                        <input type="tel" name="phone" id="phone"
                               value="{{ old('phone', $user->phone) }}"
                               class="form-control @error('phone') is-invalid @enderror"
                               autocomplete="off">
                        @error('phone')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Password (opsional) --}}
                    <div class="col-12">
                        <label class="form-label w-100 text-start">Sandi (opsional)</label>
                        <input type="password" name="password" id="password"
                               class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password (opsional) --}}
                    <div class="col-12">
                        <label class="form-label w-100 text-start">Konfirmasi Sandi</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="form-control @error('password_confirmation') is-invalid @enderror">
                        @error('password_confirmation')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Role / Role --}}
                    <div class="col-md-4">
                        <label class="form-label w-100 text-start">Role<span class="text-danger">*</span></label>
                        <select id="Role" name="role" class="form-select" @required(true)>
                            <option value="Pegawai" {{ old('role', $user->role) == 'Pegawai' ? 'selected' : '' }}>Pegawai</option>
                            <option value="Admin" {{ old('role', $user->role) == 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="Kepala Sumber Daya" {{ old('role', $user->role) == 'Kepala Sumber Daya' ? 'selected' : '' }}>Kepala Sumber Daya</option>
                            <option value="Ketua Tim" {{ old('role', $user->role) == 'Ketua Tim' ? 'selected' : '' }}>Ketua Tim</option>
                        </select>
                        @error('role')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Team (optional) --}}
                    <div class="col-md-4">
                        <label class="form-label w-100 text-start">Team</label>
                        <select name="team_id" id="team" class="form-control @error('team_id') is-invalid @enderror">
                            <option value="">Pilih Team</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}"
                                    {{ old('team_id', $user->team_id) == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('team_id')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="col-md-12 w-100 text-start">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection

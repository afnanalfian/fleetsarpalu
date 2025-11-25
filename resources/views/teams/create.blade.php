@extends('layouts.app')

@section('title', 'Tambah Tim')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light text-center rounded p-4">
                <div class="text-start mb-4">
                    <a href="{{ route('teams.index') }}" class="mb-0 text-decoration-none text-black">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                {{-- Form Tambah Tim --}}
                <form action="{{ route('teams.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label for="name" class="form-label w-100 text-start">Nama Tim <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" id="name" required>
                        @error('name')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="leader_id" class="form-label w-100 text-start">Ketua Tim <span class="text-danger">*</span></label>
                        <select name="leader_id" id="leader_id" class="form-select @error('leader_id') is-invalid @enderror" required>
                            <option value="" hidden selected>Pilih Ketua Tim</option>
                                @foreach($leaders as $leader)
                                    <option value="{{ $leader->id }}">
                                        {{ $leader->name }} ({{ $leader->NIP }})
                                    </option>
                                @endforeach
                        </select>
                        @error('leader_id')
                            <div class="text-danger text-start"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    <div class="col-md-12 text-start">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

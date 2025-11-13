@extends('layouts.app')

@section('title', 'Detail Tim')

@section('content')

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded p-4">

                {{-- Tombol Kembali --}}
                <div class="text-start mb-4">
                    <a href="{{ route('teams.index') }}" class="text-decoration-none text-black">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                {{-- Judul Tim --}}
                <h3 class="mb-3 text-center fw-bold">{{ $team->name }}</h3>

                <div class="row mb-4">
                    <div class="col-md-6 mx-auto">

                        {{-- Ketua Tim --}}
                        <div class="bg-white p-3 rounded shadow-sm mb-3">
                            <h6 class="fw-bold mb-2">Ketua Tim</h6>
                            <p class="mb-0">
                                {{ $team->leader->name }}
                                <br>
                                <small class="text-muted">{{ $team->leader->NIP }}</small>
                            </p>
                        </div>

                        {{-- Anggota Tim --}}
                        <div class="bg-white p-3 rounded shadow-sm">
                            <h6 class="fw-bold mb-2">Anggota Tim</h6>

                            @if($team->members->count() > 0)
                                <ul class="list-group text-start">
                                    @foreach($team->members as $member)
                                        {{-- Jangan tampilkan ketua di daftar anggota --}}
                                        @if($member->id != $team->leader_id)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>
                                                    {{ $member->name }}
                                                    <br>
                                                    <small class="text-muted">{{ $member->NIP }}</small>
                                                </span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">Belum ada anggota.</p>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

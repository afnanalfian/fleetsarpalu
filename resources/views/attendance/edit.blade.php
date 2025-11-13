@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Edit Kehadiran</h4>

    <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nama Pegawai</label>
            <input type="text" class="form-control" value="{{ $attendance->user->name }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Kehadiran</label><br>
            <label><input type="radio" name="present" value="1" {{ $attendance->present ? 'checked' : '' }}> Hadir</label>
            <label class="ms-3"><input type="radio" name="present" value="0" {{ !$attendance->present ? 'checked' : '' }}> Tidak Hadir</label>
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label">Alasan</label>
            <textarea name="reason" class="form-control" rows="3">{{ $attendance->reason }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Perbarui</button>
        <a href="{{ route('attendance.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

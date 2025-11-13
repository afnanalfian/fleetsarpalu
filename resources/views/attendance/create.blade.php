@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Form Kehadiran Tim</h4>

    <form action="{{ route('attendance.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="check_id" class="form-label">Jadwal Pengecekan</label>
            <select name="check_id" class="form-select" required>
                <option value="">-- Pilih Jadwal --</option>
                @foreach($checks as $check)
                    <option value="{{ $check->id }}">
                        {{ $check->scheduled_date }} - {{ $check->team->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="user_id" class="form-label">Nama Pegawai</label>
            <select name="user_id" class="form-select" required>
                <option value="">-- Pilih Anggota Tim --</option>
                @foreach($teamMembers as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Kehadiran</label><br>
            <label><input type="radio" name="present" value="1" required> Hadir</label>
            <label class="ms-3"><input type="radio" name="present" value="0"> Tidak Hadir</label>
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label">Alasan (jika tidak hadir)</label>
            <textarea name="reason" class="form-control" rows="3" placeholder="Isi alasan jika tidak hadir"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Kehadiran</button>
        <a href="{{ route('attendance.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

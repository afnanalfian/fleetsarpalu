@extends('layouts.app')
@section('content')
<h3>Notifikasi</h3>
<ul class="list-group">
<li class="list-group-item d-flex justify-content-between align-items-center">
Pengajuan peminjaman baru masuk
<span class="badge bg-primary">Baru</span>
</li>
<li class="list-group-item d-flex justify-content-between align-items-center">
Jadwal pengecekan hari ini
<span class="badge bg-warning">Reminder</span>
</li>
</ul>
@endsection


{{-- Navbar indicator example (to be included in layout navbar) --}}
{{--
<li class="nav-item">
<a class="nav-link" href="/notifications">
Notifikasi <span class="badge bg-danger">3</span>
</a>
</li>
--}}

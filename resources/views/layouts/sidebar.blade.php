@php
    $role = strtolower(auth()->user()->role);
    $routeRole = str_replace(' ', '', $role); // "ketua tim" → "ketuatim"
@endphp

<div class="sidebar pe-3">
    <nav class="navbar d-flex">
        <div class="logo mx-5 my-2">
            <img src="{{ asset('img/logo.png') }}" class="img-fluid px-3 mb-4" alt="Logo">
        </div>

        <div class="navbar-nav w-100 gap-2 fw-medium mt-7 mt-lg-0">

            {{-- Dashboard --}}
            <a href="{{ route($routeRole.'.dashboard') }}" class="nav-item nav-link ps-4 py-3">
                Dashboard
            </a>

            {{-- ADMIN --}}
            @if($role === 'admin')
                <a href="{{ route('users.index') }}" class="nav-item nav-link ps-4 py-3">User</a>
                <a href="{{ route('teams.index') }}" class="nav-item nav-link ps-4 py-3">Tim / Regu</a>
                <a href="{{ route('vehicles.index') }}" class="nav-item nav-link ps-4 py-3">Kendaraan</a>
                <a href="{{ route('borrowings.index') }}" class="nav-item nav-link ps-4 py-3">Peminjaman</a>
                <a href="{{ route('checkings.index') }}" class="nav-item nav-link ps-4 py-3">Pengecekan</a>
                <a href="{{ route('reports.index') }}" class="nav-item nav-link ps-4 py-3">Laporan</a>
            @endif

            {{-- PEGAWAI --}}
            @if($role === 'pegawai')
                <a href="{{ route('borrowings.index') }}" class="nav-item nav-link ps-4 py-3">Peminjaman</a>
                <a href="{{ route('reports.index') }}" class="nav-item nav-link ps-4 py-3">Laporan Penggunaan</a>
                <a href="{{ route('vehicles.index') }}" class="nav-item nav-link ps-4 py-3">Kendaraan</a>
            @endif

            {{-- SUMDA --}}
            @if($role === 'sumda')
                <a href="{{ route('borrowings.index') }}" class="nav-item nav-link ps-4 py-3">Pengajuan Peminjaman</a>
                <a href="{{ route('vehicles.index') }}" class="nav-item nav-link ps-4 py-3">Kendaraan</a>
            @endif

            {{-- KETUA TIM --}}
            @if($role === 'ketuatim')
                <a href="{{ route('attendance.index') }}" class="nav-item nav-link ps-4 py-3">Absensi TIM</a>
                <a href="{{ route('checkitem.index') }}" class="nav-item nav-link ps-4 py-3">Checklist Kendaraan</a>
            @endif

        </div>
    </nav>
</div>

    @php
        $role = strtolower(auth()->user()->role);
        $routeRole = str_replace(' ', '', $role); // "ketua tim" → "ketuatim"
    @endphp

    <div class="sidebar pe-3" id="sidebar">
        <nav class="navbar d-flex">
            <div class="logo mx-5 my-2">
                <img src="{{ asset('img/logo.png') }}" class="img-fluid px-3 mb-4" alt="Logo">
            </div>

            <div class="navbar-nav w-100 gap-2 fw-medium mt-7 mt-lg-0">

            {{-- DASHBOARD --}}
                <a href="{{ route($routeRole.'.dashboard') }}"
                class="nav-item nav-link ps-4 py-3 {{ Request::routeIs($routeRole.'.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>

                {{-- ============================ --}}
                {{-- ADMIN --}}
                {{-- ============================ --}}
                @if($routeRole === 'admin')

                    <a href="{{ route('users.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('users.*') ? 'active' : '' }}">
                        User
                    </a>

                    <a href="{{ route('teams.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('teams.*') ? 'active' : '' }}">
                        Tim / Regu
                    </a>

                    <a href="{{ route('schedules.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('schedules.*') ? 'active' : '' }}">
                        Jadwal
                    </a>

                    <a href="{{ route('vehicles.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('vehicles.*') ? 'active' : '' }}">
                        Kendaraan
                    </a>

                    <a href="{{ route('borrowings.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('borrowings.*') ? 'active' : '' }}">
                        Peminjaman
                    </a>

                    <a href="{{ route('checkings.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('checkings.*') ? 'active' : '' }}">
                        Pengecekan
                    </a>

                @endif

                {{-- ============================ --}}
                {{-- PEGAWAI --}}
                {{-- ============================ --}}
                @if($routeRole === 'pegawai')

                    <a href="{{ route('teams.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('teams.*') ? 'active' : '' }}">
                        Tim / Regu
                    </a>

                    <a href="{{ route('schedules.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('schedules.*') ? 'active' : '' }}">
                        Jadwal
                    </a>

                    <a href="{{ route('vehicles.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('vehicles.*') ? 'active' : '' }}">
                        Kendaraan
                    </a>

                    <a href="{{ route('borrowings.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('borrowings.*') ? 'active' : '' }}">
                        Peminjaman
                    </a>

                    <a href="{{ route('checkings.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('checkings.*') ? 'active' : '' }}">
                        Pengecekan
                    </a>

                @endif

                {{-- ============================ --}}
                {{-- KEPALA SUMBER DAYA --}}
                {{-- ============================ --}}
                @if($routeRole === 'kepalasumberdaya')

                    <a href="{{ route('teams.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('teams.*') ? 'active' : '' }}">
                        Tim / Regu
                    </a>

                    <a href="{{ route('schedules.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('schedules.*') ? 'active' : '' }}">
                        Jadwal
                    </a>

                    <a href="{{ route('vehicles.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('vehicles.*') ? 'active' : '' }}">
                        Kendaraan
                    </a>

                    <a href="{{ route('borrowings.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('borrowings.*') ? 'active' : '' }}">
                        Peminjaman
                    </a>

                    <a href="{{ route('checkings.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('checkings.*') ? 'active' : '' }}">
                        Pengecekan
                    </a>

                @endif

                {{-- ============================ --}}
                {{-- KETUA TIM --}}
                {{-- ============================ --}}
                @if($routeRole === 'ketuatim')

                    <a href="{{ route('teams.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('teams.*') ? 'active' : '' }}">
                        Tim / Regu
                    </a>

                    <a href="{{ route('schedules.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('schedules.*') ? 'active' : '' }}">
                        Jadwal
                    </a>

                    <a href="{{ route('vehicles.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('vehicles.*') ? 'active' : '' }}">
                        Kendaraan
                    </a>

                    <a href="{{ route('borrowings.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('borrowings.*') ? 'active' : '' }}">
                        Peminjaman
                    </a>

                    <a href="{{ route('checkings.index') }}"
                    class="nav-item nav-link ps-4 py-3 {{ Request::routeIs('checkings.*') ? 'active' : '' }}">
                        Pengecekan
                    </a>

                @endif

            </div>
        </nav>
    </div>

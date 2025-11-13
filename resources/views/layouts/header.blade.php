<nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-1">
    <div class="navbar-nav align-items-center ms-auto">
        <div class="nav-item dropdown">
            <a href="" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <span class="d-none d-lg-inline-flex">{{ auth()->user()->username }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end bg-light border-1 rounded-0 rounded-bottom m-0">
                <a href="{{ route('logout') }}" class="dropdown-item"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
            </div>
        </div>
    </div>
</nav>

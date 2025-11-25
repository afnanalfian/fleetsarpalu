<nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-3">
    {{-- HAMBURGER --}}
    <button class="btn btn-outline-dark me-3 sidebar-toggler">
        <i class="fa-solid fa-bars"></i>
    </button>
    <div class="d-flex align-items-center ms-auto gap-3">

        {{-- Notifikasi --}}
        @php
            // Total unread untuk badge
            $unread = App\Models\Notification::where('user_id', auth()->id())
                ->where('is_read', 0)
                ->count();

            // Ambil hanya 5 notifikasi terbaru untuk dropdown
            $notifs = App\Models\Notification::where('user_id', auth()->id())
                ->latest()
                ->limit(10)
                ->get();
        @endphp

        <div class="nav-item dropdown me-3">
            <a href="#" role="button" class="nav-link position-relative" data-bs-toggle="dropdown" style="cursor: pointer;">
                <i class="fa-solid fa-bell fa-lg text-dark"></i>

                {{-- Badge Unread --}}
                @if($unread > 0)
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-circle">
                        {{ $unread }}
                    </span>
                @endif
            </a>

            <div class="dropdown-menu dropdown-menu-end p-0 shadow notif-dropdown">

                <div class="p-3 border-bottom d-flex justify-content-between">
                    <strong>Notifikasi</strong>

                    @if($unread > 0)
                        <form action="{{ route('notifications.read-all') }}" method="POST">
                            @csrf
                            <button class="btn btn-link p-0" style="font-size: 0.8rem;">
                                Tandai semua telah dibaca
                            </button>
                        </form>
                    @endif
                </div>

                <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    @forelse($notifs as $notif)
                        <a href="{{ route('notifications.read', $notif->id) }}"
                        class="list-group-item list-group-item-action {{ $notif->is_read ? '' : 'bg-light' }}"
                        style="white-space: normal;">

                            <div class="fw-bold">{{ $notif->title }}</div>
                            <div class="small text-muted">{{ $notif->message }}</div>
                            <div class="small text-secondary mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                        </a>
                    @empty
                        <div class="p-3 text-center text-muted">
                            Tidak ada notifikasi
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Username & Dropdown --}}
        <div class="nav-item dropdown">
            <a href="#" role="button" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <span class="d-none d-lg-inline-flex">{{ auth()->user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end bg-light border-1 rounded-0 rounded-bottom m-0">
                <a href="{{ route('profile.show') }}" class="dropdown-item">
                    <i class="fa-solid fa-user"></i> Profil
                </a>
                <a href="{{ route('logout') }}" class="dropdown-item">
                    <i class="fa-solid fa-right-from-bracket"></i> Log Out
                </a>
            </div>
        </div>

    </div>
</nav>

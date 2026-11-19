<header class="app-navbar">
    <button class="btn btn-light d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Buka menu">
        <i class="bi bi-list fs-5"></i>
    </button>
    <h1 class="me-auto">@yield('judul')</h1>

    <div class="dropdown">
        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none text-dark" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="d-none d-md-block lh-sm">
                <span class="fw-semibold d-block">{{ auth()->user()->name }}</span>
                <small class="text-muted">{{ auth()->user()->role->label() }}</small>
            </span>
            <i class="bi bi-chevron-down small text-muted"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
            <li class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right"></i> Keluar</button>
                </form>
            </li>
        </ul>
    </div>
</header>

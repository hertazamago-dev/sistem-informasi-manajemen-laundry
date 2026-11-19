@php
    $role = auth()->user()->role->value;
    // [rute, ikon, label, pola rute aktif, role yang boleh]
    $menu = [
        ['dashboard', 'bi-speedometer2', 'Dashboard', 'dashboard', ['admin', 'petugas']],
        ['pelanggan.index', 'bi-people', 'Pelanggan', 'pelanggan.*', ['admin', 'petugas']],
        ['layanan.index', 'bi-basket3', $role === 'admin' ? 'Layanan' : 'Daftar Layanan', 'layanan.*', ['admin', 'petugas']],
        ['transaksi.index', 'bi-receipt', 'Transaksi', 'transaksi.*', ['petugas']], // Admin tidak mengoperasikan transaksi
        // Bagi Admin, halaman detail transaksi/nota/bukti dibuka dari Riwayat, jadi menu Riwayat ikut menyala
        ['riwayat.index', 'bi-clock-history', 'Riwayat', $role === 'admin' ? 'riwayat.*|transaksi.*|pembayaran.*' : 'riwayat.*', ['admin', 'petugas']],
        ['laporan.index', 'bi-bar-chart-line', 'Laporan', 'laporan.index|laporan.export', ['admin']],
        ['laporan.rekap', 'bi-journal-check', 'Rekap Harian Saya', 'laporan.rekap', ['petugas']],
        ['petugas.index', 'bi-person-gear', 'Akun Petugas', 'petugas.*', ['admin']],
    ];
@endphp
<aside class="offcanvas-lg offcanvas-start sidebar" tabindex="-1" id="sidebar">
    <div class="sidebar-inner">
        <a href="{{ route('dashboard') }}" class="brand">
            <span class="brand-logo"><i class="bi bi-droplet-half"></i></span>
            <span><b>{{ config('laundry.nama') }}</b><small>Manajemen Laundry</small></span>
        </a>

        @if ($role === 'petugas')
            <a href="{{ route('transaksi.create') }}" class="btn btn-light fw-semibold mb-2 text-primary">
                <i class="bi bi-plus-circle"></i> Transaksi Baru
            </a>
        @endif

        <div class="nav-label">Menu</div>
        <nav>
            @foreach ($menu as [$rute, $ikon, $label, $pola, $boleh])
                @if (in_array($role, $boleh, true))
                    <a href="{{ route($rute) }}"
                        class="nav-item-link {{ request()->routeIs(...explode('|', $pola)) ? 'active' : '' }}">
                        <i class="bi {{ $ikon }}"></i><span>{{ $label }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="sidebar-foot">
            <i class="bi bi-shield-check"></i> Masuk sebagai {{ auth()->user()->role->label() }}
        </div>
    </div>
</aside>
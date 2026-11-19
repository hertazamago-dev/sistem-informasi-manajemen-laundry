@extends('layouts.app')
@section('judul', 'Dashboard')

@section('content')
    <div class="hero-mini mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="fw-bold mb-1">Halo, {{ auth()->user()->name }} 👋</h4>
                <p class="mb-0 opacity-75">{{ now()->translatedFormat('l, d F Y') }} &middot;
                    {{ $stat['masuk_hari_ini'] }} laundry masuk hari ini, {{ $stat['selesai'] }} siap diambil.
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                @unless ($admin)
                    <a href="{{ route('transaksi.create') }}" class="btn btn-light text-primary fw-semibold"><i
                            class="bi bi-plus-circle"></i> Transaksi Baru</a>
                @endunless
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3"><x-stat-card ikon="bi-people" label="Total Pelanggan" :nilai="$stat['pelanggan']" />
        </div>
        <div class="col-6 col-xl-3"><x-stat-card ikon="bi-receipt" label="Total Transaksi" :nilai="$stat['transaksi']" />
        </div>
        <div class="col-6 col-xl-3"><x-stat-card ikon="bi-box-arrow-in-down" label="Laundry Masuk Hari Ini"
                :nilai="$stat['masuk_hari_ini']" warna="biru" /></div>
        <div class="col-6 col-xl-3"><x-stat-card ikon="bi-hourglass-split" label="Menunggu" :nilai="$stat['menunggu']"
                warna="abu" /></div>
        <div class="col-6 col-xl-3"><x-stat-card ikon="bi-droplet-half" label="Sedang Diproses" :nilai="$stat['diproses']"
                warna="oranye" /></div>
        <div class="col-6 col-xl-3"><x-stat-card ikon="bi-bag-check" label="Selesai / Siap Diambil"
                :nilai="$stat['selesai']" warna="hijau" /></div>
        <div class="col-6 col-xl-3"><x-stat-card ikon="bi-check2-all" label="Sudah Diambil" :nilai="$stat['diambil']"
                warna="hijau" /></div>
        @if ($admin)
            <div class="col-6 col-xl-3"><x-stat-card ikon="bi-cash-coin" label="Pendapatan Hari Ini"
                    :nilai="\App\Support\Format::rupiah($stat['pendapatan_hari'])" warna="hijau" /></div>
            <div class="col-12 col-xl-3"><x-stat-card ikon="bi-graph-up-arrow" label="Pendapatan Bulan Ini"
                    :nilai="\App\Support\Format::rupiah($stat['pendapatan_bulan'])" warna="biru" /></div>
        @endif
    </div>

    <div class="row g-3 mb-4">
        <div class="{{ $admin ? 'col-lg-6' : 'col-lg-8' }}">
            <div class="cardx h-100">
                <div class="cardx-head">
                    <h2><i class="bi bi-bar-chart text-primary"></i> Transaksi 7 Hari Terakhir</h2>
                </div>
                <div class="cardx-body"><canvas id="grafikTransaksi" height="150"></canvas></div>
            </div>
        </div>
        @if ($admin)
            <div class="col-lg-6">
                <div class="cardx h-100">
                    <div class="cardx-head">
                        <h2><i class="bi bi-graph-up text-success"></i> Pendapatan 6 Bulan Terakhir</h2>
                    </div>
                    <div class="cardx-body"><canvas id="grafikPendapatan" height="150"></canvas></div>
                </div>
            </div>
        @else
            <div class="col-lg-4">
                <div class="cardx h-100">
                    <div class="cardx-head">
                        <h2><i class="bi bi-pie-chart text-warning"></i> Komposisi Status</h2>
                    </div>
                    <div class="cardx-body"><canvas id="grafikStatus" height="190"></canvas></div>
                </div>
            </div>
        @endif
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="cardx">
                <div class="cardx-head">
                    <h2><i class="bi bi-receipt-cutoff text-primary"></i> Transaksi Terbaru</h2>
                    <a href="{{ route('riwayat.index') }}" class="btn btn-sm btn-outline-primary">Lihat semua</a>
                </div>
                <div class="table-wrap">
                    <table class="table tabel table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($terbaru as $t)
                                <tr>
                                    <td><a class="fw-semibold text-decoration-none"
                                            href="{{ route('transaksi.show', $t) }}">{{ $t->kode }}</a>
                                        <div class="small text-muted">{{ $t->tanggal_masuk->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td>{{ $t->pelanggan->nama }}</td>
                                    <td>@rupiah($t->total)</td>
                                    <td><x-badge-status :status="$t->status_laundry" /></td>
                                    <td><x-badge-status :status="$t->status_pembayaran" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"><x-kosong pesan="Belum ada transaksi." ikon="bi-receipt" /></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="cardx h-100">
                <div class="cardx-head">
                    <h2><i class="bi bi-list-check text-primary"></i> Ringkasan Status Laundry</h2>
                </div>
                <div class="cardx-body">
                    @foreach ($ringkasanStatus as $r)
                        <div
                            class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <x-badge-status :status="$r['status']" />
                            <span class="fw-bold">{{ $r['jumlah'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const rupiah = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');

        new Chart(document.getElementById('grafikTransaksi'), {
            type: 'bar',
            data: {
                labels: @json($grafikTransaksi['label']),
                datasets: [{ label: 'Transaksi', data: @json($grafikTransaksi['data']), backgroundColor: '#0EA5E9', borderRadius: 8 }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });

        @if ($admin)
            new Chart(document.getElementById('grafikPendapatan'), {
                type: 'line',
                data: {
                    labels: @json($grafikPendapatan['label']),
                    datasets: [{
                        label: 'Pendapatan', data: @json($grafikPendapatan['data']), borderColor: '#22C55E',
                        backgroundColor: 'rgba(34,197,94,.15)', fill: true, tension: .35, pointRadius: 4
                    }]
                },
                options: {
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => rupiah(c.parsed.y) } } },
                    scales: { y: { beginAtZero: true, ticks: { callback: (v) => rupiah(v) } } }
                }
            });
        @else
            new Chart(document.getElementById('grafikStatus'), {
                type: 'doughnut',
                data: {
                    labels: @json($ringkasanStatus->map(fn($r) => $r['status']->label())->values()),
                    datasets: [{
                        data: @json($ringkasanStatus->pluck('jumlah')->values()),
                        backgroundColor: ['#94A3B8', '#F59E0B', '#FBBF24', '#FB923C', '#22C55E', '#15803D']
                    }]
                },
                options: { plugins: { legend: { position: 'bottom' } } }
            });
        @endif
    </script>
@endpush
@extends('layouts.app')
@section('judul', 'Rekap Harian Saya')

@section('content')
@php $kas = $pembayaran->sum('jumlah_bayar'); @endphp
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h5 class="fw-bold mb-0">Rekap {{ auth()->user()->name }}</h5>
        <div class="text-muted small">{{ now()->translatedFormat('l, d F Y') }} &middot; hanya mencakup data milik Anda hari ini</div>
    </div>
    <button onclick="window.print()" class="btn btn-primary no-print"><i class="bi bi-printer"></i> Cetak Rekap</button>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-4"><x-stat-card ikon="bi-receipt" label="Transaksi yang saya terima" :nilai="$transaksi->count()" /></div>
    <div class="col-6 col-lg-4"><x-stat-card ikon="bi-cash-stack" label="Pembayaran yang saya catat" :nilai="$pembayaran->count()" warna="oranye" /></div>
    <div class="col-12 col-lg-4"><x-stat-card ikon="bi-wallet2" label="Kas tunai diterima (tutup kas)" :nilai="\App\Support\Format::rupiah($kas)" warna="hijau" /></div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="cardx mb-3">
            <div class="cardx-head"><h2>Pembayaran yang Saya Catat</h2></div>
            <div class="table-wrap">
                <table class="table tabel">
                    <thead><tr><th>Jam</th><th>Bukti</th><th>Pelanggan</th><th class="text-end">Jumlah</th></tr></thead>
                    <tbody>
                    @forelse ($pembayaran as $b)
                        <tr><td>{{ $b->tanggal_bayar->format('H:i') }}</td>
                            <td><a href="{{ route('pembayaran.bukti', $b) }}" target="_blank" class="text-decoration-none">{{ $b->kode }}</a></td>
                            <td>{{ $b->transaksi->pelanggan->nama }}</td><td class="text-end">@rupiah($b->jumlah_bayar)</td></tr>
                    @empty
                        <tr><td colspan="4"><x-kosong pesan="Belum ada pembayaran hari ini." ikon="bi-cash" /></td></tr>
                    @endforelse
                    </tbody>
                    @if ($pembayaran->isNotEmpty())<tfoot><tr class="fw-bold"><td colspan="3" class="text-end">Total kas</td><td class="text-end">@rupiah($kas)</td></tr></tfoot>@endif
                </table>
            </div>
        </div>

        <div class="cardx">
            <div class="cardx-head"><h2>Transaksi yang Saya Terima</h2></div>
            <div class="table-wrap">
                <table class="table tabel">
                    <thead><tr><th>Jam</th><th>Kode</th><th>Pelanggan</th><th>Status</th><th>Bayar</th></tr></thead>
                    <tbody>
                    @forelse ($transaksi as $t)
                        <tr><td>{{ $t->tanggal_masuk->format('H:i') }}</td>
                            <td><a href="{{ route('transaksi.show', $t) }}" class="text-decoration-none">{{ $t->kode }}</a></td>
                            <td>{{ $t->pelanggan->nama }}</td>
                            <td><x-badge-status :status="$t->status_laundry" /></td>
                            <td><x-badge-status :status="$t->status_pembayaran" /></td></tr>
                    @empty
                        <tr><td colspan="5"><x-kosong pesan="Belum ada transaksi hari ini." ikon="bi-receipt" /></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="cardx">
            <div class="cardx-head"><h2>Laundry Berdasarkan Status</h2><span class="small text-muted">Seluruh toko, tanpa nominal</span></div>
            <div class="cardx-body">
                @foreach ($statusRingkas as $r)
                    <div class="d-flex justify-content-between align-items-center py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                        <x-badge-status :status="$r['status']" /><b>{{ $r['jumlah'] }}</b>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.print', ['kembali' => route('transaksi.show', $pembayaran->transaksi)])
@section('judul', 'Bukti Pembayaran ' . $pembayaran->kode)

@section('content')
@php $t = $pembayaran->transaksi; @endphp
<div class="nota">
    <div class="text-center">
        <h5 class="fw-bold mb-0">{{ config('laundry.nama') }}</h5>
        <div>{{ config('laundry.alamat') }}</div>
        <div class="fw-bold mt-2">BUKTI PEMBAYARAN</div>
    </div>
    <hr>
    <table>
        <tr><td>No. bukti</td><td class="text-end fw-bold">{{ $pembayaran->kode }}</td></tr>
        <tr><td>Tanggal bayar</td><td class="text-end">{{ $pembayaran->tanggal_bayar->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Transaksi</td><td class="text-end">{{ $t->kode }}</td></tr>
        <tr><td>Pelanggan</td><td class="text-end">{{ $t->pelanggan->nama }}</td></tr>
        <tr><td>Layanan</td><td class="text-end">{{ $t->details->pluck('nama_layanan')->implode(', ') }}</td></tr>
        <tr><td>Metode</td><td class="text-end">Tunai</td></tr>
        <tr><td>Diterima oleh</td><td class="text-end">{{ $pembayaran->petugas->name }}</td></tr>
    </table>
    <hr>
    <table>
        <tr class="fw-bold fs-6"><td>JUMLAH DIBAYAR</td><td class="text-end">@rupiah($pembayaran->jumlah_bayar)</td></tr>
        <tr><td>Total tagihan</td><td class="text-end">@rupiah($t->total)</td></tr>
        <tr><td>Total sudah dibayar</td><td class="text-end">@rupiah($t->total_dibayar)</td></tr>
        <tr><td>Sisa tagihan</td><td class="text-end">@rupiah($t->sisa)</td></tr>
        <tr><td>Status</td><td class="text-end fw-bold">{{ $t->status_pembayaran->label() }}</td></tr>
    </table>
    @if ($pembayaran->catatan)<hr><div>Catatan: {{ $pembayaran->catatan }}</div>@endif
    <hr>
    <div class="text-center">Terima kasih 🙏</div>
</div>
@endsection

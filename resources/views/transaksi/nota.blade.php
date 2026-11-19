@extends('layouts.print', ['kembali' => route('transaksi.show', $transaksi)])
@section('judul', 'Nota ' . $transaksi->kode)

@section('content')
    <div class="nota">
        <div class="text-center">
            <h5 class="fw-bold mb-0">{{ config('laundry.nama') }}</h5>
            <div>{{ config('laundry.alamat') }}</div>
            <div>Telp. {{ config('laundry.telepon') }}</div>
        </div>
        <hr>
        <table>
            <tr>
                <td>Kode</td>
                <td class="text-end fw-bold">{{ $transaksi->kode }}</td>
            </tr>
            <tr>
                <td>Tanggal masuk</td>
                <td class="text-end">{{ $transaksi->tanggal_masuk->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td class="text-end">{{ $transaksi->pelanggan->nama }}</td>
            </tr>
            <tr>
                <td>Telepon</td>
                <td class="text-end">{{ $transaksi->pelanggan->telepon_tampil }}</td>
            </tr>
            <tr>
                <td>Petugas</td>
                <td class="text-end">{{ $transaksi->petugas->name }}</td>
            </tr>
        </table>
        <hr>
        <table>
            @foreach ($transaksi->details as $d)
                <tr>
                    <td colspan="2" class="fw-semibold">{{ $d->nama_layanan }}</td>
                </tr>
                <tr>
                    <td>{{ \App\Support\Format::jumlah($d->jumlah) }} {{ $d->satuan }} x @rupiah($d->harga_satuan)</td>
                    <td class="text-end">@rupiah($d->subtotal)</td>
                </tr>
            @endforeach
        </table>
        <hr>
        <table>
            <tr class="fw-bold">
                <td>TOTAL</td>
                <td class="text-end">@rupiah($transaksi->total)</td>
            </tr>
            <tr>
                <td>Dibayar</td>
                <td class="text-end">@rupiah($transaksi->total_dibayar)</td>
            </tr>
            <tr>
                <td>Sisa</td>
                <td class="text-end">@rupiah($transaksi->sisa)</td>
            </tr>
            <tr>
                <td>Status bayar</td>
                <td class="text-end">{{ $transaksi->status_pembayaran->label() }}</td>
            </tr>
        </table>
        <hr>
        <div>Estimasi selesai: <b>{{ $transaksi->estimasi_selesai->format('d/m/Y H:i') }}</b></div>
        @if ($transaksi->catatan)
        <div>Catatan: {{ $transaksi->catatan }}</div>@endif
        <hr>
        @if (config('laundry.info_nota'))
        <div class="text-center small mb-2">{{ config('laundry.info_nota') }}</div>@endif
        <div class="text-center">Mohon bawa nota ini saat mengambil laundry.<br>Terima kasih atas kepercayaan Anda 🙏</div>
    </div>
@endsection
@extends('layouts.app')
@section('judul', 'Transaksi Laundry')

@section('content')
<div class="cardx">
    <div class="cardx-head">
        <form method="GET" class="d-flex gap-2 flex-wrap flex-grow-1">
            <div class="input-group" style="max-width:300px">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Kode atau nama pelanggan">
            </div>
            <select name="status" class="form-select" style="max-width:190px" onchange="this.form.submit()">
                <option value="">Semua status</option>
                @foreach ($statusAktif as $s)<option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>@endforeach
            </select>
            <button class="btn btn-primary">Cari</button>
            @if (request()->hasAny(['q', 'status']))<a href="{{ route('transaksi.index') }}" class="btn btn-light">Reset</a>@endif
        </form>
        <a href="{{ route('transaksi.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Transaksi Baru</a>
    </div>
    <div class="px-3 pt-3 small text-muted">Menampilkan laundry yang masih berjalan. Transaksi yang sudah diambil ada di menu <a href="{{ route('riwayat.index') }}">Riwayat</a>.</div>
    <div class="table-wrap">
        <table class="table tabel table-hover">
            <thead><tr><th>Kode</th><th>Pelanggan</th><th>Total</th><th>Estimasi Selesai</th><th>Status</th><th>Bayar</th><th></th></tr></thead>
            <tbody>
            @forelse ($transaksi as $t)
                <tr>
                    <td><a class="fw-semibold text-decoration-none" href="{{ route('transaksi.show', $t) }}">{{ $t->kode }}</a>
                        <div class="small text-muted">{{ $t->tanggal_masuk->format('d/m/Y H:i') }}</div></td>
                    <td>{{ $t->pelanggan->nama }}</td>
                    <td>@rupiah($t->total)</td>
                    <td class="text-nowrap {{ $t->estimasi_selesai->isPast() && $t->status_laundry !== \App\Enums\StatusLaundry::SiapDiambil ? 'text-danger fw-semibold' : '' }}">
                        {{ $t->estimasi_selesai->format('d/m/Y H:i') }}</td>
                    <td><x-badge-status :status="$t->status_laundry" /></td>
                    <td><x-badge-status :status="$t->status_pembayaran" /></td>
                    <td class="text-end"><a href="{{ route('transaksi.show', $t) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="7"><x-kosong pesan="Tidak ada laundry yang sedang berjalan." ikon="bi-basket3" /></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($transaksi->hasPages())<div class="p-3">{{ $transaksi->links() }}</div>@endif
</div>
@endsection

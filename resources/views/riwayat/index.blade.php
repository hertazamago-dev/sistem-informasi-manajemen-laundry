@extends('layouts.app')
@section('judul', 'Riwayat Transaksi')

@section('content')
<div class="cardx mb-3">
    <div class="cardx-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label small mb-1">Cari</label>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Kode / nama pelanggan"></div>
            <div class="col-6 col-md-2"><label class="form-label small mb-1">Dari tanggal</label>
                <input type="date" name="dari" value="{{ request('dari') }}" class="form-control @error('dari') is-invalid @enderror"></div>
            <div class="col-6 col-md-2"><label class="form-label small mb-1">Sampai tanggal</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control @error('sampai') is-invalid @enderror">
                @error('sampai')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-6 col-md-2"><label class="form-label small mb-1">Status laundry</label>
                <select name="status_laundry" class="form-select"><option value="">Semua</option>
                    @foreach ($daftarStatus as $s)<option value="{{ $s->value }}" @selected(request('status_laundry') === $s->value)>{{ $s->label() }}</option>@endforeach</select></div>
            <div class="col-6 col-md-2"><label class="form-label small mb-1">Pembayaran</label>
                <select name="status_pembayaran" class="form-select"><option value="">Semua</option>
                    @foreach ($daftarBayar as $s)<option value="{{ $s->value }}" @selected(request('status_pembayaran') === $s->value)>{{ $s->label() }}</option>@endforeach</select></div>
            <div class="col-md-1 d-grid"><button class="btn btn-primary"><i class="bi bi-funnel"></i></button></div>
        </form>
        @if (request()->hasAny(['q', 'dari', 'sampai', 'status_laundry', 'status_pembayaran']))
            <a href="{{ route('riwayat.index') }}" class="small">Reset filter</a>
        @endif
    </div>
</div>

<div class="cardx">
    <div class="cardx-head"><h2><i class="bi bi-clock-history text-primary"></i> Semua Transaksi</h2><span class="small text-muted">{{ $transaksi->total() }} data</span></div>
    <div class="table-wrap">
        <table class="table tabel table-hover">
            <thead><tr><th>Kode</th><th>Tanggal</th><th>Pelanggan</th><th>Petugas</th><th>Total</th><th>Status</th><th>Pembayaran</th><th></th></tr></thead>
            <tbody>
            @forelse ($transaksi as $t)
                <tr>
                    <td><a class="fw-semibold text-decoration-none" href="{{ route('transaksi.show', $t) }}">{{ $t->kode }}</a></td>
                    <td class="text-nowrap">{{ $t->tanggal_masuk->format('d/m/Y H:i') }}</td>
                    <td>{{ $t->pelanggan->nama }}</td>
                    <td>{{ $t->petugas->name }}</td>
                    <td>@rupiah($t->total)</td>
                    <td><x-badge-status :status="$t->status_laundry" /></td>
                    <td><x-badge-status :status="$t->status_pembayaran" /></td>
                    <td class="text-end"><a href="{{ route('transaksi.show', $t) }}" class="btn btn-sm btn-light" title="Detail & riwayat status"><i class="bi bi-eye"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="8"><x-kosong pesan="Tidak ada transaksi yang cocok." ikon="bi-clock-history" /></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($transaksi->hasPages())<div class="p-3">{{ $transaksi->links() }}</div>@endif
</div>
@endsection

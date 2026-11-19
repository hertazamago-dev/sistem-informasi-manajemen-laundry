@extends('layouts.app')
@section('judul', 'Detail Pelanggan')

@section('content')
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="cardx">
                <div class="cardx-body text-center">
                    <span class="avatar mx-auto mb-2"
                        style="width:70px;height:70px;font-size:1.8rem">{{ strtoupper(mb_substr($pelanggan->nama, 0, 1)) }}</span>
                    <h5 class="fw-bold mb-0">{{ $pelanggan->nama }}</h5>
                    <div class="text-muted small mb-3">{{ $pelanggan->kode }}</div>
                    <ul class="list-unstyled text-start small mb-3">
                        <li class="mb-2"><i class="bi bi-telephone text-primary"></i> {{ $pelanggan->telepon_tampil }}</li>
                        <li class="mb-2"><i class="bi bi-geo-alt text-primary"></i> {{ $pelanggan->alamat ?: '-' }}</li>
                        <li class="mb-2"><i class="bi bi-calendar-plus text-primary"></i> Terdaftar
                            {{ $pelanggan->created_at->format('d/m/Y') }}
                        </li>
                        <li><i class="bi bi-wallet2 text-primary"></i> Total belanja <b>@rupiah($totalBelanja)</b></li>
                    </ul>
                    <div class="d-grid gap-2">
                        @unless (auth()->user()->isAdmin())
                            <a href="{{ route('transaksi.create', ['pelanggan' => $pelanggan->id]) }}"
                                class="btn btn-primary"><i class="bi bi-plus-circle"></i> Transaksi Baru</a>
                        @endunless
                        <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="btn btn-outline-primary"><i
                                class="bi bi-pencil"></i> Edit</a>
                        <a href="{{ route('pelanggan.index') }}" class="btn btn-light">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="cardx">
                <div class="cardx-head">
                    <h2><i class="bi bi-receipt text-primary"></i> Riwayat Transaksi Pelanggan</h2>
                </div>
                <div class="table-wrap">
                    <table class="table tabel table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transaksi as $t)
                                <tr>
                                    <td><a class="fw-semibold text-decoration-none"
                                            href="{{ route('transaksi.show', $t) }}">{{ $t->kode }}</a></td>
                                    <td>{{ $t->tanggal_masuk->format('d/m/Y') }}</td>
                                    <td>@rupiah($t->total)</td>
                                    <td><x-badge-status :status="$t->status_laundry" /></td>
                                    <td><x-badge-status :status="$t->status_pembayaran" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"><x-kosong pesan="Pelanggan ini belum pernah bertransaksi."
                                            ikon="bi-receipt" /></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($transaksi->hasPages())
                <div class="p-3">{{ $transaksi->links() }}</div>@endif
            </div>
        </div>
    </div>
@endsection
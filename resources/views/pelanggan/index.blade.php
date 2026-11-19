@extends('layouts.app')
@section('judul', 'Data Pelanggan')

@section('content')
<div class="cardx">
    <div class="cardx-head">
        <form method="GET" class="d-flex gap-2 flex-grow-1" style="max-width:420px">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama, kode, atau telepon...">
            </div>
            <button class="btn btn-primary">Cari</button>
            @if (request('q'))<a href="{{ route('pelanggan.index') }}" class="btn btn-light">Reset</a>@endif
        </form>
        <a href="{{ route('pelanggan.create') }}" class="btn btn-primary"><i class="bi bi-person-plus"></i> Tambah Pelanggan</a>
    </div>
    <div class="table-wrap">
        <table class="table tabel table-hover">
            <thead><tr><th>ID</th><th>Nama</th><th>Telepon</th><th>Alamat</th><th class="text-center">Transaksi</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse ($pelanggan as $p)
                <tr>
                    <td class="text-muted">{{ $p->kode }}</td>
                    <td class="fw-semibold">{{ $p->nama }}</td>
                    <td>{{ $p->telepon_tampil }}</td>
                    <td class="text-muted">{{ \Illuminate\Support\Str::limit($p->alamat, 40) ?: '-' }}</td>
                    <td class="text-center"><span class="bdg bdg-abu">{{ $p->transaksi_count }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('pelanggan.show', $p) }}" class="btn btn-sm btn-light" title="Detail"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('pelanggan.edit', $p) }}" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                        @if (auth()->user()->isAdmin())
                            <button type="button" class="btn btn-sm btn-light text-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#modalHapus"
                                    data-action="{{ route('pelanggan.destroy', $p) }}" data-nama="{{ $p->nama }}"><i class="bi bi-trash3"></i></button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><x-kosong pesan="Belum ada pelanggan{{ request('q') ? ' yang cocok dengan pencarian' : '' }}." ikon="bi-people" /></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($pelanggan->hasPages())<div class="p-3">{{ $pelanggan->links() }}</div>@endif
</div>
@endsection

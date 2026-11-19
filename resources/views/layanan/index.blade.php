@extends('layouts.app')
@section('judul', 'Layanan Laundry')

@section('content')
@php
    $admin = auth()->user()->isAdmin();
    $jumlahKolom = $admin ? 5 : 4;
@endphp
<div class="cardx">
    <div class="cardx-head">
        <h2><i class="bi bi-basket3 text-primary"></i> Daftar Harga Layanan @unless($admin)@endunless</h2>
        @if ($admin)<a href="{{ route('layanan.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Layanan</a>@endif
    </div>
    <div class="table-wrap">
        <table class="table tabel table-hover">
            <thead><tr><th>Layanan</th><th>Harga</th><th>Estimasi</th><th>Tahapan</th>@if($admin)<th class="text-end">Aksi</th>@endif</tr></thead>
            <tbody>
            @forelse ($layanan->groupBy(fn ($l) => $l->kategori ?: 'Lainnya') as $kategori => $daftar)
                <tr><td colspan="{{ $jumlahKolom }}" class="fw-bold text-primary" style="background:#F0F9FF">{{ $kategori }}</td></tr>
                @foreach ($daftar as $l)
                    <tr>
                        <td><div class="fw-semibold">{{ $l->nama }}</div><div class="small text-muted">{{ $l->deskripsi }}</div></td>
                        <td class="text-nowrap fw-semibold">@rupiah($l->harga) <span class="text-muted fw-normal">/ {{ $l->satuan }}</span>
                            @if ($l->minimum > 0)<div class="small text-muted fw-normal">Minimum {{ \App\Support\Format::jumlah($l->minimum) }} {{ $l->satuan }}</div>@endif
                        </td>
                        <td class="text-nowrap"><i class="bi bi-clock text-muted"></i> {{ $l->estimasi_teks }}</td>
                        <td>
                            @foreach (array_slice($l->alur(), 1, -2) as $s)<span class="bdg bdg-oranye me-1 mb-1">{{ $s->label() }}</span>@endforeach
                        </td>
                        @if ($admin)
                            <td class="text-end text-nowrap">
                                <a href="{{ route('layanan.edit', $l) }}" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-sm btn-light text-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#modalHapus"
                                        data-action="{{ route('layanan.destroy', $l) }}" data-nama="{{ $l->nama }}"><i class="bi bi-trash3"></i></button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="{{ $jumlahKolom }}"><x-kosong pesan="Belum ada layanan." ikon="bi-basket3" /></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

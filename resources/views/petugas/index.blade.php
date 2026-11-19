@extends('layouts.app')
@section('judul', 'Akun Petugas')

@section('content')
<div class="cardx">
    <div class="cardx-head">
        <h2><i class="bi bi-person-gear text-primary"></i> Daftar Petugas Laundry</h2>
        <a href="{{ route('petugas.create') }}" class="btn btn-primary"><i class="bi bi-person-plus"></i> Tambah Petugas</a>
    </div>
    <div class="table-wrap">
        <table class="table tabel table-hover">
            <thead><tr><th>Nama</th><th>Email</th><th>Telepon</th><th class="text-center">Transaksi</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse ($petugas as $p)
                <tr>
                    <td class="fw-semibold">{{ $p->name }}</td>
                    <td>{{ $p->email }}</td>
                    <td>{{ $p->no_telepon ?: '-' }}</td>
                    <td class="text-center">{{ $p->transaksi_count }}</td>
                    <td><span class="bdg {{ $p->is_active ? 'bdg-hijau' : 'bdg-merah' }}">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('petugas.edit', $p) }}" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('petugas.toggle', $p) }}" class="d-inline">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-light {{ $p->is_active ? 'text-danger' : 'text-success' }}" title="{{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <i class="bi {{ $p->is_active ? 'bi-person-slash' : 'bi-person-check' }}"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><x-kosong pesan="Belum ada akun petugas." ikon="bi-person-gear" /></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 small text-muted"><i class="bi bi-info-circle"></i> Petugas yang berhenti cukup dinonaktifkan agar riwayat transaksinya tetap utuh.</div>
</div>
@endsection

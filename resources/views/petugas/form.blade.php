@extends('layouts.app')
@php $edit = $petugas->exists; @endphp
@section('judul', $edit ? 'Edit Akun Petugas' : 'Tambah Akun Petugas')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="cardx">
            <div class="cardx-head"><h2><i class="bi bi-person-badge text-primary"></i> {{ $edit ? 'Ubah ' . $petugas->name : 'Akun Petugas Baru' }}</h2></div>
            <div class="cardx-body">
                <form method="POST" data-sekali novalidate action="{{ $edit ? route('petugas.update', $petugas) : route('petugas.store') }}">
                    @csrf
                    @if ($edit) @method('PUT') @endif
                    <div class="mb-3">
                        <label class="form-label" for="name">Nama lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $petugas->name) }}" class="form-control @error('name') is-invalid @enderror" autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label" for="email">Email (untuk login)</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $petugas->email) }}" class="form-control @error('email') is-invalid @enderror">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" for="no_telepon">Telepon</label>
                            <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $petugas->no_telepon) }}" class="form-control @error('no_telepon') is-invalid @enderror">
                            @error('no_telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="password">Kata sandi {{ $edit ? '(kosongkan jika tidak diganti)' : '' }}</label>
                            <input type="password" id="password" name="password" autocomplete="new-password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Minimal 8 karakter.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="password_confirmation">Ulangi kata sandi</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="form-control">
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('petugas.index') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

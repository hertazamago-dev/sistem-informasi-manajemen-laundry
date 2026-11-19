@extends('layouts.auth')
@section('judul', 'Masuk')

@section('content')
    <div class="login-page">
        <div class="login-art">
            <x-ilustrasi />
            <h2 class="fw-bold mt-4 text-center">Laundry rapi, usaha tertata</h2>
            <p class="opacity-75 text-center mb-0" style="max-width:380px">Kelola pelanggan, transaksi, proses cuci,
                pembayaran, hingga laporan dalam satu sistem.</p>
        </div>

        <div class="login-form">
            <div class="login-box">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <span class="brand-logo text-white" style="background:var(--primary)"><i
                            class="bi bi-droplet-half"></i></span>
                    <div class="lh-1"><b class="fs-5">{{ config('laundry.nama') }}</b><br><small class="text-muted">Sistem
                            Informasi Manajemen Laundry</small></div>
                </div>

                <h3 class="fw-bold mb-1">Selamat datang 👋</h3>
                <p class="text-muted mb-4">Masuk dengan akun Administrator atau Petugas.</p>

                <form method="POST" action="{{ route('login.proses') }}" data-sekali novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus
                                autocomplete="username" class="form-control @error('email') is-invalid @enderror"
                                placeholder="nama@email.com">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Kata sandi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                            <input type="password" id="password" name="password" autocomplete="current-password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Kata sandi">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold"><i
                            class="bi bi-box-arrow-in-right"></i> Masuk</button>
                </form>
            </div>
        </div>
    </div>
@endsection
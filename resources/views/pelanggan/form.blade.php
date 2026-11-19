@extends('layouts.app')
@php $edit = $pelanggan->exists; @endphp
@section('judul', $edit ? 'Edit Pelanggan' : 'Tambah Pelanggan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="cardx">
            <div class="cardx-head"><h2><i class="bi bi-person-vcard text-primary"></i> {{ $edit ? 'Ubah data ' . $pelanggan->nama : 'Data Pelanggan Baru' }}</h2></div>
            <div class="cardx-body">
                {{-- Peringatan nomor ganda: nomor boleh sama, tetapi petugas harus memastikan dulu --}}
                @if (session('duplikat'))
                    <div class="alert alert-warning">
                        <b><i class="bi bi-exclamation-triangle"></i> Nomor ini sudah terdaftar atas nama {{ session('duplikat') }}.</b><br>
                        Jika memang berbeda orang (mis. satu keluarga), klik <b>Tetap Simpan</b>.
                    </div>
                @endif

                <form method="POST" data-sekali novalidate
                      action="{{ $edit ? route('pelanggan.update', $pelanggan) : route('pelanggan.store') }}">
                    @csrf
                    @if ($edit) @method('PUT') @endif
                    @if (session('duplikat'))<input type="hidden" name="lanjut_duplikat" value="1">@endif
                    @if (request('dari_transaksi') || old('dari_transaksi'))<input type="hidden" name="dari_transaksi" value="1">@endif

                    <div class="mb-3">
                        <label class="form-label" for="nama">Nama pelanggan</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $pelanggan->nama) }}" class="form-control @error('nama') is-invalid @enderror" autofocus>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="telepon">Nomor telepon / WhatsApp</label>
                        <input type="tel" id="telepon" name="telepon" value="{{ old('telepon', $pelanggan->telepon ? $pelanggan->telepon_tampil : '') }}"
                               class="form-control @error('telepon') is-invalid @enderror" placeholder="081234567890">
                        @error('telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Dipakai untuk mengirim info status laundry lewat WhatsApp.</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $pelanggan->alamat) }}</textarea>
                        @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ $edit ? route('pelanggan.show', $pelanggan) : route('pelanggan.index') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i> {{ session('duplikat') ? 'Tetap Simpan' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

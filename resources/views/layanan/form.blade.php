@extends('layouts.app')
@php $edit = $layanan->exists; @endphp
@section('judul', $edit ? 'Edit Layanan' : 'Tambah Layanan')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="cardx">
                <div class="cardx-head">
                    <h2><i class="bi bi-basket3 text-primary"></i> {{ $edit ? 'Ubah ' . $layanan->nama : 'Layanan Baru' }}
                    </h2>
                </div>
                <div class="cardx-body">
                    <form method="POST" data-sekali novalidate
                        action="{{ $edit ? route('layanan.update', $layanan) : route('layanan.store') }}">
                        @csrf
                        @if ($edit) @method('PUT') @endif
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label" for="nama">Nama layanan</label>
                                <input type="text" id="nama" name="nama" value="{{ old('nama', $layanan->nama) }}"
                                    class="form-control @error('nama') is-invalid @enderror" autofocus>
                                @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-5">
                                <label class="form-label" for="kategori">Kategori</label>
                                <input type="text" id="kategori" name="kategori" list="daftarKategori"
                                    value="{{ old('kategori', $layanan->kategori) }}"
                                    class="form-control @error('kategori') is-invalid @enderror"
                                    placeholder="Contoh: Selimut">
                                <datalist id="daftarKategori">@foreach ($kategoriList as $k)
                                <option value="{{ $k }}">@endforeach
                                </datalist>
                                @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="harga">Harga (Rp)</label>
                                <input type="number" id="harga" name="harga" min="1"
                                    value="{{ old('harga', $layanan->harga) }}"
                                    class="form-control @error('harga') is-invalid @enderror">
                                @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="satuan">Satuan</label>
                                <select id="satuan" name="satuan" class="form-select @error('satuan') is-invalid @enderror">
                                    @foreach (['kg' => 'Per kilogram (kg)', 'pcs' => 'Per satuan (pcs)', 'm' => 'Per meter (m)'] as $v => $t)
                                        <option value="{{ $v }}" @selected(old('satuan', $layanan->satuan) === $v)>{{ $t }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('satuan')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="minimum">Minimum hitung</label>
                                <input type="number" step="0.1" min="0" id="minimum" name="minimum"
                                    value="{{ old('minimum', \App\Support\Format::jumlah($layanan->minimum ?? 0)) }}"
                                    class="form-control @error('minimum') is-invalid @enderror">
                                @error('minimum')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Kiloan: 3. Isi 0 jika tanpa minimum.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="estimasi_jam">Estimasi (jam)</label>
                                <input type="number" id="estimasi_jam" name="estimasi_jam" min="1"
                                    value="{{ old('estimasi_jam', $layanan->estimasi_jam) }}"
                                    class="form-control @error('estimasi_jam') is-invalid @enderror">
                                @error('estimasi_jam')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">24 jam = 1 hari.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Tahapan pengerjaan</label>
                                <div class="d-flex flex-wrap gap-3 @error('ada_cuci') is-invalid @enderror">
                                    @foreach (['ada_cuci' => 'Dicuci', 'ada_pengeringan' => 'Dikeringkan', 'ada_setrika' => 'Disetrika'] as $f => $t)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="{{ $f }}" id="{{ $f }}"
                                                value="1" @checked(old($f, $layanan->$f))>
                                            <label class="form-check-label" for="{{ $f }}">{{ $t }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('ada_cuci')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Menentukan alur status laundry. Contoh Setrika Saja: hanya
                                    "Disetrika".</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="deskripsi">Deskripsi</label>
                                <textarea id="deskripsi" name="deskripsi" rows="2"
                                    class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $layanan->deskripsi) }}</textarea>
                                @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('layanan.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i>
                                Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
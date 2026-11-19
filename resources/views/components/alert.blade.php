{{-- Notifikasi toast: sukses (hijau), gagal (merah), peringatan (oranye), dan galat validasi --}}
@php
    $pesan = [];
    if (session('success')) $pesan[] = ['hijau', 'bi-check-circle-fill', 'Berhasil', session('success')];
    if (session('error')) $pesan[] = ['merah', 'bi-x-circle-fill', 'Gagal', session('error')];
    if (session('warning')) $pesan[] = ['oranye', 'bi-exclamation-triangle-fill', 'Perhatian', session('warning')];
    if ($errors->any() && ! $errors->has('email') ) $pesan[] = ['merah', 'bi-x-circle-fill', 'Periksa kembali isian Anda', 'Ada ' . $errors->count() . ' kolom yang belum sesuai.'];
    $warna = ['hijau' => 'success', 'merah' => 'danger', 'oranye' => 'warning'];
@endphp
@if ($pesan)
    <div class="toast-wrap">
        @foreach ($pesan as [$w, $ikon, $judul, $isi])
            <div class="toast align-items-start border-0 shadow" role="alert" data-bs-autohide="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <div class="fw-semibold text-{{ $warna[$w] }}"><i class="bi {{ $ikon }}"></i> {{ $judul }}</div>
                        <div class="small">{{ $isi }}</div>
                    </div>
                    <button type="button" class="btn-close m-2" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
            </div>
        @endforeach
    </div>
@endif

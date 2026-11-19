@extends('layouts.app')
@section('judul', 'Laporan')

@section('content')
@php
    $parameter = ['jenis' => $jenis, 'dari' => $dari, 'sampai' => $sampai];
    $nilai = fn ($v) => is_array($v) ? \App\Support\Format::rupiah($v[1]) : number_format($v, 0, ',', '.');
@endphp

<div class="cardx mb-3">
    <div class="cardx-body">
        <form method="GET" id="formLaporan" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Jenis laporan</label>
                <select name="jenis" id="jenis" class="form-select">
                    @foreach ($daftarJenis as $k => $label)<option value="{{ $k }}" @selected($jenis === $k)>{{ $label }}</option>@endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small mb-1" id="labelDari">Tanggal</label>
                <input type="date" name="dari" id="dari" value="{{ $dari }}" class="form-control @error('dari') is-invalid @enderror">
            </div>
            <div class="col-6 col-md-3" id="kolomSampai">
                <label class="form-label small mb-1">Sampai tanggal</label>
                <input type="date" name="sampai" id="sampai" value="{{ $sampai }}" class="form-control @error('sampai') is-invalid @enderror">
                @error('sampai')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2 d-grid"><button class="btn btn-primary"><i class="bi bi-funnel"></i> Tampilkan</button></div>
        </form>
        <div class="small text-muted mt-2" id="petunjuk"></div>
    </div>
</div>

<div class="cardx mb-3">
    <div class="cardx-head">
        <div>
            <h2>{{ $laporan['judul'] }}</h2>
            <div class="small text-muted">Periode: {{ $laporan['periode'] }}</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('laporan.export', ['format' => 'cetak'] + $parameter) }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bi bi-printer"></i> Cetak</a>
            <a href="{{ route('laporan.export', ['format' => 'pdf'] + $parameter) }}" class="btn btn-danger btn-sm"><i class="bi bi-file-earmark-pdf"></i> Export PDF</a>
            <a href="{{ route('laporan.export', ['format' => 'excel'] + $parameter) }}" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
        </div>
    </div>

    <div class="cardx-body border-bottom">
        <div class="row g-3">
            @foreach ($laporan['ringkasan'] as $label => $v)
                <div class="col-6 col-lg-3">
                    <div class="p-3 rounded-3" style="background:#F0F9FF">
                        <div class="small text-muted">{{ $label }}</div>
                        <div class="fs-5 fw-bold text-primary">{{ $nilai($v) }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="table-wrap">
        <table class="table tabel table-hover">
            <thead><tr>@foreach ($laporan['kolom'] as $i => $k)<th class="{{ $laporan['tipe'][$i] !== 'teks' ? 'text-end' : '' }}">{{ $k }}</th>@endforeach</tr></thead>
            <tbody>
            @forelse ($laporan['baris'] as $baris)
                <tr>
                    @foreach ($baris as $i => $sel)
                        <td class="{{ $laporan['tipe'][$i] !== 'teks' ? 'text-end' : '' }}">
                            {{ $laporan['tipe'][$i] === 'rupiah' ? \App\Support\Format::rupiah($sel) : $sel }}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($laporan['kolom']) }}"><x-kosong pesan="Tidak ada data pada periode ini." ikon="bi-bar-chart" /></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Harian/mingguan/bulanan memakai satu tanggal acuan; jenis lain memakai rentang tanggal.
    const jenis = document.getElementById('jenis');
    const petunjuk = {
        harian: 'Menampilkan transaksi pada tanggal yang dipilih.',
        mingguan: 'Menampilkan transaksi pada minggu (Senin-Minggu) yang memuat tanggal yang dipilih.',
        bulanan: 'Menampilkan transaksi pada bulan yang memuat tanggal yang dipilih.',
        pendapatan: 'Pendapatan dihitung dari pembayaran yang diterima pada rentang tanggal.',
        status: 'Jumlah laundry per status untuk transaksi yang masuk pada rentang tanggal.',
        pelanggan: 'Pelanggan yang bertransaksi pada rentang tanggal.',
    };
    function atur() {
        const rentang = ['pendapatan', 'status', 'pelanggan'].includes(jenis.value);
        document.getElementById('kolomSampai').style.display = rentang ? '' : 'none';
        document.getElementById('sampai').disabled = !rentang;
        document.getElementById('labelDari').textContent = rentang ? 'Dari tanggal' : 'Tanggal acuan';
        document.getElementById('petunjuk').textContent = petunjuk[jenis.value] || '';
    }
    jenis.addEventListener('change', atur);
    atur();
</script>
@endpush

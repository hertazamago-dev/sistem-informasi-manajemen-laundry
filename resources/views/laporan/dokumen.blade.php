<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $laporan['judul'] }} - {{ config('laundry.nama') }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #0F172A; margin: 18px; }
        .kop { border-bottom: 3px solid #0369A1; padding-bottom: 8px; margin-bottom: 12px; }
        .kop h1 { margin: 0; font-size: 17px; color: #0369A1; }
        .kop div { color: #475569; }
        h2 { font-size: 13px; margin: 10px 0 2px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0369A1; color: #fff; text-align: left; padding: 5px 6px; font-size: 9px; }
        td { padding: 4px 6px; border-bottom: 1px solid #E2E8F0; }
        tr:nth-child(even) td { background: #F8FAFC; }
        .kanan { text-align: right; }
        .ringkasan { margin-top: 14px; width: 55%; }
        .ringkasan td { border: 1px solid #CBD5E1; background: #F0F9FF !important; }
        .ringkasan td:last-child { font-weight: bold; text-align: right; }
        .ttd { margin-top: 28px; width: 100%; }
        .ttd td { border: 0; background: none !important; text-align: center; }
        .tombol { text-align: center; margin-bottom: 12px; }
        @media print { .tombol { display: none; } }
    </style>
</head>
<body>
@if ($mode === 'cetak')
    <div class="tombol">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>
@endif

<div class="kop">
    <h1>{{ config('laundry.nama') }}</h1>
    <div>{{ config('laundry.alamat') }} &middot; Telp. {{ config('laundry.telepon') }}</div>
</div>

<h2>{{ $laporan['judul'] }}</h2>
<div>Periode: {{ $laporan['periode'] }}</div>
<br>

<table>
    <thead><tr>@foreach ($laporan['kolom'] as $i => $k)<th class="{{ $laporan['tipe'][$i] !== 'teks' ? 'kanan' : '' }}">{{ $k }}</th>@endforeach</tr></thead>
    <tbody>
    @forelse ($laporan['baris'] as $baris)
        <tr>
            @foreach ($baris as $i => $sel)
                <td class="{{ $laporan['tipe'][$i] !== 'teks' ? 'kanan' : '' }}">{{ $laporan['tipe'][$i] === 'rupiah' ? \App\Support\Format::rupiah($sel) : $sel }}</td>
            @endforeach
        </tr>
    @empty
        <tr><td colspan="{{ count($laporan['kolom']) }}" style="text-align:center">Tidak ada data pada periode ini.</td></tr>
    @endforelse
    </tbody>
</table>

<table class="ringkasan">
    @foreach ($laporan['ringkasan'] as $label => $v)
        <tr><td>{{ $label }}</td><td>{{ is_array($v) ? \App\Support\Format::rupiah($v[1]) : number_format($v, 0, ',', '.') }}</td></tr>
    @endforeach
</table>

<table class="ttd">
    <tr>
        <td style="width:60%"></td>
        <td>Dicetak {{ now()->translatedFormat('d F Y H:i') }}<br>Oleh: {{ auth()->user()->name }}<br><br><br><br>( ........................ )</td>
    </tr>
</table>

@if ($mode === 'cetak')<script>window.addEventListener('load', () => setTimeout(() => window.print(), 400));</script>@endif
</body>
</html>

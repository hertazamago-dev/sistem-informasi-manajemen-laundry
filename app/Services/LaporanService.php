<?php

namespace App\Services;

use App\Enums\StatusLaundry;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Carbon\Carbon;

/**
 * Menyusun data laporan dalam format seragam, sehingga tampilan layar,
 * cetak, PDF, dan Excel memakai sumber data yang sama.
 *
 * Tipe kolom: teks | angka | rupiah
 */
class LaporanService
{
    public const JENIS = [
        'harian' => 'Laporan Transaksi Harian',
        'mingguan' => 'Laporan Transaksi Mingguan',
        'bulanan' => 'Laporan Transaksi Bulanan',
        'pendapatan' => 'Laporan Pendapatan',
        'status' => 'Laporan Laundry Berdasarkan Status',
        'pelanggan' => 'Laporan Pelanggan',
    ];

    /** @return array{0: Carbon, 1: Carbon} awal & akhir periode */
    public function periode(string $jenis, ?string $dari, ?string $sampai): array
    {
        $acuan = $dari ? Carbon::parse($dari) : now();

        return match ($jenis) {
            'harian' => [$acuan->copy()->startOfDay(), $acuan->copy()->endOfDay()],
            'mingguan' => [$acuan->copy()->startOfWeek(), $acuan->copy()->endOfWeek()],
            'bulanan' => [$acuan->copy()->startOfMonth(), $acuan->copy()->endOfMonth()],
            default => [
                $acuan->copy()->startOfDay(),
                ($sampai ? Carbon::parse($sampai) : now())->copy()->endOfDay(),
            ],
        };
    }

    public function susun(string $jenis, Carbon $awal, Carbon $akhir): array
    {
        $data = match ($jenis) {
            'pendapatan' => $this->pendapatan($awal, $akhir),
            'status' => $this->perStatus($awal, $akhir),
            'pelanggan' => $this->pelanggan($awal, $akhir),
            default => $this->transaksi($awal, $akhir),
        };

        $periode = $awal->isSameDay($akhir)
            ? $awal->translatedFormat('d F Y')
            : $awal->translatedFormat('d F Y') . ' s.d. ' . $akhir->translatedFormat('d F Y');

        return ['jenis' => $jenis, 'judul' => self::JENIS[$jenis] ?? 'Laporan', 'periode' => $periode] + $data;
    }

    private function transaksi(Carbon $awal, Carbon $akhir): array
    {
        $daftar = Transaksi::with(['pelanggan', 'petugas', 'details'])
            ->whereBetween('tanggal_masuk', [$awal, $akhir])
            ->orderBy('tanggal_masuk')
            ->get();

        $baris = $daftar->map(fn (Transaksi $t, $i) => [
            $i + 1,
            $t->kode,
            $t->tanggal_masuk->format('d/m/Y H:i'),
            $t->pelanggan->nama,
            $t->petugas->name,
            $t->details->pluck('nama_layanan')->implode(', '),
            (int) $t->total,
            (int) $t->total_dibayar,
            $t->status_laundry->label(),
            $t->status_pembayaran->label(),
        ])->all();

        return [
            'kolom' => ['No', 'Kode', 'Tanggal Masuk', 'Pelanggan', 'Petugas', 'Layanan', 'Total', 'Dibayar', 'Status Laundry', 'Pembayaran'],
            'tipe' => ['angka', 'teks', 'teks', 'teks', 'teks', 'teks', 'rupiah', 'rupiah', 'teks', 'teks'],
            'baris' => $baris,
            'ringkasan' => [
                'Total Transaksi' => $daftar->count(),
                'Total Tagihan' => ['rupiah', $daftar->sum('total')],
                'Total Terbayar' => ['rupiah', $daftar->sum('total_dibayar')],
                'Belum Terbayar' => ['rupiah', $daftar->sum('total') - $daftar->sum('total_dibayar')],
            ],
        ];
    }

    private function pendapatan(Carbon $awal, Carbon $akhir): array
    {
        $perHari = Pembayaran::whereBetween('tanggal_bayar', [$awal, $akhir])
            ->orderBy('tanggal_bayar')
            ->get()
            ->groupBy(fn ($p) => $p->tanggal_bayar->format('Y-m-d'));

        $baris = [];
        $no = 1;
        foreach ($perHari as $tanggal => $kumpulan) {
            $baris[] = [$no++, Carbon::parse($tanggal)->format('d/m/Y'), $kumpulan->count(), (int) $kumpulan->sum('jumlah_bayar')];
        }

        $total = $perHari->flatten()->sum('jumlah_bayar');
        $hari = max(1, $perHari->count());

        return [
            'kolom' => ['No', 'Tanggal', 'Jumlah Pembayaran', 'Pendapatan'],
            'tipe' => ['angka', 'teks', 'angka', 'rupiah'],
            'baris' => $baris,
            'ringkasan' => [
                'Total Pendapatan' => ['rupiah', $total],
                'Jumlah Pembayaran' => $perHari->flatten()->count(),
                'Rata-rata per Hari Aktif' => ['rupiah', (int) round($total / $hari)],
            ],
        ];
    }

    private function perStatus(Carbon $awal, Carbon $akhir): array
    {
        $kelompok = Transaksi::whereBetween('tanggal_masuk', [$awal, $akhir])->get()->groupBy(fn ($t) => $t->status_laundry->value);

        $baris = [];
        foreach (StatusLaundry::cases() as $i => $status) {
            $isi = $kelompok->get($status->value, collect());
            $baris[] = [$i + 1, $status->label(), $isi->count(), (int) $isi->sum('total')];
        }

        return [
            'kolom' => ['No', 'Status Laundry', 'Jumlah Transaksi', 'Nilai Transaksi'],
            'tipe' => ['angka', 'teks', 'angka', 'rupiah'],
            'baris' => $baris,
            'ringkasan' => ['Total Transaksi' => $kelompok->flatten()->count()],
        ];
    }

    private function pelanggan(Carbon $awal, Carbon $akhir): array
    {
        $batas = fn ($q) => $q->whereBetween('tanggal_masuk', [$awal, $akhir]);

        $daftar = Pelanggan::withCount(['transaksi as jumlah_transaksi' => $batas])
            ->withSum(['transaksi as total_belanja' => $batas], 'total')
            ->having('jumlah_transaksi', '>', 0)
            ->orderByDesc('total_belanja')
            ->get();

        $baris = $daftar->map(fn ($p, $i) => [
            $i + 1, $p->kode, $p->nama, $p->telepon_tampil, (int) $p->jumlah_transaksi, (int) $p->total_belanja,
        ])->all();

        return [
            'kolom' => ['No', 'Kode', 'Nama', 'Telepon', 'Jumlah Transaksi', 'Total Belanja'],
            'tipe' => ['angka', 'teks', 'teks', 'teks', 'angka', 'rupiah'],
            'baris' => $baris,
            'ringkasan' => [
                'Pelanggan Bertransaksi' => $daftar->count(),
                'Total Pelanggan Terdaftar' => Pelanggan::count(),
                'Total Belanja' => ['rupiah', $daftar->sum('total_belanja')],
            ],
        ];
    }
}

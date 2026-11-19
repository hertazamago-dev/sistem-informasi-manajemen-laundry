<?php

namespace App\Services;

use App\Enums\StatusPembayaran;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PembayaranService
{
    /**
     * Mencatat pembayaran (boleh dicicil) dan memperbarui total_dibayar + status pembayaran.
     * Pembayaran tidak boleh melebihi sisa tagihan.
     */
    public function catat(Transaksi $transaksi, int $jumlah, User $petugas, ?string $catatan = null, ?Carbon $waktu = null): Pembayaran
    {
        return DB::transaction(function () use ($transaksi, $jumlah, $petugas, $catatan, $waktu) {
            // Kunci baris transaksi agar dua pembayaran bersamaan tidak saling menimpa
            $transaksi = Transaksi::lockForUpdate()->findOrFail($transaksi->id);
            $sisa = $transaksi->total - $transaksi->total_dibayar;

            if ($sisa <= 0) {
                throw new RuntimeException('Transaksi ini sudah lunas.');
            }
            if ($jumlah <= 0) {
                throw new RuntimeException('Jumlah bayar harus lebih dari nol.');
            }
            if ($jumlah > $sisa) {
                throw new RuntimeException('Jumlah bayar melebihi sisa tagihan (Rp ' . number_format($sisa, 0, ',', '.') . ').');
            }

            $waktu = $waktu ?? now();

            $pembayaran = Pembayaran::create([
                'kode' => $this->kodeBaru($waktu),
                'transaksi_id' => $transaksi->id,
                'user_id' => $petugas->id,
                'jumlah_bayar' => $jumlah,
                'metode' => 'tunai',
                'tanggal_bayar' => $waktu,
                'catatan' => $catatan,
            ]);

            $dibayar = $transaksi->total_dibayar + $jumlah;
            $transaksi->update([
                'total_dibayar' => $dibayar,
                'status_pembayaran' => StatusPembayaran::hitung($transaksi->total, $dibayar),
            ]);

            return $pembayaran;
        });
    }

    private function kodeBaru(Carbon $waktu): string
    {
        $awalan = 'BYR-' . $waktu->format('Ymd') . '-';
        $terakhir = Pembayaran::where('kode', 'like', $awalan . '%')->lockForUpdate()->orderByDesc('kode')->value('kode');
        $urutan = $terakhir ? ((int) substr($terakhir, -4)) + 1 : 1;

        return $awalan . str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
    }
}

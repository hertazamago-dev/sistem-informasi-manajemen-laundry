<?php

namespace App\Services;

use App\Enums\StatusLaundry;
use App\Enums\StatusPembayaran;
use App\Models\RiwayatStatus;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/** Mengatur alur status laundry yang berbeda untuk tiap jenis layanan. */
class StatusService
{
    /**
     * Alur status transaksi = gabungan tahapan semua layanan di dalamnya, tetap berurutan.
     *
     * @return array<int, StatusLaundry>
     */
    public function alur(Transaksi $transaksi): array
    {
        $transaksi->loadMissing('details.layanan');
        $cuci = $kering = $setrika = false;

        foreach ($transaksi->details as $detail) {
            $layanan = $detail->layanan;
            if (! $layanan) {
                continue;
            }
            $cuci = $cuci || $layanan->ada_cuci;
            $kering = $kering || $layanan->ada_pengeringan;
            $setrika = $setrika || $layanan->ada_setrika;
        }

        return StatusLaundry::susun($cuci, $kering, $setrika);
    }

    /** Status berikutnya dalam alur, atau null jika sudah di tahap akhir. */
    public function berikutnya(Transaksi $transaksi): ?StatusLaundry
    {
        $alur = $this->alur($transaksi);

        foreach ($alur as $i => $status) {
            if ($status === $transaksi->status_laundry) {
                return $alur[$i + 1] ?? null;
            }
        }

        return null;
    }

    /**
     * Memajukan status satu tahap dan mencatatnya di riwayat.
     * Tidak boleh loncat/mundur. "Sudah Diambil" hanya jika pembayaran lunas.
     */
    public function majukan(Transaksi $transaksi, User $user, ?string $catatan = null, ?Carbon $waktu = null): StatusLaundry
    {
        return DB::transaction(function () use ($transaksi, $user, $catatan, $waktu) {
            $transaksi = Transaksi::lockForUpdate()->findOrFail($transaksi->id);
            $berikutnya = $this->berikutnya($transaksi);

            if (! $berikutnya) {
                throw new RuntimeException('Laundry ini sudah berada di tahap akhir.');
            }

            if ($berikutnya === StatusLaundry::SudahDiambil && $transaksi->status_pembayaran !== StatusPembayaran::Lunas) {
                throw new RuntimeException('Pembayaran belum lunas. Selesaikan pembayaran sebelum laundry diambil.');
            }

            $transaksi->update(['status_laundry' => $berikutnya]);

            RiwayatStatus::create([
                'transaksi_id' => $transaksi->id,
                'user_id' => $user->id,
                'status' => $berikutnya,
                'catatan' => $catatan,
                'waktu' => $waktu ?? now(),
            ]);

            return $berikutnya;
        });
    }
}

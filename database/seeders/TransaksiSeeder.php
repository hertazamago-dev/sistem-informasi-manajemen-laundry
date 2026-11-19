<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\PembayaranService;
use App\Services\StatusService;
use App\Services\TransaksiService;
use Illuminate\Database\Seeder;

/** Data contoh 5 bulan terakhir, dibuat lewat service agar riwayat, total, dan status konsisten. */
class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        if (Transaksi::exists()) {
            return; // jangan menggandakan data contoh
        }

        mt_srand(2026); // hasil acak yang sama setiap kali seeding

        $transaksiService = app(TransaksiService::class);
        $statusService = app(StatusService::class);
        $bayarService = app(PembayaranService::class);

        $petugas = User::where('role', Role::Petugas->value)->get();
        $pelanggan = Pelanggan::all();
        $layanan = Layanan::all();

        for ($n = 0; $n < 70; $n++) {
            $hariLalu = $n < 18 ? mt_rand(0, 6) : mt_rand(7, 150);
            $tanggal = now()->subDays($hariLalu)->setTime(mt_rand(8, 17), mt_rand(0, 59));
            $kasir = $petugas->random();

            $items = $layanan->random(mt_rand(1, 2))->map(fn ($l) => [
                'layanan_id' => $l->id,
                'jumlah' => $l->satuan === 'kg' ? mt_rand(10, 80) / 10 : mt_rand(1, 6),
            ])->all();

            $transaksi = $transaksiService->buat([
                'pelanggan_id' => $pelanggan->random()->id,
                'items' => $items,
                'catatan' => mt_rand(0, 4) === 0 ? 'Pisahkan pakaian putih' : null,
            ], $kasir, $tanggal);

            $jumlahTahap = count($statusService->alur($transaksi)) - 1; // langkah menuju "Sudah Diambil"
            $lama = $hariLalu > 3;
            $langkah = $lama ? $jumlahTahap : mt_rand(0, $jumlahTahap - 1);

            // Pelanggan lama: sudah lunas sebelum mengambil. Pelanggan baru: acak.
            $pola = $lama ? 2 : mt_rand(0, 2);
            if ($pola === 1) {
                $setengah = (int) (floor($transaksi->total / 2 / 1000) * 1000);
                if ($setengah > 0) {
                    $bayarService->catat($transaksi, $setengah, $kasir, 'DP', $tanggal->copy()->addMinutes(5));
                }
            } elseif ($pola === 2 || $langkah === $jumlahTahap) {
                $bayarService->catat($transaksi, $transaksi->total, $kasir, null, $tanggal->copy()->addHours(1));
            }

            for ($s = 1; $s <= $langkah; $s++) {
                $statusService->majukan($transaksi->fresh(), $petugas->random(), null, $tanggal->copy()->addHours($s * 3));
            }
        }
    }
}

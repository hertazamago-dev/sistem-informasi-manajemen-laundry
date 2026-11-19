<?php

namespace App\Services;

use App\Enums\StatusLaundry;
use App\Enums\StatusPembayaran;
use App\Models\Layanan;
use App\Models\RiwayatStatus;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransaksiService
{
    /**
     * Membuat transaksi beserta detail layanan dan riwayat status awal.
     * Harga dan nama layanan disalin ke detail agar nota lama tidak berubah.
     *
     * @param  array{pelanggan_id:int, catatan?:?string, items:array<int,array{layanan_id:int,jumlah:float|string}>}  $data
     */
    public function buat(array $data, User $petugas, ?Carbon $tanggal = null): Transaksi
    {
        $tanggal = $tanggal ?? now();

        return DB::transaction(function () use ($data, $petugas, $tanggal) {
            $daftarLayanan = Layanan::whereIn('id', collect($data['items'])->pluck('layanan_id'))->get()->keyBy('id');

            $total = 0;
            $jamTerlama = 0;
            $baris = [];

            foreach ($data['items'] as $item) {
                $layanan = $daftarLayanan[$item['layanan_id']];
                $jumlah = round((float) $item['jumlah'], 2);
                // Minimum hitung (mis. kiloan minimum 3 kg): jumlah di bawah minimum ditagih sebesar minimum
                $jumlah = max($jumlah, (float) $layanan->minimum);
                $subtotal = (int) round($jumlah * $layanan->harga);

                $total += $subtotal;
                $jamTerlama = max($jamTerlama, $layanan->estimasi_jam);
                $baris[] = [
                    'layanan_id' => $layanan->id,
                    'nama_layanan' => $layanan->nama,
                    'satuan' => $layanan->satuan,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $layanan->harga,
                    'subtotal' => $subtotal,
                ];
            }

            $transaksi = Transaksi::create([
                'kode' => $this->kodeBaru($tanggal),
                'tanggal_masuk' => $tanggal,
                'pelanggan_id' => $data['pelanggan_id'],
                'user_id' => $petugas->id,
                'total' => $total,
                'total_dibayar' => 0,
                'estimasi_selesai' => $tanggal->copy()->addHours($jamTerlama), // layanan terlama
                'status_laundry' => StatusLaundry::Menunggu,
                'status_pembayaran' => StatusPembayaran::BelumDibayar,
                'catatan' => $data['catatan'] ?? null,
            ]);

            foreach ($baris as $b) {
                $transaksi->details()->create($b);
            }

            RiwayatStatus::create([
                'transaksi_id' => $transaksi->id,
                'user_id' => $petugas->id,
                'status' => StatusLaundry::Menunggu,
                'catatan' => 'Laundry diterima',
                'waktu' => $tanggal,
            ]);

            return $transaksi;
        });
    }

    /** Kode unik per hari: LDR-20261112-0001 */
    private function kodeBaru(Carbon $tanggal): string
    {
        $awalan = 'LDR-' . $tanggal->format('Ymd') . '-';
        $terakhir = Transaksi::where('kode', 'like', $awalan . '%')
            ->lockForUpdate()
            ->orderByDesc('kode')
            ->value('kode');

        $urutan = $terakhir ? ((int) substr($terakhir, -4)) + 1 : 1;

        return $awalan . str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
    }
}

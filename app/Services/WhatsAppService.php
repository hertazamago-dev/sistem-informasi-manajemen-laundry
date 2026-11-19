<?php

namespace App\Services;

use App\Enums\StatusLaundry;
use App\Models\NotifikasiWa;
use App\Models\Transaksi;
use App\Models\User;
use App\Support\Format;

/**
 * Menyiapkan pesan WhatsApp untuk pelanggan.
 * Driver "link": membuat tautan wa.me, petugas yang menekan Kirim di WhatsApp.
 * Untuk otomatis penuh, tambahkan driver baru (mis. Cloud API / gateway) di method kirim().
 */
class WhatsAppService
{
    public function __construct(private StatusService $statusService)
    {
    }

    /** Status yang boleh dikirimi pesan = status yang ada di alur transaksi. */
    public function statusTersedia(Transaksi $transaksi): array
    {
        return $this->statusService->alur($transaksi);
    }

    public function pesan(Transaksi $transaksi, StatusLaundry $status): string
    {
        $transaksi->loadMissing('pelanggan', 'details');

        $isi = strtr(config('laundry.pesan.' . $status->value), [
            '{nama}' => $transaksi->pelanggan->nama,
            '{kode}' => $transaksi->kode,
            '{layanan}' => $transaksi->details->pluck('nama_layanan')->implode(', '),
            '{total}' => number_format($transaksi->total, 0, ',', '.'),
            '{sisa}' => number_format($transaksi->sisa, 0, ',', '.'),
            '{estimasi}' => Format::tanggal($transaksi->estimasi_selesai, true),
            '{laundry}' => config('laundry.nama'),
            '{alamat}' => config('laundry.alamat'),
            '{jam}' => config('laundry.jam_operasional'),
        ]);

        return $isi;
    }

    /** Mencatat log lalu mengembalikan URL tujuan pengiriman. */
    public function kirim(Transaksi $transaksi, StatusLaundry $status, User $petugas): string
    {
        $pesan = $this->pesan($transaksi, $status);
        $nomor = $transaksi->pelanggan->telepon;

        NotifikasiWa::create([
            'transaksi_id' => $transaksi->id,
            'pelanggan_id' => $transaksi->pelanggan_id,
            'user_id' => $petugas->id,
            'status' => $status,
            'nomor_tujuan' => $nomor,
            'pesan' => $pesan,
            'dibuat_pada' => now(),
        ]);

        return match (config('laundry.wa_driver')) {
            // case 'cloud_api': / case 'fonnte': tambahkan di sini
            default => 'https://wa.me/' . $nomor . '?text=' . rawurlencode($pesan),
        };
    }
}

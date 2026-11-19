<?php

namespace App\Http\Controllers;

use App\Enums\StatusLaundry;
use App\Enums\StatusPembayaran;
use App\Models\Transaksi;
use Illuminate\Http\Request;

/** Riwayat seluruh transaksi (hanya lihat). */
class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ], ['sampai.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal awal.']);

        $transaksi = Transaksi::with(['pelanggan', 'petugas'])
            ->cari($request->q)
            ->when($request->dari, fn ($q, $d) => $q->whereDate('tanggal_masuk', '>=', $d))
            ->when($request->sampai, fn ($q, $d) => $q->whereDate('tanggal_masuk', '<=', $d))
            ->when($request->status_laundry, fn ($q, $s) => $q->where('status_laundry', $s))
            ->when($request->status_pembayaran, fn ($q, $s) => $q->where('status_pembayaran', $s))
            ->latest('tanggal_masuk')->latest('id')
            ->paginate(15)->withQueryString();

        return view('riwayat.index', [
            'transaksi' => $transaksi,
            'daftarStatus' => StatusLaundry::cases(),
            'daftarBayar' => StatusPembayaran::cases(),
        ]);
    }
}

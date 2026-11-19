<?php

namespace App\Http\Controllers;

use App\Http\Requests\PembayaranRequest;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use App\Services\PembayaranService;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class PembayaranController extends Controller
{
    public function store(PembayaranRequest $request, Transaksi $transaksi, PembayaranService $service): RedirectResponse
    {
        try {
            $pembayaran = $service->catat(
                $transaksi,
                (int) $request->jumlah_bayar,
                $request->user(),
                $request->catatan
            );
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', "Pembayaran {$pembayaran->kode} berhasil dicatat.");
    }

    public function bukti(Pembayaran $pembayaran)
    {
        $pembayaran->load(['transaksi.pelanggan', 'transaksi.details', 'petugas']);

        return view('pembayaran.bukti', compact('pembayaran'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\StatusLaundry;
use App\Http\Requests\TransaksiRequest;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Services\StatusService;
use App\Services\TransaksiService;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /** Daftar transaksi yang masih berjalan (belum diambil). Seluruh arsip ada di menu Riwayat. */
    public function index(Request $request)
    {
        $transaksi = Transaksi::with(['pelanggan', 'petugas'])
            ->where('status_laundry', '!=', StatusLaundry::SudahDiambil->value)
            ->cari($request->q)
            ->when($request->status, fn($q, $s) => $q->where('status_laundry', $s))
            ->latest('tanggal_masuk')->latest('id')
            ->paginate(10)->withQueryString();

        $statusAktif = array_filter(StatusLaundry::cases(), fn($s) => $s !== StatusLaundry::SudahDiambil);

        return view('transaksi.index', compact('transaksi', 'statusAktif'));
    }

    public function create(Request $request)
    {
        return view('transaksi.create', [
            'pelangganList' => Pelanggan::orderBy('nama')->get(['id', 'kode', 'nama', 'telepon']),
            'layananList' => Layanan::orderBy('id')->get(),
            'pelangganDipilih' => $request->pelanggan,
        ]);
    }

    public function store(TransaksiRequest $request, TransaksiService $service): RedirectResponse
    {
        $transaksi = $service->buat($request->validated(), $request->user());

        return redirect()->route('transaksi.show', $transaksi)
            ->with('success', "Transaksi {$transaksi->kode} berhasil disimpan.");
    }

    public function show(Transaksi $transaksi, StatusService $status, WhatsAppService $wa)
    {
        $transaksi->load(['pelanggan', 'petugas', 'details.layanan', 'pembayaran.petugas', 'riwayat.petugas', 'notifikasi.petugas']);

        return view('transaksi.show', [
            'transaksi' => $transaksi,
            'alur' => $status->alur($transaksi),
            'berikutnya' => $status->berikutnya($transaksi),
            'statusWa' => $wa->statusTersedia($transaksi),
        ]);
    }

    public function nota(Transaksi $transaksi)
    {
        $transaksi->load(['pelanggan', 'petugas', 'details', 'pembayaran']);

        return view('transaksi.nota', compact('transaksi'));
    }
}

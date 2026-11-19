<?php

namespace App\Http\Controllers;

use App\Enums\StatusLaundry;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = auth()->user()->isAdmin();

        $hitung = fn (array $status) => Transaksi::whereIn('status_laundry', array_map(fn ($s) => $s->value, $status))->count();

        $stat = [
            'pelanggan' => Pelanggan::count(),
            'transaksi' => Transaksi::count(),
            'masuk_hari_ini' => Transaksi::whereDate('tanggal_masuk', today())->count(),
            'menunggu' => $hitung([StatusLaundry::Menunggu]),
            'diproses' => $hitung(StatusLaundry::diproses()),
            'selesai' => $hitung([StatusLaundry::SiapDiambil]),
            'diambil' => $hitung([StatusLaundry::SudahDiambil]),
        ];

        if ($admin) {
            $stat['pendapatan_hari'] = (int) Pembayaran::whereDate('tanggal_bayar', today())->sum('jumlah_bayar');
            $stat['pendapatan_bulan'] = (int) Pembayaran::whereYear('tanggal_bayar', now()->year)
                ->whereMonth('tanggal_bayar', now()->month)->sum('jumlah_bayar');
        }

        // Grafik transaksi 7 hari terakhir
        $grafikTransaksi = ['label' => [], 'data' => []];
        for ($i = 6; $i >= 0; $i--) {
            $hari = today()->subDays($i);
            $grafikTransaksi['label'][] = $hari->translatedFormat('D, d M');
            $grafikTransaksi['data'][] = Transaksi::whereDate('tanggal_masuk', $hari)->count();
        }

        // Grafik pendapatan 6 bulan terakhir (hanya Admin)
        $grafikPendapatan = null;
        if ($admin) {
            $grafikPendapatan = ['label' => [], 'data' => []];
            for ($i = 5; $i >= 0; $i--) {
                $bulan = Carbon::now()->startOfMonth()->subMonths($i);
                $grafikPendapatan['label'][] = $bulan->translatedFormat('M Y');
                $grafikPendapatan['data'][] = (int) Pembayaran::whereYear('tanggal_bayar', $bulan->year)
                    ->whereMonth('tanggal_bayar', $bulan->month)->sum('jumlah_bayar');
            }
        }

        // Ringkasan status (untuk doughnut dan daftar)
        $perStatus = Transaksi::selectRaw('status_laundry, count(*) as jml')->groupBy('status_laundry')->pluck('jml', 'status_laundry');
        $ringkasanStatus = collect(StatusLaundry::cases())->map(fn ($s) => [
            'status' => $s, 'jumlah' => (int) ($perStatus[$s->value] ?? 0),
        ]);

        $terbaru = Transaksi::with('pelanggan')->latest('tanggal_masuk')->latest('id')->limit(8)->get();

        return view('dashboard.index', compact('admin', 'stat', 'grafikTransaksi', 'grafikPendapatan', 'ringkasanStatus', 'terbaru'));
    }
}

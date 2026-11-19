<?php

use App\Http\Controllers\NotifikasiWaController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\StatusLaundryController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

// OPERASIONAL TRANSAKSI: khusus Petugas (Admin tidak membuat/mengubah transaksi).
// Rute create harus didaftarkan sebelum rute show agar "create" tidak dianggap id transaksi.
Route::middleware('role:petugas')->group(function () {
    Route::resource('transaksi', TransaksiController::class)->parameters(['transaksi' => 'transaksi'])->only(['index', 'create', 'store']);

    // Proses laundry, pembayaran, dan notifikasi WhatsApp
    Route::post('transaksi/{transaksi}/status', [StatusLaundryController::class, 'update'])->name('transaksi.status');
    Route::post('transaksi/{transaksi}/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::post('transaksi/{transaksi}/wa', [NotifikasiWaController::class, 'store'])->name('transaksi.wa');
});

// HANYA LIHAT & CETAK: Admin (dari menu Riwayat) dan Petugas
Route::middleware('role:admin,petugas')->group(function () {
    Route::get('transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('transaksi/{transaksi}/nota', [TransaksiController::class, 'nota'])->name('transaksi.nota');
    Route::get('pembayaran/{pembayaran}/bukti', [PembayaranController::class, 'bukti'])->name('pembayaran.bukti');
});

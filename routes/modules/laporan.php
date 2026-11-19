<?php

use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

// Laporan lengkap: Admin
Route::middleware('role:admin')->group(function () {
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/export/{format}', [LaporanController::class, 'export'])->name('laporan.export');
});

// Rekap harian milik sendiri: khusus Petugas (Admin tidak melakukan transaksi)
Route::middleware('role:petugas')->get('rekap-saya', [LaporanController::class, 'rekapSaya'])->name('laporan.rekap');

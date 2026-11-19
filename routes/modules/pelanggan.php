<?php

use App\Http\Controllers\PelangganController;
use Illuminate\Support\Facades\Route;

Route::middleware('role:admin,petugas')->group(function () {
    Route::resource('pelanggan', PelangganController::class)->parameters(['pelanggan' => 'pelanggan'])->except('destroy');
});

// Hapus pelanggan: khusus Admin
Route::delete('pelanggan/{pelanggan}', [PelangganController::class, 'destroy'])
    ->middleware('role:admin')->name('pelanggan.destroy');

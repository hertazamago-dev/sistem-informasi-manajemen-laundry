<?php

use App\Http\Controllers\LayananController;
use Illuminate\Support\Facades\Route;

// Petugas hanya boleh melihat daftar layanan
Route::middleware('role:admin,petugas')->get('layanan', [LayananController::class, 'index'])->name('layanan.index');

Route::middleware('role:admin')->group(function () {
    Route::resource('layanan', LayananController::class)->parameters(['layanan' => 'layanan'])->except(['index', 'show']);
});

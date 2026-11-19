<?php

use App\Http\Controllers\RiwayatController;
use Illuminate\Support\Facades\Route;

Route::middleware('role:admin,petugas')->get('riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');

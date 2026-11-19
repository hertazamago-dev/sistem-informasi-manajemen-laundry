<?php

use App\Http\Controllers\PetugasController;
use Illuminate\Support\Facades\Route;

Route::middleware('role:admin')->group(function () {
    Route::resource('petugas', PetugasController::class)->parameters(['petugas' => 'petugas'])->except(['show', 'destroy']);
    Route::patch('petugas/{petugas}/toggle', [PetugasController::class, 'toggle'])->name('petugas.toggle');
});

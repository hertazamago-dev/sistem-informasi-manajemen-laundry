<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('role:admin,petugas')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

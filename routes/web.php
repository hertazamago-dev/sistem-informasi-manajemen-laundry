<?php

use Illuminate\Support\Facades\Route;

// Rute dipecah per modul (routes/modules/*.php) agar anggota tim tidak bentrok di satu file.
Route::redirect('/', '/dashboard');

foreach (glob(__DIR__ . '/modules/*.php') as $berkas) {
    require $berkas;
}

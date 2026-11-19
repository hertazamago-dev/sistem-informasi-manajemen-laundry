<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Zona waktu & bahasa tanggal Indonesia
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');
        Carbon::setLocale('id');

        // Pagination bergaya Bootstrap 5
        Paginator::useBootstrapFive();

        // Direktif Blade: @rupiah($angka)
        Blade::directive('rupiah', fn($expr) => '<?php echo \App\Support\Format::rupiah(' . $expr . '); ?>');
    }
}

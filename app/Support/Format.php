<?php

namespace App\Support;

use Carbon\Carbon;

class Format
{
    public static function rupiah($angka): string
    {
        return 'Rp ' . number_format((int) $angka, 0, ',', '.');
    }

    public static function tanggal($tanggal, bool $denganJam = false): string
    {
        if (! $tanggal) {
            return '-';
        }

        return Carbon::parse($tanggal)->format($denganJam ? 'd/m/Y H:i' : 'd/m/Y');
    }

    /** Menampilkan jumlah tanpa desimal berlebih: 2.50 => 2,5 ; 3.00 => 3 */
    public static function jumlah($angka): string
    {
        return rtrim(rtrim(number_format((float) $angka, 2, ',', '.'), '0'), ',');
    }
}

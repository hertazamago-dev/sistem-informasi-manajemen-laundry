<?php

namespace App\Enums;

enum StatusPembayaran: string
{
    case BelumDibayar = 'belum_dibayar';
    case Sebagian = 'sebagian';
    case Lunas = 'lunas';

    public function label(): string
    {
        return match ($this) {
            self::BelumDibayar => 'Belum Dibayar',
            self::Sebagian => 'Dibayar Sebagian',
            self::Lunas => 'Lunas',
        };
    }

    public function warna(): string
    {
        return match ($this) {
            self::BelumDibayar => 'merah',
            self::Sebagian => 'oranye',
            self::Lunas => 'hijau',
        };
    }

    public static function hitung(int $total, int $dibayar): self
    {
        if ($dibayar <= 0) {
            return self::BelumDibayar;
        }

        return $dibayar >= $total ? self::Lunas : self::Sebagian;
    }
}

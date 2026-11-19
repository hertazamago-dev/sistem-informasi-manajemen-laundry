<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Petugas = 'petugas';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Petugas => 'Petugas Laundry',
        };
    }
}

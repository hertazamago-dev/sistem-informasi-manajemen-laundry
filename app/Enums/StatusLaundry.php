<?php

namespace App\Enums;

enum StatusLaundry: string
{
    case Menunggu = 'menunggu';
    case Dicuci = 'dicuci';
    case Dikeringkan = 'dikeringkan';
    case Disetrika = 'disetrika';
    case SiapDiambil = 'siap_diambil';
    case SudahDiambil = 'sudah_diambil';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu',
            self::Dicuci => 'Dicuci',
            self::Dikeringkan => 'Dikeringkan',
            self::Disetrika => 'Disetrika',
            self::SiapDiambil => 'Siap Diambil',
            self::SudahDiambil => 'Sudah Diambil',
        };
    }

    /** Kelas warna badge: abu (menunggu), oranye (proses), hijau (selesai). */
    public function warna(): string
    {
        return match ($this) {
            self::Menunggu => 'abu',
            self::Dicuci, self::Dikeringkan, self::Disetrika => 'oranye',
            self::SiapDiambil, self::SudahDiambil => 'hijau',
        };
    }

    public function ikon(): string
    {
        return match ($this) {
            self::Menunggu => 'bi-hourglass-split',
            self::Dicuci => 'bi-droplet-half',
            self::Dikeringkan => 'bi-wind',
            self::Disetrika => 'bi-fire',
            self::SiapDiambil => 'bi-bag-check',
            self::SudahDiambil => 'bi-check2-all',
        };
    }

    /** Status yang dianggap "sedang diproses". */
    public static function diproses(): array
    {
        return [self::Dicuci, self::Dikeringkan, self::Disetrika];
    }

    /**
     * Menyusun alur status berdasarkan tahapan yang dimiliki layanan.
     * Contoh: Setrika Saja => Menunggu, Disetrika, Siap Diambil, Sudah Diambil.
     *
     * @return array<int, self>
     */
    public static function susun(bool $cuci, bool $pengeringan, bool $setrika): array
    {
        $alur = [self::Menunggu];
        if ($cuci) {
            $alur[] = self::Dicuci;
        }
        if ($pengeringan) {
            $alur[] = self::Dikeringkan;
        }
        if ($setrika) {
            $alur[] = self::Disetrika;
        }
        $alur[] = self::SiapDiambil;
        $alur[] = self::SudahDiambil;

        return $alur;
    }
}

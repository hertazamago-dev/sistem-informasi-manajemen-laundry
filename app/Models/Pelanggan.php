<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggan extends Model
{
    use SoftDeletes;

    protected $table = 'pelanggan';

    protected $fillable = ['kode', 'nama', 'telepon', 'alamat'];

    /** Nomor otomatis disimpan dalam format internasional (62...). */
    public function setTeleponAttribute($nilai): void
    {
        $this->attributes['telepon'] = self::normalisasiTelepon((string) $nilai);
    }

    public static function normalisasiTelepon(string $nomor): string
    {
        $angka = preg_replace('/\D+/', '', $nomor);   // buang spasi, strip, tanda +
        if (str_starts_with($angka, '0')) {
            return '62' . substr($angka, 1);
        }
        if (str_starts_with($angka, '8')) {
            return '62' . $angka;
        }

        return $angka;
    }

    /** Tampilan ramah: 6281234567890 => 081234567890 */
    public function getTeleponTampilAttribute(): string
    {
        return str_starts_with($this->telepon, '62') ? '0' . substr($this->telepon, 2) : $this->telepon;
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        if (! $kata) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($kata) {
            $q->where('nama', 'like', "%{$kata}%")
                ->orWhere('kode', 'like', "%{$kata}%")
                ->orWhere('telepon', 'like', '%' . preg_replace('/\D+/', '', $kata) . '%');
        });
    }
}

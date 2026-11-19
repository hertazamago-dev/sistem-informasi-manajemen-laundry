<?php

namespace App\Models;

use App\Enums\StatusLaundry;
use App\Enums\StatusPembayaran;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'kode', 'tanggal_masuk', 'pelanggan_id', 'user_id', 'total', 'total_dibayar',
        'estimasi_selesai', 'status_laundry', 'status_pembayaran', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'datetime',
            'estimasi_selesai' => 'datetime',
            'status_laundry' => StatusLaundry::class,
            'status_pembayaran' => StatusPembayaran::class,
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class)->withTrashed();
    }

    /** Petugas penerima. */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class)->orderBy('tanggal_bayar')->orderBy('id');
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class)->orderBy('waktu')->orderBy('id');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(NotifikasiWa::class)->orderByDesc('dibuat_pada')->orderByDesc('id');
    }

    /** Sisa tagihan yang belum dibayar. */
    public function getSisaAttribute(): int
    {
        return max(0, $this->total - $this->total_dibayar);
    }

    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        if (! $kata) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($kata) {
            $q->where('kode', 'like', "%{$kata}%")
                ->orWhereHas('pelanggan', fn ($p) => $p->where('nama', 'like', "%{$kata}%"));
        });
    }
}

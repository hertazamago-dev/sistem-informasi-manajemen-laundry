<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = ['kode', 'transaksi_id', 'user_id', 'jumlah_bayar', 'metode', 'tanggal_bayar', 'catatan'];

    protected function casts(): array
    {
        return ['tanggal_bayar' => 'datetime'];
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

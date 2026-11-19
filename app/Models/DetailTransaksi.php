<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksi';

    protected $fillable = [
        'transaksi_id', 'layanan_id', 'nama_layanan', 'satuan', 'jumlah', 'harga_satuan', 'subtotal',
    ];

    protected function casts(): array
    {
        return ['jumlah' => 'decimal:2'];
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class)->withTrashed();
    }
}

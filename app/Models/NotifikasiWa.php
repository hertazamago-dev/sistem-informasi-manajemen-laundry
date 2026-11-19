<?php

namespace App\Models;

use App\Enums\StatusLaundry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Log penyiapan pesan WhatsApp (append-only). */
class NotifikasiWa extends Model
{
    protected $table = 'notifikasi_wa';

    public $timestamps = false;

    protected $fillable = ['transaksi_id', 'pelanggan_id', 'user_id', 'status', 'nomor_tujuan', 'pesan', 'dibuat_pada'];

    protected function casts(): array
    {
        return ['status' => StatusLaundry::class, 'dibuat_pada' => 'datetime'];
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

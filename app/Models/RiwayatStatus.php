<?php

namespace App\Models;

use App\Enums\StatusLaundry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Append-only: tidak ada updated_at, dan aplikasi tidak menyediakan edit/hapus. */
class RiwayatStatus extends Model
{
    protected $table = 'riwayat_status';

    public $timestamps = false;

    protected $fillable = ['transaksi_id', 'user_id', 'status', 'catatan', 'waktu'];

    protected function casts(): array
    {
        return ['status' => StatusLaundry::class, 'waktu' => 'datetime'];
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

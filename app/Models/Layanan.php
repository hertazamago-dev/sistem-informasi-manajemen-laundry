<?php

namespace App\Models;

use App\Enums\StatusLaundry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layanan extends Model
{
    use SoftDeletes;

    protected $table = 'layanan';

    protected $fillable = [
        'nama',
        'kategori',
        'harga',
        'satuan',
        'minimum',
        'estimasi_jam',
        'deskripsi',
        'ada_cuci',
        'ada_pengeringan',
        'ada_setrika',
    ];

    protected function casts(): array
    {
        return [
            'minimum' => 'decimal:2',
            'ada_cuci' => 'boolean',
            'ada_pengeringan' => 'boolean',
            'ada_setrika' => 'boolean',
        ];
    }

    /** Alur status khusus layanan ini. */
    public function alur(): array
    {
        return StatusLaundry::susun($this->ada_cuci, $this->ada_pengeringan, $this->ada_setrika);
    }

    /** Estimasi dalam teks: 24 jam => "1 hari", 6 jam => "6 jam". */
    public function getEstimasiTeksAttribute(): string
    {
        $jam = $this->estimasi_jam;

        return ($jam >= 24 && $jam % 24 === 0) ? ($jam / 24) . ' hari' : $jam . ' jam';
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}

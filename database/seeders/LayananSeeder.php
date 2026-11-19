<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

/**
 * Daftar harga Yuhuu Laundry (sesuai papan daftar harga).
 * Urutan input = urutan tampil di daftar dan di form transaksi.
 */
class LayananSeeder extends Seeder
{
    public function run(): void
    {
        // Layanan bawaan versi lama diarsipkan (soft delete) agar tidak muncul di pilihan transaksi.
        Layanan::whereIn('nama', ['Cuci Kering', 'Cuci Setrika', 'Setrika Saja', 'Cuci Express'])->delete();

        foreach ($this->daftar() as $item) {
            $layanan = Layanan::withTrashed()->firstOrNew(['nama' => $item['nama']]);
            $layanan->fill($item)->save();
            if ($layanan->trashed()) {
                $layanan->restore(); // pulihkan jika pernah diarsipkan
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function daftar(): array
    {
        $daftar = [];

        // ---- KILOAN: tiap paket punya 3 jenis. Minimum berat 3 kg. ----
        // [nama paket, estimasi jam, harga Cuci & Setrika, harga Cuci Kering, harga Setrika Saja]
        $paket = [
            ['Reguler 2 Hari', 48, 7000, 5000, 5000],
            ['Reguler 1 Hari', 24, 10000, 8000, 8000],
            ['Express 6 Jam', 6, 13000, 10000, 10000],
            ['Express 3 Jam', 3, 15000, 12000, 12000],
        ];
        foreach ($paket as [$nama, $jam, $hargaCs, $hargaCk, $hargaSs]) {
            // jenis => [harga, cuci, pengeringan, setrika]
            $jenis = [
                'Cuci & Setrika' => [$hargaCs, true, true, true],
                'Cuci Kering' => [$hargaCk, true, true, false],
                'Setrika Saja' => [$hargaSs, false, false, true],
            ];
            foreach ($jenis as $namaJenis => [$harga, $cuci, $kering, $setrika]) {
                $daftar[] = [
                    'nama' => "$nama - $namaJenis",
                    'kategori' => "Kiloan - $nama",
                    'harga' => $harga,
                    'satuan' => 'kg',
                    'minimum' => 3,
                    'estimasi_jam' => $jam,
                    'deskripsi' => "Paket $nama, $namaJenis. Minimum berat 3 kg.",
                    'ada_cuci' => $cuci,
                    'ada_pengeringan' => $kering,
                    'ada_setrika' => $setrika,
                ];
            }
        }

        // ---- SATUAN ----
        // Estimasi satuan tidak tertera di daftar harga: diasumsikan 48 jam (ubah di menu Layanan).
        // Tahapan satuan: cuci + kering (Baju/Celana ditambah setrika).
        $satuan = [
            ['Selimut', 'Selimut Bayi', 10000, 'pcs'],
            ['Selimut', 'Selimut Tipis', 15000, 'pcs'],
            ['Selimut', 'Selimut Tebal', 20000, 'pcs'],
            ['Sprei Saja', 'Sprei Saja Kecil (<120 cm)', 8000, 'pcs'],
            ['Sprei Saja', 'Sprei Saja Sedang (160 cm)', 10000, 'pcs'],
            ['Sprei Saja', 'Sprei Saja Besar (>180 cm)', 15000, 'pcs'],
            ['Sprei Satu Set', 'Sprei Satu Set Kecil (<120 cm)', 13000, 'pcs'],
            ['Sprei Satu Set', 'Sprei Satu Set Sedang (160 cm)', 15000, 'pcs'],
            ['Sprei Satu Set', 'Sprei Satu Set Besar (>180 cm)', 20000, 'pcs'],
            ['Bed Cover', 'Bed Cover Kecil (<120 cm)', 20000, 'pcs'],
            ['Bed Cover', 'Bed Cover Sedang (160 cm)', 27000, 'pcs'],
            ['Bed Cover', 'Bed Cover Besar (>180 cm)', 35000, 'pcs'],
            ['Lain-lain', 'Handuk', 10000, 'pcs'],
            ['Lain-lain', 'Baju / Celana', 10000, 'pcs'],
            ['Lain-lain', 'Gorden', 10000, 'm'],
            ['Lain-lain', 'Karpet', 10000, 'm'],
        ];
        foreach ($satuan as [$kategori, $nama, $harga, $unit]) {
            $daftar[] = [
                'nama' => $nama,
                'kategori' => $kategori,
                'harga' => $harga,
                'satuan' => $unit,
                'minimum' => 0,
                'estimasi_jam' => 48,
                'deskripsi' => $unit === 'm' ? 'Dihitung per meter.' : 'Dihitung per pcs.',
                'ada_cuci' => true,
                'ada_pengeringan' => true,
                'ada_setrika' => $nama === 'Baju / Celana',
            ];
        }

        return $daftar;
    }
}

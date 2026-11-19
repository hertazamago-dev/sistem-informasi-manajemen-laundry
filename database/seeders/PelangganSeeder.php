<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Seeder;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Rina Wulandari', '081234560001', 'Jl. Kenanga No. 5, Jakarta Selatan'],
            ['Dika Pratama', '081234560001', 'Jl. Kenanga No. 5, Jakarta Selatan'], // nomor sama (satu keluarga)
            ['Agus Santoso', '081234560003', 'Jl. Mawar No. 12, Jakarta Timur'],
            ['Maya Lestari', '081234560004', 'Apartemen Sejuk Tower B/1203'],
            ['Hendra Wijaya', '081234560005', 'Jl. Cempaka Raya No. 77'],
            ['Putri Anggraini', '081234560006', 'Kost Melati, Jl. Dahlia No. 3'],
            ['Bambang Setiawan', '081234560007', 'Jl. Anggrek No. 21, Tangerang'],
            ['Sari Utami', '081234560008', 'Jl. Flamboyan No. 9, Bekasi'],
        ];

        foreach ($data as $i => [$nama, $telepon, $alamat]) {
            Pelanggan::updateOrCreate(
                ['kode' => 'PLG-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT)],
                ['nama' => $nama, 'telepon' => $telepon, 'alamat' => $alamat]
            );
        }
    }
}

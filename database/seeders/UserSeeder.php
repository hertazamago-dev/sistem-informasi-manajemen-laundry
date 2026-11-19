<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Kata sandi contoh: "password". WAJIB diganti jika dipakai sungguhan.
        User::updateOrCreate(['email' => 'admin@laundry.test'], [
            'name' => 'Administrator', 'password' => 'password', 'role' => Role::Admin, 'is_active' => true, 'no_telepon' => '081200000001',
        ]);
        User::updateOrCreate(['email' => 'petugas1@laundry.test'], [
            'name' => 'Sinta Petugas', 'password' => 'password', 'role' => Role::Petugas, 'is_active' => true, 'no_telepon' => '081200000002',
        ]);
        User::updateOrCreate(['email' => 'petugas2@laundry.test'], [
            'name' => 'Budi Petugas', 'password' => 'password', 'role' => Role::Petugas, 'is_active' => true, 'no_telepon' => '081200000003',
        ]);
    }
}

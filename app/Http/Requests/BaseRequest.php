<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Dasar semua FormRequest: pesan validasi berbahasa Indonesia. */
abstract class BaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // hak akses sudah dijaga middleware 'role'
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'email' => 'Format :attribute tidak valid.',
            'unique' => ':attribute sudah digunakan.',
            'min' => ':attribute minimal :min.',
            'max' => ':attribute maksimal :max.',
            'numeric' => ':attribute harus berupa angka.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'exists' => ':attribute yang dipilih tidak valid.',
            'regex' => 'Format :attribute tidak valid.',
            'confirmed' => 'Konfirmasi :attribute tidak cocok.',
            'in' => ':attribute yang dipilih tidak valid.',
            'array' => ':attribute tidak valid.',
            'distinct' => ':attribute tidak boleh dipilih lebih dari satu kali.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama', 'nama' => 'Nama', 'email' => 'Email', 'password' => 'Kata sandi',
            'no_telepon' => 'Nomor telepon', 'telepon' => 'Nomor telepon', 'alamat' => 'Alamat',
            'harga' => 'Harga', 'satuan' => 'Satuan', 'estimasi_jam' => 'Estimasi pengerjaan',
            'deskripsi' => 'Deskripsi', 'pelanggan_id' => 'Pelanggan', 'items' => 'Layanan',
            'items.*.layanan_id' => 'Layanan', 'items.*.jumlah' => 'Berat/jumlah',
            'jumlah_bayar' => 'Jumlah bayar', 'catatan' => 'Catatan',
        ];
    }
}

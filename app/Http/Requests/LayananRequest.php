<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class LayananRequest extends BaseRequest
{
    public function rules(): array
    {
        $id = $this->route('layanan')?->id;

        return [
            'nama' => ['required', 'string', 'max:100', Rule::unique('layanan', 'nama')->ignore($id)],
            'kategori' => ['nullable', 'string', 'max:50'],
            'harga' => ['required', 'integer', 'min:1', 'max:100000000'],
            'satuan' => ['required', Rule::in(['kg', 'pcs', 'm'])],
            'minimum' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'estimasi_jam' => ['required', 'integer', 'min:1', 'max:720'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'ada_cuci' => ['nullable', 'boolean'],
            'ada_pengeringan' => ['nullable', 'boolean'],
            'ada_setrika' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return parent::attributes() + ['kategori' => 'Kategori', 'minimum' => 'Minimum hitung'];
    }

    /** Validasi tambahan: minimal satu tahapan harus dipilih. */
    public function after(): array
    {
        return [
            function ($validator) {
                if (!$this->boolean('ada_cuci') && !$this->boolean('ada_pengeringan') && !$this->boolean('ada_setrika')) {
                    $validator->errors()->add('ada_cuci', 'Pilih minimal satu tahapan pengerjaan.');
                }
            }
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Models\Pelanggan;

class PelangganRequest extends BaseRequest
{
    /** Bersihkan nomor sebelum divalidasi (spasi, strip, tanda +). */
    protected function prepareForValidation(): void
    {
        if ($this->has('telepon')) {
            $this->merge(['telepon' => preg_replace('/[\s\-\(\)\.]+/', '', (string) $this->telepon)]);
        }
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            // Nomor seluler Indonesia: 08xxxxxxxx, 628xxxxxxxx, atau +628xxxxxxxx
            'telepon' => ['required', 'regex:/^(\+?62|0)8[0-9]{7,12}$/'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return parent::messages() + [
            'telepon.regex' => 'Nomor telepon harus nomor seluler Indonesia, contoh: 081234567890.',
        ];
    }
}

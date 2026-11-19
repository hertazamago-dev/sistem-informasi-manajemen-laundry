<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PetugasRequest extends BaseRequest
{
    public function rules(): array
    {
        $id = $this->route('petugas')?->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($id)],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            // Saat tambah: wajib. Saat edit: boleh kosong (tidak mengganti sandi).
            'password' => [$id ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ];
    }
}

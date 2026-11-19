<?php

namespace App\Http\Requests;

class PembayaranRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'jumlah_bayar' => ['required', 'integer', 'min:1'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ];
    }
}

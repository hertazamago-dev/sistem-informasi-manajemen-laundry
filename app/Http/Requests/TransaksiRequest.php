<?php

namespace App\Http\Requests;

use App\Models\Layanan;
use Illuminate\Validation\Rule;

class TransaksiRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'pelanggan_id' => ['required', Rule::exists('pelanggan', 'id')->whereNull('deleted_at')],
            'items' => ['required', 'array', 'min:1'],
            'items.*.layanan_id' => [
                'required',
                'distinct',
                Rule::exists('layanan', 'id')->whereNull('deleted_at'),
            ],
            'items.*.jumlah' => ['required', 'numeric', 'min:0.1', 'max:9999'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return parent::messages() + [
            'items.required' => 'Tambahkan minimal satu layanan.',
            'items.min' => 'Tambahkan minimal satu layanan.',
            'items.*.jumlah.min' => 'Berat/jumlah minimal 0,1.',
        ];
    }

    /** Layanan berhitung per pcs harus bilangan bulat (tidak boleh 1,5 pcs). */
    public function after(): array
    {
        return [
            function ($validator) {
                $items = $this->input('items');
                if (!is_array($items)) {
                    return;
                }
                $idPcs = Layanan::whereIn('id', collect($items)->pluck('layanan_id')->filter()->all())
                    ->where('satuan', 'pcs')->pluck('id')->all();

                foreach ($items as $i => $item) {
                    $jumlah = $item['jumlah'] ?? null;
                    if (in_array((int) ($item['layanan_id'] ?? 0), $idPcs, true) && is_numeric($jumlah) && fmod((float) $jumlah, 1.0) !== 0.0) {
                        $validator->errors()->add("items.$i.jumlah", 'Jumlah layanan satuan harus bilangan bulat.');
                    }
                }
            }
        ];
    }
}

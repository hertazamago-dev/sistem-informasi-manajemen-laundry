<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Services\StatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class StatusLaundryController extends Controller
{
    /** Memajukan status satu tahap sesuai alur layanan. */
    public function update(Request $request, Transaksi $transaksi, StatusService $service): RedirectResponse
    {
        $request->validate(['catatan' => ['nullable', 'string', 'max:255']]);

        try {
            $baru = $service->majukan($transaksi, $request->user(), $request->catatan);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        // 'wa_status' memunculkan tombol "Kirim WA" langsung setelah status berubah
        return back()->with('success', "Status laundry diubah menjadi {$baru->label()}.")
            ->with('wa_status', $baru->value);
    }
}

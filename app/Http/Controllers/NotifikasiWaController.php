<?php

namespace App\Http\Controllers;

use App\Enums\StatusLaundry;
use App\Models\Transaksi;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotifikasiWaController extends Controller
{
    /** Mencatat log lalu mengarahkan petugas ke WhatsApp (pesan sudah terisi). */
    public function store(Request $request, Transaksi $transaksi, WhatsAppService $wa): RedirectResponse
    {
        $status = StatusLaundry::tryFrom((string) $request->status);

        // Hanya status yang ada pada alur layanan transaksi ini yang boleh dikirim
        if (! $status || ! in_array($status, $wa->statusTersedia($transaksi), true)) {
            return back()->with('error', 'Status tidak tersedia untuk transaksi ini.');
        }

        return redirect()->away($wa->kirim($transaksi, $status, $request->user()));
    }
}

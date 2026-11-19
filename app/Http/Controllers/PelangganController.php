<?php

namespace App\Http\Controllers;

use App\Enums\StatusLaundry;
use App\Http\Requests\PelangganRequest;
use App\Models\Pelanggan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $pelanggan = Pelanggan::cari($request->q)->withCount('transaksi')->latest('id')->paginate(10)->withQueryString();

        return view('pelanggan.index', compact('pelanggan'));
    }

    public function create()
    {
        return view('pelanggan.form', ['pelanggan' => new Pelanggan()]);
    }

    public function store(PelangganRequest $request): RedirectResponse
    {
        if ($peringatan = $this->peringatanDuplikat($request)) {
            return $peringatan;
        }

        $pelanggan = Pelanggan::create($request->validated() + ['kode' => $this->kodeBaru()]);

        // Jika datang dari form transaksi, kembali ke sana dengan pelanggan terpilih
        if ($request->boolean('dari_transaksi') && !$request->user()->isAdmin()) {
            return redirect()->route('transaksi.create', ['pelanggan' => $pelanggan->id])
                ->with('success', "Pelanggan {$pelanggan->nama} berhasil ditambahkan.");
        }

        return redirect()->route('pelanggan.show', $pelanggan)->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function show(Pelanggan $pelanggan)
    {
        $transaksi = $pelanggan->transaksi()->latest('tanggal_masuk')->paginate(8);
        $totalBelanja = (int) $pelanggan->transaksi()->sum('total');

        return view('pelanggan.show', compact('pelanggan', 'transaksi', 'totalBelanja'));
    }

    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.form', compact('pelanggan'));
    }

    public function update(PelangganRequest $request, Pelanggan $pelanggan): RedirectResponse
    {
        if ($peringatan = $this->peringatanDuplikat($request, $pelanggan->id)) {
            return $peringatan;
        }

        $pelanggan->update($request->validated());

        return redirect()->route('pelanggan.show', $pelanggan)->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    /** Hapus = soft delete (khusus Admin). Ditolak jika masih ada laundry yang belum diambil. */
    public function destroy(Pelanggan $pelanggan): RedirectResponse
    {
        $aktif = $pelanggan->transaksi()->where('status_laundry', '!=', StatusLaundry::SudahDiambil->value)->exists();
        if ($aktif) {
            return back()->with('error', 'Pelanggan masih memiliki laundry yang belum diambil, tidak dapat dihapus.');
        }

        $pelanggan->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }

    /** Nomor boleh sama (satu keluarga), tetapi petugas diberi peringatan dulu. */
    private function peringatanDuplikat(Request $request, ?int $kecuali = null): ?RedirectResponse
    {
        if ($request->boolean('lanjut_duplikat')) {
            return null;
        }

        $sama = Pelanggan::where('telepon', Pelanggan::normalisasiTelepon($request->telepon))
            ->when($kecuali, fn($q) => $q->where('id', '!=', $kecuali))
            ->first();

        return $sama ? back()->withInput()->with('duplikat', $sama->nama) : null;
    }

    private function kodeBaru(): string
    {
        $terakhir = Pelanggan::withTrashed()->orderByDesc('id')->value('kode');
        $urutan = $terakhir ? ((int) substr($terakhir, 4)) + 1 : 1;

        return 'PLG-' . str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
    }
}

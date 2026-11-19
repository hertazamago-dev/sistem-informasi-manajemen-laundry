<?php

namespace App\Http\Controllers;

use App\Http\Requests\LayananRequest;
use App\Models\Layanan;
use Illuminate\Http\RedirectResponse;

class LayananController extends Controller
{
    public function index()
    {
        // Urutan id = urutan input (sama dengan urutan daftar harga)
        return view('layanan.index', ['layanan' => Layanan::orderBy('id')->get()]);
    }

    public function create()
    {
        return view('layanan.form', [
            'layanan' => new Layanan(['satuan' => 'kg', 'minimum' => 0, 'ada_cuci' => true, 'ada_pengeringan' => true]),
            'kategoriList' => $this->kategoriList(),
        ]);
    }

    public function store(LayananRequest $request): RedirectResponse
    {
        Layanan::create($this->data($request));

        return redirect()->route('layanan.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Layanan $layanan)
    {
        return view('layanan.form', ['layanan' => $layanan, 'kategoriList' => $this->kategoriList()]);
    }

    public function update(LayananRequest $request, Layanan $layanan): RedirectResponse
    {
        $layanan->update($this->data($request));

        return redirect()->route('layanan.index')->with('success', 'Layanan berhasil diperbarui. Transaksi lama tidak terpengaruh.');
    }

    public function destroy(Layanan $layanan): RedirectResponse
    {
        $layanan->delete(); // soft delete: transaksi lama tetap utuh

        return redirect()->route('layanan.index')->with('success', 'Layanan berhasil dihapus.');
    }

    private function data(LayananRequest $request): array
    {
        return array_merge(
            $request->safe()->except(['ada_cuci', 'ada_pengeringan', 'ada_setrika', 'minimum']),
            [
                'minimum' => $request->input('minimum') ?: 0,
                'ada_cuci' => $request->boolean('ada_cuci'),
                'ada_pengeringan' => $request->boolean('ada_pengeringan'),
                'ada_setrika' => $request->boolean('ada_setrika'),
            ]
        );
    }

    /** Saran kategori untuk isian form (agar penulisan konsisten). */
    private function kategoriList()
    {
        return Layanan::whereNotNull('kategori')->distinct()->orderBy('kategori')->pluck('kategori');
    }
}

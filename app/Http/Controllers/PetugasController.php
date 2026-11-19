<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\PetugasRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

/** Pengelolaan akun petugas (khusus Admin). Tidak ada registrasi publik. */
class PetugasController extends Controller
{
    public function index()
    {
        $petugas = User::where('role', Role::Petugas->value)->withCount('transaksi')->orderBy('name')->get();

        return view('petugas.index', compact('petugas'));
    }

    public function create()
    {
        return view('petugas.form', ['petugas' => new User()]);
    }

    public function store(PetugasRequest $request): RedirectResponse
    {
        User::create($request->safe()->only(['name', 'email', 'no_telepon', 'password']) + [
            'role' => Role::Petugas,
            'is_active' => true,
        ]);

        return redirect()->route('petugas.index')->with('success', 'Akun petugas berhasil dibuat.');
    }

    public function edit(User $petugas)
    {
        abort_unless($petugas->role === Role::Petugas, 404);

        return view('petugas.form', compact('petugas'));
    }

    public function update(PetugasRequest $request, User $petugas): RedirectResponse
    {
        abort_unless($petugas->role === Role::Petugas, 404);

        $data = $request->safe()->only(['name', 'email', 'no_telepon']);
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }
        $petugas->update($data);

        return redirect()->route('petugas.index')->with('success', 'Akun petugas berhasil diperbarui.');
    }

    /** Petugas tidak dihapus permanen (punya riwayat), cukup dinonaktifkan. */
    public function toggle(User $petugas): RedirectResponse
    {
        abort_unless($petugas->role === Role::Petugas, 404);

        $petugas->update(['is_active' => ! $petugas->is_active]);

        return back()->with('success', 'Akun ' . $petugas->name . ($petugas->is_active ? ' diaktifkan.' : ' dinonaktifkan.'));
    }
}

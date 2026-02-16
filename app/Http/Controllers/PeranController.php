<?php

namespace App\Http\Controllers;

use App\Models\Peran;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PeranController extends Controller
{
    /**
     * Menampilkan daftar peran.
     */
    public function index()
    {
        $perans = Peran::latest()->paginate(10);
        return view('peran.index', compact('perans'));
    }

    /**
     * Menampilkan form untuk membuat peran baru.
     */
    public function create()
    {
        return view('peran.create');
    }

    /**
     * Menyimpan peran baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_peran' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:peran,slug',
            'deskripsi' => 'nullable|string',
        ]);

        Peran::create([
            'nama_peran' => $request->nama_peran,
            'slug' => $request->slug ?: Str::slug($request->nama_peran),
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('peran.index')->with('success', 'Peran berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit peran.
     */
    public function edit($id)
    {
        $peran = Peran::findOrFail($id);
        return view('peran.edit', compact('peran'));
    }

    /**
     * Memperbarui peran di database.
     */
    public function update(Request $request, $id)
    {
        $peran = Peran::findOrFail($id);

        $request->validate([
            'nama_peran' => 'required|string|max:255',
            'slug' => 'required|string|unique:peran,slug,' . $id,
            'deskripsi' => 'nullable|string',
        ]);

        $peran->update($request->all());

        return redirect()->route('peran.index')->with('success', 'Peran berhasil diperbarui.');
    }

    /**
     * Menghapus peran dari database dengan pengecekan penggunaan pada model Pengguna.
     */
    public function destroy($id)
    {
        try {
            $peran = Peran::findOrFail($id);
            
            // Perbaikan: Menggunakan nama metode relasi baru 'penggunas'
            if ($peran->penggunas()->exists()) {
                return back()->with('error', 'Peran "' . $peran->nama_peran . '" tidak bisa dihapus karena masih digunakan oleh beberapa pengguna.');
            }

            $peran->delete();

            return redirect()->route('peran.index')->with('success', 'Peran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('peran.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
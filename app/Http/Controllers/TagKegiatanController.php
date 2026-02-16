<?php

namespace App\Http\Controllers;

use App\Models\RefTagKegiatan;
use Illuminate\Http\Request;

class TagKegiatanController extends Controller
{
    /**
     * Menampilkan daftar kelompok kegiatan (Tag).
     */
    public function index()
    {
        // Mengambil data tag dengan hitungan jumlah kegiatan terkait
        $tags = RefTagKegiatan::withCount('kegiatan')->latest()->paginate(10);
        return view('tag_kegiatan.index', compact('tags'));
    }

    /**
     * Menyimpan data tag baru ke database.
     */
    public function store(Request $request)
    {
        // Tampung hasil validasi ke variabel
        $validated = $request->validate([
            'nama_tag' => 'required|string|max:100|unique:ref_tag_kegiatan,nama_tag',
        ], [
            'nama_tag.unique' => 'Nama kelompok kegiatan ini sudah ada.'
        ]);

        // Gunakan variabel $validated untuk create agar aman
        RefTagKegiatan::create($validated);

        return redirect()->route('tag_kegiatan.index')
            ->with('success', 'Kelompok kegiatan berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail kelompok kegiatan beserta daftar kegiatannya.
     */
    public function show($id)
    {
        // Mengambil data berdasarkan ID beserta jumlah kegiatan
        $tag = RefTagKegiatan::withCount('kegiatan')->findOrFail($id);
        
        return view('tag_kegiatan.show', compact('tag'));
    }

    /**
     * Memperbarui data tag di database.
     */
    public function update(Request $request, $id)
    {
        // Tampung hasil validasi ke variabel
        $validated = $request->validate([
            'nama_tag' => 'required|string|max:100|unique:ref_tag_kegiatan,nama_tag,' . $id,
        ]);

        $tag = RefTagKegiatan::findOrFail($id);
        
        // Gunakan $validated, jangan $request->all() untuk menghindari error kolom '_token'/'_method'
        $tag->update($validated);

        return redirect()->route('tag_kegiatan.index')
            ->with('success', 'Kelompok kegiatan berhasil diperbarui.');
    }

    /**
     * Menghapus data tag dari database.
     */
    public function destroy($id)
    {
        $tag = RefTagKegiatan::findOrFail($id);
        
        // Cek jika masih ada kegiatan yang menggunakan tag ini
        if ($tag->kegiatan()->count() > 0) {
            return redirect()->route('tag_kegiatan.index')
                ->with('error', 'Gagal menghapus! Masih ada kegiatan yang terhubung dengan kelompok ini.');
        }

        $tag->delete();

        // Perbaikan nama route dari 'tag_keg_index' ke 'tag_kegiatan.index'
        return redirect()->route('tag_kegiatan.index')
            ->with('success', 'Kelompok kegiatan berhasil dihapus.');
    }
}
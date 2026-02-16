<?php

namespace App\Http\Controllers;

use App\Models\Satker;
use Illuminate\Http\Request;

class SatkerController extends Controller
{
    /**
     * Menampilkan form pengaturan Satker.
     * Karena biasanya hanya ada satu Satker, kita gunakan record pertama.
     */
    public function index()
    {
        $satker = Satker::first();
        return view('satker.index', compact('satker'));
    }

    /**
     * Menyimpan atau memperbarui data Satker.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_satker' => 'required|string|max:255',
            'kode_satker' => 'required|string|max:20',
            'alamat_lengkap' => 'required|string',
            'kota' => 'required|string|max:100',
            'nama_ppk' => 'nullable|string|max:255',
            'nip_ppk' => 'nullable|string|max:30',
        ]);

        // Menggunakan updateOrCreate agar data selalu tersimpan di ID 1 (singletone data)
        Satker::updateOrCreate(
            ['id' => 1],
            $request->only(['nama_satker', 'kode_satker', 'alamat_lengkap', 'kota', 'nama_ppk', 'nip_ppk'])
        );

        return back()->with('success', 'Konfigurasi Satuan Kerja berhasil diperbarui.');
    }
}
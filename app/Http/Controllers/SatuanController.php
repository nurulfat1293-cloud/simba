<?php

namespace App\Http\Controllers;

use App\Models\RefSatuan; // Menggunakan model RefSatuan
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index()
    {
        // Mengambil data terbaru dengan pagination
        $satuan = RefSatuan::latest()->paginate(10);
        return view('satuan.index', compact('satuan'));
    }

    public function create()
    {
        return view('satuan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // Validasi tetap cek ke tabel 'ref_satuan'
            'nama_satuan' => 'required|string|max:191|unique:ref_satuan,nama_satuan',
        ], [
            'nama_satuan.required' => 'Nama satuan wajib diisi.',
            'nama_satuan.unique' => 'Nama satuan sudah ada.',
        ]);

        RefSatuan::create($request->all());

        return redirect()->route('satuan.index')
            ->with('success', 'Data satuan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $satuan = RefSatuan::findOrFail($id);
        return view('satuan.show', compact('satuan'));
    }

    public function edit($id)
    {
        $satuan = RefSatuan::findOrFail($id);
        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:191|unique:ref_satuan,nama_satuan,'.$id,
        ]);

        $satuan = RefSatuan::findOrFail($id);
        $satuan->update($request->all());

        return redirect()->route('satuan.index')
            ->with('success', 'Data satuan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $satuan = RefSatuan::findOrFail($id);
        $satuan->delete();

        return redirect()->route('satuan.index')
            ->with('success', 'Data satuan berhasil dihapus.');
    }
}
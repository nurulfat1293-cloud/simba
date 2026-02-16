<?php

namespace App\Http\Controllers;

use App\Models\RefJabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatan = RefJabatan::latest()->paginate(10);
        return view('jabatan.index', compact('jabatan'));
    }

    public function create()
    {
        return view('jabatan.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi harus sesuai dengan input yang ada di View
        $request->validate([
            'nama_jabatan' => 'required|string|max:191|unique:ref_jabatan,nama_jabatan',
            'kode_jabatan' => 'required|string|max:50|unique:ref_jabatan,kode_jabatan',
        ], [
            'nama_jabatan.required' => 'Nama jabatan wajib diisi.',
            'nama_jabatan.unique'   => 'Nama jabatan sudah terdaftar.',
            'kode_jabatan.required' => 'Kode jabatan wajib diisi.',
            'kode_jabatan.unique'   => 'Kode jabatan sudah digunakan.',
        ]);

        try {
            // 2. Gunakan create dengan data yang sudah tervalidasi
            RefJabatan::create([
                'nama_jabatan' => $request->nama_jabatan,
                'kode_jabatan' => $request->kode_jabatan,
            ]);

            return redirect()->route('jabatan.index')
                ->with('success', 'Data jabatan berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error("Gagal Simpan Jabatan: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data ke database.']);
        }
    }

    public function show($id)
    {
        $jabatan = RefJabatan::findOrFail($id);
        return view('jabatan.show', compact('jabatan'));
    }

    public function edit($id)
    {
        $jabatan = RefJabatan::findOrFail($id);
        return view('jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:191|unique:ref_jabatan,nama_jabatan,'.$id,
            'kode_jabatan' => 'required|string|max:50|unique:ref_jabatan,kode_jabatan,'.$id,
        ]);

        try {
            $jabatan = RefJabatan::findOrFail($id);
            $jabatan->update($request->all());

            return redirect()->route('jabatan.index')
                ->with('success', 'Data jabatan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui data.']);
        }
    }

    public function destroy($id)
    {
        RefJabatan::findOrFail($id)->delete();
        return redirect()->route('jabatan.index')
            ->with('success', 'Data jabatan berhasil dihapus.');
    }
}
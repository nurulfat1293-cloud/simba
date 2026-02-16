<?php

namespace App\Http\Controllers;

use App\Models\AturanSbml;
use App\Models\RefJenisKegiatan;
use App\Models\RefJabatan;
use Illuminate\Http\Request;

class AturanSbmlController extends Controller
{
    // 1. Tampilkan Tabel Aturan
    public function index()
    {
        $aturan = AturanSbml::with(['jenisKegiatan', 'jabatan'])->latest()->get();
        $jenis_kegiatan = RefJenisKegiatan::all();
        $jabatan = RefJabatan::all();
        
        return view('sbml.index', compact('aturan', 'jenis_kegiatan', 'jabatan'));
    }

    // 2. Simpan Aturan Baru
    public function store(Request $request)
    {
        $request->validate([
            'id_jenis_kegiatan' => 'required',
            'id_jabatan' => 'required',
            'batas_honor' => 'required|numeric',
            'tahun_berlaku' => 'required|numeric',
        ]);

        // Cek agar tidak duplikat (Satu jenis+jabatan+tahun hanya boleh 1 aturan)
        $exists = AturanSbml::where('id_jenis_kegiatan', $request->id_jenis_kegiatan)
                    ->where('id_jabatan', $request->id_jabatan)
                    ->where('tahun_berlaku', $request->tahun_berlaku)
                    ->exists();

        if($exists) {
            return back()->withErrors(['msg' => 'Aturan untuk kombinasi ini sudah ada di tahun tersebut!']);
        }

        AturanSbml::create($request->all());

        return back()->with('success', 'Aturan SBML berhasil disimpan.');
    }

    // 3. Hapus Aturan
    public function destroy($id)
    {
        AturanSbml::findOrFail($id)->delete();
        return back()->with('success', 'Aturan dihapus.');
    }
}
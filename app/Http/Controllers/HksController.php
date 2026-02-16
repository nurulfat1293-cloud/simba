<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AturanHks;
use App\Models\RefTagKegiatan;
use App\Models\RefJabatan;
use App\Models\RefSatuan;
use Illuminate\Support\Facades\Log;

class HksController extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->query('cari');

        $daftar_hks = AturanHks::with(['tagKegiatan', 'jabatan', 'satuan'])
            ->when($cari, function($query) use ($cari) {
                $query->whereHas('tagKegiatan', function($q) use ($cari) {
                    $q->where('nama_tag', 'like', "%{$cari}%");
                })->orWhereHas('jabatan', function($q) use ($cari) {
                    $q->where('nama_jabatan', 'like', "%{$cari}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $tag_kegiatan = RefTagKegiatan::orderBy('nama_tag', 'asc')->get();
        $ref_jabatan = RefJabatan::orderBy('nama_jabatan', 'asc')->get();
        $satuan = RefSatuan::orderBy('nama_satuan', 'asc')->get();

        return view('hks.index', compact('daftar_hks', 'tag_kegiatan', 'ref_jabatan', 'satuan'));
    }

    public function store(Request $request)
    {
        // 1. Validasi dengan pesan kustom
        $request->validate([
            'id_tag_kegiatan' => 'required|exists:ref_tag_kegiatan,id',
            'id_jabatan'      => 'required|exists:ref_jabatan,id',
            'id_satuan'       => 'required|exists:ref_satuan,id',
            'harga_satuan'    => 'required|numeric|min:0',
        ], [
            'required' => ':attribute wajib diisi.',
            'numeric'  => ':attribute harus berupa angka.',
            'exists'   => 'Data referensi tidak ditemukan.'
        ]);

        try {
            // 2. Cek duplikasi
            $cek = AturanHks::where('id_tag_kegiatan', $request->id_tag_kegiatan)
                            ->where('id_jabatan', $request->id_jabatan)
                            ->where('id_satuan', $request->id_satuan)
                            ->exists();

            if ($cek) {
                return back()->withErrors(['msg' => 'Kombinasi data ini sudah ada di sistem!'])->withInput();
            }

            // 3. Simpan data
            AturanHks::create($request->all());

            return back()->with('success', 'Data HKS berhasil disimpan.');

        } catch (\Exception $e) {
            // Log error untuk debug
            Log::error($e->getMessage());
            return back()->withErrors(['msg' => 'Terjadi kesalahan sistem: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Menampilkan detail data HKS.
     * Method ini ditambahkan untuk mendukung fitur tombol "Show".
     */
    public function show($id)
    {
        // Mengambil data berdasarkan ID beserta relasinya
        $hks = AturanHks::with(['tagKegiatan', 'jabatan', 'satuan'])->findOrFail($id);
        
        // Mengembalikan view detail
        return view('hks.show', compact('hks'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_tag_kegiatan' => 'required|exists:ref_tag_kegiatan,id',
            'id_jabatan'      => 'required|exists:ref_jabatan,id',
            'id_satuan'       => 'required|exists:ref_satuan,id',
            'harga_satuan'    => 'required|numeric|min:0',
        ]);

        try {
            $hks = AturanHks::findOrFail($id);
            $hks->update($request->all());
            return back()->with('success', 'Data HKS berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Gagal update: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        AturanHks::findOrFail($id)->delete();
        return back()->with('success', 'Data HKS berhasil dihapus.');
    }
}
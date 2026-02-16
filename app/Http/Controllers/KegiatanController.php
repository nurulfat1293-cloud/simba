<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\RefJenisKegiatan;
use App\Models\RefTagKegiatan;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    /**
     * Menampilkan daftar semua kegiatan.
     */
    public function index()
    {
        // Eager load tagKegiatan dan jenisKegiatan untuk performa optimal
        $kegiatan = Kegiatan::with(['jenisKegiatan', 'tagKegiatan'])->latest()->paginate(10);
        return view('kegiatan.index', compact('kegiatan'));
    }

    /**
     * Menampilkan form untuk membuat kegiatan baru.
     */
    public function create()
    {
        $jenis_kegiatan = RefJenisKegiatan::all();
        $tag_kegiatan = RefTagKegiatan::all();
        return view('kegiatan.create', compact('jenis_kegiatan', 'tag_kegiatan'));
    }

    /**
     * Menyimpan kegiatan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'mata_anggaran' => 'required|string|max:50', // Validasi kolom mata anggaran
            'id_jenis_kegiatan' => 'required|exists:ref_jenis_kegiatan,id',
            'id_tag_kegiatan' => 'required|exists:ref_tag_kegiatan,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ], [
            'id_tag_kegiatan.required' => 'Kelompok kegiatan (Tag) wajib dipilih untuk keperluan honor/HKS.',
            'mata_anggaran.required' => 'Mata anggaran (Akun DIPA) wajib diisi untuk keperluan cetak SPK.'
        ]);

        $data = $request->all();
        $data['status_kegiatan'] = 'Persiapan';

        Kegiatan::create($data);

        return redirect()->route('kegiatan.index')
            ->with('success', 'Master kegiatan berhasil dibuat.');
    }

    /**
     * Menampilkan form untuk mengedit kegiatan.
     */
    public function edit($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $jenis_kegiatan = RefJenisKegiatan::all();
        $tag_kegiatan = RefTagKegiatan::all();
        return view('kegiatan.edit', compact('kegiatan', 'jenis_kegiatan', 'tag_kegiatan'));
    }

    /**
     * Memperbarui data kegiatan di database.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'mata_anggaran' => 'required|string|max:50',
            'id_jenis_kegiatan' => 'required|exists:ref_jenis_kegiatan,id',
            'id_tag_kegiatan' => 'required|exists:ref_tag_kegiatan,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'status_kegiatan' => 'required|in:Persiapan,Berjalan,Selesai',
        ]);

        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->update($request->all());

        return redirect()->route('kegiatan.index')
            ->with('success', 'Data kegiatan berhasil diperbarui.');
    }

    /**
     * Menghapus kegiatan dari database.
     */
    public function destroy($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        
        // Cek jika sudah ada relasi alokasi honor untuk mencegah data yatim
        if (method_exists($kegiatan, 'alokasiHonors') && $kegiatan->alokasiHonors()->exists()) {
            return back()->with('error', 'Kegiatan tidak bisa dihapus karena sudah memiliki data transaksi honor.');
        }

        $kegiatan->delete();
        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
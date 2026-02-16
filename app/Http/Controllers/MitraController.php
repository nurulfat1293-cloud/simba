<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use Illuminate\Http\Request;
use App\Imports\MitraImport;
use App\Exports\MitraExport; // Tambahkan import Export
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;

class MitraController extends Controller
{
    // 1. Tampilkan Daftar Mitra (Dengan Fitur Cari)
    public function index(Request $request)
    {
        $query = Mitra::latest();

        // Logika Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('asal_kecamatan', 'like', "%{$search}%")
                  ->orWhere('asal_desa', 'like', "%{$search}%");
            });
        }

        // Gunakan pagination agar halaman tidak berat jika data banyak
        $mitra = $query->paginate(10);

        return view('mitra.index', compact('mitra'));
    }

    // 2. Tampilkan Form Tambah
    public function create()
    {
        return view('mitra.create');
    }

    // 3. Simpan Data Baru
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|numeric|unique:mitra,nik',
            'nama_lengkap' => 'required',
            'no_hp' => 'required|numeric',
            'alamat' => 'required',
            'asal_kecamatan' => 'required',
            'asal_desa' => 'required',
            'nama_bank' => 'required',
            'nomor_rekening' => 'required|numeric',
        ]);

        Mitra::create($request->all());

        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil ditambahkan!');
    }

    // 4. Tampilkan Form Edit
    public function edit(string $id)
    {
        $mitra = Mitra::findOrFail($id);
        return view('mitra.edit', compact('mitra'));
    }

    // 5. Update Data
    public function update(Request $request, string $id)
    {
        $mitra = Mitra::findOrFail($id);

        $request->validate([
            'nik' => 'required|numeric|unique:mitra,nik,'.$mitra->id,
            'nama_lengkap' => 'required',
            'no_hp' => 'required',
        ]);

        $mitra->update($request->all());

        return redirect()->route('mitra.index')->with('success', 'Data Mitra berhasil diperbarui!');
    }

    // 6. Hapus Data
    public function destroy(string $id)
    {
        $mitra = Mitra::findOrFail($id);
        $mitra->delete();

        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil dihapus.');
    }

    // ==========================================
    // FITUR: IMPORT & EXPORT
    // ==========================================

    /**
     * Import Data Mitra dari Excel
     */
    public function import(Request $request) 
    {
        $request->validate([
            'file_excel' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new MitraImport, $request->file('file_excel'));
            return redirect()->route('mitra.index')->with('success', 'Data Mitra berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->route('mitra.index')->with('error', 'Gagal import data. Validasi error: ' . implode(' | ', $errorMessages));
        } catch (\Exception $e) {
            return redirect()->route('mitra.index')->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    /**
     * Export Data Mitra ke Excel
     */
    public function export() 
    {
        return Excel::download(new MitraExport, 'data_mitra_statistik.xlsx');
    }

    /**
     * Download Template Excel
     */
    public function downloadTemplate()
    {
        $filePath = storage_path('app/templates/template_import_mitra.xlsx');
        
        if (!file_exists($filePath)) {
            // Fallback CSV jika template asli tidak ditemukan
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=template_import_mitra_fallback.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
            $columns = array('nik', 'nama_lengkap', 'alamat', 'asal_kecamatan', 'asal_desa', 'no_hp', 'nama_bank', 'nomor_rekening');
            $callback = function() use ($columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        return response()->download($filePath);
    }
}
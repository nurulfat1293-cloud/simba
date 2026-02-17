<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use App\Models\Mitra;
use App\Models\Satker; 
use App\Models\AlokasiHonor; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SpkController extends Controller
{
    /**
     * Menampilkan daftar SPK dengan fitur PENCARIAN & FILTER TAHUN.
     * (Digabungkan agar fitur search di View tetap jalan)
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi Query dengan Eager Loading relasi 'mitra'
        $query = Spk::with('mitra');

        // 2. Logika Filter Tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // 3. Logika Pencarian (Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Cari berdasarkan Nomor SPK
                $q->where('nomor_spk', 'like', "%{$search}%")
                  // Atau cari berdasarkan Nama Mitra (melalui relasi)
                  ->orWhereHas('mitra', function($subQuery) use ($search) {
                      // PERBAIKAN: Hanya cari di kolom 'nama_lengkap' sesuai struktur tabel
                      $subQuery->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        // 4. Urutkan dan Pagination
        // PERBAIKAN: Mengurutkan dari Nomor Urut terkecil (1) ke terbesar (Ascending)
        $spk = $query->orderBy('nomor_urut', 'asc')
                     ->paginate(10);

        return view('spk.index', compact('spk'));
    }

    public function create()
    {
        // PERBAIKAN: Hanya mengambil mitra yang berstatus 'Aktif'
        // Sesuaikan 'status_mitra' dengan nama kolom di tabel mitra Anda (misal: 'status')
        $mitra = Mitra::where('status_mitra', 'Aktif')
                      ->orderBy('nama_lengkap', 'asc')
                      ->get();

        return view('spk.create', compact('mitra'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_mitra' => 'required|exists:mitra,id',
            'tanggal_spk' => 'required|date',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2000',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $tgl = Carbon::parse($request->tanggal_spk);

        $exists = Spk::where('id_mitra', $request->id_mitra)
                     ->where('bulan', $bulan)
                     ->where('tahun', $tahun)
                     ->exists();

        if ($exists) {
            return back()->with('error', 'Mitra ini sudah memiliki SPK untuk periode bulan ' . $bulan . ' tahun ' . $tahun)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $bulan, $tahun, $tgl) {
                $prevSpk = Spk::where('tanggal_spk', '<=', $tgl)
                             ->orderBy('tanggal_spk', 'desc')
                             ->orderBy('nomor_urut', 'desc')
                             ->first();

                $newUrut = $prevSpk ? $prevSpk->nomor_urut + 1 : 1;

                $subsequentSpks = Spk::where('nomor_urut', '>=', $newUrut)
                                     ->orderBy('nomor_urut', 'desc')
                                     ->get();
                
                foreach ($subsequentSpks as $item) {
                    $updUrut = $item->nomor_urut + 1;
                    $oldNomor = $item->nomor_spk;
                    
                    DB::table('spk')->where('nomor_spk', $oldNomor)->update([
                        'nomor_urut' => $updUrut,
                        'nomor_spk' => Spk::formatNomor($updUrut, $item->bulan, $item->tahun)
                    ]);
                }

                Spk::create([
                    'id_mitra'    => $request->id_mitra,
                    'nomor_urut'  => $newUrut,
                    'nomor_spk'   => Spk::formatNomor($newUrut, $bulan, $tahun),
                    'bulan'       => $bulan,
                    'tahun'       => $tahun,
                    'tanggal_spk' => $request->tanggal_spk
                ]);
            });

            return redirect()->route('spk.index')->with('success', 'Data SPK berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($nomor_urut)
    {
        $spk = Spk::with('mitra')->where('nomor_urut', $nomor_urut)->firstOrFail();
        $mitra = Mitra::orderBy('nama_lengkap', 'asc')->get();
        return view('spk.edit', compact('spk', 'mitra'));
    }

    public function update(Request $request, $nomor_urut)
    {
        $spk = Spk::where('nomor_urut', $nomor_urut)->firstOrFail();

        $request->validate([
            'id_mitra' => 'required|exists:mitra,id',
            'tanggal_spk' => 'required|date',
        ]);

        $tgl = Carbon::parse($request->tanggal_spk);
        $bulan = $tgl->month;
        $tahun = $tgl->year;

        // Cek duplikasi jika mitra diganti (kecuali punya sendiri)
        $exists = Spk::where('id_mitra', $request->id_mitra)
                     ->where('bulan', $bulan)
                     ->where('tahun', $tahun)
                     ->where('nomor_urut', '!=', $nomor_urut)
                     ->exists();

        if ($exists) {
            return back()->with('error', 'Mitra ini sudah memiliki SPK untuk periode tersebut.')->withInput();
        }

        try {
            DB::transaction(function () use ($spk, $request, $bulan, $tahun) {
                // Update nomor SPK jika tanggal berubah agar format bulan/tahun sesuai
                $newNomorSpk = Spk::formatNomor($spk->nomor_urut, $bulan, $tahun);
                
                $spk->update([
                    'id_mitra' => $request->id_mitra,
                    'tanggal_spk' => $request->tanggal_spk,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'nomor_spk' => $newNomorSpk
                ]);
            });

            return redirect()->route('spk.index')->with('success', 'Data SPK berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }

    public function show($nomor_urut)
    {
        // Cari SPK berdasarkan nomor_urut
        $spk = Spk::with(['mitra', 'alokasi.kegiatan', 'alokasi.jabatan'])
                  ->where('nomor_urut', $nomor_urut)
                  ->firstOrFail();

        return view('spk.show', compact('spk'));
    }

    public function destroy($nomor_urut)
    {
        try {
            DB::transaction(function () use ($nomor_urut) {
                $spk = Spk::where('nomor_urut', $nomor_urut)->firstOrFail();
                $deletedUrut = $spk->nomor_urut;
                $spk->delete();

                $subsequent = Spk::where('nomor_urut', '>', $deletedUrut)
                                 ->orderBy('nomor_urut', 'asc')
                                 ->get();
                                    
                foreach ($subsequent as $item) {
                    $newUrut = $item->nomor_urut - 1;
                    $oldNomor = $item->nomor_spk;

                    DB::table('spk')->where('nomor_spk', $oldNomor)->update([
                        'nomor_urut' => $newUrut,
                        'nomor_spk' => Spk::formatNomor($newUrut, $item->bulan, $item->tahun)
                    ]);
                }
            });

            return redirect()->route('spk.index')->with('success', 'Data SPK berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('spk.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * FITUR CETAK PDF (AUTO DETECT JABATAN - FIX)
     */
    public function print($nomor_urut)
    {
        // 1. Ambil Data SPK
        $spk = Spk::with(['mitra'])->where('nomor_urut', $nomor_urut)->firstOrFail();
        
        // 2. Ambil Satker (Manual/Dummy)
        $satker = Satker::first(); 
        if (!$satker) {
            $satker = new \stdClass();
            $satker->nama_ppk = '................';
            $satker->nama_satker = 'Badan Pusat Statistik';
            $satker->kota = '................';
        }

        // 3. AMBIL ALOKASI HONOR (FORCE MANUAL FETCH)
        $alokasi = collect();
        if (class_exists(AlokasiHonor::class)) {
             // Coba ambil berdasarkan ID SPK (Nomor SPK String)
             $alokasi = AlokasiHonor::with(['kegiatan', 'jabatan'])->where('id_spk', $spk->nomor_spk)->get();
             
             // Jika kosong, coba ambil berdasarkan Nomor Urut (Integer)
             if ($alokasi->isEmpty()) {
                 $alokasi = AlokasiHonor::with(['kegiatan', 'jabatan'])->where('id_spk', $spk->nomor_urut)->get();
             }
        }

        // 4. LOGIKA DETEKSI JABATAN (LEBIH ROBUST)
        
        // Cek PENGOLAHAN / EDCOD
        $hasPengolahan = $alokasi->contains(function ($item) {
            $namaJabatan = $item->jabatan->nama_jabatan ?? '';
            $kodeJabatan = $item->jabatan->kode_jabatan ?? ''; 
            $namaKegiatan = $item->kegiatan->nama_kegiatan ?? '';

            $keywords = ['Pengolahan', 'Edcod', 'Entri', 'Editing', 'Coding', 'Validasi', 'Verifikasi', 'Operator'];
            
            foreach ($keywords as $k) {
                if (stripos($namaJabatan, $k) !== false) return true;
                if (stripos($kodeJabatan, $k) !== false) return true;
                if (stripos($namaKegiatan, $k) !== false) return true;
            }
            return false;
        });

        // Cek PML
        $hasPml = $alokasi->contains(function ($item) {
            $namaJabatan = $item->jabatan->nama_jabatan ?? '';
            $kodeJabatan = $item->jabatan->kode_jabatan ?? '';
            
            return stripos($namaJabatan, 'PML') !== false || 
                   stripos($namaJabatan, 'Pengawas') !== false ||
                   stripos($kodeJabatan, 'PML') !== false;
        });

        // 5. Return View Sesuai Prioritas
        if ($hasPengolahan) {
            // Pastikan Anda memiliki view ini
            return view('transaksi.print_spk_edcod', compact('spk', 'alokasi', 'satker'));
        } elseif ($hasPml) {
            // Pastikan Anda memiliki view ini
            return view('transaksi.print_spk_pml', compact('spk', 'alokasi', 'satker'));
        } else {
            // Pastikan Anda memiliki view ini
            return view('transaksi.print_spk_ppl', compact('spk', 'alokasi', 'satker'));
        }
    }
}
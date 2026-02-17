<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\AturanHks;
use App\Models\Spk;
use App\Models\AlokasiHonor;
use App\Models\AturanSbml;
use App\Models\RefJabatan;
use App\Models\Satker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TransaksiController extends Controller
{
    /**
     * Menampilkan daftar alokasi honor.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $tahun = $request->query('tahun');

        $query = AlokasiHonor::with([
                'spk.mitra', 
                'kegiatan', 
                'jabatan', 
                'aturanHks.satuan'
            ]);

        if ($request->filled('tahun')) {
            $query->whereHas('spk', function($q) use ($tahun) {
                $q->where('tahun', $tahun);
            });
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('kegiatan', function($k) use ($search) {
                    $k->where('nama_kegiatan', 'like', "%{$search}%");
                })
                ->orWhereHas('spk', function($s) use ($search) {
                    $s->where('nomor_spk', 'like', "%{$search}%")
                      ->orWhereHas('mitra', function($m) use ($search) {
                          $m->where('nama_lengkap', 'like', "%{$search}%");
                      });
                });
            });
        }

        $alokasi = $query->latest()->paginate(10)->withQueryString();

        $alokasi->getCollection()->transform(function ($item) {
            if (!$item->spk && $item->id_spk) {
                $manualSpk = Spk::with('mitra')
                    ->where('nomor_urut', $item->id_spk)
                    ->orWhere('nomor_spk', $item->id_spk)
                    ->first();
                
                if ($manualSpk) {
                    $item->setRelation('spk', $manualSpk);
                }
            }
            return $item;
        });
            
        return view('transaksi.index', compact('alokasi'));
    }

    /**
     * PERBAIKAN: Method Show untuk menampilkan detail alokasi (Modal)
     */
    public function show($id)
    {
        $alokasi = AlokasiHonor::with(['kegiatan', 'jabatan', 'spk.mitra'])->find($id);

        if (!$alokasi) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Logika Fallback SPK jika relasi null
        if (!$alokasi->spk && $alokasi->id_spk) {
            $manualSpk = Spk::with('mitra')
                ->where('nomor_urut', $alokasi->id_spk)
                ->orWhere('nomor_spk', $alokasi->id_spk)
                ->first();
            if ($manualSpk) {
                $alokasi->setRelation('spk', $manualSpk);
            }
        }

        return view('transaksi.detail_modal', compact('alokasi'));
    }

    public function create()
    {
        $kegiatan = Kegiatan::whereIn('status_kegiatan', ['Persiapan', 'Berjalan'])->latest()->get();
        $jabatan = RefJabatan::all(); 
        return view('transaksi.create', compact('kegiatan', 'jabatan'));
    }

    public function getSpkByKegiatan($kegiatanId)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatanId);
        $bulanKegiatan = Carbon::parse($kegiatan->tanggal_mulai)->month;
        $tahunKegiatan = Carbon::parse($kegiatan->tanggal_mulai)->year;

        $spk = Spk::with('mitra')
            ->where('bulan', $bulanKegiatan)
            ->where('tahun', $tahunKegiatan)
            ->get();

        return response()->json($spk);
    }

    public function getHksByJabatan(Request $request)
    {
        $kegiatan = Kegiatan::findOrFail($request->id_kegiatan);
        $hks = AturanHks::with('satuan')
            ->where('id_tag_kegiatan', $kegiatan->id_tag_kegiatan)
            ->where('id_jabatan', $request->id_jabatan)
            ->first();

        if ($hks) {
            return response()->json([
                'status' => 'success',
                'id_aturan_hks' => $hks->id,
                'harga_satuan' => $hks->harga_satuan,
                'nama_satuan' => $hks->satuan ? $hks->satuan->nama_satuan : 'Kegiatan',
            ]);
        }

        return response()->json(['status' => 'not_found'], 200);
    }

    public function getHonorInfo(Request $request)
    {
        $spkInput = $request->id_spk;
        $spk = Spk::where('nomor_spk', $spkInput)
                  ->orWhere('nomor_urut', $spkInput)
                  ->first();

        if (!$spk) {
            return response()->json(['error' => 'SPK tidak ditemukan'], 404);
        }

        $kegiatan = Kegiatan::findOrFail($request->id_kegiatan);
        
        $sudah_digunakan_pokok = AlokasiHonor::whereHas('spk', function($q) use ($spk) {
                $q->where('id_mitra', $spk->id_mitra)
                  ->where('bulan', $spk->bulan)
                  ->where('tahun', $spk->tahun);
            })->selectRaw('SUM(harga_satuan_aktual * volume_target) as total_pokok')
              ->value('total_pokok') ?? 0;

        $sbml = AturanSbml::where('id_jabatan', $request->id_jabatan)
            ->where('tahun', $spk->tahun) 
            ->when($kegiatan->id_jenis_kegiatan, function($q) use ($kegiatan) {
                $q->where('id_jenis_kegiatan', $kegiatan->id_jenis_kegiatan);
            })
            ->orderBy('id', 'desc')
            ->first();

        if (!$sbml) {
            $sbml = AturanSbml::where('id_jabatan', $request->id_jabatan)
                ->where('tahun', $spk->tahun)
                ->orderBy('id', 'desc')
                ->first();
        }

        $hks = AturanHks::with('satuan')
            ->where('id_tag_kegiatan', $kegiatan->id_tag_kegiatan)
            ->where('id_jabatan', $request->id_jabatan)
            ->first();

        $limit_honor = $sbml ? (float)$sbml->batas_honor : 0;

        return response()->json([
            'limit_sbml' => $limit_honor,
            'sudah_terpakai_bulan_ini' => (float)$sudah_digunakan_pokok,
            'sisa_sbml' => $sbml ? ($limit_honor - $sudah_digunakan_pokok) : 0,
            'nama_sbml' => $sbml ? ($sbml->kategori_aturan ?? "SBML") : "Tanpa SBML",
            'hks_nilai' => $hks ? (float)$hks->harga_satuan : 0,
            'hks_satuan' => $hks && $hks->satuan ? $hks->satuan->nama_satuan : '-',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_spk' => 'required',
            'id_kegiatan' => 'required',
            'id_jabatan' => 'required',
            'volume_target' => 'required|numeric|min:0',
            'harga_input' => 'required|numeric|min:0', 
            'nilai_lain' => 'nullable|numeric|min:0',
        ]);

        $spk = Spk::where('nomor_spk', $request->id_spk)
                  ->orWhere('nomor_urut', $request->id_spk)
                  ->first();
        
        if (!$spk) return back()->withInput()->withErrors(['msg' => 'Data kontrak (SPK) tidak ditemukan.']);

        $kegiatan = Kegiatan::findOrFail($request->id_kegiatan);
        $pokok_baru = (float)$request->harga_input * (float)$request->volume_target;

        $hks = AturanHks::where('id_tag_kegiatan', $kegiatan->id_tag_kegiatan)
            ->where('id_jabatan', $request->id_jabatan)
            ->first();
            
        if ($hks && $request->harga_input > $hks->harga_satuan) {
            return back()->withInput()->withErrors(['msg' => "GAGAL! Harga input (Rp " . number_format($request->harga_input) . ") melebihi batas HKS (Rp " . number_format($hks->harga_satuan) . ")."]);
        }

        $existing_pokok = AlokasiHonor::whereHas('spk', function($q) use ($spk) {
                $q->where('id_mitra', $spk->id_mitra)
                  ->where('bulan', $spk->bulan)
                  ->where('tahun', $spk->tahun);
            })->selectRaw('SUM(harga_satuan_aktual * volume_target) as total')
              ->value('total') ?? 0;

        $sbml = AturanSbml::where('id_jabatan', $request->id_jabatan)
            ->where('tahun', $spk->tahun)
            ->orderBy('id', 'desc')
            ->first();

        if ($sbml && ($existing_pokok + $pokok_baru) > $sbml->batas_honor) {
            $sisa = $sbml->batas_honor - $existing_pokok;
            return back()->withInput()->withErrors(['msg' => "GAGAL SIMPAN! Honor Pokok kumulatif (Rp " . number_format($existing_pokok + $pokok_baru) . ") akan melebihi batas SBML bulanan (Rp " . number_format($sbml->batas_honor) . "). Sisa jatah mitra: Rp " . number_format($sisa)]);
        }

        DB::transaction(function() use ($request, $pokok_baru, $hks, $spk) {
            AlokasiHonor::create([
                'id_spk' => $spk->nomor_urut,
                'id_kegiatan' => $request->id_kegiatan,
                'id_jabatan' => $request->id_jabatan,
                'id_aturan_hks' => $hks ? $hks->id : null,
                'harga_satuan_aktual' => $request->harga_input,
                'volume_target' => $request->volume_target,
                'nilai_lain' => $request->nilai_lain ?? 0,
                'total_honor' => $pokok_baru + ($request->nilai_lain ?? 0),
                'status_pembayaran' => 'Belum',
            ]);
        });

        return redirect()->route('transaksi.index')->with('success', 'Berhasil menambahkan alokasi honor.');
    }

    public function edit($id)
    {
        $alokasi = AlokasiHonor::findOrFail($id);
        $kegiatan = Kegiatan::whereIn('status_kegiatan', ['Persiapan', 'Berjalan'])->get();
        $jabatan = RefJabatan::all();
        $currentSpk = Spk::where('nomor_urut', $alokasi->id_spk)->orWhere('nomor_spk', $alokasi->id_spk)->first();
        return view('transaksi.edit', compact('alokasi', 'kegiatan', 'jabatan', 'currentSpk'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'volume_target' => 'required|numeric|min:0',
            'harga_input' => 'required|numeric|min:0',
            'nilai_lain' => 'nullable|numeric|min:0',
        ]);

        $alokasi = AlokasiHonor::findOrFail($id);
        $spk = Spk::where('nomor_urut', $alokasi->id_spk)->orWhere('nomor_spk', $alokasi->id_spk)->first();
        $kegiatan = Kegiatan::findOrFail($alokasi->id_kegiatan);
        
        $hks = AturanHks::where('id_tag_kegiatan', $kegiatan->id_tag_kegiatan)
                        ->where('id_jabatan', $alokasi->id_jabatan)
                        ->first();

        if ($hks && $request->harga_input > $hks->harga_satuan) {
            return back()->withInput()->withErrors(['msg' => "Harga input melebihi batas HKS."]);
        }

        $pokok_baru = (float)$request->harga_input * (float)$request->volume_target;
        
        $existing_pokok_lainnya = AlokasiHonor::where('id', '!=', $id)
            ->whereHas('spk', function($q) use ($spk) {
                $q->where('id_mitra', $spk->id_mitra)
                  ->where('bulan', $spk->bulan)
                  ->where('tahun', $spk->tahun);
            })->selectRaw('SUM(harga_satuan_aktual * volume_target) as total')
              ->value('total') ?? 0;

        $sbml = AturanSbml::where('id_jabatan', $alokasi->id_jabatan)
            ->where('tahun', $spk->tahun)
            ->orderBy('id', 'desc')
            ->first();

        if ($sbml && ($existing_pokok_lainnya + $pokok_baru) > $sbml->batas_honor) {
            $sisa_sebenarnya = $sbml->batas_honor - $existing_pokok_lainnya;
            return back()->withInput()->withErrors(['msg' => "GAGAL UPDATE! Total honor bulan ini akan melebihi SBML. Maksimal honor pokok yang bisa diinput untuk baris ini adalah Rp " . number_format($sisa_sebenarnya)]);
        }

        $alokasi->update([
            'harga_satuan_aktual' => $request->harga_input,
            'volume_target' => $request->volume_target,
            'nilai_lain' => $request->nilai_lain ?? 0,
            'total_honor' => $pokok_baru + ($request->nilai_lain ?? 0),
        ]);

        return redirect()->route('transaksi.index')->with('success', 'Data honor berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $alokasi = AlokasiHonor::findOrFail($id);
        if ($alokasi->status_pembayaran !== 'Belum') {
            return back()->with('error', 'Data tidak bisa dihapus karena sudah dalam proses pembayaran.');
        }
        $alokasi->delete();
        return redirect()->route('transaksi.index')->with('success', 'Data honor berhasil dihapus.');
    }

    public function rekap(Request $request)
    {
        $query = AlokasiHonor::with(['spk.mitra', 'kegiatan.jenisKegiatan', 'jabatan']);

        if ($request->filled('search_mitra')) {
            $search = $request->search_mitra;
            $query->whereHas('spk.mitra', function($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('search_kegiatan')) {
            $searchKeg = $request->search_kegiatan;
            $query->whereHas('kegiatan', function($q) use ($searchKeg) {
                $q->where('nama_kegiatan', 'LIKE', "%{$searchKeg}%");
            });
        }

        if ($request->filled('bulan')) {
            $query->whereHas('spk', function($q) use ($request) {
                $q->where('bulan', $request->bulan);
            });
        }

        if ($request->filled('tahun')) {
            $query->whereHas('spk', function($q) use ($request) {
                $q->where('tahun', $request->tahun);
            });
        }

        $alokasiData = $query->get();

        $rekapData = $alokasiData->groupBy(function($item) {
            $mitra = $item->spk->mitra ?? null;
            return $mitra ? $mitra->nama_lengkap . ' (NIK: ' . ($mitra->nik ?? 'N/A') . ')' : 'Mitra Tidak Ditemukan';
        })->map(function($itemsByMitra) {
            
            return $itemsByMitra->groupBy(function($item) {
                return ($item->spk && $item->spk->bulan && $item->spk->tahun) 
                    ? \Carbon\Carbon::createFromDate($item->spk->tahun, $item->spk->bulan, 1)->locale('id')->translatedFormat('F Y')
                    : 'Periode Tidak Diketahui';
            })->map(function($monthlyItems) {
                
                $tahun = $monthlyItems->first()->spk->tahun ?? date('Y');
                
                $sensusItems = $monthlyItems->filter(function($i) {
                    return Str::contains(strtolower($i->kegiatan->jenisKegiatan->nama_jenis ?? ''), 'sensus');
                });

                $isSensusMode = $sensusItems->count() > 0;
                $targetItems = $isSensusMode ? $sensusItems : $monthlyItems;

                $relevantJabatanIds = $targetItems->pluck('id_jabatan')->unique();
                $relevantJenisIds = $targetItems->pluck('kegiatan.id_jenis_kegiatan')->unique();

                $sbmlMaster = AturanSbml::with('jenisKegiatan')
                    ->where('tahun', $tahun)
                    ->whereIn('id_jabatan', $relevantJabatanIds)
                    ->whereIn('id_jenis_kegiatan', $relevantJenisIds)
                    ->orderBy('batas_honor', 'desc')
                    ->first();

                if (!$sbmlMaster) {
                    $sbmlMaster = AturanSbml::with('jenisKegiatan')
                        ->where('tahun', $tahun)
                        ->whereIn('id_jabatan', $relevantJabatanIds)
                        ->orderBy('batas_honor', 'desc')
                        ->first();
                }

                $sbmlLimit = $sbmlMaster ? $sbmlMaster->batas_honor : 0;

                $totalHonorPokok = $monthlyItems->sum(function($item) {
                    return $item->volume_target * $item->harga_satuan_aktual;
                });

                $totalGantiRugi = $monthlyItems->sum('total_honor');
                $namaSbml = $sbmlMaster ? "SBML " . ($sbmlMaster->jenisKegiatan->nama_jenis ?? 'Jabatan') : "SBML Umum";

                return [
                    'detail_items' => $monthlyItems,
                    'total_honor_pokok' => $totalHonorPokok,
                    'total_ganti_rugi' => $totalGantiRugi,
                    'sbml_limit' => $sbmlLimit,
                    'sisa_kuota' => $sbmlLimit - $totalHonorPokok,
                    'status_rule' => $namaSbml
                ];
            });
        });

        return view('transaksi.rekap', compact('rekapData'));
    }

    public function printSpk($nomor_urut)
    {
        $spk = Spk::with('mitra')->where('nomor_urut', $nomor_urut)->firstOrFail();
        
        $satker = Satker::first();
        if (!$satker) {
            $satker = new \stdClass();
            $satker->nama_ppk = '................';
            $satker->nama_satker = 'Badan Pusat Statistik';
            $satker->kota = '................';
        }

        $alokasi = collect();
        if (class_exists(AlokasiHonor::class)) {
             $alokasi = AlokasiHonor::with(['kegiatan.jenisKegiatan', 'jabatan', 'aturanHks.satuan'])
                ->where('id_spk', $spk->nomor_spk)
                ->get();
             
             if ($alokasi->isEmpty()) {
                 $alokasi = AlokasiHonor::with(['kegiatan.jenisKegiatan', 'jabatan', 'aturanHks.satuan'])
                    ->where('id_spk', $spk->nomor_urut)
                    ->get();
             }
        }

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

        $hasPml = $alokasi->contains(function ($item) {
            $namaJabatan = $item->jabatan->nama_jabatan ?? '';
            $kodeJabatan = $item->jabatan->kode_jabatan ?? '';
            
            return stripos($namaJabatan, 'PML') !== false || 
                   stripos($namaJabatan, 'Pengawas') !== false ||
                   stripos($namaJabatan, 'Pemeriksa') !== false ||
                   stripos($kodeJabatan, 'PML') !== false;
        });

        $totalHonor = $alokasi->sum('total_honor');

        if ($hasPengolahan) {
            return view('transaksi.print_spk_edcod', compact('spk', 'alokasi', 'totalHonor', 'satker'));
        } elseif ($hasPml) {
            return view('transaksi.print_spk_pml', compact('spk', 'alokasi', 'totalHonor', 'satker'));
        } else {
            return view('transaksi.print_spk_ppl', compact('spk', 'alokasi', 'totalHonor', 'satker'));
        }
    }
}
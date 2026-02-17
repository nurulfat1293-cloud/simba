<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mitra;
use App\Models\Kegiatan;
use App\Models\AlokasiHonor;
use App\Models\Spk;
use App\Services\HonorariumCalculator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $calculator;

    public function __construct(HonorariumCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function index(Request $request)
    {
        // 1. Inisialisasi Filter (Casting ke Integer)
        $bulan = (int) $request->query('bulan', date('n'));
        $tahun = (int) $request->query('tahun', date('Y'));

        // 2. Statistik Utama
        $total_mitra = Mitra::count();
        $all_mitra_list = Mitra::orderBy('nama_lengkap', 'asc')->get(); // Mengambil seluruh mitra untuk dropdown
        
        $kegiatan_aktif = Kegiatan::where('status_kegiatan', 'Berjalan')
            ->whereYear('tanggal_mulai', $tahun)
            ->count();

        $total_honor = AlokasiHonor::whereHas('spk', function($query) use ($bulan, $tahun) {
            $query->where('bulan', $bulan)->where('tahun', $tahun);
        })->sum('total_honor');

        // 3. Status Digitalisasi SPK
        $total_spk = Spk::where('bulan', $bulan)->where('tahun', $tahun)->count();
        $has_folder_link = DB::table('arsip_folders')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereNotNull('link_folder')
            ->where('link_folder', '!=', '')
            ->exists();
        
        $spk_terarsip = $has_folder_link ? $total_spk : 0;
        $persen_arsip = $total_spk > 0 ? round(($spk_terarsip / $total_spk) * 100, 1) : 0;

        // 4. Top 5 Mitra (Bar Chart)
        $top_mitra_data = DB::table('alokasi_honor')
            ->join('spk', 'alokasi_honor.id_spk', '=', 'spk.nomor_urut')
            ->join('mitra', 'spk.id_mitra', '=', 'mitra.id')
            ->where('spk.bulan', $bulan)
            ->where('spk.tahun', $tahun)
            ->select(
                'mitra.nama_lengkap', 
                DB::raw('SUM(alokasi_honor.harga_satuan_aktual * alokasi_honor.volume_target) as total_pokok')
            )
            ->groupBy('mitra.id', 'mitra.nama_lengkap')
            ->orderByDesc('total_pokok')
            ->limit(5)
            ->get();

        // 5. Perhitungan SBML untuk Seluruh Mitra yang memiliki SPK di bulan terpilih
        $mitra_aktif_spk = Spk::with('mitra')->where('bulan', $bulan)->where('tahun', $tahun)->get();
        $mitra_stats_map = []; // Map untuk pencarian cepat di frontend
        $alert_sbml = [];

        foreach ($mitra_aktif_spk as $s) {
            $calc = $this->calculator->calculate($s->nomor_spk);
            if ($calc['status'] === 'success') {
                $stat = [
                    'nama' => $s->mitra->nama_lengkap ?? 'N/A',
                    'sisa_rp' => $calc['remaining'],
                    'kategori' => $calc['winning_category'],
                    'persen' => $calc['effective_sbml'] > 0 ? round(($calc['total_used'] / $calc['effective_sbml']) * 100, 1) : 0,
                    'used_rp' => $calc['total_used'],
                    'limit_rp' => $calc['effective_sbml']
                ];
                
                // Simpan ke map berdasarkan ID Mitra
                $mitra_stats_map[$s->id_mitra] = $stat;

                // Masukkan ke Alert jika persen >= 70 (Waspada/Kritis)
                if ($stat['persen'] >= 70) {
                    $alert_sbml[] = $stat;
                }
            }
        }

        return view('dashboard.index', compact(
            'total_mitra', 
            'all_mitra_list',
            'kegiatan_aktif', 
            'total_honor',
            'bulan',
            'tahun',
            'total_spk',
            'spk_terarsip',
            'persen_arsip',
            'top_mitra_data',
            'alert_sbml',
            'mitra_stats_map'
        ));
    }
}
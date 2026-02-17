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
        // 1. Inisialisasi Filter (Default: Bulan & Tahun Sekarang)
        $bulan = $request->query('bulan', date('n'));
        $tahun = $request->query('tahun', date('Y'));

        // 2. Statistik Utama (Top Cards)
        $total_mitra = Mitra::count();
        
        $kegiatan_aktif = Kegiatan::where('status_kegiatan', 'Berjalan')
            ->whereYear('tanggal_mulai', $tahun)
            ->count();

        // Total Realisasi Honor (Pokok + Lainnya) di periode terpilih
        $total_honor = AlokasiHonor::whereHas('spk', function($query) use ($bulan, $tahun) {
            $query->where('bulan', $bulan)->where('tahun', $tahun);
        })->sum('total_honor');

        // 3. Status Digitalisasi SPK (Donut Chart)
        $total_spk = Spk::where('bulan', $bulan)->where('tahun', $tahun)->count();
        $spk_terarsip = Spk::where('bulan', $bulan)->where('tahun', $tahun)
            ->whereNotNull('link_drive')
            ->where('link_drive', '!=', '')
            ->count();
        
        $persen_arsip = $total_spk > 0 ? round(($spk_terarsip / $total_spk) * 100, 1) : 0;

        // 4. Top 5 Mitra Berdasarkan Honor Pokok (Bar Chart)
        $top_mitra_data = AlokasiHonor::whereHas('spk', function($q) use ($bulan, $tahun) {
                $q->where('bulan', $bulan)->where('tahun', $tahun);
            })
            ->select('id_mitra', DB::raw('SUM(harga_satuan_aktual * volume_target) as total_pokok'))
            ->groupBy('id_mitra')
            ->orderByDesc('total_pokok')
            ->limit(5)
            ->with('mitra')
            ->get();

        // 5. Alert Box: Notifikasi Ambang Batas SBML (< 10% sisa)
        // Kita ambil mitra yang punya alokasi di bulan ini saja untuk dicheck
        $mitra_aktif_spk = Spk::where('bulan', $bulan)->where('tahun', $tahun)->get();
        $alert_sbml = [];

        foreach ($mitra_aktif_spk as $s) {
            $calc = $this->calculator->calculate($s->nomor_spk);
            if ($calc['status'] === 'success' && $calc['effective_sbml'] > 0) {
                $sisa = $calc['remaining'];
                $limit = $calc['effective_sbml'];
                $persen_sisa = ($sisa / $limit) * 100;

                if ($persen_sisa < 10) { // Jika sisa kuota kurang dari 10%
                    $alert_sbml[] = [
                        'nama' => $s->mitra->nama_lengkap ?? 'N/A',
                        'sisa_rp' => $sisa,
                        'kategori' => $calc['winning_category'],
                        'persen' => round(100 - $persen_sisa, 1)
                    ];
                }
            }
        }

        return view('dashboard.index', compact(
            'total_mitra', 
            'kegiatan_aktif', 
            'total_honor',
            'bulan',
            'tahun',
            'total_spk',
            'spk_terarsip',
            'persen_arsip',
            'top_mitra_data',
            'alert_sbml'
        ));
    }
}
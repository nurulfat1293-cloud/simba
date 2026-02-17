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
        // 1. Inisialisasi Filter (Casting ke Integer untuk menghindari TypeError pada Carbon)
        $bulan = (int) $request->query('bulan', date('n'));
        $tahun = (int) $request->query('tahun', date('Y'));

        // 2. Statistik Utama (Top Cards)
        $total_mitra = Mitra::count();
        
        $kegiatan_aktif = Kegiatan::where('status_kegiatan', 'Berjalan')
            ->whereYear('tanggal_mulai', $tahun)
            ->count();

        // Total Realisasi Honor (Pokok + Lainnya) di periode terpilih
        $total_honor = AlokasiHonor::whereHas('spk', function($query) use ($bulan, $tahun) {
            $query->where('bulan', $bulan)->where('tahun', $tahun);
        })->sum('total_honor');

        // 3. Status Digitalisasi SPK (Berdasarkan Tabel arsip_folders)
        $total_spk = Spk::where('bulan', $bulan)->where('tahun', $tahun)->count();
        
        $has_folder_link = DB::table('arsip_folders')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereNotNull('link_folder')
            ->where('link_folder', '!=', '')
            ->exists();
        
        $spk_terarsip = $has_folder_link ? $total_spk : 0;
        $persen_arsip = $total_spk > 0 ? round(($spk_terarsip / $total_spk) * 100, 1) : 0;

        // 4. PERBAIKAN: Top 5 Mitra Berdasarkan Honor Pokok (Query Builder dengan Join)
        // Menggunakan join karena id_mitra tidak ada di tabel alokasi_honor
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

        // 5. Alert Box: Notifikasi Ambang Batas SBML (< 10% sisa)
        $mitra_aktif_spk = Spk::with('mitra')->where('bulan', $bulan)->where('tahun', $tahun)->get();
        $alert_sbml = [];

        foreach ($mitra_aktif_spk as $s) {
            $calc = $this->calculator->calculate($s->nomor_spk);
            if ($calc['status'] === 'success' && $calc['effective_sbml'] > 0) {
                $sisa = $calc['remaining'];
                $limit = $calc['effective_sbml'];
                $persen_sisa = ($sisa / $limit) * 100;

                if ($persen_sisa < 10) { 
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
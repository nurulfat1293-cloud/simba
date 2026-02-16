<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mitra;
use App\Models\Kegiatan;
use App\Models\AlokasiHonor;
use App\Models\Spk;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Total Mitra Terdaftar
        $total_mitra = Mitra::count();

        // 2. Hitung Kegiatan yang Sedang Berjalan
        $kegiatan_aktif = Kegiatan::where('status_kegiatan', 'Berjalan')->count();

        // 3. Hitung Realisasi Honor Bulan Ini
        // Kita butuh filter berdasarkan SPK bulan & tahun sekarang
        $bulan_ini = date('n'); // 1-12
        $tahun_ini = date('Y');

        // Gunakan whereHas untuk filter relasi ke tabel SPK
        $total_honor = AlokasiHonor::whereHas('spk', function($query) use ($bulan_ini, $tahun_ini) {
            $query->where('bulan', $bulan_ini)->where('tahun', $tahun_ini);
        })->sum('total_honor');

        // Kirim semua variabel ke View
        return view('dashboard.index', compact('total_mitra', 'kegiatan_aktif', 'total_honor'));
    }
}
<?php

namespace App\Services;

use App\Models\AlokasiHonor; // UBAH INI: Pakai AlokasiHonor, bukan Transaksi
use App\Models\Spk;
use App\Models\Kegiatan;
use App\Models\AturanSbml;
use App\Models\RefJenisKegiatan;
use Illuminate\Support\Collection;

class HonorariumCalculator
{
    /**
     * Menghitung Batas SBML Efektif, Total Terpakai, dan Sisa Saldo
     */
    public function calculate($spkNomor, $newKegiatanId = null, $newJabatanId = null)
    {
        // 1. Ambil Data SPK
        $currentSpk = Spk::with('mitra')->where('nomor_spk', $spkNomor)->first();

        if (!$currentSpk) {
            return [
                'status' => 'error',
                'message' => 'SPK tidak ditemukan: ' . $spkNomor
            ];
        }

        $mitraId = $currentSpk->id_mitra; // Sesuaikan dengan kolom di tabel SPK (id_mitra)
        $bulan = $currentSpk->bulan;
        $tahun = $currentSpk->tahun;

        // 2. Ambil SEMUA Alokasi Honor (Transaksi) mitra di bulan & tahun yang sama
        // Menggunakan model AlokasiHonor
        $existingTransaksis = AlokasiHonor::whereHas('spk', function ($q) use ($mitraId, $bulan, $tahun) {
            $q->where('id_mitra', $mitraId)
              ->where('bulan', $bulan)
              ->where('tahun', $tahun);
        })->get();

        // 3. Buat Pool (Kumpulan) Aktivitas
        $pool = collect();

        // a. Masukkan aktivitas yang sudah ada (Dari AlokasiHonor)
        foreach ($existingTransaksis as $trx) {
            // Load manual relasi jika eager loading di atas bermasalah atau nama relasi beda
            $kegiatan = Kegiatan::with('jenisKegiatan')->find($trx->id_kegiatan);
            
            if ($kegiatan && $kegiatan->jenisKegiatan) {
                $pool->push([
                    'source' => 'existing',
                    'jenis_kegiatan_id' => $kegiatan->jenis_kegiatan_id, // Pastikan kolom ini ada di tabel kegiatan
                    'prioritas' => $kegiatan->jenisKegiatan->prioritas ?? 99,
                    'jabatan_id' => $trx->id_jabatan
                ]);
            }
        }

        // b. Masukkan aktivitas baru yang sedang diinput (Simulasi)
        if ($newKegiatanId && $newJabatanId) {
            $newKegiatan = Kegiatan::with('jenisKegiatan')->find($newKegiatanId);
            if ($newKegiatan && $newKegiatan->jenisKegiatan) {
                $pool->push([
                    'source' => 'new',
                    'jenis_kegiatan_id' => $newKegiatan->jenis_kegiatan_id,
                    'prioritas' => $newKegiatan->jenisKegiatan->prioritas ?? 99,
                    'jabatan_id' => $newJabatanId
                ]);
            }
        }

        // Jika pool kosong
        if ($pool->isEmpty()) {
            return [
                'status' => 'success',
                'effective_sbml' => 0,
                'total_used' => 0,
                'remaining' => 0,
                'winning_category' => '-',
            ];
        }

        // 4. LOGIKA UTAMA: Tentukan Prioritas Pemenang (Winning Priority)
        // Nilai terkecil menang (1 = Sensus, 2 = Survei)
        $winningPriority = $pool->min('prioritas');
        $winningPool = $pool->where('prioritas', $winningPriority);

        // 5. Cari Nilai Rupiah Tertinggi (Max SBML) dari Pool Pemenang
        $maxNominalSbml = 0;
        foreach ($winningPool as $candidate) {
            // Cari aturan SBML
            $sbmlRule = AturanSbml::where('jenis_kegiatan_id', $candidate['jenis_kegiatan_id'])
                                  ->where('jabatan_id', $candidate['jabatan_id'])
                                  ->where('tahun', $tahun)
                                  ->first();
            
            if ($sbmlRule && $sbmlRule->nominal_sbml > $maxNominalSbml) {
                $maxNominalSbml = $sbmlRule->nominal_sbml;
            }
        }

        // 6. Hitung Total Honor yang SUDAH Terpakai
        // Sesuaikan nama kolom dengan tabel alokasi_honor kamu
        // Estimasi: volume_target / volume_realisasi dan harga_satuan
        $totalUsed = $existingTransaksis->sum(function ($trx) {
            // Cek nama kolom di database, pakai fallback jika null
            $vol = $trx->volume_target ?? $trx->volume_realisasi ?? 0;
            $hrg = $trx->harga_satuan ?? 0;
            $lain = $trx->nilai_lain ?? 0; // Jika ada nilai tambah
            
            return ($vol * $hrg) + $lain;
        });

        // 7. Hitung Sisa
        $remaining = $maxNominalSbml - $totalUsed;

        // Ambil nama kategori
        $winningCategoryName = RefJenisKegiatan::where('prioritas', $winningPriority)->value('nama_jenis_kegiatan') ?? 'Standard';

        return [
            'status' => 'success',
            'mitra_name' => $currentSpk->mitra->nama_lengkap ?? '-',
            'effective_sbml' => $maxNominalSbml,
            'total_used' => $totalUsed,
            'remaining' => $remaining,
            'winning_category' => $winningCategoryName,
        ];
    }
}
<?php

namespace App\Services;

use App\Models\AlokasiHonor; // PERBAIKAN: Gunakan model AlokasiHonor
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
        // Menggunakan nomor_spk karena PK di model Spk adalah string 'nomor_spk'
        $currentSpk = Spk::with('mitra')->where('nomor_spk', $spkNomor)->first();

        if (!$currentSpk) {
            return [
                'status' => 'error',
                'message' => 'SPK tidak ditemukan: ' . $spkNomor
            ];
        }

        // PERBAIKAN: Gunakan 'id_mitra' sesuai fillable di Model Spk
        $mitraId = $currentSpk->id_mitra; 
        $bulan = $currentSpk->bulan;
        $tahun = $currentSpk->tahun;

        // 2. Ambil SEMUA Alokasi Honor (sebagai pengganti Transaksi)
        // Milik mitra tersebut, di bulan & tahun yang sama
        $existingAllocations = AlokasiHonor::whereHas('spk', function ($q) use ($mitraId, $bulan, $tahun) {
            $q->where('id_mitra', $mitraId) // PERBAIKAN: id_mitra
              ->where('bulan', $bulan)
              ->where('tahun', $tahun);
        })->get();

        // 3. Buat Pool (Kumpulan) Aktivitas
        $pool = collect();

        // a. Masukkan aktivitas yang sudah ada (Dari AlokasiHonor)
        foreach ($existingAllocations as $trx) {
            // Load manual Kegiatan jika belum ter-load
            $kegiatan = Kegiatan::with('jenisKegiatan')->find($trx->id_kegiatan);
            
            if ($kegiatan && $kegiatan->jenisKegiatan) {
                $pool->push([
                    'source' => 'existing',
                    'jenis_kegiatan_id' => $kegiatan->jenis_kegiatan_id,
                    'prioritas' => $kegiatan->jenisKegiatan->prioritas ?? 99, // Default priority rendah jika null
                    'jabatan_id' => $trx->id_jabatan
                ]);
            }
        }

        // b. Masukkan aktivitas baru yang sedang diinput user (Simulasi)
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
        // Nilai terkecil menang (Misal: 1 = Sensus, 2 = Survei)
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
        // Menggunakan kolom 'volume_target', 'harga_satuan', dan 'nilai_lain'
        $totalUsed = $existingAllocations->sum(function ($trx) {
            $vol = $trx->volume_target ?? 0;
            $hrg = $trx->harga_satuan ?? 0;
            $lain = $trx->nilai_lain ?? 0; // Nilai tambah/lain-lain
            
            return ($vol * $hrg) + $lain;
        });

        // 7. Hitung Sisa
        $remaining = $maxNominalSbml - $totalUsed;

        // Ambil nama kategori untuk UI
        $winningCategoryName = RefJenisKegiatan::where('prioritas', $winningPriority)->value('nama_jenis_kegiatan') ?? 'Standard';

        return [
            'status' => 'success',
            'mitra_name' => $currentSpk->mitra->nama_lengkap ?? '-',
            'effective_sbml' => $maxNominalSbml,
            'total_used' => $totalUsed,
            'remaining' => $remaining,
            'winning_category' => $winningCategoryName,
            'debug_pool_count' => $pool->count()
        ];
    }
}
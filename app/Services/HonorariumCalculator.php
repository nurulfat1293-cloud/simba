<?php

namespace App\Services;

use App\Models\AlokasiHonor;
use App\Models\Spk;
use App\Models\Kegiatan;
use App\Models\AturanSbml;
use App\Models\RefJenisKegiatan;
use Illuminate\Support\Collection;

class HonorariumCalculator
{
    /**
     * Menghitung Batas SBML Efektif, Total Terpakai, dan Sisa Saldo.
     * Menggunakan nama kolom sesuai struktur tabel fisik (Database).
     */
    public function calculate($spkNomor, $newKegiatanId = null, $newJabatanId = null)
    {
        // 1. Ambil Data SPK (Primary Key: nomor_spk)
        $currentSpk = Spk::with('mitra')->where('nomor_spk', $spkNomor)->first();

        if (!$currentSpk) {
            return [
                'status' => 'error',
                'message' => 'Data SPK tidak ditemukan.'
            ];
        }

        // Sesuai screenshot tabel SPK: id_mitra, bulan, tahun
        $mitraId = $currentSpk->id_mitra; 
        $bulan = $currentSpk->bulan;
        $tahun = $currentSpk->tahun;

        // 2. Ambil SEMUA Alokasi Honor mitra tersebut di bulan & tahun yang sama
        // Menggunakan id_mitra sebagai filter utama
        $existingAllocations = AlokasiHonor::whereHas('spk', function ($q) use ($mitraId, $bulan, $tahun) {
            $q->where('id_mitra', $mitraId)
              ->where('bulan', $bulan)
              ->where('tahun', $tahun);
        })->get();

        // 3. Kumpulkan Pool Aktivitas (Lama + Baru)
        $pool = collect();

        foreach ($existingAllocations as $trx) {
            $kegiatan = Kegiatan::with('jenisKegiatan')->find($trx->id_kegiatan);
            
            if ($kegiatan) {
                $pool->push([
                    'source' => 'existing',
                    'id_jenis_kegiatan' => $kegiatan->id_jenis_kegiatan, 
                    'prioritas' => $kegiatan->jenisKegiatan->prioritas ?? 99,
                    'id_jabatan' => $trx->id_jabatan 
                ]);
            }
        }

        if ($newKegiatanId && $newJabatanId) {
            $newKegiatan = Kegiatan::with('jenisKegiatan')->find($newKegiatanId);
            if ($newKegiatan) {
                $pool->push([
                    'source' => 'new',
                    'id_jenis_kegiatan' => $newKegiatan->id_jenis_kegiatan,
                    'prioritas' => $newKegiatan->jenisKegiatan->prioritas ?? 99,
                    'id_jabatan' => $newJabatanId
                ]);
            }
        }

        if ($pool->isEmpty()) {
            return [
                'status' => 'success',
                'effective_sbml' => 0,
                'total_used' => 0,
                'remaining' => 0,
                'winning_category' => '-',
            ];
        }

        // 4. Tentukan Prioritas Pemenang (Winning Priority)
        $winningPriority = $pool->min('prioritas');
        $winningPool = $pool->where('prioritas', $winningPriority);

        // 5. Cari Nilai SBML (batas_honor) dari Pool Pemenang
        $maxNominalSbml = 0;
        foreach ($winningPool as $candidate) {
            // Sesuai screenshot tabel aturan_sbml: batas_honor, id_jenis_kegiatan, id_jabatan
            $sbmlRule = AturanSbml::where('id_jenis_kegiatan', $candidate['id_jenis_kegiatan'])
                                  ->where('id_jabatan', $candidate['id_jabatan'])
                                  ->where('tahun', $tahun)
                                  ->first();
            
            if ($sbmlRule && $sbmlRule->batas_honor > $maxNominalSbml) {
                $maxNominalSbml = $sbmlRule->batas_honor;
            }
        }

        // 6. Hitung Total Honor Terpakai
        // Sesuai permintaan: pengurang murni honor saja (volume * harga), tidak termasuk nilai lain (ganti rugi).
        $totalUsed = $existingAllocations->sum(function ($trx) {
            $vol = $trx->volume_target ?? 0;
            $hrg = $trx->harga_satuan_aktual ?? 0;
            // nilai_lain tidak disertakan dalam perhitungan batas SBML
            return ($vol * $hrg);
        });

        // 7. Hitung Sisa
        $remaining = $maxNominalSbml - $totalUsed;

        // Sesuai screenshot tabel ref_jenis_kegiatan: nama_jenis
        $winningCategoryName = RefJenisKegiatan::where('prioritas', $winningPriority)->value('nama_jenis') ?? 'Standard';

        return [
            'status' => 'success',
            'mitra_name' => $currentSpk->mitra->nama_lengkap ?? '-',
            'effective_sbml' => (float)$maxNominalSbml,
            'total_used' => (float)$totalUsed,
            'remaining' => (float)$remaining,
            'winning_category' => $winningCategoryName,
        ];
    }
}
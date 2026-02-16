<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlokasiHonor extends Model
{
    protected $table = 'alokasi_honor'; 
    protected $guarded = [];

    /**
     * Matikan timestamps jika tabel alokasi_honor tidak memiliki kolom created_at/updated_at
     */
    public $timestamps = false;

    /**
     * PERBAIKAN RELASI SPK:
     * Menghubungkan id_spk (foreign key) ke nomor_spk (primary key di model Spk).
     * Pastikan kolom 'id_spk' di database Anda bertipe STRING (VARCHAR) 
     * dan berisi data yang sama persis dengan 'nomor_spk' di tabel SPK.
     */
    public function spk()
{
    // Jika id_spk di tabel alokasi_honor merujuk ke nomor_urut di tabel spk
    return $this->belongsTo(Spk::class, 'id_spk', 'nomor_urut');
    $rekapData = AlokasiHonor::with(['spk.mitra', 'kegiatan'])->get();
}

    /**
     * Relasi ke Kegiatan
     */
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan');
    }

    /**
     * Relasi ke Jabatan
     */
    public function jabatan()
    {
        return $this->belongsTo(RefJabatan::class, 'id_jabatan');
    }

    /**
     * Relasi ke Aturan HKS
     */
    public function aturanHks()
    {
        return $this->belongsTo(AturanHks::class, 'id_aturan_hks');
    }
}
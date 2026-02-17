<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spk extends Model
{
    use HasFactory;

    protected $table = 'spk';

    /**
     * Matikan timestamps karena kolom created_at/updated_at tidak ada di tabel Anda.
     */
    public $timestamps = false;

    /**
     * PERBAIKAN UTAMA:
     * Karena Anda menggunakan 'nomor_spk' (String) sebagai Primary Key,
     * Anda wajib menambahkan properti $keyType dan $incrementing.
     */
    protected $primaryKey = 'nomor_spk'; 
    protected $keyType = 'string';        // Memberitahu Laravel bahwa PK adalah string
    public $incrementing = false;        // Memberitahu Laravel bahwa PK tidak auto-increment

    protected $fillable = [
        'id_mitra', 
        'nomor_urut', 
        'nomor_spk', 
        'bulan', 
        'tahun', 
        'tanggal_spk'
    ];

    /**
     * Relasi ke model Mitra
     * Memastikan nama mitra bisa diakses melalui $spk->mitra->nama_lengkap
     */
    public function mitra()
    {
        // Berdasarkan error sebelumnya, id_mitra di tabel spk merujuk ke id di tabel mitra
        return $this->belongsTo(Mitra::class, 'id_mitra', 'id');
    }

    /**
     * Relasi ke Alokasi Honor (DIPERBAIKI)
     * Hubungan One-to-Many: 1 SPK punya Banyak Rincian Honor
     * Parameter ke-2 ('id_spk') adalah FK di tabel alokasi_honor
     * Parameter ke-3 ('nomor_urut') adalah Local Key di tabel spk (karena transaksi merujuk ke nomor urut)
     */
    public function alokasi()
    {
        // PERBAIKAN: Menggunakan 'nomor_urut' sebagai local key karena tabel alokasi_honor menyimpan nomor_urut, bukan nomor_spk string
        return $this->hasMany(AlokasiHonor::class, 'id_spk', 'nomor_urut');
    }

    /**
     * Fungsi statis untuk memformat nomor SPK
     * Format: B-[no_urut]/6103/SPK/[bulan]/[tahun]
     */
    public static function formatNomor($urut, $bulan, $tahun) 
    {
        return "B-" . $urut . "/6103/SPK/" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "/" . $tahun;
    }
}
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
     * Mengatur 'nomor_spk' sebagai Primary Key agar Laravel tidak mencari kolom 'id'.
     */
    protected $primaryKey = 'nomor_spk'; 
    protected $keyType = 'string';        // Memberitahu Laravel bahwa PK adalah string
    public $incrementing = false;         // Memberitahu Laravel bahwa PK tidak auto-increment

    protected $fillable = [
        'id_mitra', 
        'nomor_urut', 
        'nomor_spk', 
        'bulan', 
        'tahun', 
        'tanggal_spk',
        'link_drive' // Tambahan sesuai migrasi terakhir
    ];

    /**
     * Relasi ke model Mitra
     * Menggunakan 'id_mitra' di tabel spk yang merujuk ke 'id' di tabel mitra
     */
    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'id_mitra', 'id');
    }

    /**
     * Relasi ke Alokasi Honor
     * Parameter ke-2 ('id_spk'): Foreign Key di tabel alokasi_honor
     * Parameter ke-3 ('nomor_urut'): Local Key di tabel spk (sesuai catatan Anda)
     */
    public function alokasi()
    {
        return $this->hasMany(AlokasiHonor::class, 'id_spk', 'nomor_urut');
    }

    /**
     * Relasi ke Transaksi (Opsional, ditambahkan untuk kelengkapan)
     * Menghubungkan nomor_spk ke spk_id di tabel transaksi
     */
    public function transaksi()
    {
        return $this->hasMany(AlokasiHonor::class, 'spk_id', 'nomor_spk');
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
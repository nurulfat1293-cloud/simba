<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';

    protected $fillable = [
        'nama_kegiatan',
        'mata_anggaran', // Kolom baru untuk kode anggaran DIPA
        'id_jenis_kegiatan',
        'id_tag_kegiatan',
        'tanggal_mulai',
        'tanggal_akhir',
        'status_kegiatan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
    ];

    /**
     * Relasi ke Master Jenis Kegiatan.
     */
    public function jenisKegiatan()
    {
        return $this->belongsTo(RefJenisKegiatan::class, 'id_jenis_kegiatan');
    }

    /**
     * Relasi ke Master Tag Kegiatan (Pengelompokan Honor/HKS).
     * Contoh: Susenas Maret dan Susenas September memiliki Tag yang sama (Susenas).
     */
    public function tagKegiatan()
    {
        return $this->belongsTo(RefTagKegiatan::class, 'id_tag_kegiatan');
    }

    /**
     * Relasi ke Alokasi Honor.
     */
    public function alokasiHonors()
    {
        return $this->hasMany(AlokasiHonor::class, 'id_kegiatan');
    }
}
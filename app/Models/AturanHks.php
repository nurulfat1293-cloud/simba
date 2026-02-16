<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AturanHks extends Model
{
    use HasFactory;

    protected $table = 'aturan_hks';

    protected $fillable = [
        'id_tag_kegiatan',
        'id_satuan',
        'id_jabatan',
        'harga_satuan',
        'keterangan'
    ];

    /**
     * Nama relasi disesuaikan menjadi tagKegiatan agar sesuai dengan Controller & View
     */
    public function tagKegiatan()
    {
        return $this->belongsTo(RefTagKegiatan::class, 'id_tag_kegiatan');
    }

    public function satuan()
    {
        return $this->belongsTo(RefSatuan::class, 'id_satuan');
    }

    public function jabatan()
    {
        return $this->belongsTo(RefJabatan::class, 'id_jabatan');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AturanSbml extends Model
{
    protected $table = 'aturan_sbml';
    protected $guarded = ['id'];

    public function jenisKegiatan()
    {
        return $this->belongsTo(RefJenisKegiatan::class, 'id_jenis_kegiatan');
    }

    public function jabatan()
    {
        return $this->belongsTo(RefJabatan::class, 'id_jabatan');
    }
}
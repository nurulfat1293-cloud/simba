<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefSatuan extends Model
{
    use HasFactory;

    // Menghubungkan ke tabel 'ref_satuan'
    protected $table = 'ref_satuan';

    // Field yang bisa diisi (Mass Assignment)
    protected $fillable = [
        'nama_satuan',
    ];
}
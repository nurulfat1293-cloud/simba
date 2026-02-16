<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefJabatan extends Model
{
    use HasFactory;

    protected $table = 'ref_jabatan';

    // TAMBAHKAN kode_jabatan agar bisa disimpan lewat Mass Assignment
    protected $fillable = [
        'nama_jabatan',
        'kode_jabatan',
    ];
}
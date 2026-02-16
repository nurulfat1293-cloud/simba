<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peran extends Model
{
    use HasFactory;

    protected $table = 'peran';

    protected $fillable = [
        'nama_peran',
        'slug',
        'deskripsi',
    ];

    /**
     * Relasi ke model Pengguna (bukan User).
     * Menggunakan kolom 'peran' di tabel penggunas yang merujuk ke 'slug' di tabel peran.
     */
    public function penggunas()
    {
        /**
         * Menggunakan model Pengguna sesuai konfigurasi Anda.
         * Pastikan file model berada di app/Models/Pengguna.php
         */
        return $this->hasMany(\App\Models\Pengguna::class, 'peran', 'slug');
    }

    /**
     * Alias untuk relasi penggunas agar kompatibel dengan kode yang memanggil users().
     */
    public function users()
    {
        return $this->penggunas();
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefTagKegiatan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang direpresentasikan oleh model ini.
     */
    protected $table = 'ref_tag_kegiatan';

    /**
     * Kolom yang dapat diisi melalui mass-assignment.
     */
    protected $fillable = [
        'nama_tag',
    ];
    
    /**
     * Relasi ke tabel kegiatan.
     * Mengambil semua kegiatan yang memiliki tag ini.
     * Pastikan kolom 'id_tag_kegiatan' ada di tabel 'kegiatan'.
     */
    public function kegiatan()
    {
        // PERBAIKAN: Gunakan 'id_tag_kegiatan' sebagai foreign key
        return $this->hasMany(Kegiatan::class, 'id_tag_kegiatan');
    }
}
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use Notifiable;

    protected $table = 'users'; 
    protected $guarded = ['id'];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Relasi ke model Peran.
     * Fungsi ini harus berada di dalam class Pengguna.
     */
    public function role()
    {
        return $this->belongsTo(Peran::class, 'peran', 'id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsipFolder extends Model
{
    protected $table = 'arsip_folders';
    
    protected $fillable = [
        'bulan',
        'tahun',
        'link_folder'
    ];
}
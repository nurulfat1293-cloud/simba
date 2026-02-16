<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdTagKegiatanToKegiatanTable extends Migration
{
    /**
     * Jalankan migrasi untuk menambahkan kolom id_tag_kegiatan.
     * Kolom ini berfungsi sebagai kunci tamu (foreign key) ke tabel ref_tag_kegiatan.
     */
    public function up()
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            // Menambahkan kolom id_tag_kegiatan setelah id_jenis_kegiatan
            $table->foreignId('id_tag_kegiatan')
                  ->nullable() // Dibuat nullable agar data lama tidak error saat migrasi
                  ->after('id_jenis_kegiatan')
                  ->constrained('ref_tag_kegiatan')
                  ->onDelete('set null'); // Jika tag dihapus, kolom di kegiatan menjadi null
        });
    }

    /**
     * Membatalkan migrasi (Rollback).
     */
    public function down()
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->dropForeign(['id_tag_kegiatan']);
            $table->dropColumn('id_tag_kegiatan');
        });
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     * Menggunakan Raw SQL untuk memastikan konversi dari ENUM ke BIGINT berhasil.
     */
    public function up(): void
    {
        // Menggunakan pernyataan manual agar MySQL tidak terhambat batasan ENUM saat perubahan tipe
        DB::statement("ALTER TABLE users MODIFY COLUMN peran BIGINT UNSIGNED NULL");
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        // Kembalikan ke format ENUM awal jika diperlukan
        DB::statement("ALTER TABLE users MODIFY COLUMN peran ENUM('admin', 'ppk', 'subject_matter') DEFAULT 'subject_matter'");
    }
};
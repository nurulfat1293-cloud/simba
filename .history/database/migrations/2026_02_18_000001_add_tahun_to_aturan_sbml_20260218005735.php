<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek apakah tabel ada untuk menghindari error
        if (Schema::hasTable('aturan_sbml')) {
            Schema::table('aturan_sbml', function (Blueprint $table) {
                // Cek apakah kolom 'tahun' belum ada sebelum menambahkannya
                if (!Schema::hasColumn('aturan_sbml', 'tahun')) {
                    // Tambahkan kolom tahun setelah id_jabatan
                    // Kita beri default value tahun sekarang agar data lama (jika ada) tidak error/kosong
                    $table->year('tahun')->after('id_jabatan')->default(date('Y'));
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('aturan_sbml')) {
            Schema::table('aturan_sbml', function (Blueprint $table) {
                if (Schema::hasColumn('aturan_sbml', 'tahun')) {
                    $table->dropColumn('tahun');
                }
            });
        }
    }
};
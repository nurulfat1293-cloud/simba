<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('kegiatan', function (Blueprint $table) {
        // Menambahkan foreign key yang mengacu ke tabel ref_tag_kegiatan
        $table->foreignId('id_tag_kegiatan')->after('id_jenis_kegiatan')->constrained('ref_tag_kegiatan')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            //
        });
    }
};

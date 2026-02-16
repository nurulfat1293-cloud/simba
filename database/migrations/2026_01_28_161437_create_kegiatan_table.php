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
    Schema::create('kegiatan', function (Blueprint $table) {
        $table->id();
        $table->string('nama_kegiatan');
        // Foreign Key ke ref_jenis_kegiatan
        $table->foreignId('id_jenis_kegiatan')->constrained('ref_jenis_kegiatan');
        $table->date('tanggal_mulai');
        $table->date('tanggal_akhir');
        $table->enum('status_kegiatan', ['Persiapan', 'Berjalan', 'Selesai']);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};

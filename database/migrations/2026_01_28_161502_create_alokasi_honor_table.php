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
    Schema::create('alokasi_honor', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_spk')->constrained('spk')->onDelete('cascade');
        $table->foreignId('id_kegiatan')->constrained('kegiatan');
        $table->foreignId('id_jabatan')->constrained('ref_jabatan');
        $table->foreignId('id_aturan_hks')->constrained('aturan_hks');
        $table->integer('volume_target');
        $table->decimal('total_honor', 12, 2);
        $table->enum('status_pembayaran', ['Belum', 'Proses', 'Terbayar'])->default('Belum');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alokasi_honor');
    }
};

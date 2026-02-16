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
    Schema::create('aturan_hks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_kegiatan')->constrained('kegiatan')->onDelete('cascade');
        $table->foreignId('id_satuan')->constrained('ref_satuan');
        $table->decimal('harga_satuan', 12, 2); // Hingga ratusan miliar
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aturan_hks');
    }
};

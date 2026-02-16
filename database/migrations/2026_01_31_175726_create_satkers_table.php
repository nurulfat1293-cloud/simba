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
        Schema::create('satker', function (Blueprint $table) {
            $table->id();
            $table->string('nama_satker');
            $table->string('kode_satker', 20)->unique();
            $table->text('alamat_lengkap');
            $table->string('kota'); // Digunakan untuk lokasi tanda tangan (contoh: Jakarta, 01 Feb 2026)
            $table->string('nama_ppk')->nullable(); // Opsional: Untuk nama Pejabat Pembuat Komitmen
            $table->string('nip_ppk', 30)->nullable(); // Opsional: Untuk NIP PPK
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satker');
    }
};
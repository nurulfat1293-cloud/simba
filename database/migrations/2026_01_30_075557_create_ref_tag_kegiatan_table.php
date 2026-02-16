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
    Schema::create('ref_tag_kegiatan', function (Blueprint $table) {
        $table->id();
        $table->string('nama_tag')->unique(); // Contoh: Susenas, Sakernas
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_tag_kegiatan');
    }
};

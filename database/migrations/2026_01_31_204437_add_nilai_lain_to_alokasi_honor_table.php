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
        Schema::table('alokasi_honor', function (Blueprint $table) {
    $table->decimal('nilai_lain', 15, 2)->default(0)->after('volume_target');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alokasi_honor', function (Blueprint $table) {
            //
        });
    }
};

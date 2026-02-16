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
    Schema::table('alokasi_honor', function (Blueprint $table) {
        $table->decimal('harga_satuan_aktual', 15, 2)->after('id_aturan_hks')->comment('Harga satuan hasil input manual');
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

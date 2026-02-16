<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('arsip_folders', function (Blueprint $table) {
            $table->id();
            $table->integer('bulan');
            $table->integer('tahun');
            $table->text('link_folder')->nullable(); // Link Google Drive Folder
            $table->timestamps();

            // Mencegah duplikasi periode
            $table->unique(['bulan', 'tahun']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('arsip_folders');
    }
};
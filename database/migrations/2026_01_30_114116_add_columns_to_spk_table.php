<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Menggunakan anonymous class agar tidak terjadi konflik nama class
return new class extends Migration
{
    /**
     * Menambahkan kolom ke tabel spk yang sudah ada.
     */
    public function up()
    {
        Schema::table('spk', function (Blueprint $table) {
            // Cek jika kolom belum ada sebelum menambahkan (safety check)
            // Menghapus 'after(id)' karena kolom 'id' tidak ditemukan di tabel spk
            if (!Schema::hasColumn('spk', 'id_mitra')) {
                $table->foreignId('id_mitra')->constrained('mitra')->onDelete('cascade');
            }
            if (!Schema::hasColumn('spk', 'nomor_urut')) {
                $table->integer('nomor_urut');
            }
            if (!Schema::hasColumn('spk', 'nomor_spk')) {
                $table->string('nomor_spk')->unique();
            }
            if (!Schema::hasColumn('spk', 'bulan')) {
                $table->integer('bulan');
            }
            if (!Schema::hasColumn('spk', 'tahun')) {
                $table->integer('tahun');
            }
            if (!Schema::hasColumn('spk', 'tanggal_spk')) {
                $table->date('tanggal_spk');
            }

            // Menambahkan constraint unik agar 1 mitra hanya punya 1 SPK per bulan/tahun
            $table->unique(['id_mitra', 'bulan', 'tahun'], 'mitra_spk_unique');
        });
    }

    /**
     * Membatalkan perubahan (Rollback).
     */
    public function down()
    {
        Schema::table('spk', function (Blueprint $table) {
            $table->dropUnique('mitra_spk_unique');
            $table->dropColumn(['id_mitra', 'nomor_urut', 'nomor_spk', 'bulan', 'tahun', 'tanggal_spk']);
        });
    }
};
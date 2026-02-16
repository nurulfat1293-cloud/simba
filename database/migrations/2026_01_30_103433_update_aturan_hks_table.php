<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateAturanHksTable extends Migration
{
    /**
     * Jalankan migrasi untuk mengubah struktur tabel aturan_hks.
     */
    public function up()
    {
        Schema::table('aturan_hks', function (Blueprint $table) {
            // Menggunakan cara yang lebih aman: 
            // Cek apakah foreign key ada sebelum dihapus, 
            // atau langsung drop kolom jika Anda yakin DB tidak mengunci constraint-nya.
            
            // Jika SQL error "Can't DROP FOREIGN KEY", kita bisa langsung hapus kolomnya
            // Banyak driver database akan menghapus constraint secara otomatis saat kolom dihapus
            $table->dropColumn('id_kegiatan');

            // Tambahkan kolom baru id_tag_kegiatan
            $table->foreignId('id_tag_kegiatan')
                  ->after('id')
                  ->constrained('ref_tag_kegiatan')
                  ->onDelete('cascade');

            // Tambahkan kolom baru id_jabatan
            $table->foreignId('id_jabatan')
                  ->after('id_satuan')
                  ->constrained('ref_jabatan')
                  ->onDelete('cascade');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down()
    {
        Schema::table('aturan_hks', function (Blueprint $table) {
            $table->dropForeign(['id_tag_kegiatan']);
            $table->dropColumn('id_tag_kegiatan');
            
            $table->dropForeign(['id_jabatan']);
            $table->dropColumn('id_jabatan');

            // Kembalikan kolom id_kegiatan (sesuaikan dengan nama tabel aslinya, misal 'kegiatan')
            $table->foreignId('id_kegiatan')->after('id')->nullable();
        });
    }
}
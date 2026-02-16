<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReferensiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Isi Jenis Kegiatan
        DB::table('ref_jenis_kegiatan')->insert([
            ['nama_jenis' => 'Sensus', 'prioritas' => 1],
            ['nama_jenis' => 'Survei', 'prioritas' => 2],
        ]);

        // 2. Isi Jabatan
        DB::table('ref_jabatan')->insert([
            ['nama_jabatan' => 'PPL', 'kode_jabatan' => 'PPL'],
            ['nama_jabatan' => 'PML', 'kode_jabatan' => 'PML'],
            ['nama_jabatan' => 'KOSEKA', 'kode_jabatan' => 'KSK'],
        ]);

        // 3. Isi Satuan
        DB::table('ref_satuan')->insert([
            ['nama_satuan' => 'Dokumen'],
            ['nama_satuan' => 'Blok Sensus'],
            ['nama_satuan' => 'Rumah Tangga'],
            ['nama_satuan' => 'Orang/Bulan'],
        ]);

        // 4. Buat Akun Admin (Ke tabel 'users' yang sudah benar)
        DB::table('users')->insert([
            'nama_lengkap' => 'Administrator BPS', // Sesuai kolom migrasi baru
            'email' => 'admin@bps.go.id',
            'password' => Hash::make('password123'),
            'peran' => 'admin', // Kolom ini sekarang SUDAH ADA di migrasi
        ]);
        
        // 5. Buat Akun Subject Matter
        DB::table('users')->insert([
            'nama_lengkap' => 'Staff Subject Matter',
            'email' => 'staff@bps.go.id',
            'password' => Hash::make('password123'),
            'peran' => 'subject_matter', // Kolom ini sekarang SUDAH ADA di migrasi
        ]);
    }
}
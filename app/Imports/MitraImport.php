<?php

namespace App\Imports;

use App\Models\Mitra;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows; // Tambahkan ini

class MitraImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Pastikan NIK ada isinya sebelum insert
        if (!isset($row['nik']) || empty($row['nik'])) {
            return null;
        }

        return new Mitra([
            // Pastikan key array sesuai dengan header di Excel (lowercase)
            'nik'            => $row['nik'], 
            'nama_lengkap'   => $row['nama_lengkap'],
            'alamat'         => $row['alamat'] ?? '-', // Default value jika kosong
            'asal_kecamatan' => $row['asal_kecamatan'] ?? '-',
            'asal_desa'      => $row['asal_desa'] ?? '-',
            'no_hp'          => $row['no_hp'],
            'nama_bank'      => $row['nama_bank'] ?? 'BRI', // Default bank
            'nomor_rekening' => $row['nomor_rekening'],
            'status_aktif'   => 1,
        ]);
    }

    /**
     * Rules validasi untuk setiap baris excel
     */
    public function rules(): array
    {
        return [
            // Gunakan '*.field' untuk validasi baris
            'nik' => 'required|numeric|unique:mitra,nik',
            'nama_lengkap' => 'required',
            'no_hp' => 'required',
        ];
    }
}
<?php

namespace App\Exports;

use App\Models\Mitra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MitraExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * Mengambil data dari database
    */
    public function collection()
    {
        return Mitra::select(
            'nik',
            'nama_lengkap',
            'alamat',
            'asal_kecamatan',
            'asal_desa',
            'no_hp',
            'nama_bank',
            'nomor_rekening',
            'status_aktif'
        )->get()->map(function($mitra) {
            // Ubah status 1/0 jadi teks agar mudah dibaca di Excel
            $mitra->status_aktif = $mitra->status_aktif ? 'Aktif' : 'Non-Aktif';
            return $mitra;
        });
    }

    /**
    * Menambahkan Header pada file Excel
    */
    public function headings(): array
    {
        return [
            'NIK',
            'Nama Lengkap',
            'Alamat Domisili',
            'Kecamatan',
            'Desa/Kelurahan',
            'No. HP / WA',
            'Nama Bank',
            'Nomor Rekening',
            'Status'
        ];
    }

    /**
    * Styling sederhana (Bold Header)
    */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
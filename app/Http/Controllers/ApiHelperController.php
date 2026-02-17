<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HonorariumCalculator;

class ApiHelperController extends Controller
{
    protected $calculator;

    public function __construct(HonorariumCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function checkHonorLimit(Request $request)
    {
        // PERBAIKAN: Gunakan nomor_spk sebagai acuan validasi
        $request->validate([
            'spk_id' => 'required|exists:spk,nomor_spk', // Cek ke kolom nomor_spk
            'kegiatan_id' => 'nullable|exists:kegiatan,id', 
            'jabatan_id' => 'nullable|exists:ref_jabatan,id',
        ]);

        try {
            $result = $this->calculator->calculate(
                $request->spk_id, // Ini berisi nomor_spk (string)
                $request->kegiatan_id,
                $request->jabatan_id
            );

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
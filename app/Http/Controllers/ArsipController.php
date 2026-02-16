<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use App\Models\ArsipFolder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Periode Unik dari SPK
        $query = Spk::selectRaw('bulan, tahun, count(*) as total_dokumen')
            ->groupBy('bulan', 'tahun')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc');

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $periodes = $query->paginate(10);

        // 2. Ambil Data Link Folder yang sudah tersimpan
        $savedLinks = ArsipFolder::all()->keyBy(function($item) {
            return $item->bulan . '-' . $item->tahun;
        });

        // 3. Inject Link ke Collection Periode
        $periodes->getCollection()->transform(function($item) use ($savedLinks) {
            $key = $item->bulan . '-' . $item->tahun;
            $item->link_folder = $savedLinks[$key]->link_folder ?? null;
            return $item;
        });

        return view('arsip.index', compact('periodes'));
    }

    public function updateLink(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
            'link_folder' => 'nullable|url'
        ]);

        ArsipFolder::updateOrCreate(
            [
                'bulan' => $request->bulan,
                'tahun' => $request->tahun
            ],
            [
                'link_folder' => $request->link_folder
            ]
        );

        return redirect()->back()->with('success', 'Link folder arsip berhasil disimpan.');
    }
}
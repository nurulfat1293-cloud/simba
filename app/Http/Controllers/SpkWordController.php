<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use App\Models\Mitra;
use App\Models\Satker;
use App\Models\AlokasiHonor; 
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class SpkWordController extends Controller
{
    public function download($id)
    {
        // 1. CARI DATA SPK
        // PERBAIKAN: Tambahkan 'alokasi.aturanHks.satuan' di sini agar data satuan terambil
        $spk = Spk::with([
            'mitra', 
            'alokasi.kegiatan', 
            'alokasi.aturanHks.satuan' 
        ])->where('nomor_urut', $id)->first();
        
        if (!$spk) {
            $spk = Spk::with([
                'mitra', 
                'alokasi.kegiatan', 
                'alokasi.aturanHks.satuan'
            ])->where('nomor_spk', $id)->firstOrFail();
        }

        $mitra = $spk->mitra;
        $alokasi = $spk->alokasi;

        // 2. LOGIKA CERDAS PENGAMBILAN ALOKASI (FALLBACK)
        if ($alokasi->isEmpty() && class_exists(AlokasiHonor::class)) {
            // PERBAIKAN: Tambahkan 'aturanHks.satuan' di sini juga
            $alokasi = AlokasiHonor::with(['kegiatan', 'aturanHks.satuan'])
                        ->where('id_spk', $spk->nomor_spk)->get();
            
            if ($alokasi->isEmpty()) {
                $alokasi = AlokasiHonor::with(['kegiatan', 'aturanHks.satuan'])
                            ->where('id_spk', $spk->nomor_urut)->get();
            }
        }

        // 3. AMBIL DATA SATKER
        $satker = null;
        if (class_exists(Satker::class)) {
            $satker = Satker::first();
        }
        if (!$satker) {
            $satker = new \stdClass();
            $satker->nama_ppk = '................';
            $satker->nama_satker = 'Badan Pusat Statistik';
            $satker->kota = '................';
        }

        // --- FORMATTING TEXT ---
        
        $rawNamaPpk = $satker->nama_ppk ?? '................';
        $namaPpkProper = ucwords(strtolower($rawNamaPpk), " \t\r\n\f\v.,"); 

        $namaSatkerFull = trim($satker->nama_satker ?? 'BADAN PUSAT STATISTIK');
        $cleanNamaSatker = trim(preg_replace('/\s+/', ' ', $namaSatkerFull));
        $partsSatker = explode(' ', $cleanNamaSatker);
        $duaKataTerakhir = (count($partsSatker) >= 2) ? implode(' ', array_slice($partsSatker, -2)) : $cleanNamaSatker;
        $kabupatenProper = ucwords(strtolower($duaKataTerakhir));
        
        // 4. LOAD TEMPLATE
        $path = storage_path('app/templates/template_spk_ppl.docx');
        if (!file_exists($path)) {
            $path = storage_path('app/public/template_spk_ppl.docx');
            if (!file_exists($path)) {
                return back()->with('error', 'Template Word tidak ditemukan.');
            }
        }
        $template = new TemplateProcessor($path);

        // 5. DATA WAKTU
        Carbon::setLocale('id');
        $tgl_spk = Carbon::parse($spk->tanggal_spk);
        $hariIndo = $this->getHariIndo($tgl_spk->format('l'));
        $bulanIndo = $this->getBulanIndo($tgl_spk->format('F'));

        // Hitung Tanggal Kontrak
        $maxTanggal = $alokasi->max(function($item) { return $item->kegiatan->tanggal_akhir ?? null; });
        $minTanggal = $alokasi->min(function($item) { return $item->kegiatan->tanggal_mulai ?? null; });
        
        $tglAkhirKontrak = $maxTanggal ? Carbon::parse($maxTanggal) : Carbon::now();
        $tglAwalKontrak = $minTanggal ? Carbon::parse($minTanggal) : Carbon::now();

        // 6. HITUNG TOTAL UANG
        $totalHonorPokok = $alokasi->sum(function($item) {
            return $item->volume_target * $item->harga_satuan_aktual;
        });
        $totalNilaiLain = 0;
        if ($alokasi->count() > 0 && isset($alokasi->first()->nilai_lain)) {
             $totalNilaiLain = $alokasi->sum('nilai_lain');
        }
        $totalDenda = $totalHonorPokok + $totalNilaiLain;

        // --- REPLACE VARIABLES ---

        // A. Header & Identitas
        $template->setValue('nomor_spk', $spk->nomor_spk);
        $template->setValue('hari', $hariIndo);
        $template->setValue('tanggal_terbilang', $this->terbilang($tgl_spk->day));
        $template->setValue('bulan', $bulanIndo); 
        $template->setValue('tahun_terbilang', $this->terbilang($tgl_spk->year)); 
        $template->setValue('tanggal_spk', $tgl_spk->isoFormat('D MMMM Y')); 
        $template->setValue('tahun', $tgl_spk->year); 

        // Lokasi
        $template->setValue('kota', ucwords(strtolower($satker->kota ?? ''))); 
        $template->setValue('kabupaten', $kabupatenProper);
        // Alias Tambahan
        $template->setValue('alamat_satker', $kabupatenProper);
        $template->setValue('lokasi_satker', $kabupatenProper);

        // Pihak
        $template->setValue('nama_ppk', $namaPpkProper);
        $template->setValue('nama_satker', ucwords(strtolower($namaSatkerFull)));
        $template->setValue('nama_satker_upper', strtoupper($namaSatkerFull));
        
        $template->setValue('nama_lengkap', $mitra->nama_lengkap);
        $template->setValue('asal_desa', ucwords(strtolower($mitra->asal_desa ?? '')));
        $template->setValue('asal_kecamatan', ucwords(strtolower($mitra->asal_kecamatan ?? '')));
        
        // B. Pasal 3 (Jangka Waktu)
        $template->setValue('tanggal_akhir', $tglAkhirKontrak->isoFormat('D MMMM Y'));
        $template->setValue('tanggal_awal', $tglAwalKontrak->isoFormat('D MMMM Y'));
        $template->setValue('jangka_waktu_kontrak', $tglAwalKontrak->isoFormat('D MMMM') . ' s.d ' . $tglAkhirKontrak->isoFormat('D MMMM Y'));

        // C. Pasal 6 & Lampiran (Honor & Terbilang)
        $txtTerbilangHonor = $this->terbilang($totalHonorPokok) . ' Rupiah';
        
        $template->setValue('totalhonorpokok', number_format($totalHonorPokok, 0, ',', '.'));
        $template->setValue('terbilang_honor', $txtTerbilangHonor); 
        $template->setValue('terbilang_total', $txtTerbilangHonor);
        $template->setValue('terbilang', $txtTerbilangHonor); 
        $template->setValue('huruf', $txtTerbilangHonor);

        // D. Pasal 7 (Asuransi)
        $bulanSpkNama = $this->getBulanIndo(Carbon::create()->month($spk->bulan)->format('F'));
        $template->setValue('bulan_spk', $bulanSpkNama);
        $template->setValue('tahun_spk', $spk->tahun);
        $template->setValue('bulan_kegiatan', $bulanSpkNama); 
        $template->setValue('tahun_kegiatan', $spk->tahun); 

        // E. Pasal 11 (Denda)
        $txtTerbilangDenda = $this->terbilang($totalDenda) . ' Rupiah';
        
        $template->setValue('totaldenda', number_format($totalDenda, 0, ',', '.'));
        $template->setValue('terbilang_denda', $txtTerbilangDenda);
        $template->setValue('terbilang_total_denda', $txtTerbilangDenda);

        // 8. TABEL LAMPIRAN
        $rows = [];
        if ($alokasi->count() > 0) {
            foreach($alokasi as $index => $row) {
                $honorItem = $row->volume_target * $row->harga_satuan_aktual;
                $start = Carbon::parse($row->kegiatan->tanggal_mulai ?? now());
                $end = Carbon::parse($row->kegiatan->tanggal_akhir ?? now());

                if ($start->format('mY') == $end->format('mY')) {
                    $str_waktu = $start->format('d') . ' - ' . $end->format('d') . ' ' . $this->getBulanIndo($end->format('F')) . ' ' . $end->format('Y');
                } else {
                    $str_waktu = $start->format('d M') . ' - ' . $end->format('d M Y');
                }

                // --- PERBAIKAN LOGIKA SATUAN ---
                // Mengakses relasi: AlokasiHonor -> AturanHks -> Satuan
                // Pastikan model AlokasiHonor punya relasi 'aturanHks'
                // dan model AturanHks punya relasi 'satuan'
                $namaSatuan = $row->aturanHks->satuan->nama_satuan ?? 'Dokumen'; // Fallback 'Dokumen' jika null

                $rows[] = [
                    'no'                   => $index + 1,
                    'nama_kegiatan'        => $row->kegiatan->nama_kegiatan ?? '-',
                    'uraian_tugas'         => $row->kegiatan->nama_kegiatan ?? '-', 
                    'uraian'               => $row->kegiatan->nama_kegiatan ?? '-', 
                    'jangka_waktu'         => $str_waktu,
                    'waktu'                => $str_waktu,
                    'volume_target'        => $row->volume_target,
                    'vol'                  => $row->volume_target,
                    
                    // Gunakan variabel hasil relasi di atas
                    'satuan'               => $namaSatuan,
                    'sat'                  => $namaSatuan,
                    
                    'harga_satuan_aktual'  => number_format($row->harga_satuan_aktual, 0, ',', '.'),
                    'harga_satuan'         => number_format($row->harga_satuan_aktual, 0, ',', '.'),
                    'harga'                => number_format($row->harga_satuan_aktual, 0, ',', '.'),
                    'honor'                => number_format($honorItem, 0, ',', '.'),
                    'total_honor'          => number_format($honorItem, 0, ',', '.'), 
                    'total'                => number_format($honorItem, 0, ',', '.'), 
                    'mata_anggaran'        => $row->kegiatan->mata_anggaran ?? '-',
                    'beban_anggaran'       => $row->kegiatan->mata_anggaran ?? '-',
                ];
            }
            $template->cloneRowAndSetValues('no', $rows);
        } else {
            $keys = ['no', 'nama_kegiatan', 'uraian_tugas', 'jangka_waktu', 'volume_target', 'satuan', 'harga_satuan_aktual', 'honor', 'mata_anggaran'];
            foreach($keys as $key) {
                $template->setValue($key, '');
            }
        }

        // Footer Tabel
        $template->setValue('total_honor', number_format($totalHonorPokok, 0, ',', '.'));
        $template->setValue('terbilang_honor', $txtTerbilangHonor);
        $template->setValue('terbilang', $txtTerbilangHonor);
        
        // 9. DOWNLOAD
        $safeName = preg_replace('/[^A-Za-z0-9\-]/', '_', $mitra->nama_lengkap);
        $filename = 'SPK_' . $safeName . '.docx';
        $temp_file = storage_path('app/public/' . $filename);
        $template->saveAs($temp_file);

        return response()->download($temp_file)->deleteFileAfterSend(true);
    }

    // --- HELPER FUNCTIONS ---
    private function getHariIndo($day) {
        $hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        return $hari[$day] ?? $day;
    }
    private function getBulanIndo($month) {
        $bulan = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
        return $bulan[$month] ?? $month;
    }
    private function penyebut($nilai) {
        $nilai = abs($nilai);
        // PERBAIKAN: Hapus "return Nol" di sini agar tidak muncul di tengah kalimat
        
        $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $temp = "";
        if ($nilai < 12) { $temp = " ". $huruf[$nilai]; } 
        else if ($nilai < 20) { $temp = $this->penyebut($nilai - 10). " Belas"; } 
        else if ($nilai < 100) { $temp = $this->penyebut(floor($nilai/10))." Puluh". $this->penyebut($nilai % 10); } 
        else if ($nilai < 200) { $temp = " Seratus" . $this->penyebut($nilai - 100); } 
        else if ($nilai < 1000) { $temp = $this->penyebut(floor($nilai/100)) . " Ratus" . $this->penyebut($nilai % 100); } 
        else if ($nilai < 2000) { $temp = " Seribu" . $this->penyebut($nilai - 1000); } 
        else if ($nilai < 1000000) { $temp = $this->penyebut(floor($nilai/1000)) . " Ribu" . $this->penyebut($nilai % 1000); } 
        else if ($nilai < 1000000000) { $temp = $this->penyebut(floor($nilai/1000000)) . " Juta" . $this->penyebut($nilai % 1000000); }
        return $temp;
    }
    private function terbilang($nilai) {
        if ($nilai == 0) return "Nol";
        if ($nilai < 0) { $hasil = "minus ". trim($this->penyebut($nilai)); } 
        else { $hasil = trim($this->penyebut($nilai)); }
        return ucwords($hasil);
    }
}
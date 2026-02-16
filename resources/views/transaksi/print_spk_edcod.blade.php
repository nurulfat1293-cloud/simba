<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SPK Pengolahan - {{ $spk->nomor_spk ?? 'Preview' }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* CSS KHUSUS UNTUK CETAK/PDF */
        @media print {
            @page { 
                size: A4 portrait; 
                margin: 2.5cm; 
            }

            @page landscape-page {
                size: A4 landscape;
                margin: 2.5cm 1cm; 
            }

            body { 
                background-color: #fff !important; 
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .page-container { 
                box-shadow: none !important; 
                margin: 0 !important; 
                width: 100% !important;
                padding: 0 !important; 
                border: none !important;
                min-height: auto !important;
                display: block !important;
            }

            .no-print { display: none !important; }

            .landscape-section {
                page: landscape-page;
                width: 100% !important;
                break-before: page;
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .table-lampiran {
                width: 100% !important;
                border-collapse: collapse !important;
                table-layout: fixed !important; 
                font-size: 11pt !important; 
                margin-top: 0 !important;
                page-break-inside: auto !important;
            }

            .table-lampiran th, 
            .table-lampiran td {
                padding: 4px 6px !important; 
                line-height: 1.2 !important;
                border: 1px solid #000 !important;
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
                word-break: break-all !important; 
            }

            .table-lampiran tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .break-pasal-7 {
                page-break-before: always;
            }

            .landscape-section .header-lampiran {
                margin-bottom: 5px !important;
            }
            .landscape-section h4 {
                margin-bottom: 10px !important;
            }
        }
        
        /* CSS TAMPILAN LAYAR */
        body { 
            font-family: Cambria, Georgia, serif; 
            background-color: #e2e8f0;
            margin: 0;
            padding: 40px 0;
            color: #000;
        }
        
        .page-container {
            background: white;
            width: 210mm; 
            min-height: 297mm; 
            margin: 0 auto 30px auto;
            padding: 2.5cm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
            box-sizing: border-box;
        }

        .page-container.landscape {
            width: 297mm; 
            min-height: 210mm; 
            padding: 2.5cm 1cm; 
        }

        p { margin-bottom: 10px; line-height: 1.5; text-align: justify; font-size: 11pt; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-justify { text-align: justify; }
        
        table { width: 100%; border-collapse: collapse; font-size: 11pt; }
        .table-data td { vertical-align: top; padding: 2px 0; }
        
        .col-no { width: 25px; }
        .col-nama { width: 35%; } 
        .col-sep { width: 10px; text-align: center; }
        .col-desc { width: auto; text-align: justify; }

        .table-bordered th, .table-bordered td {
            border: 1px solid #000;
            padding: 4px 6px; 
            font-size: 11pt; 
            vertical-align: middle;
        }

        .table-lampiran th {
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            background-color: #fff; 
        }
        .table-lampiran td {
            line-height: 1.2; 
        }
        
        .signature-table { margin-top: 50px; width: 100%; page-break-inside: avoid; }
        .signature-table td { width: 50%; text-align: center; vertical-align: top; }
        
        ol { padding-left: 20px; margin: 0; }
        ol li { text-align: justify; margin-bottom: 5px; line-height: 1.5; }
        
        .alpha-list { list-style-type: lower-alpha; padding-left: 20px; margin-top: 5px; }

        .list-ayat {
            counter-reset: ayat;
            list-style: none;
            padding-left: 0;
            margin-left: 0;
        }
        .list-ayat > li {
            position: relative;
            padding-left: 28px; 
            text-align: justify;
            margin-bottom: 5px;
            line-height: 1.5;
        }
        .list-ayat > li::before {
            content: "(" counter(ayat) ")";
            counter-increment: ayat;
            position: absolute;
            left: 0;
            top: 0;
        }

        .pasal-title {
            text-align: center;
            font-weight: bold;
            margin: 15px 0 0 0; 
        }
        .pasal-title + p, 
        .pasal-title + ol {
            margin-top: 0 !important; 
        }
    </style>
</head>
<body>

    <div class="no-print fixed top-5 right-5 flex gap-2 z-50">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-sans font-bold py-2 px-4 rounded shadow-lg flex items-center gap-2 transition">
            <i class="fas fa-print"></i> Cetak / Simpan PDF
        </button>
    </div>

    <?php
        if (!function_exists('penyebut')) {
            function penyebut($nilai) {
                $nilai = abs($nilai);
                $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
                $temp = "";
                if ($nilai < 12) {
                    $temp = " ". $huruf[$nilai];
                } else if ($nilai < 20) {
                    $temp = penyebut($nilai - 10). " Belas";
                } else if ($nilai < 100) {
                    $temp = penyebut(floor($nilai/10))." Puluh". penyebut($nilai % 10);
                } else if ($nilai < 200) {
                    $temp = " Seratus" . penyebut($nilai - 100);
                } else if ($nilai < 1000) {
                    $temp = penyebut(floor($nilai/100)) . " Ratus" . penyebut($nilai % 100);
                } else if ($nilai < 2000) {
                    $temp = " Seribu" . penyebut($nilai - 1000);
                } else if ($nilai < 1000000) {
                    $temp = penyebut(floor($nilai/1000)) . " Ribu" . penyebut($nilai % 1000);
                } else if ($nilai < 1000000000) {
                    $temp = penyebut(floor($nilai/1000000)) . " Juta" . penyebut($nilai % 1000000);
                }
                return $temp;
            }
        }

        if (!function_exists('terbilang')) {
            function terbilang($nilai) {
                if($nilai<0) {
                    $hasil = "minus ". trim(penyebut($nilai));
                } else {
                    $hasil = trim(penyebut($nilai));
                }           
                return ucwords($hasil);
            }
        }

        if (!function_exists('formatNamaTitle')) {
            function formatNamaTitle($nama) {
                return ucwords(strtolower($nama), " \t\r\n\f\v.");
            }
        }

        $bulanIndoAngka = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $indoHari = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $indoBulan = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];

        try {
            $tgl_spk = \Carbon\Carbon::parse($spk->tanggal_spk);
            $tglBerakhirRaw = $alokasi->max(function($item) {
                return $item->kegiatan->tanggal_akhir ?? now();
            });
            $tglBerakhirObj = \Carbon\Carbon::parse($tglBerakhirRaw);
            $tglBerakhir = $tglBerakhirObj->format('d') . ' ' . ($indoBulan[$tglBerakhirObj->format('F')] ?? $tglBerakhirObj->format('F')) . ' ' . $tglBerakhirObj->format('Y');
            
            $hariInggris = $tgl_spk->format('l');
            $bulanInggris = $tgl_spk->format('F');
            $hariSpk = $indoHari[$hariInggris] ?? $hariInggris; 
            $bulanSpk = $indoBulan[$bulanInggris] ?? $bulanInggris; 
            $tanggalSpk = terbilang($tgl_spk->day);
            $tahunSpk = terbilang($tgl_spk->year); 
            $tahunAngka = $tgl_spk->year; 
            $tanggalFull = $tgl_spk->format('d') . ' ' . $bulanSpk . ' ' . $tgl_spk->format('Y');

        } catch (\Exception $e) {
            $tglBerakhir = "-";
            $hariSpk = "-";
            $tanggalSpk = "-";
            $bulanSpk = "-";
            $tahunSpk = "-";
            $tahunAngka = date('Y');
            $tanggalFull = date('d F Y');
        }

        $namaSatkerFull = trim($satker->nama_satker ?? 'BADAN PUSAT STATISTIK');
        $partsSatker = explode(' ', $namaSatkerFull);
        $duaKataTerakhir = implode(' ', array_slice($partsSatker, -2)); 
        
        $namaSatkerProper = ucwords(strtolower($namaSatkerFull));
        $kabupatenProper = ucwords(strtolower($duaKataTerakhir)); 
        $kotaProper = ucwords(strtolower($satker->kota ?? '')); 
        
        $desaMitra = ucwords(strtolower(optional($spk->mitra)->asal_desa ?? '....................'));
        $kecMitra = ucwords(strtolower(optional($spk->mitra)->asal_kecamatan ?? '....................'));

        $namaPpkProper = formatNamaTitle($satker->nama_ppk ?? '');
        $namaMitraProper = formatNamaTitle($spk->mitra->nama_lengkap ?? '');

        $totalHonorPokok = $alokasi->sum(function($item) {
            return $item->volume_target * $item->harga_satuan_aktual;
        });
        
        $totalNilaiLain = $alokasi->sum('nilai_lain');
        $totalDendaPasal11 = $totalHonorPokok + $totalNilaiLain;
        $finalTotalHonor = isset($totalHonor) ? $totalHonor : $totalHonorPokok;
    ?>

    <div class="page-container">
        <div class="text-center" style="margin-bottom: 20px; line-height: 1.15;">
            <h3 class="font-bold uppercase" style="margin:0; font-size:12pt;">PERJANJIAN KERJA</h3>
            <h3 class="font-bold uppercase" style="margin:0; font-size:12pt;">PETUGAS PENGOLAHAN DATA</h3>
            <h3 class="font-bold uppercase" style="margin:0; font-size:12pt;">PADA {{ $satker->nama_satker }}</h3>
            <div style="margin-top: 5px; font-weight:bold;">NOMOR: {{ $spk->nomor_spk }}</div>
        </div>

        <p style="margin-bottom: 0px;">
            Pada hari ini, {{ $hariSpk }}, tanggal {{ $tanggalSpk }}, bulan {{ $bulanSpk }}, tahun {{ $tahunSpk }} bertempat di {{ $kotaProper }}, yang bertanda tangan di bawah ini:
        </p>
        
        <div style="height: 1em;"></div>

        <table class="table-data" style="margin-left: 0; width: 100%;">
            <tr>
                <td class="col-no">1.</td>
                <td class="col-nama">{{ $satker->nama_ppk }}</td>
                <td class="col-sep">:</td>
                <td class="col-desc">
                    Pejabat Pembuat Komitmen {{ $namaSatkerProper }} berkedudukan di {{ $kotaProper }}, {{ $kabupatenProper }}, bertindak untuk dan atas nama {{ $namaSatkerProper }}, selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.
                </td>
            </tr>
            <tr><td colspan="4" style="height: 12px;"></td></tr>
            <tr>
                <td class="col-no">2.</td>
                <td class="col-nama">{{ $spk->mitra->nama_lengkap }}</td>
                <td class="col-sep">:</td>
                <td class="col-desc">
                    Petugas Pengolahan Data berkedudukan di Desa {{ $desaMitra }}, Kecamatan {{ $kecMitra }}, bertindak untuk dan atas nama diri sendiri, selanjutnya disebut <strong>PIHAK KEDUA</strong>.
                </td>
            </tr>
        </table>

        <p style="margin-top: 15px;">
            bahwa <strong>PIHAK PERTAMA</strong> dan <strong>PIHAK KEDUA</strong> yang secara bersama-sama disebut <strong>PARA PIHAK</strong>, sepakat untuk mengikatkan diri dalam Perjanjian Kerja Petugas Pengolahan Data pada {{ $namaSatkerProper }} Nomor: {{ $spk->nomor_spk }}, yang selanjutnya disebut Perjanjian, dengan ketentuan-ketentuan sebagai berikut:
        </p>

        <div class="pasal-title">Pasal 1</div>
        <p>
            <strong>PIHAK PERTAMA</strong> memberikan pekerjaan kepada <strong>PIHAK KEDUA</strong> dan <strong>PIHAK KEDUA</strong> menerima pekerjaan dari <strong>PIHAK PERTAMA</strong> sebagai Petugas Pengolahan Data pada {{ $namaSatkerProper }}, dengan lingkup pekerjaan yang ditetapkan oleh <strong>PIHAK PERTAMA</strong>.
        </p>

        <div class="pasal-title">Pasal 2</div>
        <p>
            Ruang lingkup pekerjaan dalam Perjanjian ini mengacu pada wilayah kerja dan beban kerja sebagaimana tertuang dalam Lampiran Perjanjian, Pedoman Petugas Pengolahan Data terkait kegiatan sebagaimana tertuang dalam Lampiran Perjanjian, dan ketentuan-ketentuan lainnya yang ditetapkan oleh <strong>PIHAK PERTAMA</strong>.
        </p>

        <div class="pasal-title">Pasal 3</div>
        <p>
            Jangka Waktu Perjanjian terhitung sejak ditandatangani sampai dengan tanggal {{ $tglBerakhir }}.
        </p>

        <div class="pasal-title">Pasal 4</div>
        <ol class="list-ayat">
            <li><strong>PIHAK KEDUA</strong> berkewajiban melaksanakan pekerjaan yang diberikan oleh <strong>PIHAMA PERTAMA</strong> sesuai dengan ruang lingkup pekerjaan sebagaimana dimaksud dalam Pasal 2.</li>
            <li><strong>PIHAK KEDUA</strong> untuk waktu yang tidak terbatas dan/atau tidak terikat kepada masa berlakunya Perjanjian ini, menjamin untuk memberlakukan sebagai rahasia setiap data/informasi yang diterima atau diperolehnya dari <strong>PIHAK PERTAMA</strong>, serta menjamin bahwa keterangan demikian hanya dipergunakan untuk melaksanakan tujuan menurut Perjanjian ini.</li>
        </ol>

        <div class="pasal-title">Pasal 5</div>
        <ol class="list-ayat">
            <li><strong>PIHAK KEDUA</strong> apabila melakukan peminjaman dokumen/data/aset milik <strong>PIHAK PERTAMA</strong>, wajib menjaga dan menggunakan sesuai dengan tujuan perjanjian dan mengembalikan dalam keadaan utuh sama dengan saat peminjaman, serta dilarang menggandakan, menyalin, menunjukkan, dan/atau mendokumentasikan dalam bentuk foto atau bentuk apapun untuk kepentingan pribadi ataupun kepentingan lain yang tidak berkaitan dengan tujuan perjanjian ini.</li>
            <li><strong>PIHAK KEDUA</strong> dilarang memberikan dokumen/data/aset milik <strong>PIHAK PERTAMA</strong> yang berada dalam penguasaan <strong>PIHAK KEDUA</strong>, baik secara langsung maupun tidak langsung, termasuk memberikan akses kepada pihak lain untuk menggunakan, menyalin, memfotokopi, menunjukkan, dan/atau mendokumentasikan dalam bentuk foto atau bentuk apapun, sehingga informasi diketahui oleh pihak lain untuk tujuan apapun.</li>
        </ol>

        <div class="pasal-title">Pasal 6</div>
        <ol class="list-ayat">
            <li>
                <strong>PIHAK KEDUA</strong> berhak untuk mendapatkan honorarium dari <strong>PIHAK PERTAMA</strong> sebesar 
                Rp {{ number_format($totalHonorPokok, 0, ',', '.') }},00
                ({{ terbilang($totalHonorPokok) }} Rupiah) untuk pekerjaan sebagaimana dimaksud dalam Pasal 2, termasuk biaya pajak, bea meterai, dan jasa pelayanan keuangan.
            </li>
            <li>Honorarium sebagaimana dimaksud pada ayat (1) dibayarkan oleh <strong>PIHAK PERTAMA</strong> kepada <strong>PIHAK KEDUA</strong> setelah menyelesaikan seluruh pekerjaan yang ditargetkan sebagaimana tercantum dalam Lampiran Perjanjian ini, dituangkan dalam Berita Acara Serah Terima Hasil Pekerjaan, dan diserahkan paling lambat tanggal {{ $tglBerakhir }}.</li>
            <li><strong>PIHAK KEDUA</strong> tidak diberikan honorarium tambahan apabila terdapat tambahan waktu pelaksanaan pekerjaan di luar jangka waktu Perjanjian sebagaimana dimaksud dalam Pasal 3, dengan tambahan waktu pelaksanaan pekerjaan paling lama sampai dengan tanggal {{ $tglBerakhir }}.</li>
        </ol>

        <div class="break-pasal-7">
            <div class="pasal-title">Pasal 7</div>
            <p>
                <strong>PIHAK KEDUA</strong> berhak untuk mendapatkan asuransi jaminan kecelakaan kerja (JKK) dan jaminan kematian (JKM) dari <strong>PIHAK PERTAMA</strong> untuk jangka waktu pelaksanaan pengolahan pada bulan {{ $bulanIndoAngka[(int)$spk->bulan] ?? '-' }} Tahun {{ $spk->tahun }}.
            </p>
        </div>

        <div class="pasal-title">Pasal 8</div>
        <ol class="list-ayat">
            <li>Pembayaran honorarium sebagaimana dimaksud dalam Pasal 6 dilakukan setelah <strong>PIHAK KEDUA</strong> menyelesaikan dan menyerahkan hasil pekerjaan sebagaimana dimaksud dalam Pasal 2 kepada <strong>PIHAK PERTAMA</strong>.</li>
            <li>Pembayaran sebagaimana dimaksud pada ayat (1) dilakukan oleh <strong>PIHAK PERTAMA</strong> kepada <strong>PIHAK KEDUA</strong> sesuai dengan ketentuan peraturan perundang-undangan.</li>
        </ol>

        <div class="pasal-title">Pasal 9</div>
        <ol class="list-ayat">
            <li><strong>PIHAK PERTAMA</strong> secara berjenjang melalui Tim Teknis di {{ $namaSatkerProper }} melakukan pemeriksaan dan evaluasi atas target penyelesaian dan kualitas hasil pekerjaan yang dilaksanakan oleh <strong>PIHAK KEDUA</strong> secara berkala.</li>
            <li>Hasil pemeriksaan dan evaluasi sebagaimana dimaksud pada ayat (1) menjadi dasar pembayaran honorarium <strong>PIHAK KEDUA</strong> oleh <strong>PIHAK PERTAMA</strong> sebagaimana dimaksud dalam Pasal 6 ayat (2), yang dituangkan dalam Berita Acara Serah Terima Hasil Pekerjaan yang ditandatangani oleh <strong>PARA PIHAK</strong>.</li>
        </ol>

        <div class="pasal-title">Pasal 10</div>
        <p>
            <strong>PIHAK PERTAMA</strong> dapat memutuskan Perjanjian ini secara sepihak sewaktu-waktu dalam hal <strong>PIHAK KEDUA</strong> tidak dapat melaksanakan kewajibannya sebagaimana dimaksud dalam Pasal 4, dengan menerbitkan Surat Pemutusan Perjanjian Kerja.
        </p>

        <div class="pasal-title">Pasal 11</div>
        <ol class="list-ayat">
            <li>
                Apabila <strong>PIHAK KEDUA</strong> mengundurkan diri dengan tidak menyelesaikan pekerjaan sebagaimana dimaksud dalam Pasal 2, maka akan diberikan sanksi oleh <strong>PIHAK PERTAMA</strong>, sebagai berikut:
                <ol class="alpha-list">
                    <li>mengundurkan diri setelah pelatihan dikenakan denda sebesar Rp {{ number_format($totalDendaPasal11, 0, ',', '.') }},00 ({{ terbilang($totalDendaPasal11) }} Rupiah);</li>
                    <li>mengundurkan diri pada saat pelaksanaan pekerjaan, maka tidak diberikan honorarium atas pekerjaan yang telah dilaksanakan.</li>
                </ol>
            </li>
            <li>Dikecualikan tidak dikenakan sanksi sebagaimana dimaksud pada ayat (1) oleh <strong>PIHAK PERTAMA</strong>, apabila <strong>PIHAK KEDUA</strong> meninggal dunia, mengundurkan diri karena sakit dengan keterangan rawat inap, kecelakaan dengan keterangan kepolisian, dan/atau telah diberikan Surat Pemutusan Perjanjian Kerja dari <strong>PIHAK PERTAMA</strong>.</li>
            <li>Dalam hal terjadi peristiwa sebagaimana dimaksud pada ayat (2), <strong>PIHAK PERTAMA</strong> membayarkan honorarium kepada <strong>PIHAK KEDUA</strong> secara proporsional sesuai dengan pekerjaan yang telah dilaksanakan.</li>
        </ol>

        <div class="pasal-title">Pasal 12</div>
        <ol class="list-ayat">
            <li>Apabila terjadi Keadaan Kahar, yang meliputi bencana alam, bencana non alam, dan bencana sosial, <strong>PIHAK KEDUA</strong> memberitahukan kepada <strong>PIHAK PERTAMA</strong> dalam waktu paling lambat 14 (empat belas) hari sejak mengetahui atas kejadian Keadaan Kahar dengan menyertakan bukti.</li>
            <li>Apabila terjadi kerusakan perangkat pengolahan (komputer/laptop) yang menyebabkan pelaksanaan pengolahan data tidak dapat dilakukan, <strong>PIHAK KEDUA</strong> memberitahukan kepada <strong>PIHAK PERTAMA</strong> dalam waktu paling lambat 14 (empat belas) hari kalender sejak terjadi kerusakan dimaksud.</li>
            <li>Dalam hal terjadi peristiwa sebagaimana dimaksud pada ayat (1) dan/atau ayat (2), pelaksanaan pekerjaan oleh <strong>PIHAK KEDUA</strong> dihentikan sementara dan dilanjutkan kembali setelah Keadaan Kahar berakhir, merujuk pada ketentuan yang ditetapkan oleh <strong>PIHAK PERTAMA</strong>.</li>
            <li>Apabila akibat Keadaan Kahar tidak memungkinkan dilanjutkan/diselesaikannya pelaksanaan pekerjaan, <strong>PIHAK KEDUA</strong> berhak menerima honorarium secara proporsional sesuai dengan pekerjaan yang telah diselesaikan dan diterima oleh <strong>PIHAK PERTAMA</strong>.</li>
        </ol>

        <div class="pasal-title">Pasal 13</div>
        <p>
            Hal-hal yang belum diatur dalam Perjanjian ini atau segala perubahan terhadap Perjanjian ini diatur lebih lanjut oleh <strong>PARA PIHAK</strong> dalam perjanjian tambahan/adendum dan merupakan bagian tidak terpisahkan dari Perjanjian ini.
        </p>

        <div class="pasal-title">Pasal 14</div>
        <ol class="list-ayat">
            <li>Segala perselisihan atau perbedaan pendapat yang mungkin timbul sebagai akibat dari Perjanjian ini, diselesaikan secara musyawarah untuk mufakat oleh <strong>PARA PIHAK</strong>.</li>
            <li>Apabila musyawarah untuk mufakat sebagaimana dimaksud pada ayat (1) tidak berhasil, maka <strong>PARA PIHAK</strong> sepakat untuk menyelesaikan perselisihan dengan memilih kedudukan/domisili hukum di Kepaniteraan Pengadilan Negeri {{ $satker->kota }}</li>
            <li>Selama perselisihan dalam proses penyelesaian pengadilan, <strong>PIHAK PERTAMA</strong> dan <strong>PIHAK KEDUA</strong> wajib tetap melaksanakan kewajiban masing-masing berdasarkan Perjanjian ini.</li>
        </ol>

        <p style="margin-top: 20px;">
            Demikian Perjanjian ini dibuat dan ditandatangani oleh <strong>PARA PIHAK</strong> dalam 2 (dua) rangkap asli bermeterai cukup, tanpa paksaan dari PIHAK manapun dan untuk dilaksanakan oleh <strong>PARA PIHAK</strong>.
        </p>

        <table class="signature-table">
            <tr>
                <td>
                    <strong>PIHAK KEDUA</strong>,<br><br><br><br><br>
                    {{ $namaMitraProper }}
                </td>
                <td>
                    <strong>PIHAK PERTAMA</strong>,<br><br><br><br><br>
                    {{ $namaPpkProper }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Pemicu Ganti Halaman -->
    <div style="page-break-after: always;" class="no-print"></div>

    <!-- Bagian Lampiran (Landscape) -->
    <div class="page-container landscape landscape-section">
        <div class="header-lampiran text-left font-bold uppercase" style="font-size: 11pt; line-height: 1.5;">
            LAMPIRAN<br>
            PERJANJIAN KERJA<br>
            PETUGAS PENGOLAHAN DATA<br>
            PADA {{ $satker->nama_satker }}<br>
            NOMOR: {{ $spk->nomor_spk }}
        </div>

        <div class="text-center" style="margin: 15px 0;">
            <h4 class="font-bold uppercase" style="margin:0; font-size:11pt;">DAFTAR URAIAN TUGAS, JANGKA WAKTU, TARGET PEKERJAAN, NILAI PERJANJIAN, DAN BEBAN ANGGARAN</h4>
        </div>

        <table class="table-bordered table-lampiran" style="width: 100%;">
            <thead>
                <tr style="background-color: #fff;">
                    <th rowspan="2" class="text-center" width="4%">No</th>
                    <th rowspan="2" class="text-center" width="20%">Uraian Tugas</th>
                    <th rowspan="2" class="text-center" width="14%">Jangka Waktu</th>
                    <th colspan="2" class="text-center" width="18%">Target Pekerjaan</th>
                    <th rowspan="2" class="text-center" width="12%">Harga Satuan</th>
                    <th rowspan="2" class="text-center" width="14%">Nilai Perjanjian</th>
                    <th rowspan="2" class="text-center" width="18%">Beban Anggaran</th>
                </tr>
                <tr style="background-color: #fff;">
                    <th class="text-center" width="8%">Volume</th>
                    <th class="text-center" width="10%">Satuan</th>
                </tr>
                <tr style="background-color: #fff; font-size: 9pt;">
                    <th class="text-center">(1)</th>
                    <th class="text-center">(2)</th>
                    <th class="text-center">(3)</th>
                    <th class="text-center">(4)</th>
                    <th class="text-center">(5)</th>
                    <th class="text-center">(6)</th>
                    <th class="text-center">(7)</th>
                    <th class="text-center">(8)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alokasi as $index => $row)
                <?php 
                    $honorItem = $row->volume_target * $row->harga_satuan_aktual; 
                    $start = \Carbon\Carbon::parse($row->kegiatan->tanggal_mulai ?? now());
                    $end = \Carbon\Carbon::parse($row->kegiatan->tanggal_akhir ?? now());
                    
                    if ($start->format('mY') == $end->format('mY')) {
                        $jangkaWaktu = $start->format('d') . ' - ' . $end->format('d') . ' ' . ($indoBulan[$start->format('F')] ?? $start->format('F')) . ' ' . $start->format('Y');
                    } else {
                        $jangkaWaktu = $start->format('d') . ' ' . ($indoBulan[$start->format('F')] ?? $start->format('F')) . ' - ' . $end->format('d') . ' ' . ($indoBulan[$end->format('F')] ?? $end->format('F')) . ' ' . $end->format('Y');
                    }
                    
                    // PERBAIKAN LOGIKA SATUAN DI SINI
                    // Mengambil satuan dari relasi AlokasiHonor -> AturanHks -> Satuan
                    // Fallback ke 'Dokumen' jika data null
                    $satuan = optional($row->aturanHks->satuan)->nama_satuan ?? 'Dokumen';
                ?>
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-left">{{ $row->kegiatan->nama_kegiatan ?? '-' }}</td>
                    <td class="text-center">{{ $jangkaWaktu }}</td>
                    <td class="text-center">{{ $row->volume_target }}</td>
                    <td class="text-center">{{ $satuan }}</td>
                    <td class="text-center">Rp {{ number_format($row->harga_satuan_aktual, 0, ',', '.') }}</td>
                    <td class="text-center">Rp {{ number_format($honorItem, 0, ',', '.') }}</td>
                    <td class="text-center" style="font-size: 10pt;">{{ $row->kegiatan->mata_anggaran ?? '-' }}</td>
                </tr>
                @endforeach

                <!-- FOOTER TOTAL -->
                <tr style="background-color: #fff; font-weight: ;">
                    <td colspan="6" class="text-center" style="vertical-align: middle; font-style: italic; font-weight: normal; border-top: 2px solid #000 !important;">
                        Terbilang: {{ terbilang($totalHonorPokok) }} Rupiah
                    </td>
                    <td class="text-center" style="border-top: 2px solid #000 !important;">Rp {{ number_format($totalHonorPokok, 0, ',', '.') }}</td>
                    <td style="border-top: 2px solid #000 !important;"></td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>
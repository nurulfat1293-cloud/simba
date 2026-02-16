<!DOCTYPE html>
<html>
<head>
    <title>Cetak SPK - {{ $spk->nomor_spk }}</title>
    <style>
        body { 
            font-family: 'Arial', sans-serif; 
            font-size: 11pt; 
            line-height: 1.5;
            color: #000;
        }
        
        /* Layout Helpers */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mt-3 { margin-top: 20px; }
        
        /* Kop Surat */
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            padding-bottom: 10px;
            border-bottom: 3px double #000; /* Garis ganda di bawah kop */
        }
        .header h2 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10pt; }

        /* Isi Surat */
        .content { margin-bottom: 20px; }
        .judul-surat { text-decoration: underline; font-weight: bold; margin-bottom: 5px; }
        
        /* Tabel Biodata (Tanpa Border) */
        .table-biodata { width: 100%; margin-left: 20px; margin-bottom: 10px; }
        .table-biodata td { padding: 3px; vertical-align: top; }
        .label-col { width: 120px; }
        .separator-col { width: 10px; }

        /* Tabel Lampiran (Dengan Border) */
        .table-data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-data th, .table-data td { 
            border: 1px solid #000; 
            padding: 6px; 
            text-align: left; 
            font-size: 10pt;
        }
        .table-data th { background-color: #f0f0f0; text-align: center; }

        /* Tanda Tangan */
        .ttd-wrapper { 
            width: 100%; 
            margin-top: 50px; 
            overflow: hidden; /* Clear float */
        }
        .ttd-box { 
            width: 40%; 
            float: left; 
            text-align: center; 
        }
        .ttd-box-right { 
            width: 40%; 
            float: right; 
            text-align: center; 
        }
        
        /* Page Break jika diperlukan untuk lampiran panjang */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="header">
        <h2>BADAN PUSAT STATISTIK</h2>
        <p>Jl. Jenderal Sudirman No. 1, Kota Contoh</p>
        <p>Telp: (021) 1234567 | Email: bps@contoh.go.id</p>
    </div>

    <!-- ISI SURAT -->
    <div class="content">
        <div class="text-center mb-2">
            <h3 class="judul-surat" style="margin: 0;">SURAT PERJANJIAN KERJA (SPK)</h3>
            <p style="margin: 0;">Nomor: {{ $spk->nomor_spk }}</p>
        </div>
        
        <br>
        
        <p>Yang bertanda tangan di bawah ini:</p>
        
        <!-- Pihak Pertama -->
        <table class="table-biodata">
            <tr>
                <td class="label-col">Nama</td>
                <td class="separator-col">:</td>
                <td class="text-bold">NAMA PEJABAT PEMBUAT KOMITMEN</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>Pejabat Pembuat Komitmen (PPK)</td>
            </tr>
            <tr>
                <td>Instansi</td>
                <td>:</td>
                <td>Badan Pusat Statistik</td>
            </tr>
        </table>

        <p>Selanjutnya disebut <b>PIHAK PERTAMA</b>.</p>

        <!-- Pihak Kedua -->
        <table class="table-biodata">
            <tr>
                <td class="label-col">Nama</td>
                <td class="separator-col">:</td>
                <td class="text-bold">{{ $spk->mitra->nama_lengkap }}</td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $spk->mitra->nik }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $spk->mitra->alamat }}</td>
            </tr>
            <tr>
                <td>Rekening</td>
                <td>:</td>
                <td>{{ $spk->mitra->nama_bank }} - {{ $spk->mitra->nomor_rekening }}</td>
            </tr>
        </table>

        <p>Selanjutnya disebut <b>PIHAK KEDUA</b>.</p>

        <p>Kedua belah pihak sepakat untuk mengikatkan diri dalam Perjanjian Kerja Pelaksanaan Kegiatan Statistik dengan rincian honorarium sebagai berikut:</p>
    </div>

    <!-- TABEL RINCIAN KEGIATAN -->
    <table class="table-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama Kegiatan</th>
                <th>Jabatan</th>
                <th width="10%">Vol</th>
                <th width="10%">Satuan</th>
                <th width="15%">Harga Satuan</th>
                <th width="15%">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $total_akhir = 0; @endphp
            @foreach($spk->listHonor as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item->kegiatan->nama_kegiatan }}</td>
                <td>{{ $item->jabatan->nama_jabatan }}</td>
                <td class="text-center">{{ $item->volume_target }}</td>
                <td class="text-center">{{ $item->aturanHks->satuan->nama_satuan }}</td>
                <td class="text-right">Rp {{ number_format($item->aturanHks->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->total_honor, 0, ',', '.') }}</td>
            </tr>
            @php $total_akhir += $item->total_honor; @endphp
            @endforeach
            <tr>
                <td colspan="6" class="text-right text-bold" style="background-color: #f9f9f9;">TOTAL YANG DITERIMA</td>
                <td class="text-right text-bold" style="background-color: #f9f9f9;">Rp {{ number_format($total_akhir, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <div class="ttd-wrapper">
        <div class="ttd-box">
            <p>PIHAK KEDUA,<br>Mitra Statistik</p>
            <br><br><br><br>
            <p class="text-bold" style="text-decoration: underline;">{{ strtoupper($spk->mitra->nama_lengkap) }}</p>
        </div>
        
        <div class="ttd-box-right">
            <p>PIHAK PERTAMA,<br>Pejabat Pembuat Komitmen</p>
            <br><br><br><br>
            <p class="text-bold" style="text-decoration: underline;">NAMA PPK DISINI</p>
            <p>NIP. 198xxxxxxxxxxxxx</p>
        </div>
    </div>

</body>
</html>
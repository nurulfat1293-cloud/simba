@extends('layout.app')

@section('content')
<div class="space-y-6">
    <!-- Header & Navigasi -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('spk.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-2xl font-bold text-slate-800">Detail SPK</h2>
            </div>
            <p class="text-sm text-slate-500 ml-6">Informasi lengkap Surat Perintah Kerja dan rincian kegiatan.</p>
        </div>
        
        <div class="flex gap-2 ml-6 sm:ml-0">
            <!-- Tombol Edit -->
            {{-- 
            <a href="{{ route('spk.edit', $spk->nomor_urut) }}" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg shadow-sm transition-all font-medium text-sm">
                <i class="fas fa-edit"></i> Edit
            </a> 
            --}}

            <!-- Cetak PDF -->
            <a href="{{ route('transaksi.print-spk', $spk->nomor_urut) }}" target="_blank" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm transition-all font-medium text-sm">
                <i class="fas fa-print"></i> PDF
            </a>

            <!-- Download Word -->
            <a href="{{ route('spk.word', $spk->nomor_urut) }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-sm transition-all font-medium text-sm">
                <i class="fas fa-file-word"></i> Word
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kartu Informasi Utama -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Info SPK -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Data Kontrak</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Nomor SPK</label>
                        <p class="text-slate-800 font-mono font-medium">{{ $spk->nomor_spk }}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Periode</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                {{ \Carbon\Carbon::create()->month($spk->bulan)->translatedFormat('F') }} {{ $spk->tahun }}
                            </span>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Tgl Tanda Tangan</label>
                            <p class="text-slate-700 text-sm">{{ \Carbon\Carbon::parse($spk->tanggal_spk)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Nomor Urut Sistem</label>
                        <p class="text-slate-500 font-mono text-xs">#{{ $spk->nomor_urut }}</p>
                    </div>
                </div>
            </div>

            <!-- Info Mitra -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Identitas Mitra</h3>
                
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-lg border border-slate-200">
                        {{ substr($spk->mitra->nama_lengkap ?? 'X', 0, 1) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">{{ $spk->mitra->nama_lengkap ?? 'Mitra Tidak Ditemukan' }}</p>
                        <p class="text-xs text-slate-500 font-mono">NIK: {{ $spk->mitra->nik ?? '-' }}</p>
                    </div>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">No. HP / WA</span>
                        <span class="font-medium text-slate-700">{{ $spk->mitra->no_hp ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">Bank</span>
                        <span class="font-medium text-slate-700">{{ $spk->mitra->nama_bank ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">No. Rekening</span>
                        <span class="font-medium text-slate-700">{{ $spk->mitra->nomor_rekening ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-1">Alamat Domisili</span>
                        <p class="font-medium text-slate-700 leading-snug">
                            {{ $spk->mitra->alamat ?? '-' }}<br>
                            <span class="text-xs text-slate-500 font-normal">
                                Desa {{ $spk->mitra->asal_desa ?? '-' }}, Kec. {{ $spk->mitra->asal_kecamatan ?? '-' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Rincian Kegiatan -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800">Rincian Kegiatan & Honor</h3>
                    <span class="text-xs font-medium px-2 py-1 bg-white border border-slate-200 rounded text-slate-500">
                        {{ $spk->alokasi->count() }} Kegiatan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 uppercase font-semibold text-xs">
                            <tr>
                                <th class="px-6 py-3 w-10">No</th>
                                <th class="px-6 py-3">Kegiatan / Jabatan</th>
                                <th class="px-6 py-3 text-center">Volume</th>
                                <th class="px-6 py-3 text-right">Harga Satuan</th>
                                <!-- Kolom Baru: Ganti Rugi -->
                                <th class="px-6 py-3 text-right">Ganti Rugi</th>
                                <th class="px-6 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($spk->alokasi as $index => $row)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-center text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-700">{{ $row->kegiatan->nama_kegiatan ?? '-' }}</p>
                                    <p class="text-xs text-blue-600 font-medium mt-0.5 uppercase tracking-wide">
                                        {{ $row->jabatan->nama_jabatan ?? '-' }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 mt-1 font-mono bg-slate-100 inline-block px-1 rounded">
                                        {{ $row->kegiatan->mata_anggaran ?? 'Kode MA -' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="font-medium text-slate-700">{{ $row->volume_target }}</span>
                                    <!-- PERBAIKAN: Mengambil satuan dari relasi aturanHks -> satuan -->
                                    <span class="text-xs text-slate-400 ml-1">{{ $row->aturanHks->satuan->nama_satuan ?? 'Dok' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-slate-600 whitespace-nowrap">
                                    Rp {{ number_format($row->harga_satuan_aktual, 0, ',', '.') }}
                                </td>
                                <!-- Data Ganti Rugi (nilai_lain) -->
                                <td class="px-6 py-4 text-right font-medium whitespace-nowrap">
                                    @if($row->nilai_lain > 0)
                                        <span class="text-amber-600" title="Transport / Biaya Lain">
                                            + Rp {{ number_format($row->nilai_lain, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-slate-800 whitespace-nowrap">
                                    Rp {{ number_format($row->total_honor, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500 italic bg-slate-50/50">
                                    Belum ada rincian kegiatan (Honor) yang diinput untuk SPK ini.
                                    <br>
                                    <a href="{{ route('transaksi.index') }}" class="text-blue-600 hover:underline text-xs mt-2 inline-block">
                                        Input Honor Sekarang
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($spk->alokasi->count() > 0)
                        <tfoot class="bg-slate-50 border-t-2 border-slate-100">
                            <tr>
                                <!-- Colspan disesuaikan menjadi 5 karena ada tambahan 1 kolom -->
                                <td colspan="5" class="px-6 py-4 text-right font-bold text-slate-700 uppercase text-xs tracking-wider">Total Honorarium</td>
                                <td class="px-6 py-4 text-right font-black text-blue-700 text-base whitespace-nowrap">
                                    Rp {{ number_format($spk->alokasi->sum('total_honor'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
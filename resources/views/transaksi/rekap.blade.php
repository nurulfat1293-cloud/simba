@extends('layout.app')

@section('content')

<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col gap-1">
        <h2 class="text-2xl font-bold text-slate-800">Rekap SPK dan Honor</h2>
        <p class="text-sm text-slate-500">Resume distribusi honor mitra berdasarkan periode kegiatan dan plafon bulanan.</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-6">
        <form action="{{ request()->url() }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Cari Mitra (Nama/NIK)</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search_mitra" value="{{ request('search_mitra') }}" 
                        placeholder="Nama atau NIK..." 
                        class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nama Kegiatan</label>
                <div class="relative">
                    <i class="fas fa-tasks absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search_kegiatan" value="{{ request('search_kegiatan') }}" 
                        placeholder="Nama kegiatan..." 
                        class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Bulan SPK</label>
                <select name="bulan" class="w-full px-4 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none appearance-none cursor-pointer">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tahun</label>
                <div class="flex gap-2">
                    <select name="tahun" class="w-full px-4 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none appearance-none cursor-pointer">
                        @php $currentYear = date('Y'); @endphp
                        <option value="">Semua Tahun</option>
                        @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors shadow-sm flex items-center gap-2">
                        <i class="fas fa-search text-xs"></i>
                        <span class="font-bold text-xs uppercase text-white">Filter</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @forelse($rekapData as $namaMitra => $periodes)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-8">
            <!-- Header Kartu Mitra -->
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-sm border border-blue-200">
                        {{ substr($namaMitra, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg leading-none">{{ $namaMitra }}</h3>
                        <p class="text-[10px] text-slate-400 font-medium mt-1 uppercase tracking-widest">Informasi Rekapitulasi Mitra</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-12">
                @foreach($periodes as $bulanGroup => $data)
                    <div class="relative pl-8 border-l-2 border-slate-100">
                        <!-- Indikator Timeline -->
                        <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-blue-500 shadow-sm"></div>
                        
                        <!-- Header Periode -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex flex-col">
                                <h4 class="font-black text-slate-700 uppercase tracking-tighter text-sm flex items-center gap-2">
                                    <i class="far fa-calendar-check text-blue-500 text-xs"></i> 
                                    {{ $bulanGroup }}
                                </h4>
                                <span class="text-[10px] text-slate-400 font-medium ml-5">Periode Pelaksanaan Kegiatan</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] text-blue-500 font-bold uppercase tracking-widest block">
                                    {{ $data['status_rule'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Tabel Rincian: Urutan Kolom Diperbarui -->
                        <div class="overflow-hidden border border-slate-200 rounded-xl mb-5 shadow-sm bg-white">
                            <table class="w-full text-sm text-left border-collapse">
                                <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-bold tracking-widest border-b">
                                    <tr>
                                        <th class="px-5 py-3">Mata Anggaran</th>
                                        <th class="px-5 py-3">Kegiatan & Jabatan</th>
                                        <th class="px-5 py-3 text-right">Nilai Lain</th>
                                        <th class="px-5 py-3 text-right text-slate-900">Ganti Rugi</th>
                                        <th class="px-5 py-3 text-right bg-slate-100/50">Honor Pokok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @foreach($data['detail_items'] as $row)
                                        @php
                                            $honorPokokRow = $row->volume_target * $row->harga_satuan_aktual;
                                        @endphp
                                        <tr class="hover:bg-slate-50 transition-all">
                                            <td class="px-5 py-4">
                                                <span class="font-mono text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded border border-slate-200">
                                                    {{ $row->kegiatan->mata_anggaran ?? '---' }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <div class="flex flex-col">
                                                    <span class="font-semibold text-slate-700 text-xs">{{ $row->kegiatan->nama_kegiatan ?? 'N/A' }}</span>
                                                    <span class="text-[9px] text-blue-500 font-bold uppercase mt-0.5 tracking-tight">{{ $row->jabatan->nama_jabatan ?? '-' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 text-right text-slate-400 text-xs">
                                                Rp {{ number_format($row->nilai_lain, 0, ',', '.') }}
                                            </td>
                                            <td class="px-5 py-4 text-right font-black text-slate-900 text-sm whitespace-nowrap">
                                                Rp {{ number_format($row->total_honor, 0, ',', '.') }}
                                            </td>
                                            <td class="px-5 py-4 text-right bg-slate-100/20">
                                                <div class="text-slate-600 font-medium text-xs">Rp {{ number_format($honorPokokRow, 0, ',', '.') }}</div>
                                                <div class="text-[8px] text-slate-400 opacity-60">
                                                    {{ number_format($row->volume_target, 2, ',', '.') }} x {{ number_format($row->harga_satuan_aktual, 0, ',', '.') }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Panel Kalkulasi Plafon & Ganti Rugi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gradient-to-br from-white to-slate-50/50 p-6 rounded-2xl border border-slate-200 shadow-sm">
                            <div class="flex flex-col justify-center">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Ganti Rugi (Akumulasi SPK)</p>
                                <div class="flex flex-wrap items-center gap-4">
                                    <span class="text-3xl font-black text-slate-800 tracking-tighter">Rp {{ number_format($data['total_ganti_rugi'], 0, ',', '.') }}</span>
                                    
                                    @php
                                        // Mengambil ID/Nomor Urut SPK dari item pertama
                                        $spkID = $data['detail_items']->first()->spk->nomor_urut ?? null;
                                        // Opsional: Jika ingin ID asli untuk route tertentu
                                        $realSpkId = $data['detail_items']->first()->spk->id ?? null;
                                        // Gunakan $realSpkId jika route spk.word membutuhkan ID primary key, 
                                        // atau $spkID jika Controller menangani fallback nomor_urut.
                                        $paramID = $realSpkId ?? $spkID;
                                    @endphp
                                    
                                    @if($spkID)
                                        <!-- Tombol Cetak PDF -->
                                        <a href="{{ route('transaksi.print-spk', $spkID) }}" target="_blank" class="flex items-center gap-2 bg-slate-800 hover:bg-black text-white px-4 py-2 rounded-lg text-[10px] font-bold transition-all shadow-md uppercase tracking-tighter">
                                            <i class="fas fa-print"></i> Cetak SPK
                                        </a>

                                        <!-- Tombol Download Word (BARU DITAMBAHKAN) -->
                                        <a href="{{ route('spk.word', $paramID) }}" class="flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-[10px] font-bold transition-all shadow-md uppercase tracking-tighter no-print">
                                            <i class="fas fa-file-word"></i> Word
                                        </a>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="md:text-right flex flex-col md:items-end justify-center border-t md:border-t-0 md:border-l border-slate-200 pt-4 md:pt-0 md:pl-6">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Sisa Plafon Anggaran (Honor Pokok Saja)</p>
                                <span class="text-3xl font-black {{ $data['sisa_kuota'] < 0 ? 'text-red-600' : 'text-blue-600' }} tracking-tighter">
                                    {{ $data['sisa_kuota'] < 0 ? '-' : '' }}Rp {{ number_format(abs($data['sisa_kuota']), 0, ',', '.') }}
                                </span>
                                <p class="text-[9px] text-slate-400 mt-1 italic font-medium">
                                    Batas Maksimal SBML: Rp {{ number_format($data['sbml_limit'], 0, ',', '.') }} / Bulan
                                </p>
                                <p class="text-[8px] text-slate-400 mt-0.5 uppercase font-bold tracking-tighter">
                                    Akumulasi Pokok: Rp {{ number_format($data['total_honor_pokok'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white p-24 rounded-2xl border-2 border-dashed border-slate-200 text-center shadow-inner">
            <h3 class="text-slate-800 font-bold text-lg">Data Belum Tersedia</h3>
            <p class="text-slate-400 text-sm mt-2 max-w-sm mx-auto">Sistem belum menemukan riwayat alokasi honor untuk periode ini.</p>
        </div>
    @endforelse
</div>

@endsection
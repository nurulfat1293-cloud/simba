<div class="space-y-6">
<!-- Status & Total Header -->
<div class="flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-100">
<div>
<p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Total Ganti Rugi</p>
<p class="text-2xl font-black text-blue-600">Rp {{ number_format($alokasi->total_honor, 0, ',', '.') }}</p>
</div>
<div class="text-right">
<p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-1">Status</p>
@php
$statusLabel = $alokasi->status_pembayaran ?? 'Belum';
$statusColor = [
'Belum' => 'bg-amber-100 text-amber-700',
'Proses' => 'bg-blue-100 text-blue-700',
'Terbayar' => 'bg-green-100 text-green-700'
][$statusLabel] ?? 'bg-slate-100 text-slate-700';
@endphp
<span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $statusColor }}">
{{ $statusLabel }}
</span>
</div>
</div>

<!-- Informasi Utama -->
<div class="grid grid-cols-1 gap-4">
    <div class="border-b border-slate-100 pb-3">
        <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Nama Mitra</label>
        <p class="text-slate-800 font-semibold">{{ $alokasi->spk->mitra->nama_lengkap ?? $alokasi->spk->mitra->nama ?? 'N/A' }}</p>
    </div>

    <div class="border-b border-slate-100 pb-3">
        <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Nomor SPK</label>
        <p class="text-slate-800 font-mono text-sm">{{ $alokasi->spk->nomor_spk ?? '-' }}</p>
    </div>

    <div class="border-b border-slate-100 pb-3">
        <div class="flex justify-between items-start">
            <div>
                <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Kegiatan</label>
                <p class="text-slate-800">{{ $alokasi->kegiatan->nama_kegiatan ?? '-' }}</p>
            </div>
            {{-- MENAMPILKAN MATA ANGGARAN --}}
            @if($alokasi->kegiatan && $alokasi->kegiatan->mata_anggaran)
            <div class="text-right">
                <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Mata Anggaran</label>
                <div class="mt-1">
                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded text-[10px] font-mono font-bold border border-indigo-100">
                        {{ $alokasi->kegiatan->mata_anggaran }}
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="border-b border-slate-100 pb-3">
            <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Jabatan</label>
            <p class="text-blue-600 font-medium">{{ $alokasi->jabatan->nama_jabatan ?? 'Petugas' }}</p>
        </div>
        <div class="border-b border-slate-100 pb-3">
            <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Volume</label>
            <p class="text-slate-800 font-semibold">
                {{ number_format($alokasi->volume_target, 2, ',', '.') }} 
                <span class="text-slate-400 font-normal text-xs ml-1">{{ $alokasi->aturanHks->satuan->nama_satuan ?? 'Satuan' }}</span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="border-b border-slate-100 pb-3">
            <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Harga Satuan Aktual</label>
            <p class="text-slate-800 font-semibold">Rp {{ number_format($alokasi->harga_satuan_aktual, 0, ',', '.') }}</p>
            @if($alokasi->aturanHks)
                <p class="text-[9px] text-slate-400 mt-1 italic leading-none">
                    *Maks HKS: Rp {{ number_format($alokasi->aturanHks->harga_satuan, 0, ',', '.') }}
                </p>
            @endif
        </div>
        <div class="border-b border-slate-100 pb-3">
            <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Nilai Tambah (Lain)</label>
            <p class="text-slate-800 font-semibold text-amber-600">Rp {{ number_format($alokasi->nilai_lain ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<!-- Footer Info -->
<div class="pt-4 flex items-center gap-2 text-slate-400 border-t border-slate-50">
    <i class="fas fa-calendar-alt text-xs"></i>
    <span class="text-[10px] font-medium uppercase tracking-tight">
        Dibuat pada: {{ $alokasi->created_at ? $alokasi->created_at->format('d M Y, H:i') : '-' }}
    </span>
</div>


</div>
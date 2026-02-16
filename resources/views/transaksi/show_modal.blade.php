<div class="space-y-6">
<!-- Header Status -->
<div class="flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-100">
<div class="flex items-center gap-3">
<div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
<i class="fas fa-file-invoice-dollar"></i>
</div>
<div>
<p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Status Pembayaran</p>
@php
$statusLabel = $alokasi->status_pembayaran ?? 'Belum';
$statusColor = [
'Belum' => 'text-amber-600',
'Proses' => 'text-blue-600',
'Terbayar' => 'text-green-600'
][$statusLabel] ?? 'text-slate-600';
@endphp
<p class="text-sm font-bold uppercase tracking-tight {{ $statusColor }}">
{{ $statusLabel }}
</p>
</div>
</div>
<div class="text-right">
<p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">ID Alokasi</p>
<p class="text-sm font-mono font-bold text-slate-700">#{{ str_pad($alokasi->id, 5, '0', STR_PAD_LEFT) }}</p>
</div>
</div>

<!-- Informasi Utama (Compact Style) -->
<div class="space-y-4 px-1">
    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
        <span class="text-slate-500 text-sm">Mitra / Penerima:</span>
        <span class="font-bold text-slate-800 text-right">{{ $alokasi->spk->mitra->nama_lengkap ?? $alokasi->spk->mitra->nama ?? 'N/A' }}</span>
    </div>

    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
        <span class="text-slate-500 text-sm">Nomor SPK:</span>
        <span class="font-mono text-sm font-bold text-slate-700">{{ $alokasi->spk->nomor_spk ?? '-' }}</span>
    </div>

    <div class="flex justify-between items-start border-b border-slate-100 pb-3">
        <span class="text-slate-500 text-sm">Kegiatan:</span>
        <span class="text-sm font-semibold text-slate-800 text-right max-w-[200px]">{{ $alokasi->kegiatan->nama_kegiatan ?? '-' }}</span>
    </div>

    @if($alokasi->kegiatan && $alokasi->kegiatan->mata_anggaran)
    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
        <span class="text-slate-500 text-sm">Mata Anggaran (Akun):</span>
        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded text-[10px] font-mono font-bold border border-indigo-100">
            {{ $alokasi->kegiatan->mata_anggaran }}
        </span>
    </div>
    @endif

    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
        <span class="text-slate-500 text-sm">Jabatan:</span>
        <span class="text-blue-600 font-bold text-sm uppercase tracking-tighter">{{ $alokasi->jabatan->nama_jabatan ?? 'Petugas' }}</span>
    </div>

    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
        <span class="text-slate-500 text-sm">Volume / Beban:</span>
        <div class="text-right">
            <span class="font-bold text-slate-800">{{ number_format($alokasi->volume_target, 2, ',', '.') }}</span>
            <span class="text-[10px] text-slate-400 uppercase font-bold ml-1">{{ $alokasi->aturanHks->satuan->nama_satuan ?? 'Satuan' }}</span>
        </div>
    </div>

    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
        <span class="text-slate-500 text-sm">Harga Satuan Aktual:</span>
        <div class="text-right">
            <span class="font-bold text-slate-800">Rp {{ number_format($alokasi->harga_satuan_aktual, 0, ',', '.') }}</span>
            @if($alokasi->aturanHks)
            <p class="text-[9px] text-slate-400 italic">Maks HKS: Rp {{ number_format($alokasi->aturanHks->harga_satuan, 0, ',', '.') }}</p>
            @endif
        </div>
    </div>

    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
        <span class="text-slate-500 text-sm">Nilai Tambah (Lain):</span>
        <span class="font-bold text-slate-800">Rp {{ number_format($alokasi->nilai_lain ?? 0, 0, ',', '.') }}</span>
    </div>

    <!-- Panel Total Ganti Rugi (Highlighted) -->
    <div class="mt-6 p-4 bg-blue-600 rounded-xl shadow-lg shadow-blue-100">
        <div class="flex justify-between items-center text-white">
            <span class="font-bold text-sm uppercase tracking-widest opacity-90">Total Ganti Rugi:</span>
            <span class="font-black text-xl tracking-tighter">Rp {{ number_format($alokasi->total_honor, 0, ',', '.') }}</span>
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
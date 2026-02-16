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

<!-- Informasi Utama -->
<div class="space-y-5 px-1">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Mitra</p>
            <p class="text-sm font-semibold text-slate-800">
                {{ $alokasi->spk->mitra->nama_lengkap ?? $alokasi->spk->mitra->nama ?? 'Data Mitra Hilang' }}
            </p>
        </div>
        <div>
            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Nomor SPK</p>
            <p class="text-sm font-mono text-slate-600">
                {{ $alokasi->spk->nomor_spk ?? 'Nomor Tidak Ada' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Jabatan</p>
            <p class="text-sm font-semibold text-blue-600 uppercase tracking-tight">
                {{ $alokasi->jabatan->nama_jabatan ?? 'Petugas' }}
            </p>
        </div>
        @if($alokasi->kegiatan && $alokasi->kegiatan->mata_anggaran)
        <div>
            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Mata Anggaran</p>
            <p class="text-sm font-mono text-indigo-600 font-bold">
                {{ $alokasi->kegiatan->mata_anggaran }}
            </p>
        </div>
        @endif
    </div>

    <hr class="border-slate-100">

    <div>
        <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Kegiatan</p>
        <p class="text-sm text-slate-700 leading-relaxed">{{ $alokasi->kegiatan->nama_kegiatan ?? '-' }}</p>
    </div>

    <!-- Rincian Perhitungan -->
    <div class="grid grid-cols-2 gap-4 pt-2">
        <div>
            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Honor Pokok</p>
            <div class="mt-1">
                <p class="text-sm font-semibold text-slate-800">
                    Rp {{ number_format($alokasi->volume_target * $alokasi->harga_satuan_aktual, 0, ',', '.') }}
                </p>
                <p class="text-[10px] text-slate-400 font-medium">
                    {{ number_format($alokasi->volume_target, 2, ',', '.') }} x {{ number_format($alokasi->harga_satuan_aktual, 0, ',', '.') }}
                </p>
            </div>
        </div>
        <div>
            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Nilai Tambah (Lain)</p>
            <p class="text-sm font-semibold text-slate-800 mt-1">
                Rp {{ number_format($alokasi->nilai_lain ?? 0, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Panel Total Ganti Rugi (Highlighted) -->
    <div class="bg-blue-600 p-4 rounded-xl shadow-lg shadow-blue-100 mt-4">
        <div class="flex justify-between items-center text-white">
            <div class="flex flex-col">
                <span class="text-[10px] font-bold uppercase tracking-widest opacity-80">Total Ganti Rugi</span>
                <span class="text-xl font-black tracking-tight">Rp {{ number_format($alokasi->total_honor, 0, ',', '.') }}</span>
            </div>
            <i class="fas fa-wallet text-2xl opacity-30"></i>
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
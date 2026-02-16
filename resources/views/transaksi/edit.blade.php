@extends('layout.app')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Edit Alokasi Honor</h2>
            <p class="text-sm text-slate-500">Perbarui data volume, harga, atau nilai ganti rugi untuk alokasi ini.</p>
        </div>
        <a href="{{ route('transaksi.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
        <div class="flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <p class="text-sm text-red-600 font-medium">{{ $errors->first() }}</p>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('transaksi.update', $alokasi->id) }}" method="POST" id="formEdit" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            <!-- Info Section (Read Only) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-slate-50 rounded-lg border border-slate-100">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Mitra / Penerima</label>
                    <p class="text-slate-800 font-semibold mt-1">
                        @if($alokasi->spk && $alokasi->spk->mitra)
                            {{ $alokasi->spk->mitra->nama_lengkap ?? $alokasi->spk->mitra->nama }}
                        @elseif(isset($currentSpk) && $currentSpk->mitra)
                            {{ $currentSpk->mitra->nama_lengkap ?? $currentSpk->mitra->nama }}
                        @else
                            <span class="text-red-400 italic">Data Mitra Tidak Terdeteksi</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Nomor SPK</label>
                    <p class="text-slate-800 font-mono text-sm mt-1">
                        {{ $alokasi->spk->nomor_spk ?? $currentSpk->nomor_spk ?? 'Nomor SPK Tidak Ditemukan' }}
                    </p>
                </div>
                <div class="md:col-span-2">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Kegiatan & Jabatan</label>
                            <p class="text-slate-800 mt-1">
                                {{ $alokasi->kegiatan->nama_kegiatan ?? '-' }} 
                                <span class="mx-2 text-slate-300">|</span>
                                <span class="text-blue-600 font-medium">{{ $alokasi->jabatan->nama_jabatan ?? 'Petugas' }}</span>
                            </p>
                        </div>
                        @if($alokasi->kegiatan && $alokasi->kegiatan->mata_anggaran)
                        <div class="sm:text-right">
                            <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Mata Anggaran</label>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-mono font-bold border border-indigo-200 mt-1">
                                <i class="fas fa-wallet text-[10px]"></i>
                                {{ $alokasi->kegiatan->mata_anggaran }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Input Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Volume -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700">Volume Pekerjaan</label>
                    <div class="relative group">
                        <input type="number" 
                               name="volume_target" 
                               id="volume_target"
                               value="{{ old('volume_target', (float)$alokasi->volume_target) }}" 
                               step="0.001"
                               min="0.001"
                               required
                               class="w-full pl-4 pr-16 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-lg font-semibold"
                               placeholder="0">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-medium text-sm">
                            {{ $alokasi->aturanHks->satuan->nama_satuan ?? 'Satuan' }}
                        </div>
                    </div>
                </div>

                <!-- Harga Satuan -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700">Harga Satuan (Aktual)</label>
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">Rp</div>
                        <input type="text" 
                               id="harga_input_display"
                               value="{{ number_format(old('harga_input', (int)$alokasi->harga_satuan_aktual), 0, ',', '.') }}" 
                               required
                               class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-lg font-semibold"
                               placeholder="0">
                        <input type="hidden" name="harga_input" id="harga_input" value="{{ old('harga_input', (int)$alokasi->harga_satuan_aktual) }}">
                    </div>
                    @if($alokasi->aturanHks)
                    <p class="text-[11px] text-amber-600 font-medium flex items-center gap-1 mt-1">
                        <i class="fas fa-info-circle"></i>
                        Maksimal HKS: Rp {{ number_format($alokasi->aturanHks->harga_satuan, 0, ',', '.') }}
                    </p>
                    @endif
                </div>

                <!-- Nilai Tambah (Lain) -->
                <div class="md:col-span-2 space-y-2">
                    <label class="block text-sm font-bold text-slate-700">Nilai Tambah (Lain / Ganti Rugi)</label>
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">Rp</div>
                        <input type="text" 
                               id="nilai_lain_display"
                               value="{{ number_format(old('nilai_lain', (int)$alokasi->nilai_lain), 0, ',', '.') }}" 
                               class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-lg font-semibold"
                               placeholder="0">
                        <input type="hidden" name="nilai_lain" id="nilai_lain" value="{{ old('nilai_lain', (int)$alokasi->nilai_lain) }}">
                    </div>
                </div>
            </div>

            <!-- Total Calculation Display -->
            <div class="p-6 bg-blue-600 rounded-2xl text-white shadow-lg shadow-blue-200">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-blue-100 text-xs font-bold uppercase tracking-widest">Total Ganti Rugi</p>
                        <h4 class="text-3xl font-black mt-1" id="total_display">
                            Rp {{ number_format($alokasi->total_honor, 0, ',', '.') }}
                        </h4>
                    </div>
                    <i class="fas fa-wallet text-4xl text-blue-400/50"></i>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('transaksi.index') }}" 
                   class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl shadow-md shadow-blue-200 transition-all font-bold text-sm flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const volumeInput = document.getElementById('volume_target');
const hargaInputDisplay = document.getElementById('harga_input_display');
const hargaInputHidden = document.getElementById('harga_input');
const nilaiLainDisplay = document.getElementById('nilai_lain_display');
const nilaiLainHidden = document.getElementById('nilai_lain');
const totalDisplay = document.getElementById('total_display');

function formatNumber(n) {
    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function calculateTotal() {
    const v = parseFloat(volumeInput.value) || 0;
    const h = parseFloat(hargaInputHidden.value) || 0;
    const l = parseFloat(nilaiLainHidden.value) || 0;
    
    // RUMUS: (Volume * Harga) + Nilai Lain
    const total = (v * h) + l;
    
    totalDisplay.innerText = 'Rp ' + total.toLocaleString('id-ID');
}

hargaInputDisplay.addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    hargaInputHidden.value = value;
    this.value = formatNumber(value);
    calculateTotal();
});

nilaiLainDisplay.addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    nilaiLainHidden.value = value;
    this.value = formatNumber(value);
    calculateTotal();
});

volumeInput.addEventListener('input', calculateTotal);

// Form submit safeguard
document.getElementById('formEdit').addEventListener('submit', function() {
    // Ensure hidden inputs have current numeric values
    hargaInputHidden.value = hargaInputDisplay.value.replace(/\D/g, '') || 0;
    nilaiLainHidden.value = nilaiLainDisplay.value.replace(/\D/g, '') || 0;
});
</script>

@endsection
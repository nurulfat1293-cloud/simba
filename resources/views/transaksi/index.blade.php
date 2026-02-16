@extends('layout.app')

@section('content')

<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Daftar Alokasi Honor</h2>
            <p class="text-sm text-slate-500">Monitoring dan pengelolaan alokasi honor mitra per kegiatan.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
            <!-- Form Pencarian & Filter -->
            <form action="{{ route('transaksi.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2 w-full">
                <!-- Filter Tahun -->
                <select name="tahun" onchange="this.form.submit()" class="px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer w-full sm:w-auto">
                    <option value="">Semua Tahun</option>
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <!-- Input Pencarian -->
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari Mitra / SPK / Kegiatan..." 
                        class="w-full pl-10 pr-10 py-2.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm">
                    
                    <!-- Icon Search -->
                    <button type="submit" class="absolute left-0 top-0 h-full w-10 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-colors">
                        <i class="fas fa-search"></i>
                    </button>

                    <!-- Icon Reset (Muncul jika ada pencarian) -->
                    @if(request('search'))
                    <a href="{{ route('transaksi.index', ['tahun' => request('tahun')]) }}" class="absolute right-0 top-0 h-full w-10 flex items-center justify-center text-slate-300 hover:text-red-500 transition-colors" title="Hapus Pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>

            <!-- Tombol Tambah -->
            <a href="{{ route('transaksi.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-sm transition-all font-medium text-sm whitespace-nowrap">
                <i class="fas fa-plus"></i>
                <span>Tambah</span>
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-start gap-3">
            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
            <p class="text-sm text-green-600">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="px-6 py-4 font-medium">Mitra & SPK</th>
                        <th class="px-6 py-4 font-medium">Kegiatan / Jabatan</th>
                        <th class="px-6 py-4 font-medium text-center">Mata Anggaran</th>
                        <th class="px-6 py-4 font-medium text-right">Volume</th>
                        <th class="px-6 py-4 font-medium text-right">Honor Pokok</th>
                        <th class="px-6 py-4 font-medium text-right">Nilai Lain</th>
                        <th class="px-6 py-4 font-medium text-right text-blue-600">Total (Ganti Rugi)</th>
                        <th class="px-6 py-4 font-medium text-center">Status</th>
                        <th class="px-6 py-4 font-medium text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($alokasi as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-800">
                                @if($item->spk && $item->spk->mitra)
                                    {{ $item->spk->mitra->nama_lengkap ?? $item->spk->mitra->nama }}
                                @else
                                    <span class="text-red-500 italic font-medium">Mitra Tidak Ditemukan</span>
                                @endif
                            </div>
                            <!-- PERBAIKAN: Menambahkan whitespace-nowrap agar Nomor SPK tidak terputus -->
                            <div class="text-[11px] text-slate-500 font-mono mt-1 flex items-center gap-1 whitespace-nowrap">
                                <i class="fas fa-file-contract text-[10px]"></i>
                                @if($item->spk)
                                    {{ $item->spk->nomor_spk }}
                                @else
                                    <span class="text-slate-400">ID SPK: {{ $item->id_spk }} (Relasi Error)</span>
                                @endif
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-sm text-slate-700 truncate max-w-xs font-medium">
                                {{ $item->kegiatan->nama_kegiatan ?? '-' }}
                            </div>
                            <div class="inline-block px-2 py-0.5 bg-blue-50 text-blue-600 rounded text-[10px] uppercase font-bold mt-1">
                                {{ $item->jabatan->nama_jabatan ?? 'Petugas' }}
                            </div>
                        </td>

                        <!-- Kolom Mata Anggaran -->
                        <td class="px-6 py-4 text-center">
                            <span class="font-mono text-[11px] font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded border border-slate-200">
                                {{ $item->kegiatan->mata_anggaran ?? '---' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <span class="text-sm font-medium text-slate-700">{{ number_format($item->volume_target, 2) }}</span>
                            <span class="text-xs text-slate-400 ml-1">{{ $item->aturanHks->satuan->nama_satuan ?? 'Urt' }}</span>
                        </td>

                        <!-- Honor Pokok (Volume x Harga Satuan) -->
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            @php $honorPokok = $item->volume_target * $item->harga_satuan_aktual; @endphp
                            <span class="text-sm font-medium text-slate-600">Rp {{ number_format($honorPokok, 0, ',', '.') }}</span>
                        </td>

                        <!-- Nilai Lain -->
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <span class="text-sm font-medium text-slate-400">Rp {{ number_format($item->nilai_lain ?? 0, 0, ',', '.') }}</span>
                        </td>

                        <!-- Total Ganti Rugi -->
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <span class="text-sm font-bold text-blue-700">Rp {{ number_format($item->total_honor, 0, ',', '.') }}</span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @php
                            $statusLabel = $item->status_pembayaran ?? 'Belum';
                            $statusClass = [
                                'Belum' => 'bg-amber-100 text-amber-700',
                                'Proses' => 'bg-blue-100 text-blue-700',
                                'Terbayar' => 'bg-green-100 text-green-700'
                            ][$statusLabel] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick="showDetail({{ $item->id }})" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Detail">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>

                                <a href="{{ route('transaksi.edit', $item->id) }}" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>

                                <form action="{{ route('transaksi.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-folder-open text-4xl mb-3 opacity-20"></i>
                                <span class="text-sm italic">Belum ada data alokasi honor.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($alokasi) && method_exists($alokasi, 'links'))
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $alokasi->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Detail -->
<div id="modalDetail" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                Detail Alokasi Honor
            </h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div id="modalContent" class="p-6">
            <div id="modalLoading" class="hidden flex flex-col items-center justify-center py-10 space-y-3">
                <div class="w-10 h-10 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin"></div>
                <p class="text-xs text-slate-400 font-medium">Memuat data...</p>
            </div>
            <div id="modalBody"></div>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="closeModal()" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">Tutup</button>
        </div>
    </div>
</div>

<script>
/**
 * Menampilkan modal detail dan memuat konten via AJAX
 */
function showDetail(id) {
    const modal = document.getElementById('modalDetail');
    const loading = document.getElementById('modalLoading');
    const body = document.getElementById('modalBody');

    modal.classList.remove('hidden');
    loading.classList.remove('hidden');
    body.innerHTML = '';

    fetch(`/transaksi/${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Server Error: ' + response.status);
        return response.text();
    })
    .then(html => {
        loading.classList.add('hidden');
        body.innerHTML = html;
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        loading.classList.add('hidden');
        body.innerHTML = `
            <div class="text-center py-6">
                <i class="fas fa-exclamation-triangle text-red-400 text-3xl mb-2"></i>
                <p class="text-sm text-slate-500 font-medium">Gagal memuat data. Periksa koneksi atau route server.</p>
                <p class="text-[10px] text-slate-400 mt-1">${error.message}</p>
            </div>`;
    });
}

function closeModal() {
    const modal = document.getElementById('modalDetail');
    if (modal) modal.classList.add('hidden');
}

window.onclick = function(event) {
    const modal = document.getElementById('modalDetail');
    if (event.target == modal) {
        closeModal();
    }
};
</script>

@endsection
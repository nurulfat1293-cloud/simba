@extends('layout.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('kegiatan.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Kegiatan
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Info Kegiatan -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-100">
                    <h5 class="font-bold text-slate-700 text-sm uppercase tracking-wider">Detail Kegiatan</h5>
                </div>
                <div class="p-6">
                    <h4 class="text-xl font-bold text-slate-800 mb-1">{{ $kegiatan->nama_kegiatan }}</h4>
                    <span class="inline-block px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 mb-6">
                        {{ $kegiatan->jenisKegiatan->nama_jenis }}
                    </span>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1">Periode Pelaksanaan</p>
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center text-slate-600 text-sm">
                                    <i class="far fa-calendar-check w-5 text-slate-400"></i>
                                    <span>{{ date('d M Y', strtotime($kegiatan->tanggal_mulai)) }}</span>
                                </div>
                                <div class="flex items-center text-slate-600 text-sm">
                                    <i class="far fa-calendar-times w-5 text-slate-400"></i>
                                    <span>{{ date('d M Y', strtotime($kegiatan->tanggal_akhir)) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-2">Status Saat Ini</p>
                            @if($kegiatan->status_kegiatan == 'Persiapan')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-amber-100 text-amber-700 w-full justify-center">
                                    <span class="w-2 h-2 bg-amber-500 rounded-full mr-2"></span> Persiapan
                                </span>
                            @elseif($kegiatan->status_kegiatan == 'Berjalan')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-700 w-full justify-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span> Berjalan
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-slate-100 text-slate-600 w-full justify-center">
                                    <span class="w-2 h-2 bg-slate-500 rounded-full mr-2"></span> Selesai
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Action (Optional) -->
            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                <p class="text-xs text-blue-600 mb-3">Perlu mengubah data dasar kegiatan ini?</p>
                <a href="{{ route('kegiatan.edit', $kegiatan->id) }}" class="block w-full text-center py-2 bg-white border border-blue-200 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all text-sm font-medium shadow-sm">
                    Edit Informasi Kegiatan
                </a>
            </div>
        </div>

        <!-- Kolom Kanan: Aturan HKS -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden h-full flex flex-col">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h5 class="font-bold text-slate-800">Aturan Harga Satuan (HKS)</h5>
                    <span class="bg-slate-100 text-slate-500 text-xs px-2 py-1 rounded">
                        {{ $daftar_hks->count() }} Aturan
                    </span>
                </div>
                
                <div class="p-6 flex-1">
                    <!-- Form Tambah HKS -->
                    <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 mb-6">
                        <h6 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">Tambah Standar Harga Baru</h6>
                        <form action="{{ route('kegiatan.storeHks') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                            @csrf
                            <input type="hidden" name="id_kegiatan" value="{{ $kegiatan->id }}">
                            
                            <div class="sm:col-span-5">
                                <label class="sr-only">Satuan</label>
                                <select name="id_satuan" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3" required>
                                    <option value="">-- Pilih Satuan --</option>
                                    @foreach($ref_satuan as $satuan)
                                        <option value="{{ $satuan->id }}">{{ $satuan->nama_satuan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="sm:col-span-5">
                                <label class="sr-only">Harga</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">Rp</span>
                                    <input type="number" name="harga_satuan" class="w-full pl-8 pr-3 py-2 rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Contoh: 5000" required>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <button type="submit" class="w-full h-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm font-medium text-sm transition-colors flex items-center justify-center gap-1">
                                    <i class="fas fa-plus text-xs"></i> <span class="hidden sm:inline">Add</span><span class="sm:hidden">Tambah</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tabel Daftar HKS -->
                    <div class="overflow-x-auto border border-slate-200 rounded-lg">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold tracking-wider border-b border-slate-200">
                                    <th class="px-4 py-3 font-medium">Satuan Output</th>
                                    <th class="px-4 py-3 font-medium text-right">Harga Satuan</th>
                                    <th class="px-4 py-3 font-medium text-center w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($daftar_hks as $hks)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        {{ $hks->satuan->nama_satuan }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-700 font-mono font-medium text-right">
                                        Rp {{ number_format($hks->harga_satuan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <form action="{{ route('kegiatan.destroyHks', $hks->id) }}" method="POST" onsubmit="return confirm('Hapus aturan harga ini?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-400 hover:text-red-600 p-1.5 hover:bg-red-50 rounded transition-all" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-slate-400 text-sm italic bg-slate-50/50">
                                        Belum ada aturan harga yang ditambahkan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Info Alert -->
                    <div class="mt-6 flex items-start gap-3 p-4 bg-sky-50 text-sky-700 rounded-lg border border-sky-100 text-sm">
                        <i class="fas fa-info-circle mt-0.5 text-sky-500"></i>
                        <p class="leading-relaxed">
                            Harga yang Anda masukkan di sini akan menjadi acuan dasar (plafon) saat membuat SPK dan menghitung honor mitra. Pastikan nominal sesuai dengan peraturan anggaran yang berlaku.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layout.app')

@section('content')

<div class="space-y-6">
    <!-- Header & Aksi -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Daftar SPK</h2>
            <p class="text-sm text-slate-500">Kelola administrasi Surat Perintah Kerja Mitra.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
            <!-- Form Filter -->
            <form action="{{ route('spk.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2 w-full">
                <!-- Filter Tahun -->
                <select name="tahun" onchange="this.form.submit()" class="px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                    <option value="">Semua Tahun</option>
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <!-- Input Pencarian -->
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari Nomor SPK / Mitra..." 
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm">
                    
                    <button type="submit" class="absolute left-0 top-0 h-full w-10 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <!-- Tombol Buat Baru -->
            <a href="{{ route('spk.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-sm transition-all font-medium text-sm whitespace-nowrap">
                <i class="fas fa-plus"></i>
                <span>Buat SPK Baru</span>
            </a>
        </div>
    </div>

    <!-- Pesan Notifikasi -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-start gap-3">
        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
        <p class="text-sm text-green-600">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex items-start gap-3">
        <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
        <p class="text-sm text-red-600">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Kartu Tabel -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="px-6 py-4 font-medium text-center w-16">No Urut</th>
                        <th class="px-6 py-4 font-medium">Nomor SPK</th>
                        <th class="px-6 py-4 font-medium">Mitra</th>
                        <th class="px-6 py-4 font-medium text-center">Periode</th>
                        <th class="px-6 py-4 font-medium text-center">Tgl. TTD</th>
                        <th class="px-6 py-4 font-medium text-center min-w-[240px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($spk as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-center font-mono text-sm text-slate-500">{{ $item->nomor_urut }}</td>
                        <td class="px-6 py-4 font-bold text-slate-800 text-sm">
                            {{ (string) $item->nomor_spk }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $item->mitra->nama_lengkap ?? ($item->mitra->nama_mitra ?? 'N/A') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-semibold uppercase tracking-wide">
                                {{-- PERBAIKAN: Menambahkan locale('id') agar tampil dalam bahasa Indonesia (Januari, dsb) --}}
                                {{ \Carbon\Carbon::create()->month($item->bulan)->locale('id')->translatedFormat('F') }} {{ $item->tahun }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-slate-500">
                            {{ date('d/m/Y', strtotime($item->tanggal_spk)) }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 justify-center">
                                <a href="{{ route('spk.show', $item->nomor_urut) }}" 
                                   class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 border border-gray-200 transition-colors"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>

                                <a href="{{ route('spk.edit', $item->nomor_urut) }}" 
                                   class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 border border-amber-100 transition-colors"
                                   title="Edit Data">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>

                                <a href="{{ route('transaksi.print-spk', $item->nomor_urut) }}" 
                                   target="_blank" 
                                   class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 border border-blue-100 transition-colors"
                                   title="Cetak PDF">
                                    <i class="fas fa-print text-xs"></i>
                                </a>

                                <a href="{{ route('spk.word', $item->nomor_urut) }}" 
                                   class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 border border-indigo-100 transition-colors"
                                   title="Download Word">
                                    <i class="fas fa-file-word text-xs"></i>
                                </a>

                                <form action="{{ route('spk.destroy', $item->nomor_urut) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SPK ini? Nomor urut lainnya akan bergeser otomatis.')" class="inline">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 border border-red-100 transition-colors" title="Hapus Data">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 bg-slate-50/50">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-file-contract text-2xl text-slate-400"></i>
                                </div>
                                <p class="font-medium text-slate-600">Belum ada data SPK.</p>
                                <p class="text-xs text-slate-400 mt-1">Silakan buat SPK baru atau ubah filter pencarian.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($spk->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $spk->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
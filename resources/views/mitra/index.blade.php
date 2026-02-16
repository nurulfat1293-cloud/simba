@extends('layout.app')

@section('content')
<div class="space-y-6">
    <!-- Header Section & Actions -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Data Mitra Statistik</h2>
            <p class="text-sm text-slate-500">Kelola data PPL, PML, dan Koseka di sini.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
            <!-- Form Pencarian -->
            <form action="{{ route('mitra.index') }}" method="GET" class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari Nama / NIK / Desa..." 
                    class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm">
                <i class="fas fa-search absolute left-3 top-3 text-slate-400"></i>
            </form>

            <div class="flex gap-2 overflow-x-auto pb-1 sm:pb-0">
                <!-- Tombol Export Data -->
                <a href="{{ url('/mitra/export') }}" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-800 text-white px-4 py-2.5 rounded-lg shadow-sm transition-all font-medium text-sm whitespace-nowrap">
                    <i class="fas fa-file-export"></i>
                    <span>Export</span>
                </a>

                <!-- Dropdown / Group Import -->
                <div class="flex gap-2">
                    <a href="{{ url('/mitra/template') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg shadow-sm transition-all font-medium text-sm whitespace-nowrap" title="Download Template Import">
                        <i class="fas fa-file-excel"></i>
                        <span class="hidden sm:inline">Template</span>
                    </a>
                    
                    <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg shadow-sm transition-all font-medium text-sm whitespace-nowrap">
                        <i class="fas fa-file-upload"></i>
                        <span>Import</span>
                    </button>
                </div>

                <!-- Tombol Tambah Manual -->
                <a href="{{ route('mitra.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-sm transition-all font-medium text-sm whitespace-nowrap">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Tambah</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-start gap-3 animate-fade-in">
        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
        <div>
            <p class="text-sm text-green-700 font-medium">Berhasil</p>
            <p class="text-sm text-green-600">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex items-start gap-3 animate-fade-in">
        <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
        <div>
            <p class="text-sm text-red-700 font-medium">Gagal</p>
            <p class="text-sm text-red-600">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="px-6 py-4 font-medium text-center w-16">No</th>
                        <th class="px-6 py-4 font-medium">Identitas Mitra</th>
                        <th class="px-6 py-4 font-medium">Asal Wilayah</th>
                        <th class="px-6 py-4 font-medium">Kontak & Bank</th>
                        <th class="px-6 py-4 font-medium text-center">Status</th>
                        <th class="px-6 py-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mitra as $item)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4 text-center text-slate-500 text-sm">
                            {{ ($mitra->currentPage() - 1) * $mitra->perPage() + $loop->iteration }}
                        </td>
                        
                        <!-- Identitas -->
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800 text-sm">{{ $item->nama_lengkap }}</span>
                                <span class="text-xs text-slate-400 font-mono mt-0.5">NIK: {{ $item->nik }}</span>
                            </div>
                        </td>

                        <!-- Wilayah -->
                        <td class="px-6 py-4 text-slate-600 text-sm">
                            <span class="block font-medium">{{ $item->asal_desa }}</span>
                            <span class="text-xs text-slate-400">Kec. {{ $item->asal_kecamatan }}</span>
                        </td>

                        <!-- Kontak & Bank -->
                        <td class="px-6 py-4 text-sm">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center text-slate-600 text-xs">
                                    <i class="fas fa-phone-alt w-5 text-slate-400"></i> 
                                    <span>{{ $item->no_hp }}</span>
                                </div>
                                <div class="flex items-center text-slate-600 text-xs">
                                    <i class="fas fa-university w-5 text-slate-400"></i> 
                                    <span class="truncate max-w-[150px]" title="{{ $item->nama_bank }} - {{ $item->nomor_rekening }}">
                                        {{ $item->nama_bank }} - {{ $item->nomor_rekening }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4 text-center">
                            @if($item->status_aktif)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                    Non-Aktif
                                </span>
                            @endif
                        </td>

                        <!-- Action Buttons -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center items-center gap-2">
                                <a href="{{ route('mitra.edit', $item->id) }}" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors border border-amber-100" title="Edit Data">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                
                                <form action="{{ route('mitra.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus mitra ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors border border-red-100" title="Hapus Permanen">
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
                                    <i class="fas fa-search text-2xl text-slate-400"></i>
                                </div>
                                <p class="font-medium text-slate-600">Data tidak ditemukan.</p>
                                <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian lain.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $mitra->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div id="importModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative p-5 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 mb-4">
                <i class="fas fa-file-import text-indigo-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Import Data Mitra</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">
                    Silakan upload file Excel (.xlsx) sesuai dengan format template yang telah disediakan.
                </p>
                <!-- Form Import -->
                <form action="{{ url('/mitra/import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2 text-left">Pilih File Excel</label>
                        <input type="file" name="file_excel" required
                            class="block w-full text-sm text-slate-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2">
                    </div>
                    <div class="flex justify-end gap-3 mt-5">
                        <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-lg w-full shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-lg w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layout.app')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Arsip Digital SPK (Per Bulan)</h2>
            <p class="text-sm text-slate-500">Kelola link folder Google Drive berisi scan SPK berdasarkan periode.</p>
        </div>
        
        <!-- Filter Tahun -->
        <form action="{{ route('arsip.index') }}" method="GET" class="flex gap-2">
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                <option value="">Semua Tahun</option>
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
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

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="px-6 py-4 font-medium text-center w-16">No</th>
                        <th class="px-6 py-4 font-medium">Periode Arsip</th>
                        <th class="px-6 py-4 font-medium text-center">Jumlah Dokumen</th>
                        <th class="px-6 py-4 font-medium text-center">Status Folder</th>
                        <th class="px-6 py-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($periodes as $item)
                    @php
                        // PERBAIKAN: Menambahkan locale('id') agar nama bulan tampil dalam Bahasa Indonesia
                        $namaBulan = \Carbon\Carbon::create()->month($item->bulan)->locale('id')->translatedFormat('F');
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-center text-slate-500 text-sm">
                            {{ ($periodes->currentPage() - 1) * $periodes->perPage() + $loop->iteration }}
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <i class="far fa-folder-open text-lg"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800 text-sm">{{ $namaBulan }} {{ $item->tahun }}</span>
                                    <span class="text-xs text-slate-400">Arsip Bulanan</span>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                {{ $item->total_dokumen }} SPK
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($item->link_folder)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                    <i class="fas fa-link"></i> Terhubung
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                    <i class="fas fa-unlink"></i> Belum Ada
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center items-center gap-2">
                                @if($item->link_folder)
                                    <a href="{{ $item->link_folder }}" target="_blank" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors border border-blue-100" title="Buka Folder GDrive">
                                        <i class="fab fa-google-drive"></i>
                                    </a>
                                @endif
                                
                                <button onclick="openModal('{{ $item->bulan }}', '{{ $item->tahun }}', '{{ $namaBulan }}', '{{ $item->link_folder }}')" 
                                    class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors border border-amber-100" title="Setting Link Folder">
                                    <i class="fas fa-cog"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500 bg-slate-50/50">
                            <p class="font-medium text-slate-600">Belum ada data SPK untuk periode ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $periodes->links() }}
        </div>
    </div>
</div>

<!-- Modal Input Link Folder -->
<div id="folderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="relative p-6 border w-full max-w-md shadow-xl rounded-xl bg-white transform transition-all">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-gray-900">Setting Folder Arsip</h3>
            <p class="text-sm text-gray-500">Periode: <span id="modalPeriode" class="font-bold text-slate-700"></span></p>
        </div>
        
        <form method="POST" action="{{ route('arsip.update') }}">
            @csrf
            <input type="hidden" name="bulan" id="inputBulan">
            <input type="hidden" name="tahun" id="inputTahun">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Link Folder Google Drive</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fab fa-google-drive text-gray-400"></i>
                    </div>
                    <input type="url" name="link_folder" id="inputLink" placeholder="https://drive.google.com/drive/folders/..." 
                        class="pl-10 w-full rounded-lg border-gray-300 border focus:border-blue-500 focus:ring-blue-500 py-2 text-sm">
                </div>
                <p class="text-xs text-slate-400 mt-1">Pastikan link folder dapat diakses (Public/Shared).</p>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('folderModal').classList.add('hidden')"
                    class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    Simpan Link
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(bulan, tahun, namaBulan, currentLink) {
        document.getElementById('inputBulan').value = bulan;
        document.getElementById('inputTahun').value = tahun;
        document.getElementById('modalPeriode').innerText = namaBulan + ' ' + tahun;
        document.getElementById('inputLink').value = currentLink || '';
        document.getElementById('folderModal').classList.remove('hidden');
    }
</script>
@endsection
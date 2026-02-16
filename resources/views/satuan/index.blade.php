@extends('layout.app')

@section('content')

<div class="space-y-6">
<!-- Header Section -->
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
<div>
<h2 class="text-2xl font-bold text-slate-800">Master Data Satuan</h2>
<p class="text-sm text-slate-500">Kelola referensi satuan untuk kegiatan statistik (Unit, Orang, Dokumen, dll).</p>
</div>
<a href="{{ route('satuan.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-sm transition-all font-medium text-sm">
<i class="fas fa-plus"></i>
<span>Tambah Satuan</span>
</a>
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
        <table class="w-full text-left border-collapse table-fixed">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                    <th class="px-6 py-4 font-medium text-center w-20">No</th>
                    <th class="px-6 py-4 font-medium">Nama Satuan</th>
                    <th class="px-6 py-4 font-medium text-center w-40">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($satuan as $item)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-center text-slate-500 text-sm font-mono">
                        {{ $loop->iteration + $satuan->firstItem() - 1 }}
                    </td>
                    
                    <!-- Nama Satuan -->
                    <td class="px-6 py-4">
                        <span class="font-bold text-slate-800 text-sm tracking-tight">{{ $item->nama_satuan }}</span>
                    </td>

                    <!-- Action Buttons (Selalu Tampil) -->
                    <td class="px-6 py-4">
                        <div class="flex justify-center items-center gap-2">
                            <!-- Tombol Detail -->
                            <a href="{{ route('satuan.show', $item->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors border border-blue-100 shrink-0" title="Lihat Detail">
                                <i class="fas fa-eye text-xs"></i>
                            </a>

                            <!-- Tombol Edit -->
                            <a href="{{ route('satuan.edit', $item->id) }}" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors border border-amber-100 shrink-0" title="Edit Data">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            
                            <!-- Tombol Hapus -->
                            <form action="{{ route('satuan.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus satuan {{ $item->nama_satuan }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors border border-red-100 shrink-0" title="Hapus Permanen">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center text-slate-500 bg-slate-50/50">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-cube text-2xl text-slate-400"></i>
                            </div>
                            <p class="font-medium text-slate-600">Belum ada data satuan.</p>
                            <p class="text-xs text-slate-400 mt-1">Silakan tambahkan satuan baru terlebih dahulu.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($satuan->hasPages())
    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
        {{ $satuan->links() }}
    </div>
    @endif
</div>


</div>
@endsection
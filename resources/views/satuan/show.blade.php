@extends('layout.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('satuan.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Detail Satuan</h2>
            <p class="text-sm text-slate-500">Informasi lengkap mengenai satuan kegiatan.</p>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 space-y-6">
            <!-- Informasi Utama -->
            <div class="grid grid-cols-1 gap-6">
                <div class="border-b border-slate-100 pb-4">
                    <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Nama Satuan</label>
                    <div class="text-lg font-medium text-slate-800">
                        {{ $satuan->nama_satuan }}
                    </div>
                </div>

                <div class="flex gap-6">
                    <div>
                        <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Dibuat Pada</label>
                        <div class="text-sm text-slate-600">
                            {{ $satuan->created_at ? $satuan->created_at->format('d M Y H:i') : '-' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Terakhir Diupdate</label>
                        <div class="text-sm text-slate-600">
                            {{ $satuan->updated_at ? $satuan->updated_at->format('d M Y H:i') : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('satuan.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-800 bg-white border border-slate-300 rounded-lg hover:bg-slate-100 transition-colors">
                Kembali
            </a>
            <a href="{{ route('satuan.edit', $satuan->id) }}" class="px-4 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                <i class="fas fa-edit"></i>
                Edit Data
            </a>
        </div>
    </div>
</div>
@endsection
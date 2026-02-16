@extends('layout.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('tag_kegiatan.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Detail Kelompok Kegiatan</h2>
            <p class="text-sm text-slate-500">Informasi detil mengenai kategori kegiatan.</p>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 space-y-6">
            <!-- Informasi Utama -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Nama Tag -->
                <div class="border-b border-slate-100 pb-4">
                    <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Nama Kelompok (Tag)</label>
                    <div class="text-lg font-medium text-slate-800 flex items-center gap-2">
                        <i class="fas fa-tag text-blue-500 text-sm"></i>
                        {{ $tag->nama_tag }}
                    </div>
                </div>

                <!-- Statistik (Opsional, jika ada relation count) -->
                @if(isset($tag->kegiatan_count))
                <div class="border-b border-slate-100 pb-4">
                    <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Jumlah Kegiatan Terkait</label>
                    <span class="inline-flex items-center px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-sm font-medium">
                        {{ $tag->kegiatan_count }} Kegiatan
                    </span>
                </div>
                @endif

                <!-- Timestamps -->
                <div class="flex flex-col sm:flex-row gap-6 pt-2">
                    <div>
                        <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Dibuat Pada</label>
                        <div class="text-sm text-slate-600 flex items-center gap-2">
                            <i class="far fa-calendar-alt text-slate-400"></i>
                            {{ $tag->created_at ? $tag->created_at->format('d M Y H:i') : '-' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Terakhir Diupdate</label>
                        <div class="text-sm text-slate-600 flex items-center gap-2">
                            <i class="far fa-clock text-slate-400"></i>
                            {{ $tag->updated_at ? $tag->updated_at->format('d M Y H:i') : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('tag_kegiatan.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-800 bg-white border border-slate-300 rounded-lg hover:bg-slate-100 transition-colors">
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection
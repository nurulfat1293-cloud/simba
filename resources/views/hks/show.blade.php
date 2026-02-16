@extends('layout.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('hks.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Detail Standar Harga</h2>
            <p class="text-sm text-slate-500">Informasi detil batasan harga kegiatan.</p>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 space-y-6">
            <!-- Informasi Utama -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Tag Kegiatan -->
                <div class="border-b border-slate-100 pb-4">
                    <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Tag Kegiatan</label>
                    <div class="text-lg font-medium text-slate-800 flex items-center gap-2">
                        <i class="fas fa-tag text-blue-500 text-sm"></i>
                        {{ $hks->tagKegiatan->nama_tag ?? 'Tidak diketahui' }}
                    </div>
                </div>

                <!-- Jabatan & Satuan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-b border-slate-100 pb-4">
                    <div>
                        <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Jabatan Petugas</label>
                        <div class="text-base text-slate-800">
                            {{ $hks->jabatan->nama_jabatan ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Satuan Hitung</label>
                        <span class="inline-block px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-sm font-bold uppercase">
                            {{ $hks->satuan->nama_satuan ?? '-' }}
                        </span>
                    </div>
                </div>

                <!-- Harga Satuan (Highlight) -->
                <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-100">
                    <label class="block text-xs uppercase tracking-wide text-emerald-600 font-semibold mb-1">Batas Harga Satuan</label>
                    <div class="text-3xl font-bold text-emerald-700 font-mono">
                        Rp {{ number_format($hks->harga_satuan, 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-emerald-600 mt-1">Harga ini adalah batas maksimal per satuan.</p>
                </div>

                <!-- Timestamps -->
                <div class="flex flex-col sm:flex-row gap-6 pt-2">
                    <div>
                        <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Dibuat Pada</label>
                        <div class="text-sm text-slate-600 flex items-center gap-2">
                            <i class="far fa-calendar-alt text-slate-400"></i>
                            {{ $hks->created_at ? $hks->created_at->format('d M Y H:i') : '-' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Terakhir Diupdate</label>
                        <div class="text-sm text-slate-600 flex items-center gap-2">
                            <i class="far fa-clock text-slate-400"></i>
                            {{ $hks->updated_at ? $hks->updated_at->format('d M Y H:i') : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('hks.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-800 bg-white border border-slate-300 rounded-lg hover:bg-slate-100 transition-colors">
                Kembali
            </a>
            <!-- Catatan: Tombol Edit tidak disertakan di sini karena Edit menggunakan Modal di halaman Index -->
        </div>
    </div>
</div>
@endsection
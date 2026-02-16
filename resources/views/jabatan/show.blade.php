@extends('layout.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('jabatan.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Detail Jabatan</h2>
            <p class="text-sm text-slate-500">Informasi lengkap mengenai peran mitra lapangan.</p>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 space-y-6">
            <!-- Informasi Utama -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Kode Jabatan -->
                <div class="border-b border-slate-100 pb-4">
                    <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Kode Jabatan</label>
                    <div class="inline-block px-3 py-1 bg-blue-50 text-blue-700 rounded-md text-sm font-bold uppercase border border-blue-100">
                        {{ $jabatan->kode_jabatan }}
                    </div>
                </div>

                <!-- Nama Jabatan -->
                <div class="border-b border-slate-100 pb-4">
                    <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Nama Jabatan</label>
                    <div class="text-lg font-medium text-slate-800">
                        {{ $jabatan->nama_jabatan }}
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="flex flex-col sm:flex-row gap-6 pt-2">
                    <div>
                        <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Dibuat Pada</label>
                        <div class="text-sm text-slate-600 flex items-center gap-2">
                            <i class="far fa-calendar-alt text-slate-400"></i>
                            {{ $jabatan->created_at ? $jabatan->created_at->format('d M Y H:i') : '-' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-wide text-slate-400 font-semibold mb-1">Terakhir Diupdate</label>
                        <div class="text-sm text-slate-600 flex items-center gap-2">
                            <i class="far fa-clock text-slate-400"></i>
                            {{ $jabatan->updated_at ? $jabatan->updated_at->format('d M Y H:i') : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('jabatan.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-800 bg-white border border-slate-300 rounded-lg hover:bg-slate-100 transition-colors">
                Kembali
            </a>
            <a href="{{ route('jabatan.edit', $jabatan->id) }}" class="px-4 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                <i class="fas fa-edit"></i>
                Edit Data
            </a>
        </div>
    </div>
</div>
@endsection
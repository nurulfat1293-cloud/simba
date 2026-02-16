@extends('layout.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('satuan.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Edit Satuan</h2>
            <p class="text-sm text-slate-500">Perbarui informasi satuan kegiatan.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('satuan.update', $satuan->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label for="nama_satuan" class="block text-sm font-medium text-slate-700 mb-1">Nama Satuan <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="nama_satuan" 
                           id="nama_satuan" 
                           class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-slate-800 placeholder:text-slate-400"
                           placeholder="Contoh: Orang, Dokumen, Rumah Tangga" 
                           value="{{ old('nama_satuan', $satuan->nama_satuan) }}" 
                           required>
                    @error('nama_satuan')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                <a href="{{ route('satuan.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-800 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
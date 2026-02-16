@extends('layout.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('jabatan.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Tambah Jabatan</h2>
            <p class="text-sm text-slate-500">Definisikan peran baru untuk mitra lapangan.</p>
        </div>
    </div>

    <!-- Alert untuk error umum -->
    @if($errors->has('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 text-red-700 text-sm">
        {{ $errors->first('error') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('jabatan.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <!-- INPUT KODE JABATAN (PENTING: Tadi ini tidak ada) -->
                <div>
                    <label for="kode_jabatan" class="block text-sm font-medium text-slate-700 mb-1">Kode Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_jabatan" id="kode_jabatan" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-slate-800 uppercase" placeholder="Contoh: PPL" value="{{ old('kode_jabatan') }}" required>
                    @error('kode_jabatan')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_jabatan" class="block text-sm font-medium text-slate-700 mb-1">Nama Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_jabatan" id="nama_jabatan" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-slate-800" placeholder="Contoh: Pendata (PPL)" value="{{ old('nama_jabatan') }}" required>
                    @error('nama_jabatan')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                <a href="{{ route('jabatan.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">Batal</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm flex items-center gap-2 transform active:scale-95 transition-all">
                    <i class="fas fa-save"></i> Simpan Jabatan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@extends('layout.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('kegiatan.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Kegiatan
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- Card Header -->
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Edit Kegiatan</h2>
                <p class="text-xs text-slate-500">Perbarui informasi dan status pelaksanaan kegiatan.</p>
            </div>
            <div class="bg-amber-100 text-amber-600 p-2 rounded-lg">
                <i class="fas fa-edit"></i>
            </div>
        </div>

        <!-- Form Content -->
        <form action="{{ route('kegiatan.update', $kegiatan->id) }}" method="POST" class="p-6 md:p-8">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Tag/Kelompok Kegiatan -->
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tag/Kelompok Kegiatan <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-tags"></i>
                        </span>
                        <select name="id_tag_kegiatan" class="w-full pl-10 pr-3 py-2.5 rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            @foreach($tag_kegiatan as $tag)
                                <option value="{{ $tag->id }}" {{ old('id_tag_kegiatan', $kegiatan->id_tag_kegiatan) == $tag->id ? 'selected' : '' }}>{{ $tag->nama_tag }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Nama Kegiatan -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kegiatan <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-heading"></i>
                        </span>
                        <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}" class="w-full pl-10 pr-3 py-2.5 rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    </div>
                </div>

                <!-- Mata Anggaran (Akun) -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mata Anggaran (Akun) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-wallet"></i>
                        </span>
                        <input type="text" name="mata_anggaran" value="{{ old('mata_anggaran', $kegiatan->mata_anggaran) }}" class="w-full pl-10 pr-3 py-2.5 rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Contoh: 524113" required>
                    </div>
                </div>

                <!-- Jenis Kegiatan -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kegiatan <span class="text-red-500">*</span></label>
                    <div class="relative">
                         <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-tag"></i>
                        </span>
                        <select name="id_jenis_kegiatan" class="w-full pl-10 pr-3 py-2.5 rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            @foreach($jenis_kegiatan as $jenis)
                                <option value="{{ $jenis->id }}" {{ old('id_jenis_kegiatan', $kegiatan->id_jenis_kegiatan) == $jenis->id ? 'selected' : '' }}>{{ $jenis->nama_jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tanggal Pelaksanaan -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <label class="block text-sm font-medium text-slate-700 mb-3">Periode Pelaksanaan</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-slate-500 mb-1 block">Tanggal Mulai</span>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $kegiatan->tanggal_mulai->format('Y-m-d')) }}" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3" required>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 mb-1 block">Tanggal Berakhir</span>
                            <input type="date" name="tanggal_akhir" value="{{ old('tanggal_akhir', $kegiatan->tanggal_akhir->format('Y-m-d')) }}" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3" required>
                        </div>
                    </div>
                </div>

                <!-- Status Kegiatan -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status Pelaksanaan</label>
                    <div class="relative">
                        <select name="status_kegiatan" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-white" required>
                            <option value="Persiapan" {{ old('status_kegiatan', $kegiatan->status_kegiatan) == 'Persiapan' ? 'selected' : '' }}>Persiapan</option>
                            <option value="Berjalan" {{ old('status_kegiatan', $kegiatan->status_kegiatan) == 'Berjalan' ? 'selected' : '' }}>Berjalan</option>
                            <option value="Selesai" {{ old('status_kegiatan', $kegiatan->status_kegiatan) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">
                        <i class="fas fa-info-circle text-blue-400 mr-1"></i>
                        Ubah ke <strong>Selesai</strong> jika kegiatan sudah rampung dan pembayaran honor telah lunas.
                    </p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('kegiatan.index') }}" class="px-5 py-2.5 rounded-lg text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 font-medium text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-md hover:shadow-lg font-medium text-sm transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
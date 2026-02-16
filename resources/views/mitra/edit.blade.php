@extends('layout.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('mitra.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Mitra
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- Card Header -->
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Edit Data Mitra</h2>
                <p class="text-xs text-slate-500">Perbarui informasi mitra di bawah ini.</p>
            </div>
            <div class="bg-amber-100 text-amber-600 p-2 rounded-lg">
                <i class="fas fa-user-edit"></i>
            </div>
        </div>

        <!-- Form Content -->
        <form action="{{ route('mitra.update', $mitra->id) }}" method="POST" class="p-6 md:p-8">
            @csrf
            @method('PUT')
            
            <!-- Section 1: Identitas & Kontak -->
            <div class="mb-8">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-id-card"></i> Identitas & Alamat
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Lengkap -->
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ $mitra->nama_lengkap }}" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3" required>
                    </div>

                    <!-- NIK -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">NIK</label>
                        <input type="number" name="nik" value="{{ $mitra->nik }}" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3" required>
                    </div>

                    <!-- No HP -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nomor HP</label>
                        <input type="number" name="no_hp" value="{{ $mitra->no_hp }}" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3" required>
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Alamat Domisili</label>
                        <textarea name="alamat" rows="2" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3" required>{{ $mitra->alamat }}</textarea>
                    </div>

                    <!-- Kecamatan -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Asal Kecamatan</label>
                        <input type="text" name="asal_kecamatan" value="{{ $mitra->asal_kecamatan }}" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3" required>
                    </div>

                    <!-- Desa -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Asal Desa/Kelurahan</label>
                        <input type="text" name="asal_desa" value="{{ $mitra->asal_desa }}" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3" required>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100 my-8">

            <!-- Section 2: Data Pembayaran -->
            <div class="mb-8">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-wallet"></i> Data Rekening
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-5 rounded-xl border border-slate-100">
                    <!-- Nama Bank (Input Text sesuai kode lama Anda, atau bisa diganti Select jika mau) -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Bank</label>
                        <input type="text" name="nama_bank" value="{{ $mitra->nama_bank }}" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3" placeholder="Contoh: BRI">
                    </div>

                    <!-- Nomor Rekening -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nomor Rekening</label>
                        <input type="number" name="nomor_rekening" value="{{ $mitra->nomor_rekening }}" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                    </div>
                </div>
            </div>

            <hr class="border-slate-100 my-8">

            <!-- Section 3: Status Mitra (Penting untuk Edit) -->
            <div class="mb-8">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-toggle-on"></i> Status Keaktifan
                </h3>
                
                <div class="bg-white border border-slate-200 rounded-lg p-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status Saat Ini</label>
                    <div class="flex items-center gap-4">
                        <select name="status_aktif" class="w-full md:w-1/2 rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                            <option value="1" {{ $mitra->status_aktif ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !$mitra->status_aktif ? 'selected' : '' }}>Tidak Aktif (Blacklist/Keluar)</option>
                        </select>
                        <p class="text-xs text-slate-500 flex-1">
                            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                            Mitra yang tidak aktif tidak akan muncul di daftar pilihan saat pembuatan honor.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('mitra.index') }}" class="px-5 py-2.5 rounded-lg text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 font-medium text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-md hover:shadow-lg font-medium text-sm transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
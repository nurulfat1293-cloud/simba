@extends('layout.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-slate-900">Pengaturan Satuan Kerja</h2>
        <p class="mt-2 text-slate-600">Data ini akan digunakan sebagai identitas resmi pada kop surat dan dokumen SPK.</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-xl rounded-2xl border border-slate-200 overflow-hidden">
        <form action="{{ route('satker.store') }}" method="POST" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Satker -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Nama Satuan Kerja</label>
                    <input type="text" name="nama_satker" value="{{ old('nama_satker', $satker->nama_satker ?? '') }}" 
                        class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-indigo-500 transition" 
                        placeholder="Contoh: BPS Kabupaten Contoh" required>
                </div>

                <!-- Kode Satker -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Kode Satker</label>
                    <input type="text" name="kode_satker" value="{{ old('kode_satker', $satker->kode_satker ?? '') }}" 
                        class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-indigo-500 transition" 
                        placeholder="Kode resmi Satker" required>
                </div>

                <!-- Kota -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Kota / Kabupaten</label>
                    <input type="text" name="kota" value="{{ old('kota', $satker->kota ?? '') }}" 
                        class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-indigo-500 transition" 
                        placeholder="Lokasi penandatanganan" required>
                </div>

                <!-- Alamat Lengkap -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Alamat Lengkap</label>
                    <textarea name="alamat_lengkap" rows="3" 
                        class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-indigo-500 transition" 
                        placeholder="Alamat kantor lengkap..." required>{{ old('alamat_lengkap', $satker->alamat_lengkap ?? '') }}</textarea>
                </div>

                <!-- Nama PPK -->
                <div class="pt-4 border-t border-slate-100 md:col-span-2">
                    <h4 class="text-xs font-black text-indigo-600 uppercase mb-4 tracking-widest">Pejabat Pembuat Komitmen (PPK)</h4>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Nama Lengkap PPK</label>
                    <input type="text" name="nama_ppk" value="{{ old('nama_ppk', $satker->nama_ppk ?? '') }}" 
                        class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-indigo-500 transition" 
                        placeholder="Nama dan gelar">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">NIP PPK</label>
                    <input type="text" name="nip_ppk" value="{{ old('nip_ppk', $satker->nip_ppk ?? '') }}" 
                        class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-indigo-500 transition" 
                        placeholder="NIP Pejabat">
                </div>
            </div>

            <div class="mt-10">
                <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all flex items-center justify-center gap-3 uppercase tracking-widest">
                    <i class="fas fa-save"></i> Simpan Konfigurasi Satker
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
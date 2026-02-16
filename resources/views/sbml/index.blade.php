@extends('layout.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Aturan Batas SBML</h2>
            <p class="text-sm text-slate-500">Standar Biaya Masukan Lainnya (Batas Maksimal Honor).</p>
        </div>
    </div>

    <!-- Alerts -->
    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex items-start gap-3 mb-6">
        <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
        <div>
            <p class="text-sm text-red-700 font-medium">Terjadi Kesalahan</p>
            <p class="text-sm text-red-600">{{ $errors->first() }}</p>
        </div>
    </div>
    @endif

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-start gap-3 mb-6">
        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
        <div>
            <p class="text-sm text-green-700 font-medium">Berhasil</p>
            <p class="text-sm text-green-600">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Form Input (Kolom Kiri) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden sticky top-24">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <h5 class="font-bold text-slate-700 text-sm uppercase tracking-wider">Tambah Aturan</h5>
                    <div class="bg-blue-100 text-blue-600 p-1.5 rounded-lg">
                        <i class="fas fa-plus text-xs"></i>
                    </div>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('sbml.store') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tahun Anggaran</label>
                            <input type="number" name="tahun_berlaku" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3" value="{{ date('Y') }}" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kegiatan</label>
                            <select name="id_jenis_kegiatan" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3" required>
                                @foreach($jenis_kegiatan as $jk)
                                    <option value="{{ $jk->id }}">{{ $jk->nama_jenis }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Jabatan Mitra</label>
                            <select name="id_jabatan" class="w-full rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3" required>
                                @foreach($jabatan as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Batas Honor (Rp)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">Rp</span>
                                <input type="number" name="batas_honor" class="w-full pl-8 pr-3 py-2 rounded-lg border-slate-300 text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="4000000" required>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Maksimal honor per orang/bulan.</p>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg shadow-md hover:shadow-lg font-medium text-sm transition-all flex items-center justify-center gap-2 mt-2">
                            <i class="fas fa-save"></i> Simpan Aturan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Data (Kolom Kanan) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h5 class="font-bold text-slate-800">Daftar Aturan Aktif</h5>
                    <span class="bg-slate-100 text-slate-500 text-xs px-2 py-1 rounded">
                        {{ $aturan->count() }} Data
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold tracking-wider border-b border-slate-200">
                                <th class="px-6 py-3 font-medium text-center w-20">Tahun</th>
                                <th class="px-6 py-3 font-medium">Jenis Kegiatan</th>
                                <th class="px-6 py-3 font-medium">Jabatan</th>
                                <th class="px-6 py-3 font-medium text-right">Batas (SBML)</th>
                                <th class="px-6 py-3 font-medium text-center w-16">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($aturan as $a)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-block px-2 py-1 rounded bg-slate-100 text-slate-600 text-xs font-bold">
                                        {{ $a->tahun_berlaku }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700">
                                    {{ $a->jenisKegiatan->nama_jenis }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700">
                                    {{ $a->jabatan->nama_jabatan }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700 font-mono font-medium text-right">
                                    Rp {{ number_format($a->batas_honor, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('sbml.destroy', $a->id) }}" method="POST" onsubmit="return confirm('Hapus aturan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors border border-red-100" title="Hapus Aturan">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500 bg-slate-50/50">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fas fa-gavel text-slate-400"></i>
                                        </div>
                                        <p class="font-medium text-slate-600">Belum ada aturan SBML.</p>
                                        <p class="text-xs text-slate-400 mt-1">Tambahkan aturan baru melalui form di samping.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Info Card -->
            <div class="mt-6 bg-amber-50 border border-amber-100 rounded-xl p-4 flex items-start gap-3">
                <i class="fas fa-lightbulb text-amber-500 mt-1"></i>
                <div>
                    <h4 class="text-sm font-bold text-amber-800 mb-1">Informasi Penting</h4>
                    <p class="text-xs text-amber-700 leading-relaxed">
                        SBML (Standar Biaya Masukan Lainnya) digunakan oleh sistem untuk memvalidasi total honor yang diterima satu orang mitra dalam satu bulan. Jika total honor melebihi batas ini, sistem akan menolak input honor baru.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
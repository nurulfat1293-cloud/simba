@extends('layout.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-2">
        <a href="{{ route('spk.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Edit SPK</h2>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex items-start gap-3">
        <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
        <p class="text-sm text-red-600">{{ session('error') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('spk.update', $spk->nomor_urut) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Field Nomor Urut (Readonly) -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nomor Urut</label>
                    <input type="text" value="{{ $spk->nomor_urut }}" readonly class="w-full px-4 py-2 border border-slate-200 rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed">
                    <p class="text-xs text-slate-400 mt-1">Nomor urut tidak dapat diubah manual.</p>
                </div>

                <!-- Field Nomor SPK (Readonly) -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nomor SPK Saat Ini</label>
                    <input type="text" value="{{ $spk->nomor_spk }}" readonly class="w-full px-4 py-2 border border-slate-200 rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed">
                    <p class="text-xs text-slate-400 mt-1">Otomatis diperbarui jika tanggal berubah.</p>
                </div>

                <!-- Field Mitra -->
                <div>
                    <label for="id_mitra" class="block text-sm font-medium text-slate-700 mb-1">Pilih Mitra</label>
                    <select name="id_mitra" id="id_mitra" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        @foreach($mitra as $m)
                            <option value="{{ $m->id }}" {{ old('id_mitra', $spk->id_mitra) == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_lengkap }} ({{ $m->asal_desa }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Field Tanggal SPK -->
                <div>
                    <label for="tanggal_spk" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Tanda Tangan</label>
                    <input type="date" name="tanggal_spk" id="tanggal_spk" value="{{ old('tanggal_spk', $spk->tanggal_spk) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('spk.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-colors font-medium text-sm">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
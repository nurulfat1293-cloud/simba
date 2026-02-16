@extends('layout.app')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('peran.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-3xl font-extrabold text-slate-900">Ubah Peran</h2>
        </div>
        <p class="text-slate-600 ml-8">Perbarui informasi peran <strong>{{ $peran->nama_peran }}</strong>.</p>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
        <ul class="list-disc ml-5 text-sm text-red-600 font-medium">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white shadow-xl rounded-2xl border border-slate-200 overflow-hidden">
        <form action="{{ route('peran.update', $peran->id) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Nama Peran</label>
                <input type="text" name="nama_peran" value="{{ old('nama_peran', $peran->nama_peran) }}" 
                    class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" 
                    required>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $peran->slug) }}" 
                    class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-mono text-sm bg-slate-50" 
                    required>
                <p class="mt-2 text-[10px] text-slate-400 italic font-medium uppercase tracking-tighter">Slug digunakan sebagai identifier sistem dan tidak disarankan untuk sering diubah.</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Deskripsi</label>
                <textarea name="deskripsi" rows="3" 
                    class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">{{ old('deskripsi', $peran->deskripsi) }}</textarea>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all flex items-center justify-center gap-3 uppercase tracking-widest">
                    <i class="fas fa-sync-alt"></i> Perbarui Peran
                </button>
                <a href="{{ route('peran.index') }}" class="px-6 py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all uppercase tracking-widest text-sm flex items-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
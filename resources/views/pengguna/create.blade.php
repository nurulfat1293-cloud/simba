@extends('layout.app')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('pengguna.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-3xl font-extrabold text-slate-900">Tambah Akun</h2>
        </div>
        <p class="text-slate-600 ml-8">Daftarkan personel baru ke dalam sistem manajemen.</p>
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
        <form action="{{ route('pengguna.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Email Resmi</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition" placeholder="email@contoh.com" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Peran / Role</label>
                    <select name="peran" class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition" required>
                        <option value="">-- Pilih Peran --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('peran') == $role->id ? 'selected' : '' }}> {{ $role->nama_peran }} </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Password</label>
                    <input type="password" name="password" class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition" required>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all flex items-center justify-center gap-3 uppercase tracking-widest">
                    <i class="fas fa-save"></i> Simpan Akun Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
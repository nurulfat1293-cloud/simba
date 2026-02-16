@extends('layout.app')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('pengguna.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-3xl font-extrabold text-slate-900">Edit Profil</h2>
        </div>
        <p class="text-slate-600 ml-8">Perbarui informasi akun <strong>{{ $user->nama_lengkap }}</strong>.</p>
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
        <form action="{{ route('pengguna.update', $user->id) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Email Resmi</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Peran / Role</label>
                    <select name="peran" class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition" required>
                        <option value="">-- Pilih Peran --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('peran', $user->peran) == $role->id ? 'selected' : '' }}>
                                {{ $role->nama_peran }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2 p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4"><i class="fas fa-lock mr-1"></i> Keamanan (Biarkan kosong jika tidak ingin ganti password)</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Password Baru</label>
                            <input type="password" name="password" class="w-full rounded-lg border-slate-200 py-2 px-4 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="w-full rounded-lg border-slate-200 py-2 px-4 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="submit" class="flex-1 bg-slate-900 hover:bg-black text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all flex items-center justify-center gap-3 uppercase tracking-widest">
                    <i class="fas fa-sync-alt"></i> Perbarui Data Pengguna
                </button>
                <a href="{{ route('pengguna.index') }}" class="px-6 py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all uppercase tracking-widest text-sm flex items-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
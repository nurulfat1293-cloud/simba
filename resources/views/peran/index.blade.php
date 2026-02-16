@extends('layout.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Manajemen Peran</h2>
            <p class="text-sm text-slate-500">Kelola hak akses dan kategori pengguna sistem.</p>
        </div>
        <a href="{{ route('peran.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-sm transition-all font-medium text-sm">
            <i class="fas fa-plus"></i>
            <span>Tambah Peran Baru</span>
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-start gap-3">
        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
        <p class="text-sm text-green-600">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex items-start gap-3">
        <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
        <p class="text-sm text-red-600">{{ session('error') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="px-6 py-4 font-medium w-16 text-center">No</th>
                        <th class="px-6 py-4 font-medium">Nama Peran</th>
                        <th class="px-6 py-4 font-medium">Slug</th>
                        <th class="px-6 py-4 font-medium">Deskripsi</th>
                        <th class="px-6 py-4 font-medium text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($perans as $index => $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-center text-sm text-slate-500">
                            {{ ($perans->currentPage() - 1) * $perans->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-800 text-sm">
                            {{ $item->nama_peran }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs font-mono">
                                {{ $item->slug }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500 italic">
                            {{ $item->deskripsi ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('peran.edit', $item->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 border border-blue-100 transition-colors">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('peran.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus peran ini?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 border border-red-100 transition-colors">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500 italic">Belum ada data peran.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($perans->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $perans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
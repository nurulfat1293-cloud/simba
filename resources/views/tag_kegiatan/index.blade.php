@extends('layout.app')

@section('content')

<div class="space-y-6">
<!-- Header Section -->
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
<div>
<h2 class="text-2xl font-bold text-slate-800">Master Kelompok Kegiatan</h2>
<p class="text-sm text-slate-500">Kelola kategori payung (Tag) untuk pengelompokan sensus dan survei.</p>
</div>
<button onclick="openModal('add')" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-sm transition-all font-medium text-sm">
<i class="fas fa-plus"></i>
<span>Tambah Kelompok Baru</span>
</button>
</div>

<!-- Alert Success -->
@if(session('success'))
<div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-start gap-3 animate-fade-in">
    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
    <div>
        <p class="text-sm text-green-700 font-medium">Berhasil</p>
        <p class="text-sm text-green-600">{{ session('success') }}</p>
    </div>
</div>
@endif

<!-- Alert Error -->
@if(session('error'))
<div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex items-start gap-3 animate-fade-in">
    <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
    <div>
        <p class="text-sm text-red-700 font-medium">Gagal</p>
        <p class="text-sm text-red-600">{{ session('error') }}</p>
    </div>
</div>
@endif

<!-- Table Card -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                    <th class="px-6 py-4 font-medium text-center w-20">No</th>
                    <th class="px-6 py-4 font-medium">Nama Kelompok (Tag)</th>
                    <th class="px-6 py-4 font-medium text-center">Jumlah Kegiatan</th>
                    <th class="px-6 py-4 font-medium text-center w-40">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($tags as $tag)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-center text-slate-500 text-sm font-mono">
                        {{ $loop->iteration + $tags->firstItem() - 1 }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-bold text-slate-800 text-sm tracking-tight">{{ $tag->nama_tag }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs font-semibold">
                            {{ $tag->kegiatan_count ?? 0 }} Kegiatan
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center items-center gap-2">
                            <a href="{{ route('tag_kegiatan.show', $tag->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 border border-blue-100 transition-colors" title="Lihat Detail">
                                <i class="fas fa-eye text-xs"></i>
                            </a>

                            <button onclick="openModal('edit', {{ $tag->id }}, '{{ $tag->nama_tag }}')" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 border border-amber-100 transition-colors" title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </button>

                            <form action="{{ route('tag_kegiatan.destroy', $tag->id) }}" method="POST" onsubmit="return confirm('Hapus kelompok ini?')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 border border-red-100 transition-colors" title="Hapus">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                        <p class="text-sm italic">Belum ada data kelompok kegiatan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tags->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
        {{ $tags->links() }}
    </div>
    @endif
</div>


</div>

<!-- Modal Form -->

<div id="tagModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
<div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
<div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

    <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
        <form id="tagForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            
            <div class="bg-white px-6 pt-6 pb-4">
                <h3 class="text-lg font-bold text-slate-800 mb-4" id="modalTitle">Tambah Kelompok Kegiatan</h3>
                <div class="space-y-4">
                    <div>
                        <label for="nama_tag" class="block text-sm font-medium text-slate-700 mb-1">Nama Kelompok <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_tag" id="nama_tag_input" required
                            class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-slate-800 placeholder:text-slate-400"
                            placeholder="Contoh: Susenas, Sakernas, Podes">
                    </div>
                </div>
            </div>
            
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm">Simpan Data</button>
            </div>
        </form>
    </div>
</div>


</div>

<script>
function openModal(mode, id = null, name = '') {
    const modal = document.getElementById('tagModal');
    const form = document.getElementById('tagForm');
    const title = document.getElementById('modalTitle');
    const method = document.getElementById('methodField');
    const input = document.getElementById('nama_tag_input');

    modal.classList.remove('hidden');
    input.value = name;

    if (mode === 'add') {
        title.innerText = 'Tambah Kelompok Kegiatan';
        form.action = "{{ route('tag_kegiatan.store') }}";
        method.value = 'POST';
    } else {
        title.innerText = 'Edit Kelompok Kegiatan';
        
        // --- PERBAIKAN DI SINI ---
        // Menggunakan helper route() agar URL lengkap tercetak benar
        // Hasilnya misal: http://localhost:8000/tag_kegiatan/1
        var baseUrl = "{{ route('tag_kegiatan.index') }}"; 
        form.action = baseUrl + "/" + id;
        
        method.value = 'PUT';
    }
}
</script>

@endsection
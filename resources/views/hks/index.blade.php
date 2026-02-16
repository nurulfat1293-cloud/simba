@extends('layout.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Master Standar Harga (HKS)</h2>
            <p class="text-sm text-slate-500">Kelola aturan batas harga satuan kegiatan.</p>
        </div>
        <button type="button" onclick="hksToggleModal('tambahModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-sm font-medium transition-all">
            <i class="fas fa-plus mr-2"></i> Tambah Standar Harga
        </button>
    </div>

    <!-- Tampilkan Semua Error -->
    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
        <div class="flex items-center mb-2">
            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
            <span class="font-bold text-red-700">Simpan Gagal:</span>
        </div>
        <ul class="list-disc list-inside text-sm text-red-600">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-center">No</th>
                        <th class="px-6 py-4">Tag Kegiatan</th>
                        <th class="px-6 py-4">Jabatan</th>
                        <th class="px-6 py-4 text-center">Satuan</th>
                        <th class="px-6 py-4 text-right">Batas Harga</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($daftar_hks as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-center text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $item->tagKegiatan->nama_tag ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $item->jabatan->nama_jabatan ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-slate-100 rounded text-[10px] font-bold uppercase">{{ $item->satuan->nama_satuan ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-right font-mono font-bold">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('hks.show', $item->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg border border-transparent hover:border-blue-200" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <button type="button" onclick="hksToggleModal('editModal{{ $item->id }}')" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-200">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('hks.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg border border-transparent hover:border-red-200">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit per Baris -->
                    <div id="editModal{{ $item->id }}" class="fixed inset-0 z-[100] hidden overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="fixed inset-0 bg-black/50" onclick="hksToggleModal('editModal{{ $item->id }}')"></div>
                            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg z-[110] relative">
                                <form action="{{ route('hks.update', $item->id) }}" method="POST" class="form-hks">
                                    @csrf @method('PUT')
                                    <div class="px-6 py-4 border-b border-slate-100 font-bold text-slate-800">Ubah Data HKS</div>
                                    <div class="p-6 space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tag Kegiatan</label>
                                            <select name="id_tag_kegiatan" class="w-full rounded-lg border-slate-300 focus:ring-blue-500">
                                                @foreach($tag_kegiatan as $tag)
                                                    <option value="{{ $tag->id }}" {{ $item->id_tag_kegiatan == $tag->id ? 'selected' : '' }}>{{ $tag->nama_tag }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jabatan</label>
                                            <select name="id_jabatan" class="w-full rounded-lg border-slate-300 focus:ring-blue-500">
                                                @foreach($ref_jabatan as $j)
                                                    <option value="{{ $j->id }}" {{ $item->id_jabatan == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Satuan</label>
                                                <select name="id_satuan" class="w-full rounded-lg border-slate-300 focus:ring-blue-500">
                                                    @foreach($satuan as $s)
                                                        <option value="{{ $s->id }}" {{ $item->id_satuan == $s->id ? 'selected' : '' }}>{{ $s->nama_satuan }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Harga (Rp)</label>
                                                <input type="text" name="harga_satuan_display" value="{{ number_format($item->harga_satuan, 0, ',', '.') }}" class="w-full rounded-lg border-slate-300 rupiah-input" required>
                                                <input type="hidden" name="harga_satuan" value="{{ $item->harga_satuan }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-6 py-4 bg-slate-50 flex justify-end gap-3 rounded-b-xl">
                                        <button type="button" onclick="hksToggleModal('editModal{{ $item->id }}')" class="px-4 py-2 text-slate-500 font-medium">Batal</button>
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold">Update Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Utama -->
<div id="tambahModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="hksToggleModal('tambahModal')"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg z-[110] relative overflow-hidden">
            <form action="{{ route('hks.store') }}" method="POST" id="formTambahHks" class="form-hks">
                @csrf
                <div class="bg-blue-600 px-6 py-4 text-white">
                    <h3 class="font-bold text-lg">Tambah Standar Harga</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tag Kegiatan</label>
                        <select name="id_tag_kegiatan" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">-- Pilih Tag --</option>
                            @foreach($tag_kegiatan as $tag)
                                <option value="{{ $tag->id }}" {{ old('id_tag_kegiatan') == $tag->id ? 'selected' : '' }}>{{ $tag->nama_tag }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Jabatan</label>
                        <select name="id_jabatan" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($ref_jabatan as $j)
                                <option value="{{ $j->id }}" {{ old('id_jabatan') == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Satuan</label>
                            <select name="id_satuan" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">-- Satuan --</option>
                                @foreach($satuan as $s)
                                    <option value="{{ $s->id }}" {{ old('id_satuan') == $s->id ? 'selected' : '' }}>{{ $s->nama_satuan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Batas Harga (Rp)</label>
                            <!-- Input Display untuk user -->
                            <input type="text" name="harga_satuan_display" value="{{ old('harga_satuan') ? number_format(old('harga_satuan'), 0, ',', '.') : '' }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 rupiah-input" placeholder="0" required>
                            <!-- Input Hidden untuk dikirim ke Server -->
                            <input type="hidden" name="harga_satuan" value="{{ old('harga_satuan') }}">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 flex justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="hksToggleModal('tambahModal')" class="px-4 py-2 text-slate-500 font-medium">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold shadow-md transform active:scale-95 transition-all">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    /**
     * Fungsi untuk format Rupiah saat mengetik
     */
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
    }

    // Pasang event listener ke semua input dengan class .rupiah-input
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('rupiah-input')) {
            let input = e.target;
            let hiddenInput = input.nextElementSibling; // Mencari input hidden di sebelahnya
            
            // Format tampilan ke user
            let formattedValue = formatRupiah(input.value);
            input.value = formattedValue;

            // Simpan nilai asli (angka saja) ke hidden input untuk database
            let rawValue = input.value.replace(/\./g, '');
            hiddenInput.value = rawValue;
        }
    });

    // Sebelum form dikirim, pastikan nilai hidden input terupdate (double-check)
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('form-hks')) {
            const form = e.target;
            const displayInput = form.querySelector('.rupiah-input');
            const hiddenInput = form.querySelector('input[name="harga_satuan"]');
            if(displayInput && hiddenInput) {
                hiddenInput.value = displayInput.value.replace(/\./g, '');
            }
        }
    });

    /**
     * Fungsi untuk buka/tutup modal
     */
    function hksToggleModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            const isHidden = modal.classList.contains('hidden');
            if (isHidden) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }
    }

    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = document.querySelectorAll('[id*="Modal"]');
            modals.forEach(m => {
                if (!m.classList.contains('hidden')) hksToggleModal(m.id);
            });
        }
    });
</script>
@endsection
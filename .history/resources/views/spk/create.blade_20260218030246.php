@extends('layout.app')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('spk.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Buat SPK Baru</h2>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r flex items-start gap-3">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <p class="text-sm text-red-600">{{ session('error') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('spk.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                
                {{-- Persiapan Data untuk Searchable Dropdown (AlpineJS) --}}
                @php
                    // PERBAIKAN: Filter koleksi mitra untuk memastikan hanya status 'Aktif' yang masuk ke dropdown
                    $activeMitra = isset($mitra) ? $mitra->where('status_mitra', 'Aktif') : collect();

                    $mitraList = $activeMitra->map(function($m) {
                        return [
                            'id' => $m->id,
                            'label' => $m->nik . ' - ' . $m->nama_lengkap,
                            'search_text' => strtolower($m->nik . ' ' . $m->nama_lengkap)
                        ];
                    })->values()->toJson();
                @endphp

                {{-- Pilih Mitra (Dropdown dengan Fitur Pencarian) --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Mitra <span class="text-red-500">*</span></label>
                    
                    <div x-data="{
                        open: false,
                        search: '',
                        selectedId: '{{ old('id_mitra') }}',
                        selectedLabel: '',
                        items: {{ $mitraList }},
                        get filteredItems() {
                            if (this.search === '') return this.items;
                            return this.items.filter(item => item.search_text.includes(this.search.toLowerCase()));
                        },
                        init() {
                            if (this.selectedId) {
                                let found = this.items.find(i => i.id == this.selectedId);
                                if (found) this.selectedLabel = found.label;
                            }
                        }
                    }" class="relative">
                        
                        <input type="hidden" name="id_mitra" :value="selectedId">

                        <button type="button" 
                            @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
                            @click.away="open = false"
                            class="w-full bg-white border rounded-lg px-3 py-2 text-left flex justify-between items-center shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            :class="{'border-slate-300 text-slate-700': selectedId, 'border-slate-300 text-slate-500': !selectedId, 'border-red-500 ring-red-200': {{ $errors->has('id_mitra') ? 'true' : 'false' }} }"
                        >
                            <span x-text="selectedLabel || '-- Cari Mitra Aktif (NIK / Nama) --'"></span>
                            <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" :class="{'rotate-180': open}"></i>
                        </button>

                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-xl max-h-60 overflow-hidden flex flex-col"
                             style="display: none;">
                            
                            <div class="p-2 border-b border-slate-100 bg-slate-50 sticky top-0">
                                <div class="relative">
                                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input x-ref="searchInput" 
                                           x-model="search" 
                                           type="text" 
                                           placeholder="Ketik NIK atau Nama..." 
                                           class="w-full pl-8 pr-3 py-1.5 text-sm border border-slate-300 rounded-md focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                                </div>
                            </div>

                            <ul class="overflow-y-auto flex-1 p-1 custom-scrollbar">
                                <template x-for="item in filteredItems" :key="item.id">
                                    <li @click="selectedId = item.id; selectedLabel = item.label; open = false; search = ''"
                                        class="px-3 py-2 text-sm rounded-md cursor-pointer transition-colors flex items-center justify-between group"
                                        :class="{'bg-blue-50 text-blue-700 font-medium': selectedId == item.id, 'text-slate-700 hover:bg-slate-50': selectedId != item.id}">
                                        <span x-text="item.label"></span>
                                        <i x-show="selectedId == item.id" class="fas fa-check text-blue-600 text-xs"></i>
                                    </li>
                                </template>
                                <li x-show="filteredItems.length === 0" class="px-3 py-4 text-center text-sm text-slate-500 italic">
                                    Data mitra aktif tidak ditemukan.
                                </li>
                            </ul>
                        </div>
                    </div>

                    @error('id_mitra')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Bulan Periode <span class="text-red-500">*</span></label>
                        <select name="bulan" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" required>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ old('bulan', date('m')) == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tahun Periode <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" min="2000" max="2100"
                            class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Tanda Tangan (Administrasi) <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_spk" value="{{ old('tanggal_spk', date('Y-m-d')) }}"
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" required>
                    <p class="mt-1 text-xs text-slate-500 italic">* Nomor urut SPK akan otomatis ditentukan berdasarkan tanggal ini.</p>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('spk.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">Batal</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan SPK
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

@endsection
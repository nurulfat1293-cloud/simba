@extends('layout.app')

@section('content')

<div class="space-y-6">
<!-- Header Section -->
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
<div>
<h2 class="text-2xl font-bold text-slate-800">Daftar Kegiatan BPS</h2>
<p class="text-sm text-slate-500">Kelola identitas dasar kegiatan sensus dan survei.</p>
</div>
<div class="flex items-center gap-2">
<!-- Navigasi ke Master Tag -->
<a href="{{ route('peran.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
<i class="fas fa-tags text-slate-400"></i>
<span>Master Kelompok (Tag)</span>
</a>

        <a href="{{ route('kegiatan.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-sm transition-all font-medium text-sm">
            <i class="fas fa-plus"></i>
            <span>Buat Kegiatan Baru</span>
        </a>
    </div>
</div>

@if(session('success'))
<div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-start gap-3 animate-fade-in">
    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
    <div>
        <p class="text-sm text-green-700 font-medium">Berhasil</p>
        <p class="text-sm text-green-600">{{ session('success') }}</p>
    </div>
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                    <th class="px-6 py-4 font-medium w-1/4">Nama Kegiatan</th>
                    <th class="px-6 py-4 font-medium text-center">Mata Anggaran</th>
                    <th class="px-6 py-4 font-medium text-center">Kelompok (Tag)</th>
                    <th class="px-6 py-4 font-medium text-center">Jenis</th>
                    <th class="px-6 py-4 font-medium">Periode Pelaksanaan</th>
                    <th class="px-6 py-4 font-medium text-center">Status</th>
                    <th class="px-6 py-4 font-medium text-center w-40">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($kegiatan as $k)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-bold text-slate-800 text-sm block">{{ $k->nama_kegiatan }}</span>
                    </td>

                    <!-- Kolom Mata Anggaran -->
                    <td class="px-6 py-4 text-center">
                        <div class="flex flex-col items-center">
                            <span class="font-mono text-xs font-bold text-slate-700 bg-slate-50 px-2 py-1 rounded border border-slate-100">
                                {{ $k->mata_anggaran ?? '---' }}
                            </span>
                        </div>
                    </td>

                    <!-- Kolom Kelompok (Tag) -->
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2 py-1 rounded bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-100 whitespace-nowrap">
                            <i class="fas fa-tag mr-1"></i>
                            {{ $k->tagKegiatan->nama_tag ?? 'Tanpa Tag' }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap">
                            {{ $k->jenisKegiatan->nama_jenis ?? 'N/A' }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1 text-xs text-slate-600 font-medium">
                            <div class="flex items-center gap-2">
                                <i class="far fa-calendar-check text-blue-500 w-3 text-center"></i>
                                <span>{{ $k->tanggal_mulai ? $k->tanggal_mulai->format('d/m/Y') : '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-400 font-normal">
                                <i class="far fa-calendar-times w-3 text-center"></i>
                                <span>{{ $k->tanggal_akhir ? $k->tanggal_akhir->format('d/m/Y') : '-' }}</span>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-center">
                        @php
                            $statusStyles = [
                                'Persiapan' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'Berjalan' => 'bg-green-100 text-green-700 border-green-200',
                                'Selesai' => 'bg-slate-100 text-slate-600 border-slate-200',
                            ];
                            $currentStyle = $statusStyles[$k->status_kegiatan] ?? $statusStyles['Selesai'];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] uppercase tracking-wider font-bold border {{ $currentStyle }}">
                            {{ $k->status_kegiatan }}
                        </span>
                    </td>

                    <!-- Aksi -->
                    <td class="px-6 py-4">
                        <div class="flex justify-center items-center gap-2">
                            <a href="{{ route('kegiatan.edit', $k->id) }}" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 border border-amber-100 transition-colors" title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <form action="{{ route('kegiatan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus kegiatan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 border border-red-100 transition-colors">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-slate-500 bg-slate-50/50">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-clipboard-list text-2xl text-slate-300 mb-3"></i>
                            <p class="font-medium text-slate-600">Data kegiatan masih kosong.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($kegiatan->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
        {{ $kegiatan->links() }}
    </div>
    @endif
</div>


</div>
@endsection
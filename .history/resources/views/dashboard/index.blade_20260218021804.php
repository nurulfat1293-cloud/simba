@extends('layout.app')

@section('content')
<div class="space-y-6">
    
    <!-- Header & Filter Global -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Ringkasan SIMBA</h1>
            <p class="text-slate-500 text-sm">Monitoring beban mitra dan progres administrasi.</p>
        </div>

        <form action="{{ route('dashboard') }}" method="GET" class="flex items-center gap-2 bg-white p-2 rounded-xl border border-slate-200 shadow-sm">
            <select name="bulan" onchange="this.form.submit()" class="text-xs font-bold border-none focus:ring-0 cursor-pointer bg-transparent">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ (int)$bulan == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <div class="h-4 w-px bg-slate-200"></div>
            <select name="tahun" onchange="this.form.submit()" class="text-xs font-bold border-none focus:ring-0 cursor-pointer bg-transparent">
                @for($y = date('Y'); $y >= 2024; $y--)
                    <option value="{{ $y }}" {{ (int)$tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="p-1.5 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="fas fa-sync-alt text-slate-400 text-xs"></i>
            </button>
        </form>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Kartu 1: Mitra Aktif -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Mitra</p>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ number_format($total_mitra) }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1 italic">Seluruh database</p>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <!-- Kartu 2: SPK Terbit -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">SPK Terbit</p>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ number_format($total_spk) }}</h3>
                    <p class="text-[10px] text-blue-500 mt-1 font-bold">Periode Terpilih</p>
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <i class="fas fa-file-contract"></i>
                </div>
            </div>
        </div>

        <!-- Kartu 3: Total Realisasi -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Honorarium</p>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Rp{{ number_format($total_honor, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-emerald-500 mt-1 font-bold">Terinput di sistem</p>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        <!-- Kartu 4: Rasio Arsip -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Digitalisasi SPK</p>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ $persen_arsip }}%</h3>
                    <p class="text-[10px] text-slate-400 mt-1">Status: {{ $persen_arsip < 100 ? 'Folder Belum Ada' : 'Folder Terhubung' }}</p>
                </div>
                <div class="p-3 {{ $persen_arsip < 100 ? 'bg-orange-50 text-orange-600' : 'bg-green-50 text-green-600' }} rounded-xl">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- PERBAIKAN: Top 5 Mitra (Dikecilkan tingginya) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Top 5 Mitra (Honor Pokok Tertinggi)</h3>
                <i class="fas fa-chart-bar text-slate-300"></i>
            </div>
            <div class="relative h-48 w-full"> {{-- Tinggi dibatasi h-48 --}}
                <canvas id="topMitraChart"></canvas>
            </div>
        </div>

        <!-- Status Digitalisasi (Donut - Dikecilkan padding-nya) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wide mb-4">Status Arsip SPK</h3>
            <div class="relative flex items-center justify-center h-40"> {{-- Tinggi dibatasi h-40 --}}
                <canvas id="arsipChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-xl font-black text-slate-800">{{ $persen_arsip }}%</span>
                    <span class="text-[8px] text-slate-400 font-bold uppercase">Terarsip</span>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2">
                <div class="p-2 bg-slate-50 rounded-lg text-center">
                    <p class="text-[8px] text-slate-400 uppercase font-bold">Total SPK</p>
                    <p class="text-xs font-bold text-slate-700">{{ $total_spk }}</p>
                </div>
                <div class="p-2 bg-slate-50 rounded-lg text-center">
                    <p class="text-[8px] text-slate-400 uppercase font-bold">Status</p>
                    <p class="text-[10px] font-bold {{ $persen_arsip < 100 ? 'text-red-500' : 'text-green-500' }}">
                        {{ $persen_arsip < 100 ? 'No Link' : 'Tersedia' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert & List Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-10">
        <!-- Notifikasi Ambang Batas SBML -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-4 bg-red-50 border-b border-red-100 flex justify-between items-center">
                <h3 class="font-bold text-red-700 text-[10px] uppercase tracking-widest">Kritis: Ambang Batas SBML (< 10%)</h3>
                <span class="px-2 py-0.5 bg-red-600 text-white rounded text-[8px] font-bold">{{ count($alert_sbml) }} Mitra</span>
            </div>
            <div class="p-4 space-y-2 max-h-[220px] overflow-y-auto"> {{-- Tinggi box scroll diperkecil --}}
                @forelse($alert_sbml as $alert)
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-800 text-[11px]">{{ $alert['nama'] }}</span>
                            <span class="text-[8px] text-slate-400 font-medium uppercase">{{ $alert['kategori'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] font-black text-red-600">Sisa: Rp{{ number_format($alert['sisa_rp'], 0, ',', '.') }}</span>
                            <div class="w-16 bg-slate-200 rounded-full h-1 mt-1">
                                <div class="bg-red-500 h-1 rounded-full" style="width: {{ $alert['persen'] }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center">
                        <i class="fas fa-check-circle text-emerald-200 text-2xl mb-2"></i>
                        <p class="text-[10px] text-slate-400 italic">Semua mitra memiliki kuota aman.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Info Sistem & Rekap Link -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 text-[10px] uppercase tracking-wide">Navigasi Cepat</h3>
                <i class="fas fa-info-circle text-slate-300"></i>
            </div>
            <div class="space-y-3">
                <div class="p-3 bg-blue-50 rounded-xl border border-blue-100">
                    <p class="text-[10px] text-blue-700 leading-snug">
                        Dashboard menggunakan data <b>Bulan {{ \Carbon\Carbon::create()->month((int)$bulan)->locale('id')->translatedFormat('F') }} {{ $tahun }}</b>.
                    </p>
                </div>

                <a href="{{ route('rekap.honor') }}" class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100 hover:bg-slate-100 transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 bg-white rounded flex items-center justify-center text-slate-400 group-hover:text-blue-600 shadow-sm border border-slate-100">
                            <i class="fas fa-file-invoice text-xs"></i>
                        </div>
                        <span class="text-[11px] font-bold text-slate-600">Buka Rekapitulasi Honor</span>
                    </div>
                    <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                </a>

                <a href="{{ route('arsip.index') }}" class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100 hover:bg-slate-100 transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 bg-white rounded flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shadow-sm border border-slate-100">
                            <i class="fas fa-archive text-xs"></i>
                        </div>
                        <span class="text-[11px] font-bold text-slate-600">Manajemen Arsip Digital</span>
                    </div>
                    <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Data untuk Bar Chart Top Mitra
    const ctxBar = document.getElementById('topMitraChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: {!! json_encode($top_mitra_data->pluck('nama_lengkap')->map(fn($n) => explode(' ', $n)[0] ?? 'N/A')) !!},
            datasets: [{
                label: 'Honor Pokok (Rp)',
                data: {!! json_encode($top_mitra_data->pluck('total_pokok')) !!},
                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y', // Mengubah menjadi horizontal agar lebih hemat ruang vertikal
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { ticks: { font: { size: 9 } } },
                x: { beginAtZero: true, ticks: { font: { size: 9 }, callback: (value) => 'Rp' + (value/1000) + 'k' } }
            }
        }
    });

    // 2. Data untuk Donut Chart Arsip
    const ctxArsip = document.getElementById('arsipChart').getContext('2d');
    new Chart(ctxArsip, {
        type: 'doughnut',
        data: {
            labels: ['Terarsip', 'Belum'],
            datasets: [{
                data: [{{ $spk_terarsip }}, {{ $total_spk - $spk_terarsip }}],
                backgroundColor: ['#10b981', '#f1f5f9'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            cutout: '85%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection
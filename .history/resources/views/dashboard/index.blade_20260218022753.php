@extends('layout.app')

@section('content')
<div class="space-y-6">
    
    <!-- Header & Filter Global -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Dashboard SIMBA</h1>
            <p class="text-slate-500 text-sm">Monitoring anggaran honorarium dan batas SBML mitra.</p>
        </div>

        <form action="{{ route('dashboard') }}" method="GET" class="flex items-center gap-2 bg-white p-2 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center px-2 text-slate-400">
                <i class="far fa-calendar-alt text-xs"></i>
            </div>
            <select name="bulan" onchange="this.form.submit()" class="text-xs font-bold border-none focus:ring-0 cursor-pointer bg-transparent py-1">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ (int)$bulan == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <div class="h-4 w-px bg-slate-200"></div>
            <select name="tahun" onchange="this.form.submit()" class="text-xs font-bold border-none focus:ring-0 cursor-pointer bg-transparent py-1">
                @for($y = date('Y'); $y >= 2024; $y--)
                    <option value="{{ $y }}" {{ (int)$tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="p-1.5 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="fas fa-sync-alt text-slate-400 text-[10px]"></i>
            </button>
        </form>
    </div>

    <!-- Ringkasan Risiko SBML (Saran 2) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @php
            $countKritis = collect($alert_sbml)->where('persen', '>=', 90)->count();
            $countWaspada = collect($alert_sbml)->whereBetween('persen', [70, 89.9])->count();
            $countAman = $total_mitra - ($countKritis + $countWaspada);
        @endphp
        <div class="bg-red-50 border border-red-100 rounded-2xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-red-200">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-red-600 uppercase tracking-widest">Kritis (>90%)</p>
                <h4 class="text-xl font-black text-red-800">{{ $countKritis }} <span class="text-xs font-medium">Mitra</span></h4>
            </div>
        </div>
        <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-amber-200">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">Waspada (70-90%)</p>
                <h4 class="text-xl font-black text-amber-800">{{ $countWaspada }} <span class="text-xs font-medium">Mitra</span></h4>
            </div>
        </div>
        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-emerald-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-emerald-200">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Aman (<70%)</p>
                <h4 class="text-xl font-black text-emerald-800">{{ max(0, $countAman) }} <span class="text-xs font-medium">Mitra</span></h4>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Realisasi Honor</p>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Rp{{ number_format($total_honor, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-emerald-500 mt-1 font-bold"><i class="fas fa-arrow-up mr-1"></i>Bulan {{ \Carbon\Carbon::create()->month((int)$bulan)->locale('id')->translatedFormat('F') }}</p>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total SPK</p>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ number_format($total_spk) }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1 italic">Dokumen terbit</p>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <i class="fas fa-file-contract"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Mitra Aktif</p>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ count($alert_sbml ?? []) }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1 italic">Memiliki alokasi</p>
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Status Arsip</p>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ $persen_arsip }}%</h3>
                    <p class="text-[10px] text-slate-400 mt-1">{{ $persen_arsip < 100 ? 'Ada scan tertunda' : 'Semua terupload' }}</p>
                </div>
                <div class="p-3 {{ $persen_arsip < 100 ? 'bg-orange-50 text-orange-600' : 'bg-green-50 text-green-600' }} rounded-xl">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 5 Mitra -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Top 5 Mitra (Beban Tertinggi)</h3>
                <i class="fas fa-chart-bar text-slate-300"></i>
            </div>
            <div class="relative h-56 w-full">
                <canvas id="topMitraChart"></canvas>
            </div>
        </div>

        <!-- Tren Bulanan (Saran 3) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Tren Realisasi Honor {{ $tahun }}</h3>
                <i class="fas fa-chart-line text-slate-300"></i>
            </div>
            <div class="relative h-56 w-full">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Alert & Monitoring Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-10">
        <!-- Monitoring SBML per Mitra (Saran 1 & 5) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-700 text-[10px] uppercase tracking-widest">Detail Utilisasi SBML Mitra</h3>
                <div class="flex gap-2">
                    <span class="flex items-center gap-1 text-[8px] font-bold text-slate-400 uppercase"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Aman</span>
                    <span class="flex items-center gap-1 text-[8px] font-bold text-slate-400 uppercase"><span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span> Waspada</span>
                    <span class="flex items-center gap-1 text-[8px] font-bold text-slate-400 uppercase"><span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Kritis</span>
                </div>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Nama Mitra</th>
                            <th class="px-5 py-3 text-center">SBML Terpakai</th>
                            <th class="px-5 py-3 text-right">Sisa Kuota</th>
                            <th class="px-5 py-3 w-40 text-center">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($alert_sbml as $alert)
                            @php
                                $colorClass = 'bg-emerald-500';
                                $textClass = 'text-emerald-600';
                                if($alert['persen'] >= 90) { $colorClass = 'bg-red-500'; $textClass = 'text-red-600'; }
                                elseif($alert['persen'] >= 70) { $colorClass = 'bg-amber-500'; $textClass = 'text-amber-600'; }
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800 text-[11px]">{{ $alert['nama'] }}</span>
                                        <span class="text-[9px] text-slate-400 uppercase font-medium">{{ $alert['kategori'] }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="text-[10px] font-mono font-bold text-slate-600">{{ $alert['persen'] }}%</span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <span class="font-black text-[11px] {{ $textClass }}">Rp{{ number_format($alert['sisa_rp'], 0, ',', '.') }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="{{ $colorClass }} h-full transition-all duration-700 shadow-sm" style="width: {{ min(100, $alert['persen']) }}%"></div>
                                    </div>
                                    @if($alert['persen'] >= 90)
                                        <p class="text-[8px] text-red-500 font-bold uppercase mt-1 text-right animate-pulse">⚠ Kritis</p>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-user-clock text-slate-200 text-3xl mb-3"></i>
                                        <p class="text-xs text-slate-400">Tidak ada mitra aktif di periode ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Navigasi & Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
            <h3 class="font-bold text-slate-800 text-[10px] uppercase tracking-wide mb-6">Navigasi Cepat</h3>
            <div class="space-y-3 flex-1">
                <a href="{{ route('rekap.honor') }}" class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-white hover:shadow-md hover:border-blue-200 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <i class="fas fa-file-invoice text-sm"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-700">Rekap Honorarium</span>
                            <span class="text-[9px] text-slate-400">Laporan akumulatif mitra</span>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                </a>

                <a href="{{ route('arsip.index') }}" class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-white hover:shadow-md hover:border-indigo-200 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <i class="fas fa-archive text-sm"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-700">Arsip Digital</span>
                            <span class="text-[9px] text-slate-400">Scan dokumen SPK</span>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                </a>
            </div>
            
            <div class="mt-6 p-4 bg-indigo-900 rounded-2xl text-white">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-info-circle text-indigo-300"></i>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-200">Tips Kontrol</span>
                </div>
                <p class="text-[10px] text-indigo-100 leading-relaxed italic">
                    "Gunakan monitoring warna untuk mendeteksi beban kerja yang tidak merata antar mitra statistik."
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Config global Chart.js
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#94a3b8';

    // 1. Bar Chart Top Mitra
    const ctxBar = document.getElementById('topMitraChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: {!! json_encode($top_mitra_data->pluck('nama_lengkap')->map(fn($n) => explode(' ', $n)[0] ?? 'N/A')) !!},
            datasets: [{
                label: 'Honor Pokok (Rp)',
                data: {!! json_encode($top_mitra_data->pluck('total_pokok')) !!},
                backgroundColor: '#4f46e5',
                borderRadius: 6,
                borderSkipped: false,
                barThickness: 15
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { display: false }, ticks: { font: { size: 9, weight: 'bold' }, color: '#475569' } },
                x: { ticks: { font: { size: 8 }, callback: (v) => 'Rp' + (v/1000) + 'k' } }
            }
        }
    });

    // 2. Trend Line Chart (Mockup data, bisa dihubungkan ke backend nantinya)
    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    const gradient = ctxTrend.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
    gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Total Realisasi',
                data: [0, {{ $total_honor }}, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], // Diisi dinamis berdasarkan bulan ini
                borderColor: '#4f46e5',
                borderWidth: 3,
                fill: true,
                backgroundColor: gradient,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#4f46e5',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { ticks: { font: { size: 8 }, callback: (v) => 'Rp' + (v/1000000) + 'jt' } },
                x: { grid: { display: false }, ticks: { font: { size: 9 } } }
            }
        }
    });

    // 3. Donut Chart Arsip
    // Karena di layout dipindah ke card statis, ini opsional jika ingin ditambahkan kembali di tempat lain.
</script>
@endsection
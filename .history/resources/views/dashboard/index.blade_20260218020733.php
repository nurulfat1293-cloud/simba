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
        <!-- Top 5 Mitra (Bar Chart) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Top 5 Mitra (Honor Pokok Tertinggi)</h3>
                <i class="fas fa-chart-bar text-slate-300"></i>
            </div>
            <canvas id="topMitraChart" height="250"></canvas>
        </div>

        <!-- Status Digitalisasi (Donut) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide mb-6">Status Arsip SPK</h3>
            <div class="relative flex items-center justify-center">
                <canvas id="arsipChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-2xl font-black text-slate-800">{{ $persen_arsip }}%</span>
                    <span class="text-[9px] text-slate-400 font-bold uppercase">Terarsip</span>
                </div>
            </div>
            <div class="mt-6 space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400">Total SPK</span>
                    <span class="font-bold text-slate-700">{{ $total_spk }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400">Status Folder</span>
                    <span class="font-bold {{ $persen_arsip < 100 ? 'text-red-500' : 'text-green-500' }}">
                        {{ $persen_arsip < 100 ? 'Belum Ada Link' : 'Tersedia' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert & List Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-10">
        <!-- Notifikasi Ambang Batas SBML -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 bg-red-50 border-b border-red-100 flex justify-between items-center">
                <h3 class="font-bold text-red-700 text-xs uppercase tracking-widest">Kritis: Ambang Batas SBML (< 10%)</h3>
                <span class="px-2 py-0.5 bg-red-600 text-white rounded text-[9px] font-bold">{{ count($alert_sbml) }} Mitra</span>
            </div>
            <div class="p-4 space-y-3 max-h-[300px] overflow-y-auto">
                @forelse($alert_sbml as $alert)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-800 text-xs">{{ $alert['nama'] }}</span>
                            <span class="text-[9px] text-slate-400 font-medium uppercase">{{ $alert['kategori'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] font-black text-red-600">Sisa: Rp{{ number_format($alert['sisa_rp'], 0, ',', '.') }}</span>
                            <div class="w-20 bg-slate-200 rounded-full h-1 mt-1">
                                {{-- PERBAIKAN: Memperbaiki atribut style pada progress bar --}}
                                <div class="bg-red-500 h-1 rounded-full" style="width: {{ $alert['persen'] }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center">
                        <i class="fas fa-check-circle text-emerald-200 text-3xl mb-2"></i>
                        <p class="text-xs text-slate-400 italic">Semua mitra masih memiliki kuota SBML yang aman.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Folder Arsip Belum Terkoneksi -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Informasi Sistem</h3>
                <i class="fas fa-info-circle text-slate-300"></i>
            </div>
            <div class="space-y-4">
                <div class="flex items-start gap-4 p-4 bg-blue-50 rounded-2xl border border-blue-100">
                    <div class="text-blue-600 mt-1"><i class="fas fa-lightbulb"></i></div>
                    <div>
                        <h4 class="font-bold text-blue-800 text-xs uppercase">Tips Dashboard</h4>
                        <p class="text-[11px] text-blue-700 leading-relaxed mt-1">
                            Dashboard menggunakan data <b>Bulan {{ \Carbon\Carbon::create()->month((int)$bulan)->locale('id')->translatedFormat('F') }} {{ $tahun }}</b>. 
                            Pastikan staf admin melakukan scan SPK dan menempelkan link Google Drive pada menu <b>Arsip Digital</b> untuk melengkapi rasio digitalisasi.
                        </p>
                    </div>
                </div>

                {{-- Catatan: Pastikan route 'transaksi.rekap' sudah didefinisikan di web.php --}}
                <a href="{{ route('transaksi.rekap') }}" class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-slate-100 transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-slate-400 group-hover:text-blue-600 shadow-sm border border-slate-100">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-600">Buka Rekapitulasi Honor</span>
                    </div>
                    <i class="fas fa-chevron-right text-slate-300"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Load Chart.js dari CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Data untuk Bar Chart Top Mitra
    const ctxBar = document.getElementById('topMitraChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: {!! json_encode($top_mitra_data->pluck('nama_lengkap')->map(fn($n) => explode(' ', $n)[0] ?? 'N/A')) !!},
            datasets: [{
                label: 'Total Honor Pokok (Rp)',
                data: {!! json_encode($top_mitra_data->pluck('total_pokok')) !!},
                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 10 } } },
                x: { ticks: { font: { size: 10 } } }
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
            cutout: '80%',
            responsive: true,
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection
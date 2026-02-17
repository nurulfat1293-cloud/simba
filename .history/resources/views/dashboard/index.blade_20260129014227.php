@extends('layout.app')

@section('content')
<div class="space-y-6">
    
    <!-- Title Section (Mobile only, since desktop has header) -->
    <div class="md:hidden mb-4">
        <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
        <p class="text-slate-500 text-sm">Ringkasan data hari ini</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Kartu 1: Total Mitra (Blue Theme) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Total Mitra</p>
                    <h3 class="text-3xl font-bold text-slate-800 tracking-tight">
                        {{ $total_mitra }}
                    </h3>
                    <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                        <i class="fas fa-check-circle mr-1"></i> Orang Terdaftar
                    </div>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Kartu 2: Kegiatan Aktif (Green Theme) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Kegiatan Aktif</p>
                    <h3 class="text-3xl font-bold text-slate-800 tracking-tight">
                        {{ $kegiatan_aktif }}
                    </h3>
                    <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span>
                        Status 'Berjalan'
                    </div>
                </div>
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Kartu 3: Total Honor (Orange/Yellow Theme) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Total Honor Realisasi</p>
                    <h3 class="text-3xl font-bold text-slate-800 tracking-tight text-truncate">
                        <span class="text-lg font-normal text-slate-400 mr-1">Rp</span>{{ number_format($total_honor, 0, ',', '.') }}
                    </h3>
                    <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700">
                        <i class="far fa-calendar-alt mr-1"></i> {{ date('F Y') }}
                    </div>
                </div>
                <div class="p-3 bg-orange-50 rounded-xl text-orange-600">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 md:p-10 flex flex-col md:flex-row items-center gap-10">
            <!-- Text Content -->
            <div class="flex-1 space-y-4">
                <div>
                    <h2 class="text-2xl font-heading font-bold text-slate-800">
                        Selamat Datang di <span class="text-blue-600">SIMBA</span>
                    </h2>
                    <p class="text-slate-500 mt-2 text-lg leading-relaxed">
                        Sistem Informasi Mitra & Batas Anggaran Badan Pusat Statistik.
                    </p>
                </div>
                
                <div class="bg-slate-50 p-6 rounded-xl border border-slate-100">
                    <h4 class="font-semibold text-slate-700 mb-3 text-sm uppercase tracking-wide">Fitur Utama Aplikasi:</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span class="text-slate-600 text-sm">Manajemen Database Mitra Statistik (PPL, PML, Koseka) terpusat.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span class="text-slate-600 text-sm">Validasi otomatis Batas Honor (SBML) untuk mencegah kelebihan bayar.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span class="text-slate-600 text-sm">Pencetakan Surat Perjanjian Kerja (SPK) secara instan.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Illustration Image -->
            <div class="w-full md:w-1/3 flex justify-center">
                <!-- Menggunakan style mix-blend-multiply agar background putih gambar menyatu -->
                <img src="https://cdn-icons-png.flaticon.com/512/2910/2910768.png" 
                     alt="Statistics Illustration" 
                     class="max-w-[180px] md:max-w-[220px] opacity-90 hover:scale-105 transition-transform duration-500">
            </div>
        </div>
    </div>
</div>
@endsection
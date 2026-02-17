<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMBA - BPS | Sistem Informasi Mitra</title>
    
    <!-- TAILWIND CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- ALPINE JS (Untuk Interaksi Sidebar) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    
    <!-- FONTS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Konfigurasi Tema Custom -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        bps: {
                            blue: '#005596',    
                            orange: '#F7941D',  
                            dark: '#0F172A',    
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6, .brand-font { font-family: 'Poppins', sans-serif; }
        
        .sidebar-scroll::-webkit-scrollbar { width: 5px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: #1e293b; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #475569; border-radius: 5px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: #64748b; }
    </style>
</head>
<body class="bg-gray-50 text-slate-800 antialiased">

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR -->
        <aside class="w-64 bg-slate-900 text-white flex flex-col shadow-2xl transition-all duration-300 z-20 hidden md:flex">
            <!-- Logo Area -->
            <div class="h-20 flex items-center justify-center border-b border-slate-800 bg-slate-950">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 text-white hover:text-blue-400 transition-colors">
                    <div class="bg-blue-600 p-2 rounded-lg shadow-lg shadow-blue-500/30">
                        <i class="fas fa-chart-pie text-lg"></i>
                    </div>
                    <div>
                        <h1 class="font-heading font-bold text-xl tracking-wider leading-none">SIMBA</h1>
                        <span class="text-[10px] text-slate-400 tracking-[0.2em] uppercase">BPS Dashboard</span>
                    </div>
                </a>
            </div>

            <!-- Menu User Info -->
            <div class="px-6 py-4 border-b border-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-sm font-bold shadow-lg">
                    {{ substr(Auth::user()->nama_lengkap ?? 'U', 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-semibold truncate">{{ Auth::user()->nama_lengkap ?? 'Guest User' }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ ucfirst(Auth::user()->peran ?? 'Admin') }}</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <!-- x-data: Diupdate sesuai grouping baru -->
            <nav class="flex-1 overflow-y-auto sidebar-scroll py-4 px-3 space-y-1" 
                 x-data="{ 
                    openMaster: {{ request()->routeIs('satker.*', 'mitra.*', 'tag_kegiatan.*', 'jabatan.*', 'satuan.*', 'kegiatan.*') ? 'true' : 'false' }},
                    openHksSbml: {{ request()->routeIs('hks.*', 'sbml.*') ? 'true' : 'false' }},
                    openSpk: {{ request()->routeIs('spk.*', 'transaksi.*', 'rekap.honor', 'arsip.*') ? 'true' : 'false' }},
                    openPengaturan: {{ request()->routeIs('peran.*', 'pengguna.*') ? 'true' : 'false' }}
                 }">
                
                <!-- DASHBOARD (Selalu Muncul) -->
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-home w-6 text-center text-sm {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-white' }}"></i>
                    <span class="font-medium text-sm">Dashboard</span>
                </a>

                <!-- GROUP 1: MASTER DATA -->
                <div class="mt-4">
                    <button @click="openMaster = !openMaster" class="w-full flex items-center justify-between px-4 py-2 text-xs font-bold text-slate-500 uppercase tracking-wider hover:text-slate-300 transition-colors focus:outline-none">
                        <span>Master Data</span>
                        <i class="fas fa-chevron-down transition-transform duration-300" :class="{'rotate-180': openMaster}"></i>
                    </button>
                    
                    <div x-show="openMaster" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mt-1 space-y-1 pl-2 border-l-2 border-slate-800 ml-4">
                        
                        <!-- 1. Identitas Satker -->
                        <a href="{{ route('satker.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('satker.*') ? 'bg-blue-900/50 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-university w-5 text-center text-xs mr-3"></i>
                            <span class="font-medium text-sm">Identitas Satker</span>
                        </a>

                        <!-- 2. Mitra -->
                        <a href="{{ route('mitra.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('mitra.*') ? 'bg-blue-900/50 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-users w-5 text-center text-xs mr-3"></i>
                            <span class="font-medium text-sm">Data Mitra</span>
                        </a>

                        <!-- 3. Tag Kegiatan -->
                        <a href="{{ route('tag_kegiatan.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('tag_kegiatan.*') ? 'bg-blue-900/50 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-tag w-5 text-center text-xs mr-3"></i>
                            <span class="font-medium text-sm">Tag Kegiatan</span>
                        </a>

                        <!-- 4. Master Jabatan -->
                        <a href="{{ route('jabatan.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('jabatan.*') ? 'bg-blue-900/50 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-user-tie w-5 text-center text-xs mr-3"></i>
                            <span class="font-medium text-sm">Master Jabatan</span>
                        </a>

                        <!-- 5. Master Satuan -->
                        <a href="{{ route('satuan.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('satuan.*') ? 'bg-blue-900/50 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-cube w-5 text-center text-xs mr-3"></i>
                            <span class="font-medium text-sm">Master Satuan</span>
                        </a>

                        <!-- 6. Kegiatan -->
                        <a href="{{ route('kegiatan.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('kegiatan.*') ? 'bg-blue-900/50 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-calendar-alt w-5 text-center text-xs mr-3"></i>
                            <span class="font-medium text-sm">Kegiatan</span>
                        </a>
                    </div>
                </div>

                <!-- GROUP 2: HKS DAN SBML (New Group) -->
                <div class="mt-4">
                    <button @click="openHksSbml = !openHksSbml" class="w-full flex items-center justify-between px-4 py-2 text-xs font-bold text-slate-500 uppercase tracking-wider hover:text-slate-300 transition-colors focus:outline-none">
                        <span>HKS & SBML</span>
                        <i class="fas fa-chevron-down transition-transform duration-300" :class="{'rotate-180': openHksSbml}"></i>
                    </button>

                    <div x-show="openHksSbml" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mt-1 space-y-1 pl-2 border-l-2 border-slate-800 ml-4">
                        
                        <!-- 1. Aturan HKS -->
                        <a href="{{ route('hks.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('hks.*') ? 'bg-blue-900/50 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-tags w-5 text-center text-xs mr-3"></i>
                            <span class="font-medium text-sm">Aturan HKS</span>
                        </a>

                        <!-- 2. Aturan SBML -->
                        <a href="{{ route('sbml.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('sbml.*') ? 'bg-blue-900/50 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-gavel w-5 text-center text-xs mr-3"></i>
                            <span class="font-medium text-sm">Aturan SBML</span>
                        </a>

                    </div>
                </div>

                <!-- GROUP 3: SPK DAN HONOR -->
                <div class="mt-4">
                    <button @click="openSpk = !openSpk" class="w-full flex items-center justify-between px-4 py-2 text-xs font-bold text-slate-500 uppercase tracking-wider hover:text-slate-300 transition-colors focus:outline-none">
                        <span>SPK & Honor</span>
                        <i class="fas fa-chevron-down transition-transform duration-300" :class="{'rotate-180': openSpk}"></i>
                    </button>

                    <div x-show="openSpk" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mt-1 space-y-1 pl-2 border-l-2 border-slate-800 ml-4">
                        
                        <!-- 1. Kelola SPK -->
                        <a href="{{ route('spk.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('spk.*') ? 'bg-blue-900/50 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-file-contract w-5 text-center text-xs mr-3"></i>
                            <span class="font-medium text-sm">Kelola SPK</span>
                        </a>

                        <!-- 2. Input Honor -->
                        <a href="{{ route('transaksi.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('transaksi.*') ? 'bg-blue-900/50 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-file-invoice-dollar w-5 text-center text-xs mr-3"></i>
                            <span class="font-medium text-sm">Input Honor</span>
                        </a>

                        <!-- 3. Rekap SPK & Honor -->
                        <a href="{{ route('rekap.honor') }}" class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('rekap.honor') ? 'bg-blue-900/50 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-chart-bar w-5 text-center text-xs mr-3"></i>
                            <span class="font-medium text-sm">Rekap SPK & Honor</span>
                        </a>

                        <!-- 4. Arsip SPK -->
                        <a href="{{ route('arsip.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('arsip.*') ? 'bg-blue-900/50 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-archive w-5 text-center text-xs mr-3"></i>
                            <span class="font-medium text-sm">Arsip SPK</span>
                        </a>
                    </div>
                </div>

                <!-- GROUP 4: PENGATURAN -->
                <div class="mt-4">
                    <button @click="openPengaturan = !openPengaturan" class="w-full flex items-center justify-between px-4 py-2 text-xs font-bold text-slate-500 uppercase tracking-wider hover:text-slate-300 transition-colors focus:outline-none">
                        <span>Pengaturan</span>
                        <i class="fas fa-chevron-down transition-transform duration-300" :class="{'rotate-180': openPengaturan}"></i>
                    </button>

                    <div x-show="openPengaturan" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mt-1 space-y-1 pl-2 border-l-2 border-slate-800 ml-4">
                        
                        <!-- 1. Manajemen Peran -->
                        <a href="{{ route('peran.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('peran.*') ? 'bg-blue-900/50 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-user-shield w-5 text-center text-xs mr-3"></i>
                            <span class="font-medium text-sm">Manajemen Peran</span>
                        </a>

                        <!-- 2. Manajemen Pengguna -->
                        <a href="{{ route('pengguna.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('pengguna.*') ? 'bg-blue-900/50 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-users-cog w-5 text-center text-xs mr-3"></i>
                            <span class="font-medium text-sm">Manajemen Pengguna</span>
                        </a>
                    </div>
                </div>
                
            </nav>

            <!-- Logout Section -->
            <div class="p-4 border-t border-slate-800 bg-slate-950">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-lg transition-colors font-medium text-sm shadow-lg shadow-red-900/20">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50 relative">
            
            <!-- TOP NAVBAR -->
            <header class="h-20 bg-white shadow-sm flex items-center justify-between px-6 lg:px-10 z-10 sticky top-0">
                <!-- Judul Halaman / Breadcrumb -->
                <div class="flex items-center gap-4">
                    <button class="md:hidden text-slate-500 hover:text-blue-600 text-2xl">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h2 class="font-heading font-bold text-xl text-slate-800">Sistem Informasi Mitra & Batas Anggaran</h2>
                        <p class="text-xs text-slate-500 hidden sm:block">Badan Pusat Statistik - Kelola Mitra dengan Mudah</p>
                    </div>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-4">
                    <button class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 text-slate-600 flex items-center justify-center transition-colors relative">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                    </button>
                    
                    <div class="hidden sm:flex items-center gap-3 pl-4 border-l border-gray-200">
                        <div class="text-right">
                            <span class="block text-sm font-bold text-slate-800">{{ Auth::user()->nama_lengkap ?? 'User' }}</span>
                            <span class="block text-xs text-slate-500">{{ ucfirst(Auth::user()->peran ?? 'Petugas') }}</span>
                        </div>
                        <!-- Profile Image Placeholder -->
                        <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-white shadow-sm flex items-center justify-center overflow-hidden">
                             <i class="fas fa-user text-slate-400"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- DYNAMIC CONTENT AREA -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 lg:p-10">
                <!-- Container untuk konten -->
                <div class="max-w-7xl mx-auto animate-fade-in-up">
                    
                    <!-- Flash Message Area (Optional) -->
                    @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r shadow-sm" role="alert">
                        <p class="font-bold">Berhasil</p>
                        <p>{{ session('success') }}</p>
                    </div>
                    @endif

                    <!-- YIELD CONTENT UTAMA -->
                    <div class="content-wrapper">
                        @yield('content')
                    </div>
                    
                </div>
            </main>
        </div>
    </div>

</body>
</html>
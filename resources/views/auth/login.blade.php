<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMBA BPS</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
                            dark: '#0F172A',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen relative overflow-hidden">

    <!-- Background Decoration (Optional) -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10">
        <div class="absolute -top-[30%] -left-[10%] w-[70%] h-[70%] rounded-full bg-blue-200/20 blur-3xl"></div>
        <div class="absolute top-[40%] -right-[10%] w-[60%] h-[60%] rounded-full bg-indigo-200/20 blur-3xl"></div>
    </div>

    <div class="w-full max-w-md p-6">
        
        <!-- Logo / Brand Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/30 mb-4">
                <i class="fas fa-chart-pie text-2xl"></i>
            </div>
            <h1 class="font-heading font-bold text-2xl text-slate-800 tracking-tight">SIMBA</h1>
            <p class="text-slate-500 text-sm mt-1">Sistem Informasi Mitra & Batas Anggaran</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
            <div class="p-8">
                <h2 class="text-lg font-bold text-slate-800 mb-6 text-center">Masuk ke Aplikasi</h2>

                <!-- Alert Error -->
                @if ($errors->any())
                <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                    <div class="text-sm text-red-600">
                        {{ $errors->first() }}
                    </div>
                </div>
                @endif

                <form action="{{ route('login.proses') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <!-- Email Input -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5 ml-1">Email BPS</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input type="email" name="email" class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-300 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all shadow-sm" placeholder="nama@bps.go.id" required>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5 ml-1">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" name="password" class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-300 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all shadow-sm" placeholder="••••••••" required>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password (Optional layout) -->
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-slate-600">Ingat Saya</label>
                        </div>
                        <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Lupa sandi?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg shadow-blue-600/20 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform hover:-translate-y-0.5">
                        Masuk Aplikasi
                    </button>
                </form>
            </div>
            
            <!-- Card Footer -->
            <div class="bg-slate-50 px-8 py-4 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-400 font-medium">
                    &copy; {{ date('Y') }} Badan Pusat Statistik
                </p>
            </div>
        </div>

    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - WLB Monitoring System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .animate-gradient {
            background-size: 400% 400%;
            animation: gradient-shift 8s ease infinite;
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out;
        }
        .glass-effect {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900 animate-gradient overflow-hidden">
    <!-- Background decorations -->
    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-r from-pink-400 to-red-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-40 left-40 w-60 h-60 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full mix-blend-multiply filter blur-xl opacity-50 animate-float" style="animation-delay: 4s;"></div>
    </div>

    <div class="h-full flex items-center justify-center relative z-10">
        <!-- Main container -->
        <div class="w-full max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8 px-4 sm:px-6 lg:px-8">
            
            <!-- Left side - Login form -->
            <div class="flex flex-col justify-center">
                <div class="w-full max-w-md mx-auto animate-fade-in-up">
                    <!-- Login Card -->
                    <div class="glass-effect rounded-3xl p-8 shadow-2xl backdrop-blur-lg">
                        <!-- Logo and title -->
                        <div class="text-center mb-8">
                            <div class="mx-auto h-20 w-20 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-3xl flex items-center justify-center mb-6 shadow-lg animate-float">
                                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h1 class="text-4xl font-bold bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent mb-2">
                                WLB Monitor
                            </h1>
                            <p class="text-lg text-gray-200 font-medium mb-1">Work-Life Balance System</p>
                            <p class="text-sm text-gray-300">Masuk untuk mengakses dashboard Anda</p>
                        </div>

                        <!-- Demo accounts info -->
                        <div class="mb-8 bg-gradient-to-r from-blue-500/20 to-purple-500/20 border border-blue-400/30 rounded-2xl p-4 backdrop-blur-sm">
                            <div class="flex items-center mb-3">
                                <div class="h-6 w-6 bg-gradient-to-r from-blue-400 to-purple-400 rounded-full flex items-center justify-center mr-2">
                                    <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-semibold text-white">Demo Accounts</h3>
                            </div>
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between items-center bg-white/10 rounded-lg p-2">
                                    <span class="text-blue-300 font-medium">👑 Admin:</span>
                                    <span class="text-white text-xs">admin@wlbapp.com</span>
                                </div>
                                <div class="flex justify-between items-center bg-white/10 rounded-lg p-2">
                                    <span class="text-green-300 font-medium">👔 Manager:</span>
                                    <span class="text-white text-xs">manager.it@wlbapp.com</span>
                                </div>
                                <div class="flex justify-between items-center bg-white/10 rounded-lg p-2">
                                    <span class="text-yellow-300 font-medium">👤 Employee:</span>
                                    <span class="text-white text-xs">john.doe@wlbapp.com</span>
                                </div>
                                <div class="text-center pt-1">
                                    <span class="text-gray-300 text-xs">🔑 Password: </span>
                                    <span class="text-white font-semibold">password</span>
                                </div>
                            </div>
                        </div>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="mb-6 bg-green-500/20 border border-green-400/30 rounded-xl p-4 backdrop-blur-sm">
                                <p class="text-sm text-green-200 text-center">{{ session('status') }}</p>
                            </div>
                        @endif

                        <!-- Login form -->
                        <form class="space-y-6" method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true">
                            @csrf

                            <div class="space-y-4">
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-200 mb-2">Email Address</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                            </svg>
                                        </div>
                                        <input id="email" name="email" type="email" autocomplete="email" required
                                               value="{{ old('email') }}"
                                               class="block w-full pl-10 pr-3 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm transition-all duration-300"
                                               placeholder="Masukkan email Anda">
                                    </div>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-semibold text-gray-200 mb-2">Password</label>
                                    <div class="relative" x-data="{ show: false }">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </div>
                                        <input id="password" name="password" :type="show ? 'text' : 'password'" autocomplete="current-password" required
                                               class="block w-full pl-10 pr-12 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm transition-all duration-300"
                                               placeholder="Masukkan password Anda">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <button type="button" @click="show = !show" class="text-gray-400 hover:text-gray-300">
                                                <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    @error('password')
                                        <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input id="remember_me" name="remember" type="checkbox"
                                           class="h-4 w-4 rounded border-white/30 bg-white/10 text-blue-500 focus:ring-blue-500 focus:ring-offset-0">
                                    <label for="remember_me" class="ml-2 block text-sm text-gray-300">Ingat saya</label>
                                </div>

                                @if (Route::has('password.request'))
                                    <div class="text-sm">
                                        <a href="{{ route('password.request') }}" class="font-medium text-blue-300 hover:text-blue-200 transition-colors">
                                            Lupa password?
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <button type="submit"
                                    class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-2xl"
                                    :disabled="loading">
                                <span x-show="!loading" class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    Masuk ke Dashboard
                                </span>
                                <span x-show="loading" class="flex items-center" x-cloak>
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memproses...
                                </span>
                            </button>

                            @if (Route::has('register'))
                                <div class="text-center">
                                    <p class="text-sm text-gray-300">
                                        Belum punya akun?
                                        <a href="{{ route('register') }}" class="font-semibold text-blue-300 hover:text-blue-200 transition-colors">
                                            Daftar sekarang
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right side - Illustration (hidden on mobile) -->
            <div class="hidden lg:flex flex-col justify-center">
                <div class="text-center animate-fade-in-up" style="animation-delay: 0.5s;">
                    <!-- Main illustration -->
                    <div class="mb-8">
                        <div class="relative mx-auto w-64 h-64">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-400/30 to-purple-500/30 rounded-full animate-float"></div>
                            <div class="absolute inset-4 bg-gradient-to-r from-purple-400/40 to-pink-500/40 rounded-full animate-float" style="animation-delay: 1s;"></div>
                            <div class="absolute inset-8 bg-gradient-to-r from-pink-400/50 to-red-500/50 rounded-full animate-float" style="animation-delay: 2s;"></div>
                            <div class="absolute inset-16 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <h2 class="text-5xl font-bold mb-6 bg-gradient-to-r from-white via-blue-100 to-purple-100 bg-clip-text text-transparent">
                        Smart WLB Analytics
                    </h2>
                    <p class="text-xl mb-8 text-gray-200 leading-relaxed max-w-lg mx-auto">
                        Transformasi digital untuk monitoring work-life balance dengan 
                        <span class="text-blue-300 font-semibold">AI-powered insights</span> 
                        dan analytics real-time
                    </p>
                    
                    <!-- Feature highlights -->
                    <div class="grid grid-cols-2 gap-4 text-center max-w-md mx-auto">
                        <div class="glass-effect rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                            <div class="text-4xl font-bold mb-2 bg-gradient-to-r from-blue-300 to-cyan-300 bg-clip-text text-transparent">7+</div>
                            <div class="text-sm text-blue-200 font-medium">WLB Metrics</div>
                        </div>
                        <div class="glass-effect rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                            <div class="text-4xl font-bold mb-2 bg-gradient-to-r from-purple-300 to-pink-300 bg-clip-text text-transparent">3</div>
                            <div class="text-sm text-purple-200 font-medium">User Roles</div>
                        </div>
                        <div class="glass-effect rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                            <div class="text-4xl font-bold mb-2 bg-gradient-to-r from-green-300 to-emerald-300 bg-clip-text text-transparent">100%</div>
                            <div class="text-sm text-green-200 font-medium">Real-time</div>
                        </div>
                        <div class="glass-effect rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                            <div class="text-4xl font-bold mb-2 bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent">24/7</div>
                            <div class="text-sm text-yellow-200 font-medium">Monitoring</div>
                        </div>
                    </div>

                    <!-- Technology badges -->
                    <div class="mt-8 flex flex-wrap justify-center gap-2">
                        <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-medium text-gray-200 backdrop-blur-sm">Laravel 11</span>
                        <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-medium text-gray-200 backdrop-blur-sm">TailwindCSS</span>
                        <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-medium text-gray-200 backdrop-blur-sm">Alpine.js</span>
                        <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-medium text-gray-200 backdrop-blur-sm">MySQL</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>

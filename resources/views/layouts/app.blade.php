<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50 dark:bg-gray-900" x-data="{ dark: localStorage.getItem('theme') === 'dark', toggleTheme(){ this.dark = !this.dark; localStorage.setItem('theme', this.dark ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', this.dark); } }" x-init="document.documentElement.classList.toggle('dark', dark)">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'WLB Monitoring System')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-gray-900 dark:text-gray-100">
    <div x-data="{ sidebarOpen: false }" class="h-full">
        <!-- Off-canvas menu for mobile -->
        <div x-show="sidebarOpen" class="relative z-50 lg:hidden" x-cloak>
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/80"></div>

            <div class="fixed inset-0 flex">
                <div x-show="sidebarOpen"
                     x-transition:enter="transition ease-in-out duration-300 transform"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-300 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full"
                     class="relative mr-16 flex w-full max-w-xs flex-1">
                    <!-- Close button -->
                    <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                        <button type="button" @click="sidebarOpen = false" class="-m-2.5 p-2.5">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Sidebar component for mobile -->
                    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4">
                        @include('partials.sidebar-content')
                    </div>
                </div>
            </div>
        </div>

        <!-- Static sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 pb-4">
                @include('partials.sidebar-content')
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-72">
            <!-- Top bar -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white dark:bg-gray-800 px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button type="button" @click="sidebarOpen = true" class="-m-2.5 p-2.5 text-gray-700 lg:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Separator -->
                <div class="h-6 w-px bg-gray-200 lg:hidden"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <!-- Search -->
                    <div class="flex flex-1 items-center">
                        <h1 class="text-xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <!-- Profile dropdown -->
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- Dark mode toggle -->
                        <button type="button" @click="toggleTheme()" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="Toggle dark mode">
                            <svg x-show="!dark" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M12 6.75a5.25 5.25 0 100 10.5 5.25 5.25 0 000-10.5z" />
                            </svg>
                            <svg x-show="dark" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75 9.75 9.75 0 018.25 6 9.718 9.718 0 019 2.248 9.75 9.75 0 1021.752 15z" />
                            </svg>
                        </button>
                        <!-- Notifications -->
                        <button type="button" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500">
                            <span class="sr-only">View notifications</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                        </button>

                        <!-- Direct Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Logout
                            </button>
                        </form>

                        <!-- Separator -->
                        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200"></div>

                        <!-- Profile dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button type="button" @click="open = !open" class="-m-1.5 flex items-center p-1.5">
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <span class="hidden lg:flex lg:items-center">
                                    <span class="ml-4 text-sm font-semibold leading-6 text-gray-900">{{ auth()->user()->name }}</span>
                                    <span class="ml-2 text-xs text-gray-500">({{ auth()->user()->getRoleNames()->first() }})</span>
                                    <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>

                <div x-show="open" 
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white dark:bg-gray-900 py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
                 x-cloak>
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800">Profile</a>
                </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content area -->
            <main class="py-8">
                    @if(session('success'))
                        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mb-6">
                            <div class="rounded-md bg-green-50 dark:bg-green-900/30 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                    @if(session('error'))
                        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mb-6">
                            <div class="rounded-md bg-red-50 dark:bg-red-900/30 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Floating toast container (Alpine) -->
    <div x-data="{show:false, msg:'', type:'success', fire(message, t='success'){ this.msg=message; this.type=t; this.show=true; setTimeout(()=>this.show=false, 2800); }}" x-ref="toast"
         x-show="show" x-transition
         class="fixed bottom-6 right-6 z-50">
        <div :class="type==='success' ? 'bg-green-600' : 'bg-red-600'" class="text-white px-4 py-3 rounded-lg shadow-lg">
            <span x-text="msg"></span>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

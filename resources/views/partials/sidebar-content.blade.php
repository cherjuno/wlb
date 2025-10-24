<!-- Logo -->
<div class="flex h-16 shrink-0 items-center">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <div class="h-8 w-8 rounded-lg bg-blue-600 flex items-center justify-center">
                <span class="text-white font-bold text-sm">WLB</span>
            </div>
        </div>
        <div class="ml-3">
            <h1 class="text-xl font-bold text-gray-900">WLB Monitor</h1>
            <p class="text-xs text-gray-500">{{ \App\Models\WlbSetting::get('company_name', 'Perusahaan A') }}</p>
        </div>
    </div>
</div>

<!-- Navigation -->
<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0018 16.5h-2.25m-7.5 0V10.5a2.25 2.25 0 012.25-2.25h3a2.25 2.25 0 012.25 2.25v6zM12 3.75h-3z" />
                        </svg>
                        Dashboard
                    </a>
                </li>

                <!-- Attendance -->
                <li>
                    <a href="{{ route('attendance.index') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ request()->routeIs('attendance.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('attendance.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Absensi
                    </a>
                </li>

                <!-- Leave -->
                <li>
                    <a href="{{ route('leave.index') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ request()->routeIs('leave.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('leave.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        Cuti
                        @if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin'))
                            @php
                                $pendingLeaves = \App\Models\Leave::where('status', 'pending');
                                if(auth()->user()->hasRole('manager')) {
                                    $subordinateIds = auth()->user()->subordinates()->pluck('id');
                                    $pendingLeaves = $pendingLeaves->whereIn('user_id', $subordinateIds);
                                }
                                $pendingCount = $pendingLeaves->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="ml-auto w-5 h-5 text-xs flex items-center justify-center bg-amber-100 text-amber-800 rounded-full">{{ $pendingCount }}</span>
                            @endif
                        @endif
                    </a>
                </li>

                <!-- Overtime -->
                <li>
                    <a href="{{ route('overtime.index') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ request()->routeIs('overtime.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('overtime.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                        Lembur
                        @if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin'))
                            @php
                                $pendingOvertimes = \App\Models\Overtime::where('status', 'pending');
                                if(auth()->user()->hasRole('manager')) {
                                    $subordinateIds = auth()->user()->subordinates()->pluck('id');
                                    $pendingOvertimes = $pendingOvertimes->whereIn('user_id', $subordinateIds);
                                }
                                $pendingOvertimeCount = $pendingOvertimes->count();
                            @endphp
                            @if($pendingOvertimeCount > 0)
                                <span class="ml-auto w-5 h-5 text-xs flex items-center justify-center bg-amber-100 text-amber-800 rounded-full">{{ $pendingOvertimeCount }}</span>
                            @endif
                        @endif
                    </a>
                </li>
            </ul>
        </li>

        <!-- Personal Section -->
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400">Personal</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <!-- Profile -->
                <li>
                    <a href="{{ route('profile.edit') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ request()->routeIs('profile.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('profile.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Profile
                    </a>
                </li>

                <!-- Salary -->
                <li>
                    <a href="{{ route('salary.index') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ request()->routeIs('salary.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('salary.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Gaji Bulanan
                        <span class="ml-auto text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full font-medium">
                            Rp {{ number_format(Auth::user()->salary ?? 0, 0, ',', '.') }}
                        </span>
                    </a>
                </li>

                <!-- Job Stress Scale -->
                <li>
                    @php
                        $hasFilledStress = Auth::user()->hasFilledJobStressThisMonth();
                        $currentStressLevel = Auth::user()->getCurrentMonthStressLevel();
                    @endphp
                    <a href="{{ $hasFilledStress ? route('job-stress.history') : route('job-stress.create') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ request()->routeIs('job-stress.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('job-stress.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                        </svg>
                        Job Stress Scale
                        @if(!$hasFilledStress)
                            <span class="ml-auto w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                        @elseif($currentStressLevel === 'high')
                            <span class="ml-auto text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full font-medium">
                                Tinggi
                            </span>
                        @elseif($currentStressLevel === 'moderate')
                            <span class="ml-auto text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full font-medium">
                                Sedang
                            </span>
                        @elseif($currentStressLevel === 'low')
                            <span class="ml-auto text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full font-medium">
                                Rendah
                            </span>
                        @endif
                    </a>
                </li>
            </ul>
        </li>

        @if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin'))
        <!-- Manager/Admin Section -->
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400">Management</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <!-- Pending Approvals -->
                <li>
                    <a href="{{ route('leave.pending') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ request()->routeIs('*.pending') ? 'bg-amber-50 text-amber-600' : 'text-gray-700 hover:bg-gray-50 hover:text-amber-600' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('*.pending') ? 'text-amber-600' : 'text-gray-400 group-hover:text-amber-600' }}" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Persetujuan
                        @php
                            $totalPending = $pendingCount + $pendingOvertimeCount;
                        @endphp
                        @if($totalPending > 0)
                            <span class="ml-auto w-5 h-5 text-xs flex items-center justify-center bg-red-100 text-red-800 rounded-full">{{ $totalPending }}</span>
                        @endif
                    </a>
                </li>

                <!-- Reports -->
                <li>
                    <a href="{{ route('attendance.report') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ request()->routeIs('*.report') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('*.report') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        Laporan
                    </a>
                </li>

                <!-- Job Stress Management -->
                @if(auth()->user()->hasRole('manager'))
                <li>
                    <a href="{{ route('job-stress.manager.dashboard') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ request()->routeIs('job-stress.manager.*') ? 'bg-purple-50 text-purple-600' : 'text-gray-700 hover:bg-gray-50 hover:text-purple-600' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('job-stress.manager.*') ? 'text-purple-600' : 'text-gray-400 group-hover:text-purple-600' }}" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                        </svg>
                        Stres Tim
                        @php
                            $subordinateIds = auth()->user()->subordinates()->pluck('id');
                            $highStressCount = \App\Models\JobStressScale::whereIn('user_id', $subordinateIds)
                                ->where('month', now()->month)
                                ->where('year', now()->year)
                                ->where('stress_level', 'high')
                                ->count();
                        @endphp
                        @if($highStressCount > 0)
                            <span class="ml-auto w-5 h-5 text-xs flex items-center justify-center bg-red-100 text-red-800 rounded-full">{{ $highStressCount }}</span>
                        @endif
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('admin'))
                <!-- Job Stress Administration -->
                <li>
                    <a href="{{ route('job-stress.admin.index') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ request()->routeIs('job-stress.admin.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('job-stress.admin.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }}" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0018 16.5h-2.25m-7.5 0V10.5a2.25 2.25 0 012.25-2.25h3a2.25 2.25 0 012.25 2.25v6zM12 3.75h-3z" />
                        </svg>
                        Job Stress Admin
                        @php
                            $totalEmployees = \App\Models\User::role('employee')->count();
                            $completedForms = \App\Models\JobStressScale::where('month', now()->month)
                                ->where('year', now()->year)
                                ->count();
                            $completionRate = $totalEmployees > 0 ? round(($completedForms / $totalEmployees) * 100) : 0;
                        @endphp
                        <span class="ml-auto text-xs 
                            @if($completionRate >= 80) bg-green-100 text-green-800
                            @elseif($completionRate >= 50) bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif px-2 py-1 rounded-full font-medium">
                            {{ $completionRate }}%
                        </span>
                    </a>
                </li>

                <!-- Analytics Detail -->
                <li>
                    <a href="{{ route('analytics.employee-matrix') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ request()->routeIs('analytics.*') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50 hover:text-green-600' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('analytics.*') ? 'text-green-600' : 'text-gray-400 group-hover:text-green-600' }}" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0018 16.5h-2.25m-7.5 0V10.5a2.25 2.25 0 012.25-2.25h3a2.25 2.25 0 012.25 2.25v6zM12 3.75h-3z" />
                        </svg>
                        Analytics Detail
                        @php
                            $currentUser = auth()->user();
                            $employeesQuery = \App\Models\User::where('is_active', true);
                            if ($currentUser->hasRole('manager')) {
                                $subordinateIds = $currentUser->subordinates()->pluck('id');
                                $employeesQuery->whereIn('id', $subordinateIds);
                            }
                            $totalEmployees = $employeesQuery->count();
                        @endphp
                        <span class="ml-auto text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium">
                            {{ $totalEmployees }}
                        </span>
                    </a>
                </li>

                <!-- User Management -->
                <li>
                    <a href="{{ route('users.index') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('users.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Kelola User
                        @php
                            $totalUsers = \App\Models\User::count();
                            $activeUsers = \App\Models\User::where('is_active', true)->count();
                        @endphp
                        <span class="ml-auto text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full font-medium">
                            {{ $activeUsers }}/{{ $totalUsers }}
                        </span>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- Quick Actions -->
        <li class="mt-auto">
            <div class="text-xs font-semibold leading-6 text-gray-400">Quick Actions</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <li>
                    <button type="button" 
                            onclick="quickCheckIn()" 
                            class="group flex w-full gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-700 hover:bg-green-50 hover:text-green-600">
                        <svg class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-green-600" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Quick Check-in
                    </button>
                </li>
                <li>
                    <a href="{{ route('leave.create') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                        <svg class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-blue-600" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Ajukan Cuti
                    </a>
                </li>
                <li>
                    <a href="{{ route('overtime.create') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-700 hover:bg-amber-50 hover:text-amber-600">
                        <svg class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-amber-600" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Ajukan Lembur
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>

<!-- User info -->
<div class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-gray-900 border-t border-gray-200">
    <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
        <span class="text-sm font-medium text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
    </div>
    <div class="flex-1">
        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
        <p class="text-xs text-gray-500">
            @if(auth()->user()->hasRole('admin'))
                Administrator
            @elseif(auth()->user()->hasRole('manager'))
                Manager
            @else
                Employee
            @endif
        </p>
    </div>
</div>

@push('scripts')
<script>
async function quickCheckIn() {
    try {
        const response = await fetch('{{ route("attendance.quick-check-in") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();
        
        if (data.success) {
            alert('Check-in berhasil!');
            location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    } catch (error) {
        alert('Terjadi kesalahan saat melakukan check-in');
    }
}
</script>
@endpush
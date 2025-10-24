@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome Header --}}
    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 text-white rounded-3xl p-8 shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">
                    👋 Welcome back, {{ auth()->user()->name }}!
                </h1>
                <p class="text-blue-100 text-lg">
                    @php
                        $hour = now()->hour;
                        if($hour < 12) echo "🌅 Good morning";
                        elseif($hour < 17) echo "☀️ Good afternoon";
                        else echo "🌙 Good evening";
                    @endphp
                    • Today is {{ now()->format('l, F j, Y') }}
                </p>
                <div class="mt-4 flex items-center space-x-4">
                    <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-semibold">
                        @if(auth()->user()->hasRole('admin'))
                            👨‍💼 Administrator
                        @elseif(auth()->user()->hasRole('manager'))
                            👨‍💼 Manager
                        @else
                            👩‍💻 Employee
                        @endif
                    </span>
                    <span class="text-blue-100">•</span>
                    <span class="text-blue-100">{{ \App\Models\WlbSetting::get('company_name', 'Perusahaan A') }}</span>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="h-24 w-24 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0018 16.5h-2.25m-7.5 0V10.5a2.25 2.25 0 012.25-2.25h3a2.25 2.25 0 012.25 2.25v6zM12 3.75h-3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $todayAttendance = \App\Models\Attendance::where('user_id', auth()->id())->whereDate('created_at', today())->first();
            $thisMonthLeaves = \App\Models\Leave::where('user_id', auth()->id())->where('status', 'approved')->whereMonth('start_date', now()->month)->count();
            $thisMonthOvertimes = \App\Models\Overtime::where('user_id', auth()->id())->where('status', 'approved')->whereMonth('date', now()->month)->count();
            $currentStressLevel = auth()->user()->getCurrentMonthStressLevel() ?? 'not_filled';
        @endphp

        {{-- Attendance Status --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                    @if($todayAttendance) bg-green-100 text-green-800
                    @else bg-red-100 text-red-800 @endif">
                    @if($todayAttendance)
                        ✅ Present
                    @else
                        ❌ Not Checked In
                    @endif
                </span>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Today's Attendance</h3>
            <p class="text-gray-600 text-sm mt-1">
                @if($todayAttendance)
                    Check-in: {{ $todayAttendance->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') : '-' }}
                @else
                    Please check in for today
                @endif
            </p>
        </div>

        {{-- Monthly Leave --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-blue-600">{{ $thisMonthLeaves }}</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900">This Month Leaves</h3>
            <p class="text-gray-600 text-sm mt-1">Approved leave days</p>
        </div>

        {{-- Monthly Overtime --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"></path>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-purple-600">{{ $thisMonthOvertimes }}</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Overtime Hours</h3>
            <p class="text-gray-600 text-sm mt-1">This month approved</p>
        </div>

        {{-- Stress Level --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 
                    @if($currentStressLevel === 'high') bg-red-100
                    @elseif($currentStressLevel === 'moderate') bg-yellow-100
                    @elseif($currentStressLevel === 'low') bg-green-100
                    @else bg-gray-100 @endif
                    rounded-xl flex items-center justify-center">
                    <svg class="h-6 w-6 
                        @if($currentStressLevel === 'high') text-red-600
                        @elseif($currentStressLevel === 'moderate') text-yellow-600
                        @elseif($currentStressLevel === 'low') text-green-600
                        @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"></path>
                    </svg>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                    @if($currentStressLevel === 'high') bg-red-100 text-red-800
                    @elseif($currentStressLevel === 'moderate') bg-yellow-100 text-yellow-800
                    @elseif($currentStressLevel === 'low') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800 @endif">
                    @if($currentStressLevel === 'high') High Stress
                    @elseif($currentStressLevel === 'moderate') Moderate
                    @elseif($currentStressLevel === 'low') Low Stress
                    @else Not Filled @endif
                </span>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Stress Level</h3>
            <p class="text-gray-600 text-sm mt-1">
                @if($currentStressLevel === 'not_filled')
                    Please fill job stress scale
                @else
                    Current month assessment
                @endif
            </p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <span class="mr-3">⚡</span>
            Quick Actions
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Quick Check-in --}}
            <button onclick="quickCheckIn()" 
                    class="group p-6 bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl hover:from-green-100 hover:to-emerald-100 hover:border-green-300 transition-all duration-300 hover:scale-105">
                <div class="flex flex-col items-center text-center space-y-3">
                    <div class="h-12 w-12 bg-green-500 rounded-xl flex items-center justify-center text-white group-hover:bg-green-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-green-800">Quick Check-in</h3>
                    <p class="text-sm text-green-600">Mark attendance now</p>
                </div>
            </button>

            {{-- Apply Leave --}}
            <a href="{{ route('leave.create') }}" 
               class="group p-6 bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl hover:from-blue-100 hover:to-indigo-100 hover:border-blue-300 transition-all duration-300 hover:scale-105">
                <div class="flex flex-col items-center text-center space-y-3">
                    <div class="h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center text-white group-hover:bg-blue-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-blue-800">Apply Leave</h3>
                    <p class="text-sm text-blue-600">Request time off</p>
                </div>
            </a>

            {{-- Request Overtime --}}
            <a href="{{ route('overtime.create') }}" 
               class="group p-6 bg-gradient-to-br from-purple-50 to-violet-50 border-2 border-purple-200 rounded-2xl hover:from-purple-100 hover:to-violet-100 hover:border-purple-300 transition-all duration-300 hover:scale-105">
                <div class="flex flex-col items-center text-center space-y-3">
                    <div class="h-12 w-12 bg-purple-500 rounded-xl flex items-center justify-center text-white group-hover:bg-purple-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-purple-800">Request Overtime</h3>
                    <p class="text-sm text-purple-600">Apply for extra hours</p>
                </div>
            </a>

            {{-- Job Stress Assessment --}}
            @php
                $hasFilledStress = Auth::user()->hasFilledJobStressThisMonth();
            @endphp
            <a href="{{ $hasFilledStress ? route('job-stress.history') : route('job-stress.create') }}" 
               class="group p-6 bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl hover:from-amber-100 hover:to-orange-100 hover:border-amber-300 transition-all duration-300 hover:scale-105">
                <div class="flex flex-col items-center text-center space-y-3">
                    <div class="h-12 w-12 bg-amber-500 rounded-xl flex items-center justify-center text-white group-hover:bg-amber-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-amber-800">
                        @if($hasFilledStress) View Stress History @else Stress Assessment @endif
                    </h3>
                    <p class="text-sm text-amber-600">
                        @if($hasFilledStress) Check previous results @else Fill monthly assessment @endif
                    </p>
                </div>
            </a>
        </div>
    </div>

    @if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin'))
    {{-- Management Dashboard --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <span class="mr-3">📊</span>
            @if(auth()->user()->hasRole('admin'))
                Administrative Overview
            @else
                Team Management
            @endif
        </h2>

        @php
            if(auth()->user()->hasRole('admin')) {
                $pendingLeaves = \App\Models\Leave::where('status', 'pending')->count();
                $pendingOvertimes = \App\Models\Overtime::where('status', 'pending')->count();
                $totalEmployees = \App\Models\User::role('employee')->count();
            } else {
                $subordinateIds = auth()->user()->subordinates()->pluck('id');
                $pendingLeaves = \App\Models\Leave::where('status', 'pending')->whereIn('user_id', $subordinateIds)->count();
                $pendingOvertimes = \App\Models\Overtime::where('status', 'pending')->whereIn('user_id', $subordinateIds)->count();
                $totalEmployees = $subordinateIds->count();
            }
            $highStressEmployees = \App\Models\JobStressScale::where('month', now()->month)
                ->where('year', now()->year)
                ->where('stress_level', 'high');
            if(auth()->user()->hasRole('manager')) {
                $highStressEmployees = $highStressEmployees->whereIn('user_id', $subordinateIds);
            }
            $highStressCount = $highStressEmployees->count();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Pending Approvals --}}
            <div class="bg-gradient-to-br from-red-50 to-pink-50 border border-red-200 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 bg-red-500 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-red-600">{{ $pendingLeaves + $pendingOvertimes }}</span>
                </div>
                <h3 class="text-lg font-bold text-red-800">Pending Approvals</h3>
                <p class="text-red-600 text-sm">{{ $pendingLeaves }} leaves, {{ $pendingOvertimes }} overtimes</p>
            </div>

            {{-- Total Team --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-blue-600">{{ $totalEmployees }}</span>
                </div>
                <h3 class="text-lg font-bold text-blue-800">
                    @if(auth()->user()->hasRole('admin')) Total Employees @else Team Members @endif
                </h3>
                <p class="text-blue-600 text-sm">Active personnel</p>
            </div>

            {{-- High Stress Alerts --}}
            <div class="bg-gradient-to-br from-yellow-50 to-amber-50 border border-yellow-200 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 bg-yellow-500 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-yellow-600">{{ $highStressCount }}</span>
                </div>
                <h3 class="text-lg font-bold text-yellow-800">High Stress Alert</h3>
                <p class="text-yellow-600 text-sm">Employees need attention</p>
            </div>

            {{-- Quick Management --}}
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-6">
                <div class="flex items-center justify-center h-full">
                    <a href="{{ route('leave.pending') }}" 
                       class="w-full text-center py-4 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold transition-colors">
                        🚀 Manage Team
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Recent Activity --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <span class="mr-3">📋</span>
            Recent Activity
        </h2>

        @php
            $recentActivities = collect();
            
            // Recent attendance
            $recentAttendances = \App\Models\Attendance::where('user_id', auth()->id())
                ->latest()
                ->limit(3)
                ->get()
                ->map(function($item) {
                    return [
                        'type' => 'attendance',
                        'message' => 'Checked in at ' . \Carbon\Carbon::parse($item->check_in)->format('H:i'),
                        'date' => $item->created_at,
                        'status' => 'success',
                        'icon' => '🕐'
                    ];
                });
            
            // Recent leaves
            $recentLeaves = \App\Models\Leave::where('user_id', auth()->id())
                ->latest()
                ->limit(2)
                ->get()
                ->map(function($item) {
                    return [
                        'type' => 'leave',
                        'message' => ucfirst($item->status) . ' leave request (' . $item->type . ')',
                        'date' => $item->updated_at,
                        'status' => $item->status,
                        'icon' => '📅'
                    ];
                });
            
            // Recent overtimes
            $recentOvertimes = \App\Models\Overtime::where('user_id', auth()->id())
                ->latest()
                ->limit(2)
                ->get()
                ->map(function($item) {
                    return [
                        'type' => 'overtime',
                        'message' => ucfirst($item->status) . ' overtime request (' . $item->hours . 'h)',
                        'date' => $item->updated_at,
                        'status' => $item->status,
                        'icon' => '🌙'
                    ];
                });
            
            $recentActivities = $recentActivities
                ->merge($recentAttendances)
                ->merge($recentLeaves)
                ->merge($recentOvertimes)
                ->sortByDesc('date')
                ->take(6);
        @endphp

        @if($recentActivities->count() > 0)
            <div class="space-y-4">
                @foreach($recentActivities as $activity)
                    <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-2xl border border-gray-200">
                        <div class="text-2xl">{{ $activity['icon'] }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['message'] }}</p>
                            <p class="text-sm text-gray-500">{{ $activity['date']->format('M j, Y H:i') }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($activity['status'] === 'approved' || $activity['status'] === 'success') bg-green-100 text-green-800
                            @elseif($activity['status'] === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($activity['status']) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="h-16 w-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Recent Activity</h3>
                <p class="text-gray-600">Start by checking in or applying for leave/overtime</p>
            </div>
        @endif
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
            alert('✅ Check-in berhasil!');
            location.reload();
        } else {
            alert('❌ ' + (data.message || 'Terjadi kesalahan'));
        }
    } catch (error) {
        alert('❌ Terjadi kesalahan saat melakukan check-in');
        console.error('Check-in error:', error);
    }
}
</script>
@endpush
@endsection

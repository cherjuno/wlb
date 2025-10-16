@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    {{-- Profile Header --}}
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white rounded-3xl p-8 shadow-2xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6">
                {{-- Avatar --}}
                <div class="relative">
                    <div class="h-28 w-28 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm border-4 border-white/30">
                        <span class="text-5xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <div class="absolute -bottom-2 -right-2 h-10 w-10 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                
                {{-- User Info --}}
                <div class="flex-1">
                    <h1 class="text-4xl font-bold mb-2">{{ $user->name }}</h1>
                    <p class="text-lg text-purple-100 mb-3">{{ $user->employee_id }}</p>
                    <div class="flex items-center space-x-6 text-blue-100">
                        <span class="flex items-center space-x-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ $user->position->name ?? 'Staff' }}</span>
                        </span>
                        <span class="flex items-center space-x-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span>{{ $user->department->name ?? 'Department' }}</span>
                        </span>
                    </div>
                    
                    {{-- Role Badge --}}
                    <div class="mt-4">
                        @foreach($user->getRoleNames() as $role)
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white/20 text-white border border-white/30 backdrop-blur-sm">
                                @if($role === 'admin')
                                    🛡️ Administrator
                                @elseif($role === 'manager')
                                    👔 Manager
                                @else
                                    👤 Employee
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            
            {{-- Edit Button --}}
            <div class="flex flex-col space-y-3">
                <a href="{{ route('profile.edit') }}" 
                   class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl font-semibold hover:bg-white/30 transition-colors inline-flex items-center space-x-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Edit Profile</span>
                </a>
                <a href="{{ route('salary.index') }}" 
                   class="bg-emerald-500/80 backdrop-blur-sm text-white px-6 py-3 rounded-xl font-semibold hover:bg-emerald-600/80 transition-colors inline-flex items-center space-x-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    <span>Salary Info</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Years of Service --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Years of Service</p>
                    <p class="text-2xl font-bold text-blue-600">
                        {{ $user->hire_date ? \Carbon\Carbon::parse($user->hire_date)->diffInYears(now()) : 0 }}
                    </p>
                    <p class="text-xs text-gray-500">Since {{ $user->hire_date ? \Carbon\Carbon::parse($user->hire_date)->format('M Y') : '-' }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">🏆</span>
                </div>
            </div>
        </div>

        {{-- Leave Balance --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Leave Balance</p>
                    <p class="text-2xl font-bold text-green-600">{{ $user->remaining_leave ?? 12 }}</p>
                    <p class="text-xs text-gray-500">Days remaining</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">🏖️</span>
                </div>
            </div>
        </div>

        {{-- This Month Attendance --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">This Month</p>
                    @php
                        $thisMonthAttendance = $user->attendances()
                            ->whereYear('date', now()->year)
                            ->whereMonth('date', now()->month)
                            ->whereNotNull('check_in')
                            ->count();
                    @endphp
                    <p class="text-2xl font-bold text-purple-600">{{ $thisMonthAttendance }}</p>
                    <p class="text-xs text-gray-500">Working days</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">📅</span>
                </div>
            </div>
        </div>

        {{-- WLB Score --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">WLB Score</p>
                    @php
                        $wlbScore = \App\Helpers\WlbHelper::calculateWlbScore($user->id);
                    @endphp
                    <p class="text-2xl font-bold 
                        @if($wlbScore >= 80) text-green-600
                        @elseif($wlbScore >= 60) text-blue-600
                        @elseif($wlbScore >= 40) text-yellow-600
                        @else text-red-600
                        @endif">{{ $wlbScore }}</p>
                    <p class="text-xs text-gray-500">
                        @if($wlbScore >= 80) Excellent
                        @elseif($wlbScore >= 60) Good
                        @elseif($wlbScore >= 40) Fair
                        @else Needs Attention
                        @endif
                    </p>
                </div>
                <div class="h-12 w-12 
                    @if($wlbScore >= 80) bg-green-100
                    @elseif($wlbScore >= 60) bg-blue-100
                    @elseif($wlbScore >= 40) bg-yellow-100
                    @else bg-red-100
                    @endif rounded-xl flex items-center justify-center">
                    <span class="text-2xl">
                        @if($wlbScore >= 80) 😊
                        @elseif($wlbScore >= 60) 🙂
                        @elseif($wlbScore >= 40) 😐
                        @else 😟
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Personal Information --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Basic Information --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-2">
                <span class="text-2xl">ℹ️</span>
                <span>Personal Information</span>
            </h3>
            
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Full Name</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Employee ID</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->employee_id }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                        <p class="text-lg text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Phone</label>
                        <p class="text-lg text-gray-900">{{ $user->phone ?? '-' }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Birth Date</label>
                        <p class="text-lg text-gray-900">{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Gender</label>
                        <p class="text-lg text-gray-900">{{ $user->gender ? ucfirst($user->gender) : '-' }}</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Address</label>
                    <p class="text-lg text-gray-900">{{ $user->address ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Work Information --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-2">
                <span class="text-2xl">💼</span>
                <span>Work Information</span>
            </h3>
            
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Department</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->department->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Position</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->position->name ?? '-' }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Hire Date</label>
                        <p class="text-lg text-gray-900">{{ $user->hire_date ? \Carbon\Carbon::parse($user->hire_date)->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Manager</label>
                        <p class="text-lg text-gray-900">{{ $user->manager->name ?? '-' }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Annual Leave Quota</label>
                        <p class="text-lg text-gray-900">{{ $user->annual_leave_quota ?? 12 }} days</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                        <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-2">
            <span class="text-2xl">📊</span>
            <span>Recent Activity</span>
        </h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Recent Attendance --}}
            <div>
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Recent Attendance</h4>
                @php
                    $recentAttendances = $user->attendances()
                        ->orderBy('date', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                
                <div class="space-y-3">
                    @forelse($recentAttendances as $attendance)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($attendance->date)->format('M d') }}</p>
                                <p class="text-sm text-gray-600">{{ $attendance->check_in }} - {{ $attendance->check_out ?? 'Ongoing' }}</p>
                            </div>
                            <span class="text-sm px-2 py-1 rounded-full
                                @if($attendance->status === 'present') bg-green-100 text-green-800
                                @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No recent attendance</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent Leaves --}}
            <div>
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Recent Leaves</h4>
                @php
                    $recentLeaves = $user->leaves()
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                
                <div class="space-y-3">
                    @forelse($recentLeaves as $leave)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ ucfirst($leave->type) }}</p>
                                <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('M d') }}</p>
                            </div>
                            <span class="text-sm px-2 py-1 rounded-full
                                @if($leave->status === 'approved') bg-green-100 text-green-800
                                @elseif($leave->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No recent leaves</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent Overtime --}}
            <div>
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Recent Overtime</h4>
                @php
                    $recentOvertimes = $user->overtimes()
                        ->orderBy('date', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                
                <div class="space-y-3">
                    @forelse($recentOvertimes as $overtime)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ number_format($overtime->hours, 1, '.', '') }}h</p>
                                <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($overtime->date)->format('M d') }} ({{ ucfirst($overtime->type) }})</p>
                            </div>
                            <span class="text-sm px-2 py-1 rounded-full
                                @if($overtime->status === 'approved') bg-green-100 text-green-800
                                @elseif($overtime->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($overtime->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No recent overtime</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
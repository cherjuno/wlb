@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-bold text-gray-900">User Details</h1>
            <p class="mt-2 text-sm text-gray-700">Complete information for {{ $user->name }}.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none space-x-3">
            @can('update', $user)
                <a href="{{ route('users.edit', $user) }}" class="inline-flex rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    Edit User
                </a>
            @endcan
            <a href="{{ route('users.index') }}" class="inline-flex rounded-md bg-gray-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                Back to Users
            </a>
        </div>
    </div>

    <!-- User Profile Card -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white rounded-3xl p-8 shadow-2xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <!-- Avatar -->
                <div class="relative">
                    <div class="h-24 w-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm border-4 border-white/30">
                        <span class="text-4xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <div class="absolute -bottom-2 -right-2 h-8 w-8 {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }} rounded-full border-4 border-white flex items-center justify-center">
                        @if($user->is_active)
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        @endif
                    </div>
                </div>
                
                <!-- User Info -->
                <div class="flex-1">
                    <h1 class="text-3xl font-bold mb-1">{{ $user->name }}</h1>
                    <p class="text-lg text-purple-100 mb-2">{{ $user->employee_id }}</p>
                    <div class="flex items-center space-x-4 text-blue-100">
                        <span class="flex items-center space-x-1">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ $user->position->name ?? 'N/A' }}</span>
                        </span>
                        <span class="flex items-center space-x-1">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span>{{ $user->department->name ?? 'N/A' }}</span>
                        </span>
                    </div>
                    
                    <!-- Role Badge -->
                    <div class="mt-3">
                        @foreach($user->getRoleNames() as $role)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white border border-white/30 backdrop-blur-sm">
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
            
            <!-- Status Badge -->
            <div class="text-center">
                <span class="inline-flex px-4 py-2 text-sm font-medium rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Years of Service -->
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

        <!-- Leave Balance -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Leave Balance</p>
                    <p class="text-2xl font-bold text-green-600">{{ $user->remaining_leave ?? $user->annual_leave_quota ?? 12 }}</p>
                    <p class="text-xs text-gray-500">of {{ $user->annual_leave_quota ?? 12 }} days</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">🏖️</span>
                </div>
            </div>
        </div>

        <!-- Subordinates -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Subordinates</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $user->subordinates->count() }}</p>
                    <p class="text-xs text-gray-500">Team members</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">👥</span>
                </div>
            </div>
        </div>

        <!-- WLB Score -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">WLB Score</p>
                    <p class="text-2xl font-bold 
                        @if($wlbScore >= 80) text-green-600
                        @elseif($wlbScore >= 60) text-blue-600
                        @elseif($wlbScore >= 40) text-yellow-600
                        @else text-red-600
                        @endif">{{ $wlbScore }}</p>
                    <p class="text-xs text-gray-500">{{ $wlbStatus }}</p>
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

    <!-- Detailed Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Personal Information -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center space-x-2">
                <span class="text-2xl">👤</span>
                <span>Personal Information</span>
            </h3>
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Full Name</label>
                        <p class="text-lg text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Employee ID</label>
                        <p class="text-lg text-gray-900">{{ $user->employee_id }}</p>
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

        <!-- Work Information -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center space-x-2">
                <span class="text-2xl">💼</span>
                <span>Work Information</span>
            </h3>
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Department</label>
                        <p class="text-lg text-gray-900">{{ $user->department->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Position</label>
                        <p class="text-lg text-gray-900">{{ $user->position->name ?? '-' }}</p>
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
                        <label class="block text-sm font-medium text-gray-600 mb-1">Role</label>
                        <span class="inline-flex px-2 py-1 text-sm font-medium rounded-full {{ $user->getRoleNames()->first() === 'admin' ? 'bg-red-100 text-red-800' : ($user->getRoleNames()->first() === 'manager' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($user->getRoleNames()->first()) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members (if user is manager) -->
    @if($user->subordinates->count() > 0)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center space-x-2">
            <span class="text-2xl">👥</span>
            <span>Team Members ({{ $user->subordinates->count() }})</span>
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($user->subordinates as $subordinate)
                <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-blue-600">{{ strtoupper(substr($subordinate->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $subordinate->name }}</p>
                        <p class="text-xs text-gray-500">{{ $subordinate->position->name ?? 'N/A' }}</p>
                    </div>
                    <a href="{{ route('users.show', $subordinate) }}" class="text-blue-600 hover:text-blue-800">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- WLB Recommendations -->
    @if(count($recommendations) > 0)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center space-x-2">
            <span class="text-2xl">💡</span>
            <span>WLB Recommendations</span>
        </h3>
        
        <div class="space-y-3">
            @foreach($recommendations as $recommendation)
                <div class="flex items-start space-x-3 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-yellow-800">{{ $recommendation }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
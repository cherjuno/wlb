@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    {{-- Profile Header --}}
    <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white rounded-3xl p-8 shadow-xl">
        <div class="flex items-center space-x-6">
            {{-- Avatar --}}
            <div class="relative">
                <div class="h-24 w-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm border-4 border-white/30">
                    <span class="text-4xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <div class="absolute -bottom-2 -right-2 h-8 w-8 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            
            {{-- User Info --}}
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-2">{{ $user->name }}</h1>
                <div class="flex items-center space-x-4 text-purple-100">
                    <span class="flex items-center space-x-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>{{ $user->position->name ?? 'Staff' }}</span>
                    </span>
                    <span class="flex items-center space-x-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>{{ $user->department->name ?? 'Department' }}</span>
                    </span>
                </div>
                
                {{-- Role Badge --}}
                <div class="mt-3">
                    @foreach($user->getRoleNames() as $role)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white border border-white/30 backdrop-blur-sm">
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
            
            {{-- Status --}}
            <div class="text-center">
                <div class="bg-white/20 rounded-2xl p-4 backdrop-blur-sm border border-white/30">
                    <div class="text-2xl font-bold">{{ $user->is_active ? 'Active' : 'Inactive' }}</div>
                    <div class="text-sm text-purple-100">Status</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Profile Statistics Cards --}}
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
                        $monthlyAttendance = $user->attendances()->whereMonth('date', now()->month)->whereYear('date', now()->year)->count();
                    @endphp
                    <p class="text-2xl font-bold text-purple-600">{{ $monthlyAttendance }}</p>
                    <p class="text-xs text-gray-500">Days attended</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">📅</span>
                </div>
            </div>
        </div>

        {{-- Monthly Salary --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Monthly Salary</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($user->salary ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">IDR per month</p>
                </div>
                <div class="h-12 w-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">💰</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Personal Information --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Basic Info --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">📋 Personal Information</h3>
                    <button onclick="openEditModal()" class="text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors">Edit</button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Full Name</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Employee ID</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->employee_id ?? 'Not Set' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Email Address</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->email }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Phone Number</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Birth Date</label>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('F d, Y') : 'Not provided' }}
                            @if($user->birth_date)
                                <span class="text-sm text-gray-500">({{ \Carbon\Carbon::parse($user->birth_date)->age }} years old)</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Gender</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->gender ? ucfirst($user->gender) : 'Not specified' }}</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Address</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->address ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            {{-- Work Information --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6">💼 Work Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Department</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->department->name ?? 'Not assigned' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Position</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->position->name ?? 'Not assigned' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Hire Date</label>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $user->hire_date ? \Carbon\Carbon::parse($user->hire_date)->format('F d, Y') : 'Not provided' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Employee Status</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? '✅ Active' : '❌ Inactive' }}
                        </span>
                    </div>

                    @if($user->hasRole(['manager', 'admin']))
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Team Members</label>
                        @php
                            $teamCount = $user->hasRole('admin') ? \App\Models\User::count() - 1 : $user->subordinates()->count();
                        @endphp
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $teamCount }} {{ $user->hasRole('admin') ? 'Total Employees' : 'Direct Reports' }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Quick Stats --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6">
                <h4 class="text-lg font-bold text-gray-900 mb-4">📊 Quick Stats</h4>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Leaves</span>
                        <span class="font-semibold">{{ $user->leaves()->count() }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Approved Leaves</span>
                        <span class="font-semibold text-green-600">{{ $user->leaves()->where('status', 'approved')->count() }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Overtime Hours</span>
                        <span class="font-semibold">{{ $user->overtimes()->where('status', 'approved')->sum('hours') }}h</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Attendance Rate</span>
                        @php
                            $totalWorkDays = now()->startOfMonth()->diffInWeekdays(now());
                            $attendedDays = $user->attendances()->whereMonth('date', now()->month)->whereYear('date', now()->year)->count();
                            $attendanceRate = $totalWorkDays > 0 ? round(($attendedDays / $totalWorkDays) * 100) : 0;
                        @endphp
                        <span class="font-semibold {{ $attendanceRate >= 90 ? 'text-green-600' : ($attendanceRate >= 75 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $attendanceRate }}%
                        </span>
                    </div>
                </div>
            </div>

            {{-- Recent Activities --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6">
                <h4 class="text-lg font-bold text-gray-900 mb-4">🕐 Recent Activities</h4>
                
                <div class="space-y-3">
                    @php
                        $recentAttendance = $user->attendances()->latest()->first();
                        $recentLeave = $user->leaves()->latest()->first();
                        $recentOvertime = $user->overtimes()->latest()->first();
                    @endphp
                    
                    @if($recentAttendance)
                        <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-xl">
                            <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs">✓</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium">Attended</p>
                                <p class="text-xs text-gray-500">{{ $recentAttendance->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($recentLeave)
                        <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-xl">
                            <div class="h-8 w-8 bg-green-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs">🏖️</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium">Leave Request</p>
                                <p class="text-xs text-gray-500">{{ $recentLeave->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($recentOvertime)
                        <div class="flex items-center space-x-3 p-3 bg-orange-50 rounded-xl">
                            <div class="h-8 w-8 bg-orange-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs">⏰</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium">Overtime Request</p>
                                <p class="text-xs text-gray-500">{{ $recentOvertime->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6">
                <h4 class="text-lg font-bold text-gray-900 mb-4">⚡ Quick Actions</h4>
                
                <div class="space-y-3">
                    <a href="{{ route('salary.index') }}" class="w-full bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-xl p-3 text-center font-medium hover:from-green-600 hover:to-emerald-600 transition-all block">
                        💰 View Salary Details
                    </a>
                    
                    <a href="{{ route('leave.create') }}" class="w-full bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-xl p-3 text-center font-medium hover:from-blue-600 hover:to-indigo-600 transition-all block">
                        📅 Request Leave
                    </a>
                    
                    <a href="{{ route('overtime.create') }}" class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl p-3 text-center font-medium hover:from-purple-600 hover:to-pink-600 transition-all block">
                        ⏰ Request Overtime
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Settings Section --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <h3 class="text-xl font-bold text-gray-900 mb-6">⚙️ Account Settings</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="border border-gray-200 rounded-2xl p-6 hover:border-blue-300 transition-colors">
                <h4 class="font-semibold text-gray-900 mb-2">Update Profile</h4>
                <p class="text-gray-600 text-sm mb-4">Update your personal information and contact details</p>
                <button onclick="openEditModal()" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Edit Profile →</button>
            </div>
            
            <div class="border border-gray-200 rounded-2xl p-6 hover:border-yellow-300 transition-colors">
                <h4 class="font-semibold text-gray-900 mb-2">Change Password</h4>
                <p class="text-gray-600 text-sm mb-4">Update your password to keep your account secure</p>
                <button class="text-yellow-600 hover:text-yellow-800 font-medium text-sm">Change Password →</button>
            </div>
            
            <div class="border border-gray-200 rounded-2xl p-6 hover:border-red-300 transition-colors">
                <h4 class="font-semibold text-gray-900 mb-2">Privacy Settings</h4>
                <p class="text-gray-600 text-sm mb-4">Manage your privacy preferences and notifications</p>
                <button class="text-red-600 hover:text-red-800 font-medium text-sm">Manage Privacy →</button>
            </div>
        </div>
    </div>
</div>

{{-- Edit Profile Modal --}}
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeEditModal()"></div>

        {{-- Modal panel --}}
        <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Edit Personal Information</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('birth_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select id="gender" name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea id="address" name="address" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Save Changes
                    </button>
                    <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Show success message if profile was updated
@if(session('status') === 'profile-updated')
    showNotification('Profile updated successfully!', 'success');
@endif

function showNotification(message, type) {
    const color = type === 'success' ? 'green' : 'red';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg bg-${color}-100 border border-${color}-200`;
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <div class="flex-shrink-0">
                ${type === 'success' ? 
                    '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
                    '<svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                }
            </div>
            <p class="text-${color}-800">${message}</p>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush
@endsection

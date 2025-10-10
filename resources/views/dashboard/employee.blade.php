@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ 
    showWlbDetails: false,
    attendanceLoading: false,
    leaveLoading: false,
    overtimeLoading: false
}">
    {{-- WLB Score Header with Interactive Elements --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white rounded-3xl p-8 shadow-2xl">
        {{-- Background Animation --}}
        <div class="absolute inset-0 bg-gradient-to-r from-blue-400/20 via-purple-500/20 to-pink-500/20 animate-pulse"></div>
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full animate-bounce opacity-50"></div>
        <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-white/10 rounded-full animate-ping opacity-30"></div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="h-12 w-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold mb-1">Work-Life Balance Score</h1>
                            <p class="text-blue-100">Real-time analytics untuk kesejahteraan Anda</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <div class="text-center">
                            <div class="text-6xl font-bold mb-2 bg-gradient-to-b from-white to-blue-100 bg-clip-text text-transparent">
                                {{ $wlbScore }}
                            </div>
                            <div class="text-sm text-blue-100">out of 100</div>
                        </div>
                        
                        <div class="flex-1 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-blue-100">Status</span>
                                <div class="px-4 py-2 rounded-full text-sm font-bold backdrop-blur-sm
                                    @if($wlbStatus['color'] == 'green') bg-green-500/30 text-green-100
                                    @elseif($wlbStatus['color'] == 'yellow') bg-yellow-500/30 text-yellow-100
                                    @else bg-red-500/30 text-red-100 @endif">
                                    {{ $wlbStatus['label'] }}
                                </div>
                            </div>
                            
                            <button @click="showWlbDetails = !showWlbDetails" 
                                    class="w-full text-left text-blue-100 hover:text-white transition-colors text-sm underline">
                                <span x-text="showWlbDetails ? 'Sembunyikan Detail' : 'Lihat Detail WLB'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="text-8xl opacity-20 animate-pulse">📊</div>
            </div>

            {{-- WLB Details Expandable --}}
            <div x-show="showWlbDetails" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                @if(count($recommendations) > 0)
                    @foreach($recommendations as $recommendation)
                        <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    @if($recommendation['type'] == 'warning') ⚠️
                                    @elseif($recommendation['type'] == 'danger') 🚨
                                    @else 💡 @endif
                                </div>
                                <div class="text-sm text-blue-100">{{ $recommendation['message'] }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-3 bg-white/10 rounded-xl p-4 backdrop-blur-sm text-center">
                        <p class="text-blue-100">🎉 Excellent! You're maintaining great work-life balance!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick Actions & Today Status with Interactive Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Today's Status --}}
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Today's Status</h3>
                <div class="h-12 w-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            
            @if($todayAttendance)
                <div class="space-y-4">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 font-medium">Check In:</span>
                            <span class="text-xl font-bold text-blue-600">
                                {{ $todayAttendance->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') : '-' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 font-medium">Check Out:</span>
                            <span class="text-xl font-bold text-purple-600">
                                {{ $todayAttendance->check_out ? \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i') : 'Belum check out' }}
                            </span>
                        </div>
                    </div>
                    
                    @if($todayAttendance->check_in && $todayAttendance->check_out)
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 font-medium">Work Hours:</span>
                                <span class="text-xl font-bold text-green-600">
                                    {{ \Carbon\Carbon::parse($todayAttendance->check_in)->diffInHours(\Carbon\Carbon::parse($todayAttendance->check_out)) }} jam
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-6xl mb-4">😴</div>
                    <p class="text-gray-500 text-lg italic">Belum ada catatan kehadiran hari ini</p>
                </div>
            @endif
        </div>

        {{-- Quick Actions with Enhanced Interactivity --}}
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Quick Actions</h3>
                <div class="h-12 w-12 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>

            <div class="space-y-4">
                @if(!$todayAttendance || !$todayAttendance->check_in)
                    <button @click="attendanceLoading = true; quickCheckIn()" 
                            :disabled="attendanceLoading"
                            class="w-full bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center justify-center space-x-3">
                        <template x-if="!attendanceLoading">
                            <div class="flex items-center space-x-3">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>🕐 Quick Check In</span>
                            </div>
                        </template>
                        <template x-if="attendanceLoading">
                            <div class="flex items-center space-x-3">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Processing...</span>
                            </div>
                        </template>
                    </button>
                @elseif(!$todayAttendance->check_out)
                    <button @click="attendanceLoading = true; quickCheckOut()" 
                            :disabled="attendanceLoading"
                            class="w-full bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center justify-center space-x-3">
                        <template x-if="!attendanceLoading">
                            <div class="flex items-center space-x-3">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>🕐 Quick Check Out</span>
                            </div>
                        </template>
                        <template x-if="attendanceLoading">
                            <div class="flex items-center space-x-3">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Processing...</span>
                            </div>
                        </template>
                    </button>
                @endif
                
                <a href="{{ route('leave.create') }}" 
                   class="block w-full bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg text-center">
                    <div class="flex items-center justify-center space-x-3">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>📅 Ajukan Cuti</span>
                    </div>
                </a>
                
                <a href="{{ route('overtime.create') }}" 
                   class="block w-full bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-purple-600 hover:to-indigo-600 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg text-center">
                    <div class="flex items-center justify-center space-x-3">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>⏰ Ajukan Lembur</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- Monthly Statistics with Modern Cards --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all duration-300">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-bold text-gray-900">Monthly Statistics</h3>
            <div class="text-sm text-gray-500 bg-gray-100 rounded-full px-4 py-2">
                {{ now()->format('F Y') }}
            </div>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-3xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                <div class="h-16 w-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $monthlyStats['total_work_days'] }}</div>
                <div class="text-sm text-gray-600 font-medium">Work Days</div>
            </div>
            
            <div class="text-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-3xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                <div class="h-16 w-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-purple-600 mb-2">{{ $monthlyStats['total_overtime_hours'] }}</div>
                <div class="text-sm text-gray-600 font-medium">Overtime Hours</div>
            </div>
            
            <div class="text-center p-6 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-3xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                <div class="h-16 w-16 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-yellow-600 mb-2">{{ $monthlyStats['pending_leaves'] }}</div>
                <div class="text-sm text-gray-600 font-medium">Pending Leaves</div>
            </div>
            
            <div class="text-center p-6 bg-gradient-to-br from-red-50 to-red-100 rounded-3xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                <div class="h-16 w-16 bg-gradient-to-r from-red-500 to-red-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-red-600 mb-2">{{ $monthlyStats['pending_overtimes'] }}</div>
                <div class="text-sm text-gray-600 font-medium">Pending OT</div>
            </div>
        </div>
    </div>

    {{-- Leave Balance & Work Pattern --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Leave Balance --}}
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Leave Balance</h3>
                <div class="h-12 w-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="text-center">
                    <div class="relative w-32 h-32 mx-auto">
                        <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                  fill="none" stroke="#E5E7EB" stroke-width="2"/>
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                  fill="none" stroke="#10B981" stroke-width="2"
                                  stroke-dasharray="{{ $leaveBalance['annual_quota'] > 0 ? (($leaveBalance['remaining'] / $leaveBalance['annual_quota']) * 100) : 0 }}, 100"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $leaveBalance['remaining'] }}</div>
                                <div class="text-xs text-gray-500">remaining</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="bg-blue-50 rounded-2xl p-4">
                        <div class="text-lg font-bold text-blue-600">{{ $leaveBalance['annual_quota'] }}</div>
                        <div class="text-xs text-gray-600">Total Quota</div>
                    </div>
                    <div class="bg-red-50 rounded-2xl p-4">
                        <div class="text-lg font-bold text-red-600">{{ $leaveBalance['used'] }}</div>
                        <div class="text-xs text-gray-600">Used</div>
                    </div>
                    <div class="bg-green-50 rounded-2xl p-4">
                        <div class="text-lg font-bold text-green-600">{{ $leaveBalance['remaining'] }}</div>
                        <div class="text-xs text-gray-600">Available</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Work Pattern Chart --}}
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Work Pattern</h3>
                <div class="text-sm text-gray-500 bg-gray-100 rounded-full px-4 py-2">
                    Last 7 Days
                </div>
            </div>
            
            <div class="grid grid-cols-7 gap-3">
                @foreach($workPattern as $day)
                    <div class="text-center">
                        <div class="text-xs text-gray-500 mb-3 font-medium">{{ \Carbon\Carbon::parse($day['date'])->format('D') }}</div>
                        <div class="h-24 flex items-end justify-center">
                            <div class="w-8 rounded-full transition-all duration-300 hover:scale-110 @if($day['status'] == 'present') bg-gradient-to-t from-green-400 to-green-500 @else bg-gradient-to-t from-gray-200 to-gray-300 @endif" 
                                 style="height: {{ $day['work_hours'] > 0 ? min($day['work_hours'] / 12 * 100, 100) : 10 }}%">
                            </div>
                        </div>
                        <div class="text-xs text-gray-600 mt-2 font-medium">{{ $day['work_hours'] }}h</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Activities with Enhanced Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Attendance --}}
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Recent Attendance</h3>
                <a href="{{ route('attendance.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
            </div>
            
            @if(count($recentActivities['attendances']) > 0)
                <div class="space-y-4">
                    @foreach($recentActivities['attendances'] as $attendance)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl hover:shadow-md transition-all duration-300">
                            <div>
                                <div class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($attendance->date)->format('M d') }}</div>
                                <div class="text-sm text-gray-500 capitalize">{{ $attendance->status }}</div>
                            </div>
                            <div class="text-right">
                                @if($attendance->check_in && $attendance->check_out)
                                    <div class="text-lg font-bold text-blue-600">
                                        {{ \Carbon\Carbon::parse($attendance->check_in)->diffInHours(\Carbon\Carbon::parse($attendance->check_out)) }}h
                                    </div>
                                @else
                                    <div class="text-sm text-gray-400">-</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-4xl mb-4">📅</div>
                    <p class="text-gray-500 text-sm italic">No recent attendance</p>
                </div>
            @endif
        </div>

        {{-- Recent Leaves --}}
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Recent Leaves</h3>
                <a href="{{ route('leave.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
            </div>
            
            @if(count($recentActivities['leaves']) > 0)
                <div class="space-y-4">
                    @foreach($recentActivities['leaves'] as $leave)
                        <div class="p-4 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-2xl hover:shadow-md transition-all duration-300">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-bold text-gray-900 capitalize">{{ $leave->type }}</div>
                                <span class="px-3 py-1 text-xs rounded-full font-medium
                                    @if($leave->status == 'pending') bg-yellow-200 text-yellow-800
                                    @elseif($leave->status == 'approved') bg-green-200 text-green-800
                                    @else bg-red-200 text-red-800 @endif">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} - 
                                {{ \Carbon\Carbon::parse($leave->end_date)->format('M d') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-4xl mb-4">🏖️</div>
                    <p class="text-gray-500 text-sm italic">No recent leaves</p>
                </div>
            @endif
        </div>

        {{-- Recent Overtimes --}}
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Recent Overtime</h3>
                <a href="{{ route('overtime.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
            </div>
            
            @if(count($recentActivities['overtimes']) > 0)
                <div class="space-y-4">
                    @foreach($recentActivities['overtimes'] as $overtime)
                        <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl hover:shadow-md transition-all duration-300">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-bold text-gray-900">{{ $overtime->hours }} hours</div>
                                <span class="px-3 py-1 text-xs rounded-full font-medium
                                    @if($overtime->status == 'pending') bg-yellow-200 text-yellow-800
                                    @elseif($overtime->status == 'approved') bg-green-200 text-green-800
                                    @else bg-red-200 text-red-800 @endif">
                                    {{ ucfirst($overtime->status) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($overtime->date)->format('M d, Y') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-4xl mb-4">⏰</div>
                    <p class="text-gray-500 text-sm italic">No recent overtime</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function quickCheckIn() {
    fetch('{{ route("attendance.quick-check-in") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification
            showNotification('✅ Check-in berhasil!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('❌ ' + (data.message || 'Check-in gagal'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Terjadi kesalahan saat check-in', 'error');
    })
    .finally(() => {
        // Reset loading state in Alpine.js
        Alpine.store('attendance', { loading: false });
    });
}

function quickCheckOut() {
    fetch('{{ route("attendance.quick-check-out") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ Check-out berhasil!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('❌ ' + (data.message || 'Check-out gagal'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Terjadi kesalahan saat check-out', 'error');
    })
    .finally(() => {
        // Reset loading state
        Alpine.store('attendance', { loading: false });
    });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white font-medium transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    // Add to DOM
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
@endsection

    {{-- Leave Balance & Recommendations --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Leave Balance --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave Balance</h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Annual Quota:</span>
                    <span class="font-medium">{{ $leaveBalance['annual_quota'] }} days</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Used:</span>
                    <span class="font-medium text-red-600">{{ $leaveBalance['used'] }} days</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Remaining:</span>
                    <span class="font-medium text-green-600">{{ $leaveBalance['remaining'] }} days</span>
                </div>
                
                {{-- Progress Bar --}}
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $leaveBalance['annual_quota'] > 0 ? ($leaveBalance['used'] / $leaveBalance['annual_quota']) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        {{-- WLB Recommendations --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recommendations</h3>
            
            @if(count($recommendations) > 0)
                <div class="space-y-3">
                    @foreach($recommendations as $recommendation)
                        <div class="flex items-start space-x-3 p-3 rounded-lg
                            @if($recommendation['type'] == 'warning') bg-yellow-50 
                            @elseif($recommendation['type'] == 'danger') bg-red-50
                            @else bg-blue-50 @endif">
                            <div class="flex-shrink-0 mt-0.5">
                                @if($recommendation['type'] == 'warning') ⚠️
                                @elseif($recommendation['type'] == 'danger') 🚨
                                @else 💡 @endif
                            </div>
                            <div class="text-sm text-gray-700">{{ $recommendation['message'] }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 italic">You're doing great! Keep up the good work-life balance.</p>
            @endif
        </div>
    </div>

    {{-- Work Pattern Chart --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Work Pattern (Last 7 Days)</h3>
        
        <div class="grid grid-cols-7 gap-2">
            @foreach($workPattern as $day)
                <div class="text-center">
                    <div class="text-xs text-gray-500 mb-2">{{ \Carbon\Carbon::parse($day['date'])->format('D') }}</div>
                    <div class="h-20 flex items-end justify-center">
                        <div class="w-8 rounded-t @if($day['status'] == 'present') bg-green-500 @else bg-gray-200 @endif" 
                             style="height: {{ $day['work_hours'] > 0 ? min($day['work_hours'] / 12 * 100, 100) : 5 }}%">
                        </div>
                    </div>
                    <div class="text-xs text-gray-600 mt-1">{{ $day['work_hours'] }}h</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Attendance --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Attendance</h3>
            
            @if(count($recentActivities['attendances']) > 0)
                <div class="space-y-3">
                    @foreach($recentActivities['attendances'] as $attendance)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                            <div>
                                <div class="font-medium">{{ \Carbon\Carbon::parse($attendance->date)->format('M d') }}</div>
                                <div class="text-sm text-gray-500">{{ $attendance->status }}</div>
                            </div>
                            <div class="text-right">
                                @if($attendance->check_in && $attendance->check_out)
                                    <div class="text-sm font-medium">
                                        {{ \Carbon\Carbon::parse($attendance->check_in)->diffInHours(\Carbon\Carbon::parse($attendance->check_out)) }}h
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 italic text-sm">No recent attendance</p>
            @endif
        </div>

        {{-- Recent Leaves --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Leaves</h3>
            
            @if(count($recentActivities['leaves']) > 0)
                <div class="space-y-3">
                    @foreach($recentActivities['leaves'] as $leave)
                        <div class="py-2 border-b border-gray-100 last:border-0">
                            <div class="flex items-center justify-between">
                                <div class="font-medium">{{ ucfirst($leave->type) }}</div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($leave->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($leave->status == 'approved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} - 
                                {{ \Carbon\Carbon::parse($leave->end_date)->format('M d') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 italic text-sm">No recent leaves</p>
            @endif
        </div>

        {{-- Recent Overtimes --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Overtime</h3>
            
            @if(count($recentActivities['overtimes']) > 0)
                <div class="space-y-3">
                    @foreach($recentActivities['overtimes'] as $overtime)
                        <div class="py-2 border-b border-gray-100 last:border-0">
                            <div class="flex items-center justify-between">
                                <div class="font-medium">{{ $overtime->hours }}h</div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($overtime->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($overtime->status == 'approved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($overtime->status) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($overtime->date)->format('M d, Y') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 italic text-sm">No recent overtime</p>
            @endif
        </div>
    </div>
</div>

<script>
function quickCheckIn() {
    fetch('{{ route("attendance.quick-check-in") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to check in');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while checking in');
    });
}

function quickCheckOut() {
    fetch('{{ route("attendance.quick-check-out") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to check out');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while checking out');
    });
}
</script>
@endsection
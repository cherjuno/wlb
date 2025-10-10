@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">
    {{-- Manager Overview Header --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-emerald-600 via-blue-600 to-indigo-600 text-white rounded-3xl p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/0 to-white/10"></div>
        <div class="absolute -top-10 -right-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Team Analytics — Manager</h1>
                <p class="mt-2 text-emerald-100">Pantau keseimbangan kerja dan produktivitas tim</p>
            </div>
            <div class="text-7xl opacity-20 select-none">👥</div>
        </div>
    </div>

    {{-- Team Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow border border-white/60 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Team Size</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $teamSize }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow border border-white/60 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Today's Attendance</p>
                    <p class="text-3xl font-bold text-green-600">{{ $teamAttendanceRate }}%</p>
                    <p class="text-sm text-gray-500">{{ $teamCheckedIn }}/{{ $teamSize }} present</p>
                </div>
                <div class="p-3 bg-green-50 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow border border-white/60 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Average WLB Score</p>
                    <p class="text-3xl font-bold 
                        @if($teamAverageWlbScore >= 70) text-green-600
                        @elseif($teamAverageWlbScore >= 50) text-yellow-600
                        @else text-red-600 @endif">{{ $teamAverageWlbScore }}</p>
                    <p class="text-sm text-gray-500">out of 100</p>
                </div>
                <div class="p-3 
                    @if($teamAverageWlbScore >= 70) bg-green-50
                    @elseif($teamAverageWlbScore >= 50) bg-yellow-50
                    @else bg-red-50 @endif rounded-lg">
                    <svg class="w-6 h-6 
                        @if($teamAverageWlbScore >= 70) text-green-600
                        @elseif($teamAverageWlbScore >= 50) text-yellow-600
                        @else text-red-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow border border-white/60 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Approvals</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $pendingLeaves + $pendingOvertimes }}</p>
                    <p class="text-sm text-gray-500">{{ $pendingLeaves }} leaves, {{ $pendingOvertimes }} OT</p>
                </div>
                <div class="p-3 bg-orange-50 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Red Zone Employees Alert --}}
    @if(count($redZoneMembers) > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl p-6">
        <div class="flex items-center space-x-3 mb-4">
            <div class="text-2xl">🚨</div>
            <div>
                <h3 class="text-lg font-semibold text-red-800">Team Members Need Attention</h3>
                <p class="text-red-600">The following team members have low WLB scores and may be at risk of burnout:</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($redZoneMembers as $member)
                <div class="bg-white rounded-lg p-4 border border-red-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900">{{ $member['user']->name }}</h4>
                        <span class="text-lg font-bold text-red-600">{{ $member['score'] }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ $member['user']->position->name ?? 'N/A' }}</p>
                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                        {{ $member['status']['label'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Team Performance Chart & Pending Approvals --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Team Performance Trend --}}
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Team Attendance Trend (7 Days)</h3>
            
            <div class="space-y-4">
                @foreach($performanceData as $data)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($data['date'])->format('M d') }}</span>
                        <div class="flex items-center space-x-3 flex-1 ml-4">
                            <div class="flex-1 bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $data['attendance_rate'] }}%"></div>
                            </div>
                            <span class="text-sm font-medium w-12 text-right">{{ $data['attendance_rate'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            
            <div class="space-y-3">
                <a href="{{ route('leave.pending') }}" class="flex items-center justify-between p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-yellow-500 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Review Leave Requests</p>
                            <p class="text-sm text-gray-600">{{ $pendingLeaves }} pending requests</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('overtime.pending') }}" class="flex items-center justify-between p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-purple-500 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Review Overtime Requests</p>
                            <p class="text-sm text-gray-600">{{ $pendingOvertimes }} pending requests</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('attendance.index') }}" class="flex items-center justify-between p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-blue-500 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">View Team Attendance</p>
                            <p class="text-sm text-gray-600">Monitor daily attendance</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('users.index') }}" class="flex items-center justify-between p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-green-500 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Manage Team Members</p>
                            <p class="text-sm text-gray-600">View and edit team details</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Small Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Leave Types (Team)</h3>
            <div class="h-56"><canvas id="mgrLeaveType"></canvas></div>
        </div>
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Overtime Types (Team)</h3>
            <div class="h-56"><canvas id="mgrOtType"></canvas></div>
        </div>
    </div>

    {{-- Pending Approvals Details --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Pending Leave Requests --}}
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Pending Leave Requests</h3>
                <a href="{{ route('leave.pending') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
            </div>
            
            @if(count($pendingApprovals['leaves']) > 0)
                <div class="space-y-4">
                    @foreach($pendingApprovals['leaves'] as $leave)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $leave->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ ucfirst($leave->type) }} - {{ $leave->days_requested }} day(s)</p>
                                <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} - 
                                    {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                </p>
                            </div>
                            <div class="flex space-x-2">
                                <form action="{{ route('leave.approve', $leave) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded transition-colors">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('leave.reject', $leave) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 italic">No pending leave requests</p>
            @endif
        </div>

        {{-- Pending Overtime Requests --}}
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Pending Overtime Requests</h3>
                <a href="{{ route('overtime.pending') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
            </div>
            
            @if(count($pendingApprovals['overtimes']) > 0)
                <div class="space-y-4">
                    @foreach($pendingApprovals['overtimes'] as $overtime)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $overtime->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $overtime->hours }} hours - {{ ucfirst($overtime->type) }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($overtime->date)->format('M d, Y') }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <form action="{{ route('overtime.approve', $overtime) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded transition-colors">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('overtime.reject', $overtime) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 italic">No pending overtime requests</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const leaveTypeStats = @json($leaveTypeStats ?? []);
const otTypeStats = @json($overtimeTypeStats ?? []);

function makeDonut(el, labels, data, colors){
    const ctx = document.getElementById(el);
    if(!ctx) return;
    new Chart(ctx.getContext('2d'), {
        type: 'doughnut',
        data: { labels, datasets: [{ data, backgroundColor: colors }] },
        options: { maintainAspectRatio:false, cutout:'60%', animation:{duration:900}, plugins:{legend:{position:'bottom'}} }
    });
}

const leaveLabels = Object.keys(leaveTypeStats);
const leaveData = Object.values(leaveTypeStats);
makeDonut('mgrLeaveType', leaveLabels, leaveData, ['#6366F1','#F59E0B','#10B981','#EF4444']);

const otLabels = Object.keys(otTypeStats);
const otData = Object.values(otTypeStats);
makeDonut('mgrOtType', otLabels, otData, ['#8B5CF6','#06B6D4','#22C55E']);
</script>
@endpush
@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">
    {{-- Admin Overview Header --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white rounded-3xl p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/0 to-white/10"></div>
        <div class="absolute -top-8 -right-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">WLB Analytics — Admin</h1>
                <p class="mt-2 text-indigo-100">Kesehatan sistem dan keseimbangan kerja skala organisasi</p>
            </div>
            <div class="text-7xl opacity-20 select-none">⚙️</div>
        </div>
    </div>

    {{-- Global Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow border border-white/60 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Employees</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalEmployees }}</p>
                    <p class="text-sm text-gray-500">Active users</p>
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
                    <p class="text-sm font-medium text-gray-600">Departments</p>
                    <p class="text-3xl font-bold text-green-600">{{ $totalDepartments }}</p>
                    <p class="text-sm text-gray-500">Active departments</p>
                </div>
                <div class="p-3 bg-green-50 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow border border-white/60 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Today's Attendance</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $attendanceRate }}%</p>
                    <p class="text-sm text-gray-500">{{ $checkedInToday }}/{{ $totalEmployees }} present</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow border border-white/60 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Org. WLB Score</p>
                    <p class="text-3xl font-bold 
                        @if($averageWlbScore >= 70) text-green-600
                        @elseif($averageWlbScore >= 50) text-yellow-600
                        @else text-red-600 @endif">{{ $averageWlbScore }}</p>
                    <p class="text-sm text-gray-500">Company average</p>
                </div>
                <div class="p-3 
                    @if($averageWlbScore >= 70) bg-green-50
                    @elseif($averageWlbScore >= 50) bg-yellow-50
                    @else bg-red-50 @endif rounded-lg">
                    <svg class="w-6 h-6 
                        @if($averageWlbScore >= 70) text-green-600
                        @elseif($averageWlbScore >= 50) text-yellow-600
                        @else text-red-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Section (Chart.js) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Rerata WLB per Departemen</h3>
            <div class="h-56">
                <canvas id="deptChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Distribusi Status Pengajuan</h3>
            <div class="h-56">
                <canvas id="approvalChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Top Zona Merah</h3>
            <ul class="space-y-3 max-h-64 overflow-auto">
                @foreach(collect($redZoneEmployees)->sortBy('score')->take(6) as $rz)
                    <li class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-red-100 text-red-700 flex items-center justify-center font-bold">{{ strtoupper(substr($rz['user']->name,0,1)) }}</div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $rz['user']->name }}</div>
                                <div class="text-xs text-gray-500">{{ $rz['user']->department->name ?? '—' }}</div>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-semibold">{{ $rz['score'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Pending Approvals Alert --}}
    @if($pendingLeaves > 0 || $pendingOvertimes > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <div class="flex items-center space-x-3">
            <div class="text-2xl">⏳</div>
            <div>
                <h3 class="text-lg font-semibold text-yellow-800">Pending Approvals System-Wide</h3>
                <p class="text-yellow-700">
                    There are <strong>{{ $pendingLeaves }} leave requests</strong> and 
                    <strong>{{ $pendingOvertimes }} overtime requests</strong> awaiting approval across all departments.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Red Zone Employees Critical Alert --}}
    @if(count($redZoneEmployees) > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl p-6">
        <div class="flex items-center space-x-3 mb-4">
            <div class="text-2xl">🚨</div>
            <div>
                <h3 class="text-lg font-semibold text-red-800">Critical: Employees at Risk of Burnout</h3>
                <p class="text-red-600">{{ count($redZoneEmployees) }} employees have critically low WLB scores and require immediate attention.</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($redZoneEmployees as $employee)
                <div class="bg-white rounded-lg p-4 border border-red-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900">{{ $employee['user']->name }}</h4>
                        <span class="text-lg font-bold text-red-600">{{ $employee['score'] }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">{{ $employee['user']->department->name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600 mb-2">{{ $employee['user']->position->name ?? 'N/A' }}</p>
                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                        {{ $employee['status']['label'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Department Performance & System Analytics --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Department Performance --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Department Performance</h3>
            
            @if(count($departmentStats) > 0)
                <div class="space-y-4">
                    @foreach($departmentStats as $dept)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $dept['name'] }}</h4>
                                <p class="text-sm text-gray-600">{{ $dept['employee_count'] }} employees</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold 
                                    @if($dept['avg_wlb_score'] >= 70) text-green-600
                                    @elseif($dept['avg_wlb_score'] >= 50) text-yellow-600
                                    @else text-red-600 @endif">
                                    {{ $dept['avg_wlb_score'] }}
                                </div>
                                <div class="text-sm text-gray-500">WLB Score</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 italic">No department data available</p>
            @endif
        </div>

        {{-- System Administration --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">System Administration</h3>
            
            <div class="space-y-3">
                <a href="{{ route('users.index') }}" class="flex items-center justify-between p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-blue-500 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Manage Users</p>
                            <p class="text-sm text-gray-600">{{ $totalEmployees }} active users</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('attendance.index') }}" class="flex items-center justify-between p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-green-500 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Attendance Overview</p>
                            <p class="text-sm text-gray-600">{{ $attendanceRate }}% today</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('leave.index') }}" class="flex items-center justify-between p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-yellow-500 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Leave Management</p>
                            <p class="text-sm text-gray-600">{{ $pendingLeaves }} pending requests</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('overtime.index') }}" class="flex items-center justify-between p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-purple-500 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Overtime Management</p>
                            <p class="text-sm text-gray-600">{{ $pendingOvertimes }} pending requests</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Leaves --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Leave Activities</h3>
                <a href="{{ route('leave.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
            </div>
            
            @if(count($recentLeaves) > 0)
                <div class="space-y-4">
                    @foreach($recentLeaves as $leave)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $leave->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ ucfirst($leave->type) }} - {{ $leave->days_requested }} day(s)</p>
                                <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} - 
                                    {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($leave->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($leave->status == 'approved') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 italic">No recent leave activities</p>
            @endif
        </div>

        {{-- Recent Overtimes --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Overtime Activities</h3>
                <a href="{{ route('overtime.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
            </div>
            
            @if(count($recentOvertimes) > 0)
                <div class="space-y-4">
                    @foreach($recentOvertimes as $overtime)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $overtime->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $overtime->hours }} hours - {{ ucfirst($overtime->type) }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($overtime->date)->format('M d, Y') }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($overtime->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($overtime->status == 'approved') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($overtime->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 italic">No recent overtime activities</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
const deptLabels = @json(collect($departmentStats)->pluck('name'));
const deptScores = @json(collect($departmentStats)->pluck('avg_wlb_score'));
const approvalPending = {{ $pendingLeaves + $pendingOvertimes }};
const approvalApproved = {{ collect($recentLeaves)->where('status','approved')->count() + collect($recentOvertimes)->where('status','approved')->count() }};
const approvalRejected = {{ collect($recentLeaves)->where('status','rejected')->count() + collect($recentOvertimes)->where('status','rejected')->count() }};

if (document.getElementById('deptChart')) {
    const ctx = document.getElementById('deptChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: { labels: deptLabels, datasets: [{ label: 'Avg WLB', data: deptScores, backgroundColor: '#A78BFA', borderRadius: 8, hoverBackgroundColor: '#8B5CF6' }] },
        options: {
            maintainAspectRatio: false,
            animation: { duration: 900, easing: 'easeOutQuart' },
            scales: { y: { beginAtZero: true, max: 100, grid: { drawBorder: false } }, x: { grid: { display: false } } },
            plugins: { legend: { display:false }, tooltip: { intersect: false } }
        }
    });
}

if (document.getElementById('approvalChart')) {
    const ctx2 = document.getElementById('approvalChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: { labels:['Pending','Approved','Rejected'], datasets:[{ data:[approvalPending, approvalApproved, approvalRejected], backgroundColor:['#F59E0B','#10B981','#EF4444'] }] },
        options: { maintainAspectRatio: false, animation: { duration: 900 }, cutout: '60%', plugins: { legend: { position:'bottom' } } }
    });
}
</script>
@endpush

@endsection
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Overtime;
use App\Models\WlbSetting;
use App\Helpers\WlbHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('manager')) {
            return $this->managerDashboard();
        } else {
            return $this->employeeDashboard();
        }
    }

    private function adminDashboard()
    {
        // Global statistics
        $totalEmployees = User::where('is_active', true)->count();
        $totalDepartments = Department::where('is_active', true)->count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $pendingOvertimes = Overtime::where('status', 'pending')->count();
        
        // Today's attendance
        $today = now()->format('Y-m-d');
        $checkedInToday = Attendance::whereDate('date', $today)
            ->whereNotNull('check_in')
            ->count();
        
        $attendanceRate = $totalEmployees > 0 ? round(($checkedInToday / $totalEmployees) * 100, 1) : 0;

        // WLB metrics untuk semua karyawan
        $allUsers = User::where('is_active', true)->get();
        $wlbScores = [];
        $redZoneEmployees = [];
        
        foreach ($allUsers as $user) {
            $score = WlbHelper::calculateWlbScore($user->id);
            $wlbScores[] = $score;
            
            if ($score < 50) { // Red zone threshold
                $redZoneEmployees[] = [
                    'user' => $user,
                    'score' => $score,
                    'status' => WlbHelper::getWlbStatus($score)
                ];
            }
        }
        
        $averageWlbScore = count($wlbScores) > 0 ? round(array_sum($wlbScores) / count($wlbScores), 1) : 0;
        
        // Department performance
        $departmentStats = Department::where('is_active', true)->get()->map(function ($dept) {
            $employeeCount = $dept->users()->where('is_active', true)->count();
            $deptUsers = $dept->users()->where('is_active', true)->get();
            
            $totalScore = 0;
            foreach ($deptUsers as $user) {
                $totalScore += WlbHelper::calculateWlbScore($user->id);
            }
            
            return [
                'name' => $dept->name,
                'employee_count' => $employeeCount,
                'avg_wlb_score' => $employeeCount > 0 ? round($totalScore / $employeeCount, 1) : 0
            ];
        });

        // Recent activities
        $recentLeaves = Leave::with(['user', 'approver'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $recentOvertimes = Overtime::with(['user', 'approver'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalEmployees', 'totalDepartments', 'pendingLeaves', 'pendingOvertimes',
            'checkedInToday', 'attendanceRate', 'averageWlbScore', 'redZoneEmployees',
            'departmentStats', 'recentLeaves', 'recentOvertimes'
        ));
    }

    private function managerDashboard()
    {
        $user = Auth::user();
        $subordinateIds = $user->subordinates()->pluck('id');
        
        // Team statistics
        $teamSize = $subordinateIds->count();
        $pendingLeaves = Leave::whereIn('user_id', $subordinateIds)
            ->where('status', 'pending')
            ->count();
        $pendingOvertimes = Overtime::whereIn('user_id', $subordinateIds)
            ->where('status', 'pending')
            ->count();
        
        // Today's team attendance
        $today = now()->format('Y-m-d');
        $teamCheckedIn = Attendance::whereIn('user_id', $subordinateIds)
            ->whereDate('date', $today)
            ->whereNotNull('check_in')
            ->count();
        
        $teamAttendanceRate = $teamSize > 0 ? round(($teamCheckedIn / $teamSize) * 100, 1) : 0;
        
        // Team WLB metrics
        $teamUsers = User::whereIn('id', $subordinateIds)->where('is_active', true)->get();
        $wlbScores = [];
        $redZoneMembers = [];
        
        foreach ($teamUsers as $member) {
            $score = WlbHelper::calculateWlbScore($member->id);
            $wlbScores[] = $score;
            
            if ($score < 50) {
                $redZoneMembers[] = [
                    'user' => $member,
                    'score' => $score,
                    'status' => WlbHelper::getWlbStatus($score)
                ];
            }
        }
        
        $teamAverageWlbScore = count($wlbScores) > 0 ? round(array_sum($wlbScores) / count($wlbScores), 1) : 0;
        
        // Pending approvals
        $pendingApprovals = [
            'leaves' => Leave::whereIn('user_id', $subordinateIds)
                ->where('status', 'pending')
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'overtimes' => Overtime::whereIn('user_id', $subordinateIds)
                ->where('status', 'pending')
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        ];
        
        // Team performance trends (last 7 days)
        $performanceData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $attendanceCount = Attendance::whereIn('user_id', $subordinateIds)
                ->whereDate('date', $date)
                ->whereNotNull('check_in')
                ->count();
            
            $performanceData[] = [
                'date' => $date,
                'attendance_rate' => $teamSize > 0 ? round(($attendanceCount / $teamSize) * 100, 1) : 0
            ];
        }

        // Aggregates for charts (by type)
        $leaveTypeStats = Leave::whereIn('user_id', $subordinateIds)
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        $overtimeTypeStats = Overtime::whereIn('user_id', $subordinateIds)
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        return view('dashboard.manager', compact(
            'teamSize', 'pendingLeaves', 'pendingOvertimes', 'teamCheckedIn',
            'teamAttendanceRate', 'teamAverageWlbScore', 'redZoneMembers',
            'pendingApprovals', 'performanceData', 'leaveTypeStats', 'overtimeTypeStats'
        ));
    }

    private function employeeDashboard()
    {
        $user = Auth::user();
        
        // Personal WLB metrics
        $wlbScore = WlbHelper::calculateWlbScore($user->id);
        $wlbStatus = WlbHelper::getWlbStatus($wlbScore);
        $recommendations = WlbHelper::generateRecommendations($user->id);
        
        // Today's attendance status
        $today = now()->format('Y-m-d');
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
        
        // This month statistics
        $currentMonth = now()->format('Y-m');
        $monthlyStats = [
            'total_work_days' => Attendance::where('user_id', $user->id)
                ->whereDate('date', 'like', $currentMonth . '%')
                ->whereNotNull('check_in')
                ->count(),
            'total_overtime_hours' => Overtime::where('user_id', $user->id)
                ->whereDate('date', 'like', $currentMonth . '%')
                ->where('status', 'approved')
                ->sum('hours'),
            'pending_leaves' => Leave::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'pending_overtimes' => Overtime::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
        ];
        
        // Leave balance
        $leaveBalance = [
            'annual_quota' => $user->annual_leave_quota ?? 12,
            'remaining' => $user->remaining_leave ?? 12,
            'used' => ($user->annual_leave_quota ?? 12) - ($user->remaining_leave ?? 12)
        ];
        
        // Recent activities
        $recentActivities = [
            'attendances' => Attendance::where('user_id', $user->id)
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get(),
            'leaves' => Leave::where('user_id', $user->id)
                ->with('approver')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get(),
            'overtimes' => Overtime::where('user_id', $user->id)
                ->with('approver')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get()
        ];
        
        // Work pattern analysis (last 7 days)
        $workPattern = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $date)
                ->first();
            
            $workHours = 0;
            if ($attendance && $attendance->check_in && $attendance->check_out) {
                $checkIn = \Carbon\Carbon::parse($attendance->check_in);
                $checkOut = \Carbon\Carbon::parse($attendance->check_out);
                $workHours = $checkOut->diffInHours($checkIn);
            }
            
            $workPattern[] = [
                'date' => $date,
                'work_hours' => $workHours,
                'status' => $attendance ? 'present' : 'absent'
            ];
        }

        return view('dashboard.employee_simple', compact(
            'wlbScore', 'wlbStatus', 'recommendations', 'todayAttendance',
            'monthlyStats', 'leaveBalance', 'recentActivities', 'workPattern'
        ));
    }

    // 7 Metrik Utama WLB
    private function getWlbMetrics()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return [
            // 1. Rata-rata jam kerja mingguan
            'avgWeeklyWorkHours' => $this->getAverageWeeklyWorkHours(),
            
            // 2. Rasio lembur terhadap jam kerja normal
            'overtimeRatio' => $this->getOvertimeRatio(),
            
            // 3. Tingkat pengambilan cuti tahunan
            'leaveUtilizationRate' => $this->getLeaveUtilizationRate(),
            
            // 4. Distribusi lembur (malam vs akhir pekan)
            'overtimeDistribution' => $this->getOvertimeDistribution(),
            
            // 5. Persentase karyawan dengan beban kerja tinggi
            'highWorkloadPercentage' => $this->getHighWorkloadPercentage(),
            
            // 6. Daftar "Zona Merah WLB"
            'redZoneCount' => $this->getRedZoneCount(),
            
            // 7. Korelasi lembur ekstrem dengan cuti sakit
            'overtimeSickLeaveCorrelation' => $this->getOvertimeSickLeaveCorrelation(),
        ];
    }

    private function getAverageWeeklyWorkHours()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $totalWorkHours = Attendance::whereBetween('date', [$startOfWeek, $endOfWeek])
            ->sum('work_hours');
        
        $activeEmployees = User::where('is_active', true)->count();
        
        return $activeEmployees > 0 ? round($totalWorkHours / $activeEmployees, 2) : 0;
    }

    private function getOvertimeRatio()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalWorkHours = Attendance::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('work_hours');
        
        $totalOvertimeHours = Overtime::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('status', 'approved')
            ->sum('hours');

        return $totalWorkHours > 0 ? round(($totalOvertimeHours / $totalWorkHours) * 100, 2) : 0;
    }

    private function getLeaveUtilizationRate()
    {
        $currentYear = Carbon::now()->year;
        
        $totalQuota = User::where('is_active', true)->sum('annual_leave_quota');
        
        $usedLeaves = Leave::whereYear('start_date', $currentYear)
            ->where('status', 'approved')
            ->where('type', 'annual')
            ->sum('days_requested');

        return $totalQuota > 0 ? round(($usedLeaves / $totalQuota) * 100, 2) : 0;
    }

    private function getOvertimeDistribution()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $weekdayOvertime = Overtime::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('status', 'approved')
            ->where('type', 'weekday')
            ->sum('hours');

        $weekendOvertime = Overtime::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('status', 'approved')
            ->where('type', 'weekend')
            ->sum('hours');

        $total = $weekdayOvertime + $weekendOvertime;

        return [
            'weekday' => $total > 0 ? round(($weekdayOvertime / $total) * 100, 2) : 0,
            'weekend' => $total > 0 ? round(($weekendOvertime / $total) * 100, 2) : 0,
        ];
    }

    private function getHighWorkloadPercentage()
    {
        $redZoneThreshold = WlbSetting::get('wlb_red_zone_work_hours_threshold', 50);
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $highWorkloadUsers = User::where('is_active', true)
            ->whereHas('attendances', function($query) use ($startOfWeek, $endOfWeek, $redZoneThreshold) {
                $query->whereBetween('date', [$startOfWeek, $endOfWeek])
                    ->groupBy('user_id')
                    ->havingRaw('SUM(work_hours) > ?', [$redZoneThreshold]);
            })
            ->count();

        $totalActiveUsers = User::where('is_active', true)->count();

        return $totalActiveUsers > 0 ? round(($highWorkloadUsers / $totalActiveUsers) * 100, 2) : 0;
    }

    private function getRedZoneCount()
    {
        return $this->getRedZoneEmployees()->count();
    }

    private function getRedZoneEmployees()
    {
        $overtimeThreshold = WlbSetting::get('wlb_red_zone_overtime_threshold', 8);
        $workHoursThreshold = WlbSetting::get('wlb_red_zone_work_hours_threshold', 50);
        
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return User::where('is_active', true)
            ->with(['department', 'position'])
            ->where(function($query) use ($overtimeThreshold, $workHoursThreshold, $startOfWeek, $endOfWeek) {
                // High overtime hours
                $query->whereHas('overtimes', function($q) use ($overtimeThreshold, $startOfWeek, $endOfWeek) {
                    $q->whereBetween('date', [$startOfWeek, $endOfWeek])
                        ->where('status', 'approved')
                        ->groupBy('user_id')
                        ->havingRaw('SUM(hours) > ?', [$overtimeThreshold]);
                })
                // OR high total work hours
                ->orWhereHas('attendances', function($q) use ($workHoursThreshold, $startOfWeek, $endOfWeek) {
                    $q->whereBetween('date', [$startOfWeek, $endOfWeek])
                        ->groupBy('user_id')
                        ->havingRaw('SUM(work_hours) > ?', [$workHoursThreshold]);
                });
            })
            ->get();
    }

    private function getOvertimeSickLeaveCorrelation()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $highOvertimeUsers = User::whereHas('overtimes', function($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'approved')
                ->groupBy('user_id')
                ->havingRaw('SUM(hours) > 20'); // More than 20 hours overtime
        })->pluck('id');

        $sickLeaveFromHighOvertimeUsers = Leave::whereIn('user_id', $highOvertimeUsers)
            ->where('type', 'sick')
            ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
            ->count();

        $totalSickLeaves = Leave::where('type', 'sick')
            ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
            ->count();

        return $totalSickLeaves > 0 ? round(($sickLeaveFromHighOvertimeUsers / $totalSickLeaves) * 100, 2) : 0;
    }

    private function getDepartmentOvertimeData()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return Department::with(['users.overtimes' => function($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'approved');
        }])
        ->get()
        ->map(function($dept) {
            return [
                'department' => $dept->name,
                'overtime_hours' => $dept->users->sum(function($user) {
                    return $user->overtimes->sum('hours');
                })
            ];
        });
    }

    private function getWeeklyWorkHoursData()
    {
        $weeks = [];
        for ($i = 6; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $totalHours = Attendance::whereBetween('date', [$startOfWeek, $endOfWeek])
                ->sum('work_hours');
            
            $weeks[] = [
                'week' => $startOfWeek->format('M d'),
                'hours' => $totalHours
            ];
        }
        
        return $weeks;
    }

    private function getLeaveUsageData()
    {
        $currentYear = Carbon::now()->year;
        
        return [
            'annual' => Leave::whereYear('start_date', $currentYear)
                ->where('status', 'approved')
                ->where('type', 'annual')
                ->sum('days_requested'),
            'sick' => Leave::whereYear('start_date', $currentYear)
                ->where('status', 'approved')
                ->where('type', 'sick')
                ->sum('days_requested'),
            'emergency' => Leave::whereYear('start_date', $currentYear)
                ->where('status', 'approved')
                ->where('type', 'emergency')
                ->sum('days_requested'),
        ];
    }

    private function getOvertimeDistributionData()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return [
            'weekday' => Overtime::whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'approved')
                ->where('type', 'weekday')
                ->sum('hours'),
            'weekend' => Overtime::whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'approved')
                ->where('type', 'weekend')
                ->sum('hours'),
            'holiday' => Overtime::whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'approved')
                ->where('type', 'holiday')
                ->sum('hours'),
        ];
    }

    private function getPersonalMetrics($userId)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return [
            'workHoursThisWeek' => Attendance::where('user_id', $userId)
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->sum('work_hours'),
            'overtimeHoursThisMonth' => Overtime::where('user_id', $userId)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'approved')
                ->sum('hours'),
            'pendingLeaves' => Leave::where('user_id', $userId)
                ->where('status', 'pending')
                ->count(),
            'pendingOvertimes' => Overtime::where('user_id', $userId)
                ->where('status', 'pending')
                ->count(),
        ];
    }

    private function getAttendanceThisWeek($userId)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return Attendance::where('user_id', $userId)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->orderBy('date')
            ->get();
    }

    private function getMonthlyStats($userId)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return [
            'totalWorkDays' => Attendance::where('user_id', $userId)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'present')
                ->count(),
            'totalWorkHours' => Attendance::where('user_id', $userId)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('work_hours'),
            'totalOvertimeHours' => Overtime::where('user_id', $userId)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'approved')
                ->sum('hours'),
            'lateCount' => Attendance::where('user_id', $userId)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'late')
                ->count(),
        ];
    }

    private function getTeamWlbMetrics($subordinateIds)
    {
        if ($subordinateIds->isEmpty()) {
            return [
                'avgWeeklyWorkHours' => 0,
                'avgOvertimeHours' => 0,
                'teamWlbScore' => 100,
            ];
        }

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $totalWorkHours = Attendance::whereIn('user_id', $subordinateIds)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->sum('work_hours');

        $totalOvertimeHours = Overtime::whereIn('user_id', $subordinateIds)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('status', 'approved')
            ->sum('hours');

        $teamSize = $subordinateIds->count();

        return [
            'avgWeeklyWorkHours' => $teamSize > 0 ? round($totalWorkHours / $teamSize, 2) : 0,
            'avgOvertimeHours' => $teamSize > 0 ? round($totalOvertimeHours / $teamSize, 2) : 0,
            'teamWlbScore' => $this->calculateTeamWlbScore($subordinateIds),
        ];
    }

    private function calculateTeamWlbScore($subordinateIds)
    {
        // WLB Score calculation based on multiple factors
        $score = 100;
        
        $redZoneThreshold = WlbSetting::get('wlb_red_zone_overtime_threshold', 8);
        $yellowZoneThreshold = WlbSetting::get('wlb_yellow_zone_overtime_threshold', 5);
        
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        foreach ($subordinateIds as $userId) {
            $overtimeHours = Overtime::where('user_id', $userId)
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->where('status', 'approved')
                ->sum('hours');

            if ($overtimeHours > $redZoneThreshold) {
                $score -= 20;
            } elseif ($overtimeHours > $yellowZoneThreshold) {
                $score -= 10;
            }
        }

        return max(0, $score);
    }

    private function getTeamPerformanceData($subordinateIds)
    {
        $weeks = [];
        for ($i = 3; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $totalHours = Attendance::whereIn('user_id', $subordinateIds)
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->sum('work_hours');
            
            $totalOvertime = Overtime::whereIn('user_id', $subordinateIds)
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->where('status', 'approved')
                ->sum('hours');
            
            $weeks[] = [
                'week' => $startOfWeek->format('M d'),
                'work_hours' => $totalHours,
                'overtime_hours' => $totalOvertime
            ];
        }
        
        return $weeks;
    }

    private function getTeamRedZoneEmployees($subordinateIds)
    {
        $overtimeThreshold = WlbSetting::get('wlb_red_zone_overtime_threshold', 8);
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return User::whereIn('id', $subordinateIds)
            ->with(['department', 'position'])
            ->whereHas('overtimes', function($query) use ($overtimeThreshold, $startOfWeek, $endOfWeek) {
                $query->whereBetween('date', [$startOfWeek, $endOfWeek])
                    ->where('status', 'approved')
                    ->groupBy('user_id')
                    ->havingRaw('SUM(hours) > ?', [$overtimeThreshold]);
            })
            ->get();
    }

    private function getRecentActivities()
    {
        $activities = collect();

        // Recent leave requests
        $recentLeaves = Leave::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($leave) {
                return [
                    'type' => 'leave',
                    'message' => "{$leave->user->name} mengajukan cuti {$leave->type_display}",
                    'status' => $leave->status,
                    'created_at' => $leave->created_at,
                ];
            });

        // Recent overtime requests
        $recentOvertimes = Overtime::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($overtime) {
                return [
                    'type' => 'overtime',
                    'message' => "{$overtime->user->name} mengajukan lembur {$overtime->hours} jam",
                    'status' => $overtime->status,
                    'created_at' => $overtime->created_at,
                ];
            });

        return $activities->merge($recentLeaves)
            ->merge($recentOvertimes)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();
    }
}

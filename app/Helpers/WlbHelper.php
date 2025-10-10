<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Leave;
use App\Models\WlbSetting;
use Carbon\Carbon;

class WlbHelper
{
    /**
     * Calculate WLB score for a user
     */
    public static function calculateWlbScore($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return 0;
        }

        $score = 100;
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Factor 1: Weekly overtime hours
        $weeklyOvertimeHours = $user->getTotalOvertimeHoursThisWeek();
        $redZoneOvertimeThreshold = WlbSetting::get('wlb_red_zone_overtime_threshold', 8);
        $yellowZoneOvertimeThreshold = WlbSetting::get('wlb_yellow_zone_overtime_threshold', 5);

        if ($weeklyOvertimeHours > $redZoneOvertimeThreshold) {
            $score -= 30;
        } elseif ($weeklyOvertimeHours > $yellowZoneOvertimeThreshold) {
            $score -= 15;
        }

        // Factor 2: Total work hours per week
        $weeklyWorkHours = $user->getWorkHoursThisWeek();
        $redZoneWorkHoursThreshold = WlbSetting::get('wlb_red_zone_work_hours_threshold', 50);

        if ($weeklyWorkHours > $redZoneWorkHoursThreshold) {
            $score -= 25;
        } elseif ($weeklyWorkHours > 45) {
            $score -= 10;
        }

        // Factor 3: Leave utilization (too little is also bad)
        $currentYear = Carbon::now()->year;
        $usedLeaves = Leave::where('user_id', $userId)
            ->whereYear('start_date', $currentYear)
            ->where('status', 'approved')
            ->where('type', 'annual')
            ->sum('days_requested');

        $leaveUtilizationRate = $user->annual_leave_quota > 0 
            ? ($usedLeaves / $user->annual_leave_quota) * 100 
            : 0;

        if ($leaveUtilizationRate < 30) { // Too little leave usage
            $score -= 10;
        }

        // Factor 4: Consistency of work hours
        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->get();

        if ($attendances->count() > 0) {
            $avgWorkHours = $attendances->avg('work_hours');
            $workHoursVariance = $attendances->reduce(function ($carry, $attendance) use ($avgWorkHours) {
                return $carry + pow($attendance->work_hours - $avgWorkHours, 2);
            }, 0) / $attendances->count();

            if ($workHoursVariance > 4) { // High variance in work hours
                $score -= 10;
            }
        }

        return max(0, min(100, $score));
    }

    /**
     * Get WLB status based on score
     */
    public static function getWlbStatus($score)
    {
        if ($score >= 80) {
            return [
                'status' => 'excellent',
                'label' => 'Excellent',
                'color' => 'green',
                'description' => 'Work-life balance sangat baik'
            ];
        } elseif ($score >= 60) {
            return [
                'status' => 'good',
                'label' => 'Good',
                'color' => 'blue',
                'description' => 'Work-life balance baik'
            ];
        } elseif ($score >= 40) {
            return [
                'status' => 'warning',
                'label' => 'Warning',
                'color' => 'yellow',
                'description' => 'Perlu perhatian pada work-life balance'
            ];
        } else {
            return [
                'status' => 'critical',
                'label' => 'Critical',
                'color' => 'red',
                'description' => 'Work-life balance dalam zona merah'
            ];
        }
    }

    /**
     * Get employees in red zone
     */
    public static function getRedZoneEmployees()
    {
        $redZoneUsers = [];
        $users = User::where('is_active', true)->get();

        foreach ($users as $user) {
            $score = self::calculateWlbScore($user->id);
            if ($score < 40) {
                $redZoneUsers[] = [
                    'user' => $user,
                    'score' => $score,
                    'status' => self::getWlbStatus($score)
                ];
            }
        }

        return collect($redZoneUsers)->sortBy('score');
    }

    /**
     * Get department WLB summary
     */
    public static function getDepartmentWlbSummary($departmentId)
    {
        $users = User::where('department_id', $departmentId)
            ->where('is_active', true)
            ->get();

        if ($users->isEmpty()) {
            return [
                'avgScore' => 0,
                'totalUsers' => 0,
                'redZoneCount' => 0,
                'yellowZoneCount' => 0,
                'greenZoneCount' => 0,
            ];
        }

        $scores = [];
        $redZoneCount = 0;
        $yellowZoneCount = 0;
        $greenZoneCount = 0;

        foreach ($users as $user) {
            $score = self::calculateWlbScore($user->id);
            $scores[] = $score;

            if ($score < 40) {
                $redZoneCount++;
            } elseif ($score < 60) {
                $yellowZoneCount++;
            } else {
                $greenZoneCount++;
            }
        }

        return [
            'avgScore' => round(array_sum($scores) / count($scores), 2),
            'totalUsers' => $users->count(),
            'redZoneCount' => $redZoneCount,
            'yellowZoneCount' => $yellowZoneCount,
            'greenZoneCount' => $greenZoneCount,
        ];
    }

    /**
     * Generate WLB recommendations for user
     */
    public static function generateRecommendations($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return [];
        }

        $recommendations = [];
        $score = self::calculateWlbScore($userId);

        // Check overtime hours
        $weeklyOvertimeHours = $user->getTotalOvertimeHoursThisWeek();
        $redZoneOvertimeThreshold = WlbSetting::get('wlb_red_zone_overtime_threshold', 8);

        if ($weeklyOvertimeHours > $redZoneOvertimeThreshold) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Lembur Berlebihan',
                'message' => "Anda telah lembur {$weeklyOvertimeHours} jam minggu ini. Pertimbangkan untuk mengurangi beban kerja.",
                'action' => 'Diskusikan dengan manager untuk redistribusi tugas'
            ];
        }

        // Check work hours
        $weeklyWorkHours = $user->getWorkHoursThisWeek();
        $redZoneWorkHoursThreshold = WlbSetting::get('wlb_red_zone_work_hours_threshold', 50);

        if ($weeklyWorkHours > $redZoneWorkHoursThreshold) {
            $recommendations[] = [
                'type' => 'danger',
                'title' => 'Jam Kerja Berlebihan',
                'message' => "Total jam kerja minggu ini: {$weeklyWorkHours} jam. Ini melebihi batas yang disarankan.",
                'action' => 'Ambil istirahat yang cukup dan pertimbangkan cuti'
            ];
        }

        // Check leave utilization
        $currentYear = Carbon::now()->year;
        $usedLeaves = Leave::where('user_id', $userId)
            ->whereYear('start_date', $currentYear)
            ->where('status', 'approved')
            ->where('type', 'annual')
            ->sum('days_requested');

        $leaveUtilizationRate = $user->annual_leave_quota > 0 
            ? ($usedLeaves / $user->annual_leave_quota) * 100 
            : 0;

        if ($leaveUtilizationRate < 30) {
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Kurang Memanfaatkan Cuti',
                'message' => "Anda baru menggunakan {$leaveUtilizationRate}% dari kuota cuti tahunan.",
                'action' => 'Rencanakan cuti untuk refreshing dan work-life balance yang lebih baik'
            ];
        }

        // Check late attendance
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $lateCount = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('status', 'late')
            ->count();

        if ($lateCount > 5) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Sering Terlambat',
                'message' => "Anda terlambat {$lateCount} kali bulan ini.",
                'action' => 'Atur waktu tidur dan persiapan pagi yang lebih baik'
            ];
        }

        return $recommendations;
    }

    /**
     * Calculate burnout risk score
     */
    public static function calculateBurnoutRisk($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return 0;
        }

        $riskScore = 0;

        // Factor 1: Consecutive overtime days
        $consecutiveOvertimeDays = self::getConsecutiveOvertimeDays($userId);
        if ($consecutiveOvertimeDays > 5) {
            $riskScore += 30;
        } elseif ($consecutiveOvertimeDays > 3) {
            $riskScore += 15;
        }

        // Factor 2: Monthly overtime hours
        $monthlyOvertimeHours = $user->getTotalOvertimeHoursThisMonth();
        $maxMonthlyOvertime = WlbSetting::get('max_overtime_hours_per_month', 40);
        
        if ($monthlyOvertimeHours > $maxMonthlyOvertime) {
            $riskScore += 25;
        } elseif ($monthlyOvertimeHours > ($maxMonthlyOvertime * 0.8)) {
            $riskScore += 15;
        }

        // Factor 3: No leaves taken in last 3 months
        $recentLeaves = Leave::where('user_id', $userId)
            ->where('status', 'approved')
            ->where('start_date', '>=', Carbon::now()->subMonths(3))
            ->count();

        if ($recentLeaves == 0) {
            $riskScore += 20;
        }

        // Factor 4: High variation in work hours (stress indicator)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        if ($attendances->count() > 5) {
            $avgWorkHours = $attendances->avg('work_hours');
            $variance = $attendances->reduce(function ($carry, $attendance) use ($avgWorkHours) {
                return $carry + pow($attendance->work_hours - $avgWorkHours, 2);
            }, 0) / $attendances->count();

            if ($variance > 6) {
                $riskScore += 15;
            }
        }

        return min(100, $riskScore);
    }

    /**
     * Get consecutive overtime days for user
     */
    private static function getConsecutiveOvertimeDays($userId)
    {
        $overtimes = Overtime::where('user_id', $userId)
            ->where('status', 'approved')
            ->orderBy('date', 'desc')
            ->take(14) // Check last 2 weeks
            ->get();

        $consecutiveDays = 0;
        $previousDate = null;

        foreach ($overtimes as $overtime) {
            $currentDate = Carbon::parse($overtime->date);
            
            if ($previousDate === null || $currentDate->diffInDays($previousDate) === 1) {
                $consecutiveDays++;
                $previousDate = $currentDate;
            } else {
                break;
            }
        }

        return $consecutiveDays;
    }

    /**
     * Get team WLB metrics for manager
     */
    public static function getTeamWlbMetrics($managerUserId)
    {
        $manager = User::find($managerUserId);
        if (!$manager) {
            return [];
        }

        $subordinates = $manager->subordinates()->where('is_active', true)->get();
        
        if ($subordinates->isEmpty()) {
            return [
                'teamSize' => 0,
                'avgWlbScore' => 0,
                'redZoneCount' => 0,
                'burnoutRiskCount' => 0,
                'recommendations' => []
            ];
        }

        $scores = [];
        $redZoneCount = 0;
        $burnoutRiskCount = 0;
        $teamRecommendations = [];

        foreach ($subordinates as $subordinate) {
            $score = self::calculateWlbScore($subordinate->id);
            $burnoutRisk = self::calculateBurnoutRisk($subordinate->id);
            
            $scores[] = $score;
            
            if ($score < 40) {
                $redZoneCount++;
            }
            
            if ($burnoutRisk > 60) {
                $burnoutRiskCount++;
                $teamRecommendations[] = [
                    'employee' => $subordinate->name,
                    'issue' => 'High burnout risk',
                    'action' => 'Schedule one-on-one meeting and consider workload adjustment'
                ];
            }
        }

        return [
            'teamSize' => $subordinates->count(),
            'avgWlbScore' => count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0,
            'redZoneCount' => $redZoneCount,
            'burnoutRiskCount' => $burnoutRiskCount,
            'recommendations' => $teamRecommendations
        ];
    }
}
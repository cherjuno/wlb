<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JobStressScale;
use App\Models\WlbMatrixCalculation;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Overtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|manager')->except(['matrixOverview']);
    }

    /**
     * Show WLB-Stress Matrix overview for dashboard
     * Accessible by all authenticated users
     */
    public function matrixOverview(Request $request)
    {
        $period = $request->get('period', 'current_month');
        $dateRange = $this->getDateRange($period);
        
        $matrixData = $this->calculateMatrixDistribution($dateRange);
        
        // If it's an AJAX request, return JSON
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json($matrixData);
        }
        
        // Otherwise, return the view
        return view('analytics.matrix-overview', compact('matrixData'));
    }

    /**
     * Show detailed employee matrix analysis
     * Only accessible by admin and manager
     */
    public function employeeMatrix(Request $request)
    {
        $user = Auth::user();
        
        // Get base query for employees
        $employeesQuery = User::with(['department', 'position'])
            ->where('is_active', true);
            
        // Manager can only see subordinates
        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id');
            $employeesQuery->whereIn('id', $subordinateIds);
        }
        
        // Apply filters
        if ($request->filled('department_id')) {
            $employeesQuery->where('department_id', $request->department_id);
        }
        
        $period = $request->get('period', 'current_month');
        $dateRange = $this->getDateRange($period);
        
        $employees = $employeesQuery->get();
        
        // Calculate matrix position for each employee
        $employeeMatrix = [];
        foreach ($employees as $employee) {
            $matrixPosition = $this->calculateEmployeeMatrixPosition($employee, $dateRange);
            $employeeMatrix[] = [
                'employee' => $employee,
                'matrix_data' => $matrixPosition
            ];
        }
        
        // Group by quadrant for summary
        $quadrantSummary = [
            'q1' => collect($employeeMatrix)->where('matrix_data.quadrant', 1)->count(), // High WLB, Low Stress
            'q2' => collect($employeeMatrix)->where('matrix_data.quadrant', 2)->count(), // High WLB, High Stress  
            'q3' => collect($employeeMatrix)->where('matrix_data.quadrant', 3)->count(), // Low WLB, Low Stress
            'q4' => collect($employeeMatrix)->where('matrix_data.quadrant', 4)->count(), // Low WLB, High Stress
        ];
        
        return view('analytics.employee-matrix', compact(
            'employeeMatrix', 
            'quadrantSummary', 
            'period',
            'dateRange'
        ));
    }

    /**
     * Get individual employee matrix details
     */
    public function employeeDetail(Request $request, User $employee)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id');
            if (!$subordinateIds->contains($employee->id)) {
                abort(403, 'Tidak dapat mengakses data karyawan ini.');
            }
        }
        
        $period = $request->get('period', 'current_month');
        $dateRange = $this->getDateRange($period);
        
        $matrixData = $this->calculateEmployeeMatrixPosition($employee, $dateRange, true);
        
        return view('analytics.employee-detail', compact('employee', 'matrixData', 'period'));
    }

    /**
     * Calculate matrix distribution for all employees
     */
    private function calculateMatrixDistribution($dateRange)
    {
        // Try to get data from WlbMatrixCalculation first
        $matrixData = WlbMatrixCalculation::whereBetween('calculation_date', [$dateRange['start'], $dateRange['end']])
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->get();
            
        if ($matrixData->isNotEmpty()) {
            // Use existing calculations
            $quadrants = [
                1 => $matrixData->where('quadrant', 1)->count(),
                2 => $matrixData->where('quadrant', 2)->count(),
                3 => $matrixData->where('quadrant', 3)->count(),
                4 => $matrixData->where('quadrant', 4)->count()
            ];
        } else {
            // Fallback to manual calculation
            $employees = User::where('is_active', true)->get();
            
            $quadrants = [
                1 => 0, // High WLB, Low Stress
                2 => 0, // High WLB, High Stress
                3 => 0, // Low WLB, Low Stress  
                4 => 0  // Low WLB, High Stress
            ];
            
            foreach ($employees as $employee) {
                $position = $this->calculateEmployeeMatrixPosition($employee, $dateRange);
                $quadrants[$position['quadrant']]++;
            }
        }
        
        return [
            'quadrants' => $quadrants,
            'total_employees' => $employees->count(),
            'period' => $dateRange,
            'matrix_data' => [
                'high_wlb_low_stress' => $quadrants[1],
                'high_wlb_high_stress' => $quadrants[2], 
                'low_wlb_low_stress' => $quadrants[3],
                'low_wlb_high_stress' => $quadrants[4]
            ]
        ];
    }

    /**
     * Calculate individual employee matrix position
     */
    private function calculateEmployeeMatrixPosition(User $employee, $dateRange, $detailed = false)
    {
        // Try to get existing matrix calculation for current period
        $matrixCalc = WlbMatrixCalculation::where('user_id', $employee->id)
            ->where('calculation_date', '>=', $dateRange['start'])
            ->where('calculation_date', '<=', $dateRange['end'])
            ->latest('calculation_date')
            ->first();
            
        if ($matrixCalc) {
            // Use existing calculation
            $result = [
                'quadrant' => $matrixCalc->quadrant,
                'quadrant_name' => $matrixCalc->quadrant_name,
                'wlb_score' => $matrixCalc->wlb_score,
                'wlb_level' => $matrixCalc->wlb_level,
                'jss_score' => $matrixCalc->jss_score,
                'stress_level' => $matrixCalc->stress_level,
                'recommendation' => $matrixCalc->recommendation ?? $this->getRecommendation($matrixCalc->quadrant)
            ];
        } else {
            // Fallback to manual calculation
            $jssScore = $this->getJSSScore($employee, $dateRange);
            $wlbScore = $this->getWLBScore($employee, $dateRange);
            
            $stressLevel = $jssScore >= 3.0 ? 'high' : 'low';
            $wlbLevel = $wlbScore >= 70 ? 'high' : 'low';
            $quadrant = $this->getQuadrant($wlbLevel, $stressLevel);
            
            $result = [
                'quadrant' => $quadrant,
                'quadrant_name' => $this->getQuadrantName($quadrant),
                'wlb_score' => $wlbScore,
                'wlb_level' => $wlbLevel,
                'jss_score' => $jssScore,
                'stress_level' => $stressLevel,
                'recommendation' => $this->getRecommendation($quadrant)
            ];
        }
        
        if ($detailed) {
            $result['detailed_metrics'] = $this->getDetailedMetrics($employee, $dateRange);
        }
        
        return $result;
    }

    /**
     * Get JSS (Job Stress Scale) score for employee
     */
    private function getJSSScore(User $employee, $dateRange)
    {
        $startDate = \Carbon\Carbon::parse($dateRange['start']);
        $endDate = \Carbon\Carbon::parse($dateRange['end']);
        
        // Get JSS data for the period (month/year based)
        $jss = JobStressScale::where('user_id', $employee->id)
            ->where('year', $startDate->year)
            ->where('month', '>=', $startDate->month)
            ->where('month', '<=', $endDate->month)
            ->latest('year')
            ->latest('month')
            ->first();
            
        if (!$jss) {
            // If no JSS data, return neutral score
            return 2.5;
        }
        
        // Convert total_score (10-50) to average score (1-5)
        return round($jss->total_score / 10, 2);
    }

    /**
     * Get WLB score for employee based on attendance, overtime, leave data
     */
    private function getWLBScore(User $employee, $dateRange)
    {
        // Base score
        $baseScore = 100;
        
        // Overtime penalty
        $overtimeHours = Overtime::where('user_id', $employee->id)
            ->where('status', 'approved')
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->sum('hours');
        
        $overtimePenalty = min(($overtimeHours / 40) * 20, 30); // Max 30 points penalty
        
        // Late arrival penalty
        $lateCount = Attendance::where('user_id', $employee->id)
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->whereNotNull('check_in')
            ->where('status', 'late')
            ->count();
        
        $latePenalty = min($lateCount * 2, 20); // Max 20 points penalty
        
        // Leave balance bonus (taking appropriate leave is good for WLB)
        $leavesTaken = Leave::where('user_id', $employee->id)
            ->where('status', 'approved')
            ->whereBetween('start_date', [$dateRange['start'], $dateRange['end']])
            ->sum('days_requested');
        
        $leaveBonus = min($leavesTaken * 1, 10); // Max 10 points bonus
        
        $finalScore = $baseScore - $overtimePenalty - $latePenalty + $leaveBonus;
        
        return max(0, min(100, $finalScore)); // Ensure score is between 0-100
    }

    /**
     * Determine quadrant based on WLB and stress levels
     */
    private function getQuadrant($wlbLevel, $stressLevel)
    {
        if ($wlbLevel === 'high' && $stressLevel === 'low') {
            return 1; // Ideal state
        } elseif ($wlbLevel === 'high' && $stressLevel === 'high') {
            return 2; // Good WLB but high stress
        } elseif ($wlbLevel === 'low' && $stressLevel === 'low') {
            return 3; // Poor WLB but low stress
        } else {
            return 4; // Poor WLB and high stress - critical
        }
    }

    /**
     * Get quadrant name
     */
    private function getQuadrantName($quadrant)
    {
        $names = [
            1 => 'Optimal (WLB Tinggi, Stress Rendah)',
            2 => 'Berpotensi Burnout (WLB Tinggi, Stress Tinggi)', 
            3 => 'Kurang Engaged (WLB Rendah, Stress Rendah)',
            4 => 'Kritis (WLB Rendah, Stress Tinggi)'
        ];
        
        return $names[$quadrant] ?? 'Unknown';
    }

    /**
     * Get recommendation based on quadrant
     */
    private function getRecommendation($quadrant)
    {
        $recommendations = [
            1 => 'Pertahankan keseimbangan yang baik ini. Dapat menjadi mentor untuk rekan kerja.',
            2 => 'Fokus pada manajemen stress. Pertimbangkan teknik relaksasi dan delegasi tugas.',
            3 => 'Tingkatkan engagement dan work-life balance. Pertimbangkan training dan pengembangan.',
            4 => 'Perlu intervensi segera. Evaluasi beban kerja dan berikan dukungan tambahan.'
        ];
        
        return $recommendations[$quadrant] ?? 'Perlu evaluasi lebih lanjut.';
    }

    /**
     * Get detailed metrics for employee
     */
    private function getDetailedMetrics(User $employee, $dateRange)
    {
        return [
            'overtime_hours' => Overtime::where('user_id', $employee->id)
                ->where('status', 'approved')
                ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
                ->sum('hours'),
            'late_count' => Attendance::where('user_id', $employee->id)
                ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
                ->where('status', 'late')
                ->count(),
            'leave_days' => Leave::where('user_id', $employee->id)
                ->where('status', 'approved')
                ->whereBetween('start_date', [$dateRange['start'], $dateRange['end']])
                ->sum('days_requested'),
            'attendance_rate' => $this->calculateAttendanceRate($employee, $dateRange)
        ];
    }

    /**
     * Calculate attendance rate
     */
    private function calculateAttendanceRate(User $employee, $dateRange)
    {
        $workDays = $this->getWorkDaysCount($dateRange['start'], $dateRange['end']);
        $attendedDays = Attendance::where('user_id', $employee->id)
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->whereNotNull('check_in')
            ->count();
            
        return $workDays > 0 ? round(($attendedDays / $workDays) * 100, 2) : 0;
    }

    /**
     * Get work days count (excluding weekends)
     */
    private function getWorkDaysCount($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $workDays = 0;
        
        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $workDays++;
            }
            $start->addDay();
        }
        
        return $workDays;
    }

    /**
     * Get date range based on period
     */
    private function getDateRange($period)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'current_month':
                return [
                    'start' => $now->startOfMonth()->toDateString(),
                    'end' => $now->endOfMonth()->toDateString(),
                    'label' => 'Bulan Ini'
                ];
            case 'last_month':
                $lastMonth = $now->subMonth();
                return [
                    'start' => $lastMonth->startOfMonth()->toDateString(),
                    'end' => $lastMonth->endOfMonth()->toDateString(),
                    'label' => 'Bulan Lalu'
                ];
            case 'current_quarter':
                return [
                    'start' => $now->startOfQuarter()->toDateString(),
                    'end' => $now->endOfQuarter()->toDateString(),
                    'label' => 'Kuartal Ini'
                ];
            case 'current_year':
                return [
                    'start' => $now->startOfYear()->toDateString(),
                    'end' => $now->endOfYear()->toDateString(),
                    'label' => 'Tahun Ini'
                ];
            default:
                return [
                    'start' => $now->startOfMonth()->toDateString(),
                    'end' => $now->endOfMonth()->toDateString(),
                    'label' => 'Bulan Ini'
                ];
        }
    }
}
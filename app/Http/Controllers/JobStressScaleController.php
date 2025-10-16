<?php

namespace App\Http\Controllers;

use App\Models\JobStressScale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class JobStressScaleController extends Controller
{
    /**
     * Display a listing for admin (overview all employees)
     */
    public function index(Request $request): View
    {
        // Only admin can access this
        abort_unless(Auth::user()->hasRole('admin'), 403);

        $currentMonth = $request->get('month', now()->month);
        $currentYear = $request->get('year', now()->year);

        $stressScales = JobStressScale::with('user')
            ->forMonth($currentMonth, $currentYear)
            ->orderBy('total_score', 'desc')
            ->get();

        // Statistics
        $totalEmployees = User::role('employee')->count();
        $completedForms = $stressScales->count();
        $completionRate = $totalEmployees > 0 ? round(($completedForms / $totalEmployees) * 100, 1) : 0;

        $stressLevelStats = [
            'low' => $stressScales->where('stress_level', 'low')->count(),
            'moderate' => $stressScales->where('stress_level', 'moderate')->count(),
            'high' => $stressScales->where('stress_level', 'high')->count(),
        ];

        $averageScore = $stressScales->avg('total_score') ?? 0;

        // Get employees who haven't filled the form
        $filledUserIds = $stressScales->pluck('user_id')->toArray();
        $missingEmployees = User::role('employee')
            ->whereNotIn('id', $filledUserIds)
            ->get();

        return view('job-stress.admin.index', compact(
            'stressScales',
            'currentMonth',
            'currentYear',
            'totalEmployees',
            'completedForms',
            'completionRate',
            'stressLevelStats',
            'averageScore',
            'missingEmployees'
        ));
    }

    /**
     * Show manager dashboard for viewing team's stress levels
     */
    public function managerDashboard(Request $request): View
    {
        // Only manager can access this
        abort_unless(Auth::user()->hasRole('manager'), 403);

        $manager = Auth::user();
        $currentMonth = $request->get('month', now()->month);
        $currentYear = $request->get('year', now()->year);

        // Get subordinates' stress scales
        $subordinateIds = $manager->subordinates()->pluck('id');
        $teamStressScales = JobStressScale::with('user')
            ->whereIn('user_id', $subordinateIds)
            ->forMonth($currentMonth, $currentYear)
            ->orderBy('total_score', 'desc')
            ->get();

        // Team statistics
        $totalTeamMembers = $subordinateIds->count();
        $completedForms = $teamStressScales->count();
        $completionRate = $totalTeamMembers > 0 ? round(($completedForms / $totalTeamMembers) * 100, 1) : 0;

        $teamStressLevelStats = [
            'low' => $teamStressScales->where('stress_level', 'low')->count(),
            'moderate' => $teamStressScales->where('stress_level', 'moderate')->count(),
            'high' => $teamStressScales->where('stress_level', 'high')->count(),
        ];

        $teamAverageScore = $teamStressScales->avg('total_score') ?? 0;

        // Get team members who haven't filled the form
        $filledUserIds = $teamStressScales->pluck('user_id')->toArray();
        $missingTeamMembers = User::whereIn('id', $subordinateIds)
            ->whereNotIn('id', $filledUserIds)
            ->get();

        return view('job-stress.manager.dashboard', compact(
            'teamStressScales',
            'currentMonth',
            'currentYear',
            'totalTeamMembers',
            'completedForms',
            'completionRate',
            'teamStressLevelStats',
            'teamAverageScore',
            'missingTeamMembers'
        ));
    }

    /**
     * Show the form for creating a new stress scale entry
     */
    public function create(): View
    {
        $user = Auth::user();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Check if user already filled this month
        $existingEntry = JobStressScale::where('user_id', $user->id)
            ->forMonth($currentMonth, $currentYear)
            ->first();

        if ($existingEntry) {
            return redirect()->route('job-stress.show', $existingEntry->id)
                ->with('info', 'Anda sudah mengisi Job Stress Scale untuk bulan ini.');
        }

        $questions = JobStressScale::getQuestions();

        return view('job-stress.create', compact('questions', 'currentMonth', 'currentYear'));
    }

    /**
     * Store a newly created stress scale entry
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Check if user already filled this month
        $existingEntry = JobStressScale::where('user_id', $user->id)
            ->forMonth($currentMonth, $currentYear)
            ->first();

        if ($existingEntry) {
            return redirect()->route('job-stress.show', $existingEntry->id)
                ->with('error', 'Anda sudah mengisi Job Stress Scale untuk bulan ini.');
        }

        $validated = $request->validate([
            'question_1' => 'required|integer|min:1|max:5',
            'question_2' => 'required|integer|min:1|max:5',
            'question_3' => 'required|integer|min:1|max:5',
            'question_4' => 'required|integer|min:1|max:5',
            'question_5' => 'required|integer|min:1|max:5',
            'question_6' => 'required|integer|min:1|max:5',
            'question_7' => 'required|integer|min:1|max:5',
            'question_8' => 'required|integer|min:1|max:5',
            'question_9' => 'required|integer|min:1|max:5',
            'question_10' => 'required|integer|min:1|max:5',
        ]);

        // Calculate total score
        $totalScore = array_sum($validated);

        // Determine stress level
        $stressLevel = 'moderate';
        if ($totalScore >= 10 && $totalScore <= 20) {
            $stressLevel = 'low';
        } elseif ($totalScore >= 21 && $totalScore <= 35) {
            $stressLevel = 'moderate';
        } elseif ($totalScore >= 36 && $totalScore <= 50) {
            $stressLevel = 'high';
        }

        $stressScale = JobStressScale::create([
            'user_id' => $user->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'question_1' => $validated['question_1'],
            'question_2' => $validated['question_2'],
            'question_3' => $validated['question_3'],
            'question_4' => $validated['question_4'],
            'question_5' => $validated['question_5'],
            'question_6' => $validated['question_6'],
            'question_7' => $validated['question_7'],
            'question_8' => $validated['question_8'],
            'question_9' => $validated['question_9'],
            'question_10' => $validated['question_10'],
            'total_score' => $totalScore,
            'stress_level' => $stressLevel,
        ]);

        return redirect()->route('job-stress.show', $stressScale->id)
            ->with('success', 'Job Stress Scale berhasil disimpan!');
    }

    /**
     * Display the specified stress scale entry
     */
    public function show(JobStressScale $jobStressScale): View
    {
        $user = Auth::user();

        // Check authorization
        if ($user->hasRole('employee') && $jobStressScale->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id');
            if (!$subordinateIds->contains($jobStressScale->user_id) && $jobStressScale->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
            }
        }

        $questions = JobStressScale::getQuestions();

        return view('job-stress.show', compact('jobStressScale', 'questions'));
    }

    /**
     * Check if current user has filled this month's form
     */
    public function checkMonthlyStatus(): array
    {
        $user = Auth::user();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $existingEntry = JobStressScale::where('user_id', $user->id)
            ->forMonth($currentMonth, $currentYear)
            ->first();

        return [
            'has_filled' => !is_null($existingEntry),
            'entry_id' => $existingEntry?->id,
            'stress_level' => $existingEntry?->stress_level,
            'total_score' => $existingEntry?->total_score,
            'month' => $currentMonth,
            'year' => $currentYear,
            'month_name' => now()->locale('id')->monthName,
        ];
    }

    /**
     * Get employee's stress scale history
     */
    public function history(): View
    {
        $user = Auth::user();
        
        $stressScales = JobStressScale::where('user_id', $user->id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('job-stress.history', compact('stressScales'));
    }

    /**
     * Generate monthly report (admin only)
     */
    public function monthlyReport(Request $request): View
    {
        abort_unless(Auth::user()->hasRole('admin'), 403);

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $stressScales = JobStressScale::with('user.department')
            ->forMonth($month, $year)
            ->get();

        // Department-wise analysis
        $departmentStats = $stressScales->groupBy('user.department.name')->map(function ($group) {
            return [
                'count' => $group->count(),
                'average_score' => round($group->avg('total_score'), 1),
                'low_stress' => $group->where('stress_level', 'low')->count(),
                'moderate_stress' => $group->where('stress_level', 'moderate')->count(),
                'high_stress' => $group->where('stress_level', 'high')->count(),
            ];
        });

        return view('job-stress.admin.report', compact(
            'stressScales',
            'departmentStats',
            'month',
            'year'
        ));
    }
}

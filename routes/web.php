<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JobStressScaleController;
use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Overtime;

Route::get('/test-user-crud', function() {
    // Test dengan admin user
    $admin = App\Models\User::where('name', 'like', '%Admin%')->first();
    if (!$admin) {
        $admin = App\Models\User::find(1); // fallback
    }
    
    auth()->login($admin);
    
    return [
        'user_info' => [
            'id' => $admin->id,
            'name' => $admin->name,
            'roles' => $admin->getRoleNames()->toArray(),
            'can_view_users' => $admin->can('viewAny', App\Models\User::class),
            'can_create_users' => $admin->can('create', App\Models\User::class),
        ],
        'user_count' => [
            'total' => App\Models\User::count(),
            'active' => App\Models\User::where('is_active', true)->count(),
            'admins' => App\Models\User::role('admin')->count(),
            'managers' => App\Models\User::role('manager')->count(),
            'employees' => App\Models\User::role('employee')->count(),
        ],
        'routes_available' => [
            'users_index' => route('users.index'),
            'users_create' => route('users.create'),
        ],
        'policy_test' => [
            'can_view_any' => $admin->can('viewAny', App\Models\User::class),
            'can_create' => $admin->can('create', App\Models\User::class),
        ]
    ];
});

Route::get('/test-manager-checkout', function() {
    // Test dengan manager user
    $manager = App\Models\User::where('name', 'like', '%Manager%')->first();
    if (!$manager) {
        $manager = App\Models\User::find(2); // fallback
    }
    
    auth()->login($manager);
    
    $today = Carbon\Carbon::today();
    $attendance = App\Models\Attendance::where('user_id', $manager->id)
        ->whereDate('date', $today)
        ->first();
    
    // Create sample attendance if not exists
    if (!$attendance) {
        $attendance = App\Models\Attendance::create([
            'user_id' => $manager->id,
            'date' => $today,
            'check_in' => '08:00',
            'status' => 'present'
        ]);
    }
    
    return [
        'manager_info' => [
            'id' => $manager->id,
            'name' => $manager->name,
            'roles' => $manager->getRoleNames()->toArray()
        ],
        'attendance_today' => [
            'exists' => $attendance ? true : false,
            'check_in' => $attendance ? $attendance->check_in : null,
            'check_out' => $attendance ? $attendance->check_out : null,
            'has_checked_in' => $attendance && $attendance->check_in,
            'has_checked_out' => $attendance && $attendance->check_out,
        ],
        'quick_checkout_test' => [
            'can_checkout' => $attendance && $attendance->check_in && !$attendance->check_out,
            'checkout_url' => route('attendance.quick-check-out'),
            'csrf_token' => csrf_token()
        ]
    ];
});

Route::get('/test-overtime', function() {
    // Test dengan manager user
    $manager = App\Models\User::where('name', 'like', '%Manager%')->first();
    if (!$manager) {
        $manager = App\Models\User::find(2); // fallback
    }
    
    auth()->login($manager);
    
    $today = Carbon\Carbon::today();
    $attendance = App\Models\Attendance::where('user_id', $manager->id)
        ->whereDate('date', $today)
        ->first();
    
    return [
        'manager_info' => [
            'id' => $manager->id,
            'name' => $manager->name,
            'roles' => $manager->getRoleNames()->toArray()
        ],
        'attendance_today' => [
            'exists' => $attendance ? true : false,
            'check_in' => $attendance ? $attendance->check_in : null,
            'check_out' => $attendance ? $attendance->check_out : null,
            'has_checked_in' => $attendance && $attendance->check_in,
            'has_checked_out' => $attendance && $attendance->check_out,
        ],
        'today_status_endpoint' => [
            'hasCheckedIn' => $attendance && $attendance->check_in,
            'hasCheckedOut' => $attendance && $attendance->check_out,
            'checkInTime' => $attendance ? $attendance->check_in : null,
            'checkOutTime' => $attendance ? $attendance->check_out : null,
        ]
    ];
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Salary routes
    Route::get('/salary', [ProfileController::class, 'salary'])->name('salary.index');

    // Attendance routes
    Route::resource('attendance', AttendanceController::class);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::get('/attendance/today/status', [AttendanceController::class, 'todayStatus'])->name('attendance.today-status');
    Route::post('/attendance/quick/check-in', [AttendanceController::class, 'quickCheckIn'])->name('attendance.quick-check-in');
    Route::post('/attendance/quick/check-out', [AttendanceController::class, 'quickCheckOut'])->name('attendance.quick-check-out');
    Route::get('/attendance-report', [AttendanceController::class, 'report'])->name('attendance.report');

    // Leave routes
    Route::resource('leave', LeaveController::class);
    Route::post('/leave/{leave}/approve', [LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/{leave}/reject', [LeaveController::class, 'reject'])->name('leave.reject');
    Route::get('/pending-leaves', [LeaveController::class, 'pending'])->name('leave.pending');
    Route::get('/leave-report', [LeaveController::class, 'report'])->name('leave.report');

    // Overtime routes
    Route::resource('overtime', OvertimeController::class);
    Route::post('/overtime/{overtime}/approve', [OvertimeController::class, 'approve'])->name('overtime.approve');
    Route::post('/overtime/{overtime}/reject', [OvertimeController::class, 'reject'])->name('overtime.reject');
    Route::get('/pending-overtimes', [OvertimeController::class, 'pending'])->name('overtime.pending');
    Route::get('/overtime-report', [OvertimeController::class, 'report'])->name('overtime.report');
    Route::post('/overtime/quick-submit', [OvertimeController::class, 'quickSubmit'])->name('overtime.quick-submit');
    Route::get('/overtime/limits', [OvertimeController::class, 'getOvertimeLimits'])->name('overtime.limits');

    // Job Stress Scale routes
    Route::get('/job-stress/create', [JobStressScaleController::class, 'create'])->name('job-stress.create');
    Route::post('/job-stress', [JobStressScaleController::class, 'store'])->name('job-stress.store');
    Route::get('/job-stress/{jobStressScale}', [JobStressScaleController::class, 'show'])->name('job-stress.show');
    Route::get('/job-stress-history', [JobStressScaleController::class, 'history'])->name('job-stress.history');
    Route::get('/job-stress-status', [JobStressScaleController::class, 'checkMonthlyStatus'])->name('job-stress.status');

    // User management routes (Admin and Manager only)
    Route::middleware(['role:admin|manager'])->group(function () {
        Route::resource('users', UserController::class);
        
        // Analytics routes  
        Route::get('/analytics/matrix-overview', [AnalyticsController::class, 'matrixOverview'])->name('analytics.matrix-overview');
        Route::get('/analytics/employee-matrix', [AnalyticsController::class, 'employeeMatrix'])->name('analytics.employee-matrix');
        Route::get('/analytics/employee/{employee}', [AnalyticsController::class, 'employeeDetail'])->name('analytics.employee-detail');
    });

    // Manager only routes
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/team-stress-dashboard', [JobStressScaleController::class, 'managerDashboard'])->name('job-stress.manager.dashboard');
    });

    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/job-stress-admin', [JobStressScaleController::class, 'index'])->name('job-stress.admin.index');
        Route::get('/job-stress-report', [JobStressScaleController::class, 'monthlyReport'])->name('job-stress.admin.report');
    });
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

    // User management routes (Admin and Manager only)
    Route::middleware(['role:admin|manager'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        // Settings, departments, positions management can be added here
    });
});

require __DIR__.'/auth.php';

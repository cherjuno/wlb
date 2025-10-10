<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Attendance::with('user');

        // Filter berdasarkan role
        if ($user->hasRole('employee')) {
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('manager')) {
            // Manager bisa melihat attendances dari subordinates
            $subordinateIds = $user->subordinates()->pluck('id')->push($user->id);
            $query->whereIn('user_id', $subordinateIds);
        }
        // Admin bisa melihat semua

        // Filter berdasarkan parameter
        if ($request->filled('user_id') && ($user->hasRole('admin') || $user->hasRole('manager'))) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(15);

        // Data untuk filter dropdown
        $users = collect();
        if ($user->hasRole('admin')) {
            $users = User::where('is_active', true)->get();
        } elseif ($user->hasRole('manager')) {
            $users = $user->subordinates()->where('is_active', true)->get();
        }

        return view('attendance.index', compact('attendances', 'users'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Check if user already has attendance for today
        $today = Carbon::today();
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($existingAttendance) {
            return redirect()->route('attendance.index')
                ->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        return view('attendance.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $today = Carbon::today();

        // Check if user already has attendance for today
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($existingAttendance) {
            return redirect()->route('attendance.index')
                ->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => $request->check_in,
            'notes' => $request->notes,
        ]);

        $attendance->updateStatus();

        return redirect()->route('attendance.index')
            ->with('success', 'Check-in berhasil dicatat.');
    }

    public function show(Attendance $attendance)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->hasRole('employee') && $attendance->user_id !== $user->id) {
            abort(403, 'Tidak memiliki akses ke data ini.');
        }

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id')->push($user->id);
            if (!$subordinateIds->contains($attendance->user_id)) {
                abort(403, 'Tidak memiliki akses ke data ini.');
            }
        }

        return view('attendance.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        $user = Auth::user();

        // Only allow editing own attendance or if admin/manager
        if ($user->hasRole('employee') && $attendance->user_id !== $user->id) {
            abort(403, 'Tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id')->push($user->id);
            if (!$subordinateIds->contains($attendance->user_id)) {
                abort(403, 'Tidak memiliki akses untuk mengedit data ini.');
            }
        }

        return view('attendance.edit', compact('attendance'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'break_duration' => 'nullable|numeric|min:0|max:8',
            'notes' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        // Authorization check
        if ($user->hasRole('employee') && $attendance->user_id !== $user->id) {
            abort(403, 'Tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id')->push($user->id);
            if (!$subordinateIds->contains($attendance->user_id)) {
                abort(403, 'Tidak memiliki akses untuk mengedit data ini.');
            }
        }

        $attendance->update([
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'break_duration' => $request->break_duration ?? 1.0,
            'notes' => $request->notes,
        ]);

        $attendance->updateWorkHours();
        $attendance->updateStatus();

        return redirect()->route('attendance.index')
            ->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function destroy(Attendance $attendance)
    {
        $user = Auth::user();

        // Only admin can delete attendance records
        if (!$user->hasRole('admin')) {
            abort(403, 'Tidak memiliki akses untuk menghapus data ini.');
        }

        $attendance->delete();

        return redirect()->route('attendance.index')
            ->with('success', 'Data absensi berhasil dihapus.');
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'check_out' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            return redirect()->route('attendance.index')
                ->with('error', 'Tidak ada data check-in untuk hari ini.');
        }

        if ($attendance->check_out) {
            return redirect()->route('attendance.index')
                ->with('error', 'Anda sudah melakukan check-out hari ini.');
        }

        $attendance->update([
            'check_out' => $request->check_out,
            'notes' => $request->notes ?: $attendance->notes,
        ]);

        $attendance->updateWorkHours();
        $attendance->updateStatus();

        return redirect()->route('attendance.index')
            ->with('success', 'Check-out berhasil dicatat.');
    }

    public function todayStatus()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $status = [
            'hasCheckedIn' => $attendance && $attendance->check_in,
            'hasCheckedOut' => $attendance && $attendance->check_out,
            'checkInTime' => $attendance ? $attendance->check_in : null,
            'checkOutTime' => $attendance ? $attendance->check_out : null,
            'workHours' => $attendance ? $attendance->work_hours : 0,
            'status' => $attendance ? $attendance->status : null,
        ];

        return response()->json($status);
    }

    public function quickCheckIn()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();

        // Check if user already has attendance for today
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi hari ini.'
            ], 400);
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => $now->format('H:i'),
        ]);

        $attendance->updateStatus();

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil dicatat.',
            'data' => $attendance
        ]);
    }

    public function quickCheckOut()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data check-in untuk hari ini.'
            ], 400);
        }

        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-out hari ini.'
            ], 400);
        }

        $attendance->update([
            'check_out' => $now->format('H:i'),
        ]);

        $attendance->updateWorkHours();
        $attendance->updateStatus();

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil dicatat.',
            'data' => $attendance
        ]);
    }

    public function report(Request $request)
    {
        $user = Auth::user();

        // Only admin and manager can access reports
        if ($user->hasRole('employee')) {
            abort(403, 'Tidak memiliki akses ke laporan.');
        }

        $query = Attendance::with('user.department');

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id');
            $query->whereIn('user_id', $subordinateIds);
        }

        // Filter berdasarkan parameter
        if ($request->filled('department_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        // CSV export
        if ($request->get('export') === 'csv') {
            $filename = 'attendance_report_' . now()->format('Ymd_His') . '.csv';
            return response()->streamDownload(function () use ($attendances) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Tanggal', 'Karyawan', 'Departemen', 'Check-in', 'Check-out', 'Jam Kerja', 'Status']);
                foreach ($attendances as $a) {
                    fputcsv($handle, [
                        optional($a->date)->format('Y-m-d') ?? ($a->date ?? ''),
                        optional($a->user)->name,
                        optional(optional($a->user)->department)->name,
                        $a->check_in,
                        $a->check_out,
                        number_format($a->work_hours, 1),
                        $a->status,
                    ]);
                }
                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        }

        // Summary statistics
        $summary = [
            'totalDays' => $attendances->count(),
            'presentDays' => $attendances->where('status', 'present')->count(),
            'lateDays' => $attendances->where('status', 'late')->count(),
            'absentDays' => $attendances->where('status', 'absent')->count(),
            'totalWorkHours' => $attendances->sum('work_hours'),
            'avgWorkHours' => $attendances->avg('work_hours'),
        ];

        $departments = Department::where('is_active', true)->get();
        return view('attendance.report', compact('attendances', 'summary', 'departments'));
    }
}

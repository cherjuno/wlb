<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Overtime;
use App\Models\User;
use App\Models\WlbSetting;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OvertimeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Overtime::with(['user', 'approver']);

        // Debug info
        \Log::info('Overtime index accessed', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_roles' => $user->getRoleNames()->toArray(),
            'total_overtimes' => Overtime::count()
        ]);

        // Filter berdasarkan role
        if ($user->hasRole('employee')) {
            $query->where('user_id', $user->id);
            \Log::info('Applied employee filter', ['user_id' => $user->id]);
        } elseif ($user->hasRole('manager')) {
            // Manager bisa melihat overtimes dari subordinates dan dirinya sendiri
            $subordinateIds = $user->subordinates()->pluck('id')->push($user->id);
            $query->whereIn('user_id', $subordinateIds);
            \Log::info('Applied manager filter', ['subordinate_ids' => $subordinateIds->toArray()]);
        }
        // Admin bisa melihat semua
        else {
            \Log::info('Admin user - no filter applied');
        }

        // Filter berdasarkan parameter
        if ($request->filled('user_id') && ($user->hasRole('admin') || $user->hasRole('manager'))) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $overtimes = $query->orderBy('date', 'desc')->paginate(15);

        \Log::info('Query result', [
            'query_count' => $query->count(),
            'paginated_count' => $overtimes->count()
        ]);

        // Data untuk filter dropdown
        $users = collect();
        if ($user->hasRole('admin')) {
            $users = User::where('is_active', true)->get();
        } elseif ($user->hasRole('manager')) {
            $users = $user->subordinates()->where('is_active', true)->get();
        }

        return view('overtime.index', compact('overtimes', 'users'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Get monthly overtime hours
        $monthlyOvertime = Overtime::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('hours');
        
        return view('overtime.create', compact('user', 'monthlyOvertime'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        // Calculate hours
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        
        // If end time is before start time, assume it's next day
        if ($endTime->lt($startTime)) {
            $endTime->addDay();
        }
        
        $hours = round($endTime->diffInMinutes($startTime) / 60, 2);

        // Determine overtime type
        $date = Carbon::parse($request->date);
        $type = $date->isWeekend() ? 'weekend' : 'weekday';

        // Check maximum overtime limits
        $maxWeeklyOvertime = WlbSetting::get('max_overtime_hours_per_week', 10);
        $maxMonthlyOvertime = WlbSetting::get('max_overtime_hours_per_month', 40);

        $currentWeekOvertime = $user->getTotalOvertimeHoursThisWeek();
        $currentMonthOvertime = $user->getTotalOvertimeHoursThisMonth();

        if (($currentWeekOvertime + $hours) > $maxWeeklyOvertime) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Melebihi batas maksimal lembur mingguan ({$maxWeeklyOvertime} jam). Lembur minggu ini: {$currentWeekOvertime} jam.");
        }

        if (($currentMonthOvertime + $hours) > $maxMonthlyOvertime) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Melebihi batas maksimal lembur bulanan ({$maxMonthlyOvertime} jam). Lembur bulan ini: {$currentMonthOvertime} jam.");
        }

        $overtime = Overtime::create([
            'user_id' => $user->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'hours' => $hours,
            'type' => $type,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('overtime.index')
            ->with('success', 'Pengajuan lembur berhasil disubmit dan menunggu persetujuan.');
    }

    public function show(Overtime $overtime)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->hasRole('employee') && $overtime->user_id !== $user->id) {
            abort(403, 'Tidak memiliki akses ke data ini.');
        }

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id')->push($user->id);
            if (!$subordinateIds->contains($overtime->user_id)) {
                abort(403, 'Tidak memiliki akses ke data ini.');
            }
        }

        return view('overtime.show', compact('overtime'));
    }

    public function edit(Overtime $overtime)
    {
        $user = Auth::user();

        // Only allow editing own overtime and if still pending
        if ($user->hasRole('employee') && ($overtime->user_id !== $user->id || $overtime->status !== 'pending')) {
            abort(403, 'Tidak dapat mengedit pengajuan lembur ini.');
        }

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id')->push($user->id);
            if (!$subordinateIds->contains($overtime->user_id) || $overtime->status !== 'pending') {
                abort(403, 'Tidak dapat mengedit pengajuan lembur ini.');
            }
        }

        return view('overtime.edit', compact('overtime'));
    }

    public function update(Request $request, Overtime $overtime)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        // Authorization check
        if ($user->hasRole('employee') && ($overtime->user_id !== $user->id || $overtime->status !== 'pending')) {
            abort(403, 'Tidak dapat mengedit pengajuan lembur ini.');
        }

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id')->push($user->id);
            if (!$subordinateIds->contains($overtime->user_id) || $overtime->status !== 'pending') {
                abort(403, 'Tidak dapat mengedit pengajuan lembur ini.');
            }
        }

        // Calculate new hours
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        
        if ($endTime->lt($startTime)) {
            $endTime->addDay();
        }
        
        $hours = round($endTime->diffInMinutes($startTime) / 60, 2);

        // Determine new overtime type
        $date = Carbon::parse($request->date);
        $type = $date->isWeekend() ? 'weekend' : 'weekday';

        // Check overtime limits (excluding current overtime hours)
        $maxWeeklyOvertime = WlbSetting::get('max_overtime_hours_per_week', 10);
        $maxMonthlyOvertime = WlbSetting::get('max_overtime_hours_per_month', 40);

        $currentWeekOvertime = $overtime->user->getTotalOvertimeHoursThisWeek() - $overtime->hours;
        $currentMonthOvertime = $overtime->user->getTotalOvertimeHoursThisMonth() - $overtime->hours;

        if (($currentWeekOvertime + $hours) > $maxWeeklyOvertime) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Melebihi batas maksimal lembur mingguan ({$maxWeeklyOvertime} jam).");
        }

        if (($currentMonthOvertime + $hours) > $maxMonthlyOvertime) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Melebihi batas maksimal lembur bulanan ({$maxMonthlyOvertime} jam).");
        }

        $overtime->update([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'hours' => $hours,
            'type' => $type,
            'reason' => $request->reason,
        ]);

        return redirect()->route('overtime.index')
            ->with('success', 'Pengajuan lembur berhasil diperbarui.');
    }

    public function destroy(Overtime $overtime)
    {
        $user = Auth::user();

        // Only allow deleting own overtime if still pending
        if ($user->hasRole('employee') && ($overtime->user_id !== $user->id || $overtime->status !== 'pending')) {
            abort(403, 'Tidak dapat menghapus pengajuan lembur ini.');
        }

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id')->push($user->id);
            if (!$subordinateIds->contains($overtime->user_id) || $overtime->status !== 'pending') {
                abort(403, 'Tidak dapat menghapus pengajuan lembur ini.');
            }
        }

        $overtime->delete();

        return redirect()->route('overtime.index')
            ->with('success', 'Pengajuan lembur berhasil dihapus.');
    }

    public function approve(Request $request, Overtime $overtime)
    {
        $user = Auth::user();

        // Only manager and admin can approve
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            abort(403, 'Tidak memiliki akses untuk menyetujui lembur.');
        }

        // Manager can only approve subordinates' overtimes
        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id');
            if (!$subordinateIds->contains($overtime->user_id)) {
                abort(403, 'Tidak dapat menyetujui lembur karyawan ini.');
            }
        }

        if (!$overtime->canBeApproved()) {
            return redirect()->back()
                ->with('error', 'Pengajuan lembur ini tidak dapat disetujui.');
        }

        $request->validate([
            'approval_notes' => 'nullable|string|max:500',
        ]);

        $overtime->approve($user->id, $request->approval_notes);

        return redirect()->route('overtime.index')
            ->with('success', 'Pengajuan lembur berhasil disetujui.');
    }

    public function reject(Request $request, Overtime $overtime)
    {
        $user = Auth::user();

        // Only manager and admin can reject
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            abort(403, 'Tidak memiliki akses untuk menolak lembur.');
        }

        // Manager can only reject subordinates' overtimes
        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id');
            if (!$subordinateIds->contains($overtime->user_id)) {
                abort(403, 'Tidak dapat menolak lembur karyawan ini.');
            }
        }

        if (!$overtime->canBeApproved()) {
            return redirect()->back()
                ->with('error', 'Pengajuan lembur ini tidak dapat ditolak.');
        }

        $request->validate([
            'approval_notes' => 'required|string|max:500',
        ]);

        $overtime->reject($user->id, $request->approval_notes);

        return redirect()->route('overtime.index')
            ->with('success', 'Pengajuan lembur berhasil ditolak.');
    }

    public function pending()
    {
        $user = Auth::user();

        // Only manager and admin can see pending approvals
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            abort(403, 'Tidak memiliki akses ke halaman ini.');
        }

        $query = Overtime::with(['user', 'approver'])->where('status', 'pending');

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id');
            $query->whereIn('user_id', $subordinateIds);
        }

        $pendingOvertimes = $query->orderBy('date', 'asc')->paginate(15);

        return view('overtime.pending', compact('pendingOvertimes'));
    }

    public function report(Request $request)
    {
        $user = Auth::user();

        // Only admin and manager can access reports
        if ($user->hasRole('employee')) {
            abort(403, 'Tidak memiliki akses ke laporan.');
        }

        $query = Overtime::with(['user.department', 'approver']);

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

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $overtimes = $query->orderBy('date', 'desc')->get();

        // Summary statistics
        $summary = [
            'totalRequests' => $overtimes->count(),
            'approvedRequests' => $overtimes->where('status', 'approved')->count(),
            'pendingRequests' => $overtimes->where('status', 'pending')->count(),
            'rejectedRequests' => $overtimes->where('status', 'rejected')->count(),
            'totalHours' => $overtimes->where('status', 'approved')->sum('hours'),
            'weekdayHours' => $overtimes->where('status', 'approved')->where('type', 'weekday')->sum('hours'),
            'weekendHours' => $overtimes->where('status', 'approved')->where('type', 'weekend')->sum('hours'),
            'holidayHours' => $overtimes->where('status', 'approved')->where('type', 'holiday')->sum('hours'),
        ];

        // CSV export
        if ($request->get('export') === 'csv') {
            $filename = 'overtime_report_' . now()->format('Ymd_His') . '.csv';
            return response()->streamDownload(function () use ($overtimes) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Karyawan', 'Departemen', 'Tanggal', 'Durasi (jam)', 'Jenis', 'Status']);
                foreach ($overtimes as $ot) {
                    fputcsv($handle, [
                        optional($ot->user)->name,
                        optional(optional($ot->user)->department)->name,
                        optional($ot->date)->format('Y-m-d') ?? ($ot->date ?? ''),
                        number_format($ot->hours, 1),
                        $ot->type,
                        $ot->status,
                    ]);
                }
                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        }

        $departments = \App\Models\Department::where('is_active', true)->get();
        return view('overtime.report', compact('overtimes', 'summary', 'departments'));
    }

    public function quickSubmit(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'hours' => 'required|numeric|min:0.5|max:12',
            'reason' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $date = Carbon::parse($request->date);

        // Check if overtime already exists for this date
        $existingOvertime = Overtime::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if ($existingOvertime) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah ada pengajuan lembur untuk tanggal ini.'
            ], 400);
        }

        // Determine overtime type
        $type = $date->isWeekend() ? 'weekend' : 'weekday';

        // Check overtime limits
        $maxWeeklyOvertime = WlbSetting::get('max_overtime_hours_per_week', 10);
        $currentWeekOvertime = $user->getTotalOvertimeHoursThisWeek();

        if (($currentWeekOvertime + $request->hours) > $maxWeeklyOvertime) {
            return response()->json([
                'success' => false,
                'message' => "Melebihi batas maksimal lembur mingguan ({$maxWeeklyOvertime} jam)."
            ], 400);
        }

        // Calculate start and end time (assuming 8 hours after normal work hours)
        $startTime = '18:00';
        $endTime = Carbon::parse('18:00')->addHours($request->hours)->format('H:i');

        $overtime = Overtime::create([
            'user_id' => $user->id,
            'date' => $request->date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'hours' => $request->hours,
            'type' => $type,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan lembur berhasil disubmit.',
            'data' => $overtime
        ]);
    }

    public function getOvertimeLimits()
    {
        $user = Auth::user();
        
        $maxWeeklyOvertime = WlbSetting::get('max_overtime_hours_per_week', 10);
        $maxMonthlyOvertime = WlbSetting::get('max_overtime_hours_per_month', 40);
        
        $currentWeekOvertime = $user->getTotalOvertimeHoursThisWeek();
        $currentMonthOvertime = $user->getTotalOvertimeHoursThisMonth();

        return response()->json([
            'maxWeekly' => $maxWeeklyOvertime,
            'maxMonthly' => $maxMonthlyOvertime,
            'currentWeekly' => $currentWeekOvertime,
            'currentMonthly' => $currentMonthOvertime,
            'remainingWeekly' => max(0, $maxWeeklyOvertime - $currentWeekOvertime),
            'remainingMonthly' => max(0, $maxMonthlyOvertime - $currentMonthOvertime),
        ]);
    }
}

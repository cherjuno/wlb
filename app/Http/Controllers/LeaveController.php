<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Leave::with(['user', 'approver']);

        // Filter berdasarkan role
        if ($user->hasRole('employee')) {
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('manager')) {
            // Manager bisa melihat leaves dari subordinates dan dirinya sendiri
            $subordinateIds = $user->subordinates()->pluck('id')->push($user->id);
            $query->whereIn('user_id', $subordinateIds);
        }
        // Admin bisa melihat semua

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
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15);

        // Data untuk filter dropdown
        $users = collect();
        if ($user->hasRole('admin')) {
            $users = User::where('is_active', true)->get();
        } elseif ($user->hasRole('manager')) {
            $users = $user->subordinates()->where('is_active', true)->get();
        }

        return view('leave.index', compact('leaves', 'users'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Calculate leave balance
        $leaveBalance = [
            'annual_quota' => $user->annual_leave_quota ?? 12,
            'used' => $user->used_leave ?? 0,
            'remaining' => $user->remaining_leave ?? 12
        ];
        
        return view('leave.create', compact('user', 'leaveBalance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:annual,sick,maternity,emergency,unpaid',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

        // Calculate days requested
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $daysRequested = $this->calculateBusinessDays($startDate, $endDate);

        // Check if user has enough leave balance for annual leave
        if ($request->type === 'annual' && $daysRequested > $user->remaining_leave) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Sisa cuti Anda tidak mencukupi. Sisa cuti: {$user->remaining_leave} hari.");
        }

        // Handle document uploads
        $documentPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('leave-documents', 'public');
                $documentPaths[] = $path;
            }
        }

        $leave = Leave::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_requested' => $daysRequested,
            'reason' => $request->reason,
            'documents' => $documentPaths,
            'status' => 'pending',
        ]);

        return redirect()->route('leave.index')
            ->with('success', 'Pengajuan cuti berhasil disubmit dan menunggu persetujuan.');
    }

    public function show(Leave $leave)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->hasRole('employee') && $leave->user_id !== $user->id) {
            abort(403, 'Tidak memiliki akses ke data ini.');
        }

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id')->push($user->id);
            if (!$subordinateIds->contains($leave->user_id)) {
                abort(403, 'Tidak memiliki akses ke data ini.');
            }
        }

        return view('leave.show', compact('leave'));
    }

    public function edit(Leave $leave)
    {
        $user = Auth::user();

        // Only allow editing own leave and if still pending
        if ($user->hasRole('employee') && ($leave->user_id !== $user->id || $leave->status !== 'pending')) {
            abort(403, 'Tidak dapat mengedit pengajuan cuti ini.');
        }

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id')->push($user->id);
            if (!$subordinateIds->contains($leave->user_id) || $leave->status !== 'pending') {
                abort(403, 'Tidak dapat mengedit pengajuan cuti ini.');
            }
        }

        return view('leave.edit', compact('leave'));
    }

    public function update(Request $request, Leave $leave)
    {
        $request->validate([
            'type' => 'required|in:annual,sick,maternity,emergency,unpaid',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

        // Authorization check
        if ($user->hasRole('employee') && ($leave->user_id !== $user->id || $leave->status !== 'pending')) {
            abort(403, 'Tidak dapat mengedit pengajuan cuti ini.');
        }

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id')->push($user->id);
            if (!$subordinateIds->contains($leave->user_id) || $leave->status !== 'pending') {
                abort(403, 'Tidak dapat mengedit pengajuan cuti ini.');
            }
        }

        // Calculate new days requested
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $daysRequested = $this->calculateBusinessDays($startDate, $endDate);

        // Check leave balance for annual leave
        if ($request->type === 'annual' && $daysRequested > $leave->user->remaining_leave) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Sisa cuti tidak mencukupi. Sisa cuti: {$leave->user->remaining_leave} hari.");
        }

        // Handle new document uploads
        $documentPaths = $leave->documents ?? [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('leave-documents', 'public');
                $documentPaths[] = $path;
            }
        }

        $leave->update([
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_requested' => $daysRequested,
            'reason' => $request->reason,
            'documents' => $documentPaths,
        ]);

        return redirect()->route('leave.index')
            ->with('success', 'Pengajuan cuti berhasil diperbarui.');
    }

    public function destroy(Leave $leave)
    {
        $user = Auth::user();

        // Only allow deleting own leave if still pending
        if ($user->hasRole('employee') && ($leave->user_id !== $user->id || $leave->status !== 'pending')) {
            abort(403, 'Tidak dapat menghapus pengajuan cuti ini.');
        }

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id')->push($user->id);
            if (!$subordinateIds->contains($leave->user_id) || $leave->status !== 'pending') {
                abort(403, 'Tidak dapat menghapus pengajuan cuti ini.');
            }
        }

        // Delete uploaded documents
        if ($leave->documents) {
            foreach ($leave->documents as $document) {
                Storage::disk('public')->delete($document);
            }
        }

        $leave->delete();

        return redirect()->route('leave.index')
            ->with('success', 'Pengajuan cuti berhasil dihapus.');
    }

    public function approve(Request $request, Leave $leave)
    {
        $user = Auth::user();

        // Only manager and admin can approve
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            abort(403, 'Tidak memiliki akses untuk menyetujui cuti.');
        }

        // Manager can only approve subordinates' leaves
        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id');
            if (!$subordinateIds->contains($leave->user_id)) {
                abort(403, 'Tidak dapat menyetujui cuti karyawan ini.');
            }
        }

        if (!$leave->canBeApproved()) {
            return redirect()->back()
                ->with('error', 'Pengajuan cuti ini tidak dapat disetujui.');
        }

        $request->validate([
            'approval_notes' => 'nullable|string|max:500',
        ]);

        $leave->approve($user->id, $request->approval_notes);

        return redirect()->route('leave.index')
            ->with('success', 'Pengajuan cuti berhasil disetujui.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $user = Auth::user();

        // Only manager and admin can reject
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            abort(403, 'Tidak memiliki akses untuk menolak cuti.');
        }

        // Manager can only reject subordinates' leaves
        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id');
            if (!$subordinateIds->contains($leave->user_id)) {
                abort(403, 'Tidak dapat menolak cuti karyawan ini.');
            }
        }

        if (!$leave->canBeApproved()) {
            return redirect()->back()
                ->with('error', 'Pengajuan cuti ini tidak dapat ditolak.');
        }

        $request->validate([
            'approval_notes' => 'required|string|max:500',
        ]);

        $leave->reject($user->id, $request->approval_notes);

        return redirect()->route('leave.index')
            ->with('success', 'Pengajuan cuti berhasil ditolak.');
    }

    public function pending()
    {
        $user = Auth::user();

        // Only manager and admin can see pending approvals
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            abort(403, 'Tidak memiliki akses ke halaman ini.');
        }

        $query = Leave::with(['user', 'approver'])->where('status', 'pending');

        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id');
            $query->whereIn('user_id', $subordinateIds);
        }

        $pendingLeaves = $query->orderBy('created_at', 'asc')->paginate(15);

        return view('leave.pending', compact('pendingLeaves'));
    }

    public function report(Request $request)
    {
        $user = Auth::user();

        // Only admin and manager can access reports
        if ($user->hasRole('employee')) {
            abort(403, 'Tidak memiliki akses ke laporan.');
        }

        $query = Leave::with(['user.department', 'approver']);

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
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $leaves = $query->orderBy('start_date', 'desc')->get();

        // CSV export
        if ($request->get('export') === 'csv') {
            $filename = 'leave_report_' . now()->format('Ymd_His') . '.csv';
            return response()->streamDownload(function () use ($leaves) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Karyawan', 'Departemen', 'Tipe', 'Mulai', 'Selesai', 'Hari', 'Status']);
                foreach ($leaves as $l) {
                    fputcsv($handle, [
                        optional($l->user)->name,
                        optional(optional($l->user)->department)->name,
                        $l->type,
                        optional($l->start_date)->format('Y-m-d') ?? ($l->start_date ?? ''),
                        optional($l->end_date)->format('Y-m-d') ?? ($l->end_date ?? ''),
                        $l->days_requested,
                        $l->status,
                    ]);
                }
                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        }

        // Summary statistics
        $summary = [
            'totalRequests' => $leaves->count(),
            'approvedRequests' => $leaves->where('status', 'approved')->count(),
            'pendingRequests' => $leaves->where('status', 'pending')->count(),
            'rejectedRequests' => $leaves->where('status', 'rejected')->count(),
            'totalDays' => $leaves->where('status', 'approved')->sum('days_requested'),
            'annualLeaveDays' => $leaves->where('status', 'approved')->where('type', 'annual')->sum('days_requested'),
            'sickLeaveDays' => $leaves->where('status', 'approved')->where('type', 'sick')->sum('days_requested'),
        ];

        $departments = Department::where('is_active', true)->get();
        return view('leave.report', compact('leaves', 'summary', 'departments'));
    }

    private function calculateBusinessDays($startDate, $endDate)
    {
        $days = 0;
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            if ($current->isWeekday()) {
                $days++;
            }
            $current->addDay();
        }
        
        return $days;
    }
}

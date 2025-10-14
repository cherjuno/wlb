<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|manager']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = User::with(['department', 'position', 'manager']);

        // Manager hanya bisa melihat subordinates
        if ($user->hasRole('manager')) {
            $subordinateIds = $user->subordinates()->pluck('id');
            $query->whereIn('id', $subordinateIds);
        }

        // Filter berdasarkan parameter
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15);

        // Data untuk filter dropdown
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();

        return view('users.index', compact('users', 'departments', 'positions'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        $managers = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['manager', 'admin']);
        })->where('is_active', true)->get();
        $roles = Role::all();

        return view('users.create', compact('departments', 'positions', 'managers', 'roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'employee_id' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'hire_date' => 'required|date',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'manager_id' => 'nullable|exists:users,id',
            'annual_leave_quota' => 'required|integer|min:0|max:30',
            'salary' => 'nullable|numeric|min:0',
            'role' => 'required|in:admin,manager,employee',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'employee_id' => $request->employee_id,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'hire_date' => $request->hire_date,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'address' => $request->address,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'manager_id' => $request->manager_id,
            'annual_leave_quota' => $request->annual_leave_quota,
            'remaining_leave' => $request->annual_leave_quota,
            'salary' => $request->salary,
            'is_active' => $request->is_active ? true : false,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load(['department', 'position', 'manager', 'subordinates']);

        // WLB metrics untuk user ini
        $wlbScore = \App\Helpers\WlbHelper::calculateWlbScore($user->id);
        $wlbStatus = \App\Helpers\WlbHelper::getWlbStatus($wlbScore);
        $recommendations = \App\Helpers\WlbHelper::generateRecommendations($user->id);

        return view('users.show', compact('user', 'wlbScore', 'wlbStatus', 'recommendations'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        $managers = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['manager', 'admin']);
        })->where('is_active', true)->where('id', '!=', $user->id)->get();
        $roles = Role::all();

        return view('users.edit', compact('user', 'departments', 'positions', 'managers', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'employee_id' => 'required|string|max:50|unique:users,employee_id,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'hire_date' => 'required|date',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'manager_id' => 'nullable|exists:users,id',
            'annual_leave_quota' => 'required|integer|min:0|max:30',
            'is_active' => 'required|boolean',
            'role' => 'required|in:admin,manager,employee',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'employee_id' => $request->employee_id,
            'phone' => $request->phone,
            'hire_date' => $request->hire_date,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'address' => $request->address,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'manager_id' => $request->manager_id,
            'annual_leave_quota' => $request->annual_leave_quota,
            'is_active' => $request->is_active,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Update role
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Tidak bisa menghapus diri sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        // Soft delete dengan mengubah status aktif
        $user->update(['is_active' => false]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dinonaktifkan.');
    }
}

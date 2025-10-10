@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
            <p class="mt-2 text-sm text-gray-700">Manage system users and their access permissions.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            @can('create', App\Models\User::class)
                <a href="{{ route('users.create') }}" class="block rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    Add User
                </a>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, email, or employee ID" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                <select name="department_id" id="department_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="position_id" class="block text-sm font-medium text-gray-700">Position</label>
                <select name="position_id" id="position_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Positions</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                            {{ $position->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="is_active" id="is_active" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="sm:col-span-2 lg:col-span-4">
                <button type="submit" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    Filter
                </button>
                <a href="{{ route('users.index') }}" class="ml-2 rounded-md bg-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Users List -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead>
                        <tr class="divide-x divide-gray-200">
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Employee</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Department</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Position</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Manager</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">WLB Score</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="divide-x divide-gray-200">
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center mr-3">
                                            <span class="text-sm font-medium text-white">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-gray-500">{{ $user->email }}</div>
                                            <div class="text-xs text-gray-400">ID: {{ $user->employee_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    {{ $user->department->name ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    {{ $user->position->name ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    {{ $user->manager->name ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm">
                                    @foreach($user->roles as $role)
                                        <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5
                                            @if($role->name == 'admin') bg-red-100 text-red-800
                                            @elseif($role->name == 'manager') bg-blue-100 text-blue-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm">
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5
                                        @if($user->is_active) bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    @php
                                        $wlbScore = \App\Helpers\WlbHelper::calculateWlbScore($user->id);
                                    @endphp
                                    <div class="flex items-center">
                                        <span class="text-lg font-bold 
                                            @if($wlbScore >= 70) text-green-600
                                            @elseif($wlbScore >= 50) text-yellow-600
                                            @else text-red-600 @endif">
                                            {{ $wlbScore }}
                                        </span>
                                        <span class="text-xs text-gray-500 ml-1">/100</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm font-medium">
                                    @can('view', $user)
                                        <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    @endcan
                                    
                                    @can('update', $user)
                                        <a href="{{ route('users.edit', $user) }}" class="ml-2 text-amber-600 hover:text-amber-900">Edit</a>
                                    @endcan
                                    
                                    @can('delete', $user)
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to deactivate this user?')">
                                                    Deactivate
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
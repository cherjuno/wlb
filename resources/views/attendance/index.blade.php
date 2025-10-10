@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-bold text-gray-900">Attendance Management</h1>
            <p class="mt-2 text-sm text-gray-700">Monitor daily attendance records and work hours.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            @if(!auth()->user()->hasRole('admin'))
                <a href="{{ route('attendance.create') }}" class="block rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    New Attendance
                </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
        <form method="GET" action="{{ route('attendance.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @if(auth()->user()->hasRole(['admin', 'manager']))
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700">Employee</label>
                    <select name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Employees</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">To Date</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                </select>
            </div>
            
            <div class="sm:col-span-2 lg:col-span-4">
                <button type="submit" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    Filter
                </button>
                <a href="{{ route('attendance.index') }}" class="ml-2 rounded-md bg-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance List -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead>
                        <tr class="divide-x divide-gray-200">
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                            @if(auth()->user()->hasRole(['admin', 'manager']))
                                <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Employee</th>
                            @endif
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Check In</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Check Out</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Work Hours</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($attendances as $attendance)
                            <tr class="divide-x divide-gray-200">
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}
                                </td>
                                @if(auth()->user()->hasRole(['admin', 'manager']))
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                <span class="text-xs font-medium text-gray-600">{{ substr($attendance->user->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $attendance->user->name }}</div>
                                                <div class="text-gray-500">{{ $attendance->user->employee_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    @if($attendance->check_in && $attendance->check_out)
                                        {{ \Carbon\Carbon::parse($attendance->check_in)->diffInHours(\Carbon\Carbon::parse($attendance->check_out)) }}h
                                        {{ \Carbon\Carbon::parse($attendance->check_in)->diffInMinutes(\Carbon\Carbon::parse($attendance->check_out)) % 60 }}m
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm">
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5
                                        @if($attendance->status == 'present') bg-green-100 text-green-800
                                        @elseif($attendance->status == 'late') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm font-medium">
                                    <a href="{{ route('attendance.show', $attendance) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    @if(auth()->user()->hasRole(['admin', 'manager']) || auth()->id() == $attendance->user_id)
                                        <a href="{{ route('attendance.edit', $attendance) }}" class="ml-2 text-amber-600 hover:text-amber-900">Edit</a>
                                    @endif
                                    @if(auth()->user()->hasRole('admin'))
                                        <form action="{{ route('attendance.destroy', $attendance) }}" method="POST" class="inline ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No attendance records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($attendances->hasPages())
                <div class="mt-6">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
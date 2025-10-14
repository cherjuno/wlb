@extends('layouts.app')

@section('content')
<style>
/* Force text visibility in form elements */
.leave-form-control {
    color: #111827 !important;
    background-color: #ffffff !important;
}
.leave-form-control option {
    color: #111827 !important;
    background-color: #ffffff !important;
}
.leave-form-control:focus {
    color: #111827 !important;
}
</style>

<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-bold text-gray-900">Leave Management</h1>
            <p class="mt-2 text-sm text-gray-700">Manage leave requests and approvals.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none space-x-2">
            <a href="{{ route('leave.create') }}" class="inline-flex rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                Request Leave
            </a>
            @if(auth()->user()->hasRole(['admin', 'manager']))
                <a href="{{ route('leave.pending') }}" class="inline-flex rounded-md bg-amber-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-amber-500">
                    Pending Approvals
                    @php
                        $pendingLeaves = \App\Models\Leave::where('status', 'pending');
                        if(auth()->user()->hasRole('manager')) {
                            $subordinateIds = auth()->user()->subordinates()->pluck('id');
                            $pendingLeaves = $pendingLeaves->whereIn('user_id', $subordinateIds);
                        }
                        $pendingCount = $pendingLeaves->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="ml-1 bg-amber-200 text-amber-800 rounded-full px-2 py-0.5 text-xs">{{ $pendingCount }}</span>
                    @endif
                </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
        <form method="GET" action="{{ route('leave.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @if(auth()->user()->hasRole(['admin', 'manager']))
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700">Employee</label>
                    <select name="user_id" id="user_id" class="leave-form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" style="color: #111827 !important; background-color: #ffffff !important;">
                        <option value="" style="color: #111827 !important; background-color: #ffffff !important;">All Employees</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }} style="color: #111827 !important; background-color: #ffffff !important;">
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Leave Type</label>
                <select name="type" id="type" class="leave-form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" style="color: #111827 !important; background-color: #ffffff !important;">
                    <option value="" style="color: #111827 !important; background-color: #ffffff !important;">All Types</option>
                    <option value="annual" {{ request('type') == 'annual' ? 'selected' : '' }} style="color: #111827 !important; background-color: #ffffff !important;">Annual Leave</option>
                    <option value="sick" {{ request('type') == 'sick' ? 'selected' : '' }} style="color: #111827 !important; background-color: #ffffff !important;">Sick Leave</option>
                    <option value="emergency" {{ request('type') == 'emergency' ? 'selected' : '' }} style="color: #111827 !important; background-color: #ffffff !important;">Emergency Leave</option>
                    <option value="maternity" {{ request('type') == 'maternity' ? 'selected' : '' }} style="color: #111827 !important; background-color: #ffffff !important;">Maternity Leave</option>
                    <option value="paternity" {{ request('type') == 'paternity' ? 'selected' : '' }} style="color: #111827 !important; background-color: #ffffff !important;">Paternity Leave</option>
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="leave-form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" style="color: #111827 !important; background-color: #ffffff !important;">
                    <option value="" style="color: #111827 !important; background-color: #ffffff !important;">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }} style="color: #111827 !important; background-color: #ffffff !important;">Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }} style="color: #111827 !important; background-color: #ffffff !important;">Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }} style="color: #111827 !important; background-color: #ffffff !important;">Rejected</option>
                </select>
            </div>
            
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="leave-form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" style="color: #111827 !important; background-color: #ffffff !important;">
            </div>
            
            <div class="sm:col-span-2 lg:col-span-4">
                <button type="submit" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    Filter
                </button>
                <a href="{{ route('leave.index') }}" class="ml-2 rounded-md bg-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Leave List -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead>
                        <tr class="divide-x divide-gray-200">
                            @if(auth()->user()->hasRole(['admin', 'manager']))
                                <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Employee</th>
                            @endif
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Type</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Dates</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Days</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Approved By</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($leaves as $leave)
                            <tr class="divide-x divide-gray-200">
                                @if(auth()->user()->hasRole(['admin', 'manager']))
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                <span class="text-xs font-medium text-gray-600">{{ substr($leave->user->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $leave->user->name }}</div>
                                                <div class="text-gray-500">{{ $leave->user->employee_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-blue-100 text-blue-800">
                                        {{ ucfirst($leave->type) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    <div>{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</div>
                                    <div class="text-gray-500">to {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    {{ $leave->days_requested }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm">
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5
                                        @if($leave->status == 'approved') bg-green-100 text-green-800
                                        @elseif($leave->status == 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($leave->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    {{ $leave->approver ? $leave->approver->name : '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm font-medium">
                                    <a href="{{ route('leave.show', $leave) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    
                                    @if($leave->status == 'pending')
                                        @if(auth()->user()->hasRole(['admin', 'manager']) && auth()->id() != $leave->user_id)
                                            <form action="{{ route('leave.approve', $leave) }}" method="POST" class="inline ml-2">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                            </form>
                                            <form action="{{ route('leave.reject', $leave) }}" method="POST" class="inline ml-2">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                            </form>
                                        @endif
                                        
                                        @if(auth()->id() == $leave->user_id)
                                            <a href="{{ route('leave.edit', $leave) }}" class="ml-2 text-amber-600 hover:text-amber-900">Edit</a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No leave requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($leaves->hasPages())
                <div class="mt-6">
                    {{ $leaves->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
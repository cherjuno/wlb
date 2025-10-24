@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-bold text-gray-900">Overtime Management</h1>
            <p class="mt-2 text-sm text-gray-700">Manage overtime requests and approvals.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none space-x-2">
            <a href="{{ route('overtime.create') }}" class="inline-flex rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                Submit Overtime
            </a>
            @if(auth()->user()->hasRole(['admin', 'manager']))
                <a href="{{ route('overtime.pending') }}" class="inline-flex rounded-md bg-amber-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-amber-500">
                    Pending Approvals
                    @php
                        $pendingOvertimes = \App\Models\Overtime::where('status', 'pending');
                        if(auth()->user()->hasRole('manager')) {
                            $subordinateIds = auth()->user()->subordinates()->pluck('id');
                            $pendingOvertimes = $pendingOvertimes->whereIn('user_id', $subordinateIds);
                        }
                        $pendingCount = $pendingOvertimes->count();
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
        <form method="GET" action="{{ route('overtime.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
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
                <label for="type" class="block text-sm font-medium text-gray-700">Overtime Type</label>
                <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <option value="regular" {{ request('type') == 'regular' ? 'selected' : '' }}>Regular</option>
                    <option value="weekend" {{ request('type') == 'weekend' ? 'selected' : '' }}>Weekend</option>
                    <option value="holiday" {{ request('type') == 'holiday' ? 'selected' : '' }}>Holiday</option>
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div class="sm:col-span-2 lg:col-span-4">
                <button type="submit" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    Filter
                </button>
                <a href="{{ route('overtime.index') }}" class="ml-2 rounded-md bg-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Overtime List -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead>
                        <tr class="divide-x divide-gray-200">
                            @if(auth()->user()->hasRole(['admin', 'manager']))
                                <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Employee</th>
                            @endif
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Type</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Hours</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Description</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Approved By</th>
                            <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($overtimes as $overtime)
                            <tr class="divide-x divide-gray-200">
                                @if(auth()->user()->hasRole(['admin', 'manager']))
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                <span class="text-xs font-medium text-gray-600">{{ substr($overtime->user->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $overtime->user->name }}</div>
                                                <div class="text-gray-500">{{ $overtime->user->employee_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($overtime->date)->format('M d, Y') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-purple-100 text-purple-800">
                                        {{ ucfirst($overtime->type) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    {{ $overtime->hours }}h
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 max-w-xs truncate">
                                    {{ $overtime->description }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm">
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5
                                        @if($overtime->status == 'approved') bg-green-100 text-green-800
                                        @elseif($overtime->status == 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($overtime->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                                    {{ $overtime->approver ? $overtime->approver->name : '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm font-medium">
                                    <a href="{{ route('overtime.show', $overtime) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    
                                    @if($overtime->status == 'pending')
                                        @if(auth()->user()->hasRole(['admin', 'manager']) && auth()->id() != $overtime->user_id)
                                            <form action="{{ route('overtime.approve', $overtime) }}" method="POST" class="inline ml-2">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                            </form>
                                            <form action="{{ route('overtime.reject', $overtime) }}" method="POST" class="inline ml-2" 
                                                  onsubmit="return confirm('Are you sure you want to reject this overtime request?')">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1 rounded border border-red-300 hover:bg-red-50">
                                                    Reject
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if(auth()->id() == $overtime->user_id)
                                            <a href="{{ route('overtime.edit', $overtime) }}" class="ml-2 text-amber-600 hover:text-amber-900">Edit</a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No overtime requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($overtimes->hasPages())
                <div class="mt-6">
                    {{ $overtimes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-2xl p-6">
        <h1 class="text-2xl font-bold">Laporan Cuti</h1>
        <p class="text-amber-100">Ringkasan pengajuan dan pemakaian cuti</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow p-6">
        <form method="GET" action="{{ route('leave.report') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Department</label>
                <select name="department_id" class="w-full rounded-lg border-gray-300">
                    <option value="">Semua</option>
                    @foreach(($departments ?? []) as $dept)
                        <option value="{{ $dept->id }}" @selected(request('department_id')==$dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tipe</label>
                <select name="type" class="w-full rounded-lg border-gray-300">
                    <option value="">Semua</option>
                    @foreach(['annual'=>'Annual','sick'=>'Sick','unpaid'=>'Unpaid'] as $k=>$v)
                        <option value="{{ $k }}" @selected(request('type')==$k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300">
                    <option value="">Semua</option>
                    @foreach(['approved'=>'Approved','pending'=>'Pending','rejected'=>'Rejected'] as $k=>$v)
                        <option value="{{ $k }}" @selected(request('status')==$k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Dari</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-300" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai</label>
                <div class="flex gap-2">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-300" />
                    <button class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg">Filter</button>
                    <a href="{{ route('leave.report', array_merge(request()->all(), ['export' => 'csv'])) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700">Export CSV</a>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow p-6">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="text-center p-4 rounded-xl bg-indigo-50">
                <div class="text-xs text-gray-600">Total Request</div>
                <div class="text-2xl font-bold text-indigo-700">{{ $summary['totalRequests'] }}</div>
            </div>
            <div class="text-center p-4 rounded-xl bg-green-50">
                <div class="text-xs text-gray-600">Approved</div>
                <div class="text-2xl font-bold text-green-700">{{ $summary['approvedRequests'] }}</div>
            </div>
            <div class="text-center p-4 rounded-xl bg-yellow-50">
                <div class="text-xs text-gray-600">Pending</div>
                <div class="text-2xl font-bold text-yellow-700">{{ $summary['pendingRequests'] }}</div>
            </div>
            <div class="text-center p-4 rounded-xl bg-red-50">
                <div class="text-xs text-gray-600">Rejected</div>
                <div class="text-2xl font-bold text-red-700">{{ $summary['rejectedRequests'] }}</div>
            </div>
            <div class="text-center p-4 rounded-xl bg-blue-50">
                <div class="text-xs text-gray-600">Total Hari</div>
                <div class="text-2xl font-bold text-blue-700">{{ $summary['totalDays'] }}</div>
            </div>
            <div class="text-center p-4 rounded-xl bg-purple-50">
                <div class="text-xs text-gray-600">Annual Leave</div>
                <div class="text-2xl font-bold text-purple-700">{{ $summary['annualLeaveDays'] }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Karyawan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Periode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Hari</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($leaves as $l)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-900">{{ $l->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $l->user->department->name ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-3 capitalize">{{ $l->type }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($l->start_date)->format('d M Y') }} — {{ \Carbon\Carbon::parse($l->end_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $l->days_requested }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                @if($l->status === 'approved') bg-green-100 text-green-800
                                @elseif($l->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($l->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
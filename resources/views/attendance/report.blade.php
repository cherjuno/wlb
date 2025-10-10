@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data>
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl p-6">
        <h1 class="text-2xl font-bold">Laporan Absensi</h1>
        <p class="text-blue-100">Ringkasan kehadiran dan jam kerja</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow p-6">
        <form method="GET" action="{{ route('attendance.report') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-300" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-300" />
            </div>
            <div class="flex items-end gap-2">
                <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Filter</button>
                <a href="{{ route('attendance.report', array_merge(request()->all(), ['export' => 'csv'])) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700">Export CSV</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow p-6">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="text-center p-4 rounded-xl bg-blue-50">
                <div class="text-xs text-gray-600">Total Hari</div>
                <div class="text-2xl font-bold text-blue-700">{{ $summary['totalDays'] }}</div>
            </div>
            <div class="text-center p-4 rounded-xl bg-green-50">
                <div class="text-xs text-gray-600">Hadir</div>
                <div class="text-2xl font-bold text-green-700">{{ $summary['presentDays'] }}</div>
            </div>
            <div class="text-center p-4 rounded-xl bg-yellow-50">
                <div class="text-xs text-gray-600">Terlambat</div>
                <div class="text-2xl font-bold text-yellow-700">{{ $summary['lateDays'] }}</div>
            </div>
            <div class="text-center p-4 rounded-xl bg-red-50">
                <div class="text-xs text-gray-600">Absen</div>
                <div class="text-2xl font-bold text-red-700">{{ $summary['absentDays'] }}</div>
            </div>
            <div class="text-center p-4 rounded-xl bg-indigo-50">
                <div class="text-xs text-gray-600">Total Jam</div>
                <div class="text-2xl font-bold text-indigo-700">{{ number_format($summary['totalWorkHours'], 1) }}</div>
            </div>
            <div class="text-center p-4 rounded-xl bg-purple-50">
                <div class="text-xs text-gray-600">Rata-rata Jam</div>
                <div class="text-2xl font-bold text-purple-700">{{ number_format($summary['avgWorkHours'], 1) }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Check-in</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Check-out</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Jam Kerja</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($attendances as $a)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($a->date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $a->check_in ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $a->check_out ?? '—' }}</td>
                        <td class="px-4 py-3">{{ number_format($a->work_hours, 1) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                @if($a->status === 'present') bg-green-100 text-green-800 
                                @elseif($a->status === 'late') bg-yellow-100 text-yellow-800 
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($a->status) }}
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
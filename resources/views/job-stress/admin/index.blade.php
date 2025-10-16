@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white rounded-3xl p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/0 to-white/10"></div>
        <div class="absolute -top-10 -right-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Job Stress Scale Management</h1>
                <p class="mt-2 text-purple-100">Monitor dan kelola tingkat stres karyawan</p>
            </div>
            <div class="text-7xl opacity-20 select-none">📊</div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <form method="GET" action="{{ route('job-stress.admin.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="month" class="block text-sm font-medium text-gray-600 mb-2">Bulan</label>
                <select name="month" id="month" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            
            <div>
                <label for="year" class="block text-sm font-medium text-gray-600 mb-2">Tahun</label>
                <select name="year" id="year" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                Filter
            </button>
            
            <a href="{{ route('job-stress.admin.report', ['month' => $currentMonth, 'year' => $currentYear]) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                📋 Laporan Detail
            </a>
        </form>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Karyawan</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalEmployees }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Form Terisi</p>
                    <p class="text-3xl font-bold text-green-600">{{ $completedForms }}</p>
                    <p class="text-sm text-gray-500">{{ $completionRate }}% completion</p>
                </div>
                <div class="p-3 bg-green-50 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Rata-rata Skor</p>
                    <p class="text-3xl font-bold 
                        @if($averageScore <= 20) text-green-600
                        @elseif($averageScore <= 35) text-yellow-600
                        @else text-red-600 @endif">{{ number_format($averageScore, 1) }}</p>
                    <p class="text-sm text-gray-500">dari 50</p>
                </div>
                <div class="p-3 
                    @if($averageScore <= 20) bg-green-50
                    @elseif($averageScore <= 35) bg-yellow-50
                    @else bg-red-50 @endif rounded-lg">
                    <svg class="w-6 h-6 
                        @if($averageScore <= 20) text-green-600
                        @elseif($averageScore <= 35) text-yellow-600
                        @else text-red-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Stres Tinggi</p>
                    <p class="text-3xl font-bold text-red-600">{{ $stressLevelStats['high'] }}</p>
                    <p class="text-sm text-gray-500">perlu perhatian</p>
                </div>
                <div class="p-3 bg-red-50 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Stress Level Distribution --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Tingkat Stres</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-800">Stres Rendah (10-20)</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stressLevelStats['low'] }}</p>
                    </div>
                    <span class="text-3xl">😌</span>
                </div>
            </div>
            
            <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Stres Sedang (21-35)</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stressLevelStats['moderate'] }}</p>
                    </div>
                    <span class="text-3xl">😐</span>
                </div>
            </div>
            
            <div class="bg-red-50 rounded-xl p-4 border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-800">Stres Tinggi (36-50)</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stressLevelStats['high'] }}</p>
                    </div>
                    <span class="text-3xl">😰</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Missing Employees Alert --}}
    @if(count($missingEmployees) > 0)
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">⚠️ Karyawan Belum Mengisi Form</h3>
            <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full">
                {{ count($missingEmployees) }} orang
            </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($missingEmployees as $employee)
                <div class="flex items-center p-3 bg-red-50 rounded-lg border border-red-200">
                    <div class="flex-shrink-0 w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-medium">{{ substr($employee->name, 0, 1) }}</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $employee->name }}</p>
                        <p class="text-xs text-gray-600">{{ $employee->department->name ?? 'No Department' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Completed Forms Table --}}
    @if(count($stressScales) > 0)
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Form yang Telah Terisi</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departemen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Skor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat Stres</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Isi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stressScales as $scale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">{{ substr($scale->user->name, 0, 1) }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $scale->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $scale->user->employee_id ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $scale->user->department->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-lg font-bold 
                                    @if($scale->total_score <= 20) text-green-600
                                    @elseif($scale->total_score <= 35) text-yellow-600
                                    @else text-red-600 @endif">
                                    {{ $scale->total_score }}
                                </span>
                                <span class="text-sm text-gray-500">/50</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($scale->stress_level === 'low') bg-green-100 text-green-800
                                    @elseif($scale->stress_level === 'moderate') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $scale->getStressLevelIndonesian() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $scale->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('job-stress.show', $scale) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 font-medium">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-8 text-center">
            <div class="text-6xl mb-4">📋</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Form yang Terisi</h3>
            <p class="text-gray-600">Tidak ada karyawan yang mengisi Job Stress Scale untuk bulan {{ DateTime::createFromFormat('!m', $currentMonth)->format('F') }} {{ $currentYear }}.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Auto-refresh every 5 minutes to check for new submissions
setInterval(function() {
    window.location.reload();
}, 300000);
</script>
@endpush
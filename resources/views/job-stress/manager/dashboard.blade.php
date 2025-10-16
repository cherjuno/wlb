@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 text-white rounded-3xl p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/0 to-white/10"></div>
        <div class="absolute -top-10 -right-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Team Stress Monitoring</h1>
                <p class="mt-2 text-purple-100">Monitor tingkat stres tim Anda</p>
            </div>
            <div class="text-7xl opacity-20 select-none">👥</div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <form method="GET" action="{{ route('job-stress.manager.dashboard') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="month" class="block text-sm font-medium text-gray-600 mb-2">Bulan</label>
                <select name="month" id="month" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            
            <div>
                <label for="year" class="block text-sm font-medium text-gray-600 mb-2">Tahun</label>
                <select name="year" id="year" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                Filter
            </button>
        </form>
    </div>

    {{-- Team Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tim</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalTeamMembers }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Rata-rata Stres Tim</p>
                    <p class="text-3xl font-bold 
                        @if($teamAverageScore <= 20) text-green-600
                        @elseif($teamAverageScore <= 35) text-yellow-600
                        @else text-red-600 @endif">{{ number_format($teamAverageScore, 1) }}</p>
                    <p class="text-sm text-gray-500">dari 50</p>
                </div>
                <div class="p-3 
                    @if($teamAverageScore <= 20) bg-green-50
                    @elseif($teamAverageScore <= 35) bg-yellow-50
                    @else bg-red-50 @endif rounded-lg">
                    <svg class="w-6 h-6 
                        @if($teamAverageScore <= 20) text-green-600
                        @elseif($teamAverageScore <= 35) text-yellow-600
                        @else text-red-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Tim Stres Tinggi</p>
                    <p class="text-3xl font-bold text-red-600">{{ $teamStressLevelStats['high'] }}</p>
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

    {{-- Team Stress Level Distribution --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Tingkat Stres Tim</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-800">Stres Rendah (10-20)</p>
                        <p class="text-2xl font-bold text-green-600">{{ $teamStressLevelStats['low'] }}</p>
                        <p class="text-xs text-green-600">
                            {{ $totalTeamMembers > 0 ? round(($teamStressLevelStats['low']/$totalTeamMembers)*100, 1) : 0 }}% dari tim
                        </p>
                    </div>
                    <span class="text-3xl">😌</span>
                </div>
            </div>
            
            <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Stres Sedang (21-35)</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $teamStressLevelStats['moderate'] }}</p>
                        <p class="text-xs text-yellow-600">
                            {{ $totalTeamMembers > 0 ? round(($teamStressLevelStats['moderate']/$totalTeamMembers)*100, 1) : 0 }}% dari tim
                        </p>
                    </div>
                    <span class="text-3xl">😐</span>
                </div>
            </div>
            
            <div class="bg-red-50 rounded-xl p-4 border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-800">Stres Tinggi (36-50)</p>
                        <p class="text-2xl font-bold text-red-600">{{ $teamStressLevelStats['high'] }}</p>
                        <p class="text-xs text-red-600">
                            {{ $totalTeamMembers > 0 ? round(($teamStressLevelStats['high']/$totalTeamMembers)*100, 1) : 0 }}% dari tim
                        </p>
                    </div>
                    <span class="text-3xl">😰</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Missing Team Members Alert --}}
    @if(count($missingTeamMembers) > 0)
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">⚠️ Tim Belum Mengisi Form</h3>
            <span class="bg-orange-100 text-orange-800 text-sm font-medium px-3 py-1 rounded-full">
                {{ count($missingTeamMembers) }} orang
            </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($missingTeamMembers as $member)
                <div class="flex items-center p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="flex-shrink-0 w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-medium">{{ substr($member->name, 0, 1) }}</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                        <p class="text-xs text-gray-600">{{ $member->employee_id ?? 'No ID' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-sm text-blue-800">
                💡 <strong>Tip:</strong> Ingatkan anggota tim untuk mengisi Job Stress Scale setiap bulan. 
                Form ini membantu memantau kesejahteraan tim dan mencegah burnout.
            </p>
        </div>
    </div>
    @endif

    {{-- Team Stress Levels Details --}}
    @if(count($teamStressScales) > 0)
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Tingkat Stres Tim</h3>
        
        <div class="space-y-4">
            @foreach($teamStressScales->sortBy('total_score', SORT_REGULAR, true) as $scale)
                <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow
                    @if($scale->stress_level === 'high') bg-red-50 border-red-200
                    @elseif($scale->stress_level === 'moderate') bg-yellow-50 border-yellow-200
                    @else bg-green-50 border-green-200 @endif">
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 
                                @if($scale->stress_level === 'high') bg-red-500
                                @elseif($scale->stress_level === 'moderate') bg-yellow-500
                                @else bg-green-500 @endif rounded-full flex items-center justify-center">
                                <span class="text-white font-medium">{{ substr($scale->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $scale->user->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $scale->user->employee_id ?? 'No ID' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-2xl font-bold 
                                    @if($scale->stress_level === 'high') text-red-600
                                    @elseif($scale->stress_level === 'moderate') text-yellow-600
                                    @else text-green-600 @endif">
                                    {{ $scale->total_score }}/50
                                </p>
                                <p class="text-sm font-medium 
                                    @if($scale->stress_level === 'high') text-red-800
                                    @elseif($scale->stress_level === 'moderate') text-yellow-800
                                    @else text-green-800 @endif">
                                    {{ $scale->getStressLevelIndonesian() }}
                                </p>
                            </div>
                            
                            <a href="{{ route('job-stress.show', $scale) }}" 
                               class="text-purple-600 hover:text-purple-800 font-medium text-sm">
                                Detail →
                            </a>
                        </div>
                    </div>
                    
                    {{-- Quick stress indicators --}}
                    <div class="mt-3 flex space-x-2">
                        @for($i = 1; $i <= 10; $i++)
                            @php
                                $questionField = 'question_' . $i;
                                $score = $scale->$questionField;
                            @endphp
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                @if($score >= 4) bg-red-500 text-white
                                @elseif($score >= 3) bg-yellow-500 text-white
                                @else bg-green-500 text-white @endif">
                                {{ $score }}
                            </div>
                        @endfor
                    </div>
                    
                    <div class="mt-3 text-xs text-gray-600">
                        Diisi pada: {{ $scale->created_at->format('d M Y H:i') }}
                    </div>
                    
                    {{-- Action buttons for high stress --}}
                    @if($scale->stress_level === 'high')
                        <div class="mt-3 p-2 bg-red-100 rounded-lg">
                            <p class="text-xs text-red-800 font-medium">
                                🚨 Perhatian: Karyawan ini menunjukkan tingkat stres tinggi. 
                                Pertimbangkan untuk melakukan follow-up personal atau penyesuaian beban kerja.
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @else
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-8 text-center">
            <div class="text-6xl mb-4">📋</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Data Tim</h3>
            <p class="text-gray-600">Belum ada anggota tim yang mengisi Job Stress Scale untuk bulan {{ DateTime::createFromFormat('!m', $currentMonth)->format('F') }} {{ $currentYear }}.</p>
        </div>
    @endif

    {{-- Manager Recommendations --}}
    @if(count($teamStressScales) > 0)
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">💡 Rekomendasi untuk Tim</h3>
        
        <div class="space-y-4">
            @php
                $highStressMembers = $teamStressScales->where('stress_level', 'high');
                $moderateStressMembers = $teamStressScales->where('stress_level', 'moderate');
            @endphp
            
            @if($highStressMembers->count() > 0)
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h4 class="font-semibold text-red-800 mb-2">🚨 Tindakan Segera Diperlukan</h4>
                    <p class="text-red-700 text-sm mb-3">
                        {{ $highStressMembers->count() }} anggota tim mengalami stres tinggi:
                    </p>
                    <ul class="text-red-700 text-sm space-y-1 ml-4">
                        @foreach($highStressMembers as $member)
                            <li>• {{ $member->user->name }} (Skor: {{ $member->total_score }})</li>
                        @endforeach
                    </ul>
                    <p class="text-red-700 text-sm mt-3">
                        <strong>Saran:</strong> Lakukan one-on-one meeting, pertimbangkan redistributsi tugas, atau berikan dukungan tambahan.
                    </p>
                </div>
            @endif
            
            @if($moderateStressMembers->count() > 0)
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h4 class="font-semibold text-yellow-800 mb-2">⚠️ Monitoring Diperlukan</h4>
                    <p class="text-yellow-700 text-sm">
                        {{ $moderateStressMembers->count() }} anggota tim berada di tingkat stres sedang. 
                        Monitor perkembangan mereka dan pastikan beban kerja tetap manageable.
                    </p>
                </div>
            @endif
            
            @if($teamStressLevelStats['low'] > 0)
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h4 class="font-semibold text-green-800 mb-2">✅ Tim dengan Performa Baik</h4>
                    <p class="text-green-700 text-sm">
                        {{ $teamStressLevelStats['low'] }} anggota tim menunjukkan tingkat stres rendah. 
                        Pertahankan praktik manajemen yang baik dan gunakan sebagai benchmark.
                    </p>
                </div>
            @endif
            
            @if($completionRate < 100)
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-semibold text-blue-800 mb-2">📝 Tingkatkan Partisipasi</h4>
                    <p class="text-blue-700 text-sm">
                        Completion rate saat ini {{ $completionRate }}%. Dorong seluruh anggota tim untuk mengisi 
                        Job Stress Scale agar mendapat gambaran menyeluruh tentang kondisi tim.
                    </p>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Auto-refresh every 10 minutes to check for new submissions from team
setInterval(function() {
    window.location.reload();
}, 600000);
</script>
@endpush
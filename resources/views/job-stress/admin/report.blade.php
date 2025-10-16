@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 text-white rounded-3xl p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/0 to-white/10"></div>
        <div class="absolute -top-10 -right-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Laporan Job Stress Scale</h1>
                <p class="mt-2 text-purple-100">{{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}</p>
            </div>
            <div class="text-7xl opacity-20 select-none">📊</div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="flex items-center space-x-4">
        <a href="{{ route('job-stress.admin.index', ['month' => $month, 'year' => $year]) }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
        
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print Laporan
        </button>
    </div>

    {{-- Summary Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600">Total Responden</p>
                <p class="text-3xl font-bold text-indigo-600">{{ $stressScales->count() }}</p>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600">Rata-rata Skor</p>
                <p class="text-3xl font-bold text-purple-600">{{ number_format($stressScales->avg('total_score'), 1) }}</p>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600">Skor Tertinggi</p>
                <p class="text-3xl font-bold text-red-600">{{ $stressScales->max('total_score') ?? 0 }}</p>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600">Skor Terendah</p>
                <p class="text-3xl font-bold text-green-600">{{ $stressScales->min('total_score') ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Department Analysis --}}
    @if(count($departmentStats) > 0)
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Analisis per Departemen</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departemen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Responden</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata Skor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stres Rendah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stres Sedang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stres Tinggi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($departmentStats as $deptName => $stats)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $deptName ?: 'Tidak ada departemen' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $stats['count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-lg font-bold 
                                    @if($stats['average_score'] <= 20) text-green-600
                                    @elseif($stats['average_score'] <= 35) text-yellow-600
                                    @else text-red-600 @endif">
                                    {{ $stats['average_score'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $stats['low_stress'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ $stats['moderate_stress'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ $stats['high_stress'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Detailed Responses --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Respon Karyawan</h3>
        
        @if(count($stressScales) > 0)
            <div class="space-y-6">
                @foreach($stressScales->sortBy('total_score', SORT_REGULAR, true) as $scale)
                    <div class="border border-gray-200 rounded-xl p-6 
                        @if($scale->stress_level === 'high') bg-red-50 border-red-200
                        @elseif($scale->stress_level === 'moderate') bg-yellow-50 border-yellow-200
                        @else bg-green-50 border-green-200 @endif">
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 
                                    @if($scale->stress_level === 'high') bg-red-500
                                    @elseif($scale->stress_level === 'moderate') bg-yellow-500
                                    @else bg-green-500 @endif rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium">{{ substr($scale->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $scale->user->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $scale->user->department->name ?? 'No Department' }}</p>
                                </div>
                            </div>
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
                        </div>
                        
                        {{-- Question Responses in Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                            @for($i = 1; $i <= 10; $i++)
                                @php
                                    $questionField = 'question_' . $i;
                                    $score = $scale->$questionField;
                                @endphp
                                <div class="text-center p-3 bg-white rounded-lg border">
                                    <p class="text-xs font-medium text-gray-600 mb-1">Q{{ $i }}</p>
                                    <p class="text-lg font-bold 
                                        @if($score >= 4) text-red-600
                                        @elseif($score >= 3) text-yellow-600
                                        @else text-green-600 @endif">
                                        {{ $score }}
                                    </p>
                                </div>
                            @endfor
                        </div>
                        
                        <div class="mt-4 text-right">
                            <a href="{{ route('job-stress.show', $scale) }}" 
                               class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                Lihat Detail →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-6xl mb-4">📋</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Data</h3>
                <p class="text-gray-600">Tidak ada karyawan yang mengisi Job Stress Scale untuk periode ini.</p>
            </div>
        @endif
    </div>

    {{-- Recommendations --}}
    @if(count($stressScales) > 0)
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">💡 Rekomendasi</h3>
        
        <div class="space-y-4">
            @php
                $highStressCount = $stressScales->where('stress_level', 'high')->count();
                $moderateStressCount = $stressScales->where('stress_level', 'moderate')->count();
                $totalResponses = $stressScales->count();
                $averageScore = $stressScales->avg('total_score');
            @endphp
            
            @if($highStressCount > 0)
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h4 class="font-semibold text-red-800 mb-2">🚨 Perhatian Khusus Diperlukan</h4>
                    <p class="text-red-700 text-sm">
                        {{ $highStressCount }} karyawan ({{ round(($highStressCount/$totalResponses)*100, 1) }}%) mengalami tingkat stres tinggi. 
                        Pertimbangkan untuk memberikan dukungan tambahan, meninjau beban kerja, atau menyediakan program wellness.
                    </p>
                </div>
            @endif
            
            @if($averageScore > 30)
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h4 class="font-semibold text-yellow-800 mb-2">⚠️ Tingkat Stres di Atas Normal</h4>
                    <p class="text-yellow-700 text-sm">
                        Rata-rata skor stres ({{ number_format($averageScore, 1) }}) berada di kategori sedang-tinggi. 
                        Evaluasi kebijakan kerja dan pertimbangkan implementasi program manajemen stres.
                    </p>
                </div>
            @endif
            
            @if($moderateStressCount > ($totalResponses * 0.5))
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-semibold text-blue-800 mb-2">📈 Monitor Berkelanjutan</h4>
                    <p class="text-blue-700 text-sm">
                        Mayoritas karyawan berada di tingkat stres sedang. Lakukan monitoring rutin dan 
                        pertimbangkan program pencegahan untuk mencegah eskalasi ke tingkat stres tinggi.
                    </p>
                </div>
            @endif
            
            @if($highStressCount == 0 && $averageScore <= 25)
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h4 class="font-semibold text-green-800 mb-2">✅ Kondisi Baik</h4>
                    <p class="text-green-700 text-sm">
                        Tingkat stres karyawan berada dalam kondisi yang baik. Pertahankan praktik manajemen 
                        yang ada dan lakukan evaluasi berkala untuk memastikan konsistensi.
                    </p>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .bg-gradient-to-r { background: #6366f1 !important; }
}
</style>
@endsection
@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white rounded-3xl p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/0 to-white/10"></div>
        <div class="absolute -top-10 -right-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10">
            <h1 class="text-3xl font-extrabold tracking-tight">Riwayat Job Stress Assessment</h1>
            <p class="mt-2 text-purple-100">Tracking perkembangan tingkat stres Anda dari waktu ke waktu</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <div class="flex flex-wrap gap-4 justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">📊 Riwayat Assessment Anda</h3>
            <div class="flex space-x-3">
                <a href="{{ route('dashboard') }}" 
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    🏠 Dashboard
                </a>
                
                @php
                    $currentMonth = now()->month;
                    $currentYear = now()->year;
                    $hasCurrentMonth = $stressScales->where('month', $currentMonth)->where('year', $currentYear)->count() > 0;
                @endphp
                
                @if(!$hasCurrentMonth)
                    <a href="{{ route('job-stress.create') }}" 
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        📝 Isi Assessment Bulan Ini
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if(count($stressScales) > 0)
        {{-- Trend Overview --}}
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Trend Tingkat Stres</h3>
            
            {{-- Chart placeholder - we'll add Chart.js here --}}
            <div class="h-64 bg-gray-50 rounded-lg mb-4 flex items-center justify-center">
                <canvas id="stressTrendChart" class="w-full h-full"></canvas>
            </div>
            
            {{-- Summary Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm font-medium text-blue-600">Total Assessment</p>
                    <p class="text-2xl font-bold text-blue-800">{{ $stressScales->count() }}</p>
                </div>
                
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-sm font-medium text-green-600">Skor Rata-rata</p>
                    <p class="text-2xl font-bold text-green-800">{{ number_format($stressScales->avg('total_score'), 1) }}</p>
                </div>
                
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-sm font-medium text-red-600">Skor Tertinggi</p>
                    <p class="text-2xl font-bold text-red-800">{{ $stressScales->max('total_score') }}</p>
                </div>
                
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <p class="text-sm font-medium text-yellow-600">Skor Terendah</p>
                    <p class="text-2xl font-bold text-yellow-800">{{ $stressScales->min('total_score') }}</p>
                </div>
            </div>
        </div>

        {{-- Assessment History Timeline --}}
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">📅 Timeline Assessment</h3>
            
            <div class="space-y-6">
                @foreach($stressScales as $scale)
                    <div class="relative flex items-start space-x-4">
                        {{-- Timeline indicator --}}
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold
                                @if($scale->stress_level === 'high') bg-red-500
                                @elseif($scale->stress_level === 'moderate') bg-yellow-500
                                @else bg-green-500
                                @endif">
                                {{ $scale->total_score }}
                            </div>
                            @if(!$loop->last)
                                <div class="w-0.5 h-16 bg-gray-300 mt-4"></div>
                            @endif
                        </div>
                        
                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow
                                @if($scale->stress_level === 'high') bg-red-50 border-red-200
                                @elseif($scale->stress_level === 'moderate') bg-yellow-50 border-yellow-200
                                @else bg-green-50 border-green-200
                                @endif">
                                
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-xl font-bold text-gray-900">
                                            {{ $scale->month_name }} {{ $scale->year }}
                                        </h4>
                                        <p class="text-sm text-gray-600">
                                            Assessment diisi pada {{ $scale->created_at->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                    
                                    <div class="text-right">
                                        <p class="text-3xl font-bold 
                                            @if($scale->stress_level === 'high') text-red-600
                                            @elseif($scale->stress_level === 'moderate') text-yellow-600
                                            @else text-green-600
                                            @endif">{{ $scale->total_score }}/50</p>
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                            @if($scale->stress_level === 'high') bg-red-100 text-red-800
                                            @elseif($scale->stress_level === 'moderate') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800
                                            @endif">
                                            {{ $scale->getStressLevelIndonesian() }}
                                        </span>
                                    </div>
                                </div>
                                
                                {{-- Question breakdown visualization --}}
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Breakdown per Pertanyaan:</p>
                                    <div class="grid grid-cols-10 gap-1">
                                        @for($i = 1; $i <= 10; $i++)
                                            @php
                                                $questionField = 'question_' . $i;
                                                $score = $scale->$questionField;
                                            @endphp
                                            <div class="text-center">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white
                                                    @if($score >= 4) bg-red-500
                                                    @elseif($score >= 3) bg-yellow-500
                                                    @else bg-green-500
                                                    @endif">
                                                    {{ $score }}
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">Q{{ $i }}</p>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                
                                {{-- Comparison with previous month --}}
                                @if(!$loop->last)
                                    @php
                                        $previousScale = $stressScales[$loop->index + 1];
                                        $difference = $scale->total_score - $previousScale->total_score;
                                    @endphp
                                    <div class="flex items-center space-x-2 text-sm">
                                        @if($difference > 0)
                                            <span class="text-red-600 font-medium">
                                                ↗️ +{{ $difference }} poin dari bulan sebelumnya
                                            </span>
                                        @elseif($difference < 0)
                                            <span class="text-green-600 font-medium">
                                                ↘️ {{ $difference }} poin dari bulan sebelumnya
                                            </span>
                                        @else
                                            <span class="text-gray-600 font-medium">
                                                ➡️ Tidak ada perubahan dari bulan sebelumnya
                                            </span>
                                        @endif
                                    </div>
                                @endif
                                
                                <div class="mt-4 flex justify-end">
                                    <a href="{{ route('job-stress.show', $scale) }}" 
                                       class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                        Lihat Detail →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Insights & Recommendations --}}
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">💡 Insights dari Riwayat Anda</h3>
            
            @php
                $recentScales = $stressScales->take(3);
                $averageRecent = $recentScales->avg('total_score');
                $trend = 'stable';
                
                if ($stressScales->count() >= 2) {
                    $latest = $stressScales->first()->total_score;
                    $previous = $stressScales->skip(1)->first()->total_score;
                    if ($latest > $previous + 2) $trend = 'increasing';
                    elseif ($latest < $previous - 2) $trend = 'decreasing';
                }
                
                $highStressMonths = $stressScales->where('stress_level', 'high')->count();
                $lowStressMonths = $stressScales->where('stress_level', 'low')->count();
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    @if($trend === 'increasing')
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <h4 class="font-semibold text-red-800 mb-2">📈 Trend Meningkat</h4>
                            <p class="text-red-700 text-sm">
                                Tingkat stres Anda menunjukkan tren meningkat. Pertimbangkan untuk mengevaluasi 
                                faktor-faktor yang mungkin menyebabkan peningkatan ini.
                            </p>
                        </div>
                    @elseif($trend === 'decreasing')
                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <h4 class="font-semibold text-green-800 mb-2">📉 Trend Menurun</h4>
                            <p class="text-green-700 text-sm">
                                Bagus! Tingkat stres Anda menunjukkan tren menurun. Pertahankan strategi 
                                manajemen stres yang sedang Anda lakukan.
                            </p>
                        </div>
                    @else
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="font-semibold text-blue-800 mb-2">➡️ Trend Stabil</h4>
                            <p class="text-blue-700 text-sm">
                                Tingkat stres Anda relatif stabil. Terus monitor kondisi dan jaga konsistensi 
                                dalam manajemen work-life balance.
                            </p>
                        </div>
                    @endif
                    
                    @if($highStressMonths > 0)
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <h4 class="font-semibold text-yellow-800 mb-2">⚠️ Perhatian Khusus</h4>
                            <p class="text-yellow-700 text-sm">
                                Anda pernah mengalami tingkat stres tinggi selama {{ $highStressMonths }} bulan. 
                                Identifikasi pola dan faktor pemicu untuk pencegahan di masa depan.
                            </p>
                        </div>
                    @endif
                </div>
                
                <div class="space-y-4">
                    @if($averageRecent <= 25)
                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <h4 class="font-semibold text-green-800 mb-2">✅ Performa Baik</h4>
                            <p class="text-green-700 text-sm">
                                Rata-rata stres 3 bulan terakhir ({{ number_format($averageRecent, 1) }}) menunjukkan 
                                kondisi yang baik. Terus pertahankan!
                            </p>
                        </div>
                    @endif
                    
                    @if($lowStressMonths > 0)
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="font-semibold text-blue-800 mb-2">🌟 Achievement</h4>
                            <p class="text-blue-700 text-sm">
                                Anda berhasil mencapai tingkat stres rendah selama {{ $lowStressMonths }} bulan. 
                                Analisis strategi yang berhasil untuk diterapkan konsisten.
                            </p>
                        </div>
                    @endif
                    
                    <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg">
                        <h4 class="font-semibold text-purple-800 mb-2">🎯 Target</h4>
                        <p class="text-purple-700 text-sm">
                            Target ideal adalah mempertahankan skor di bawah 25 dan tingkat stres rendah-sedang. 
                            Lakukan assessment rutin setiap bulan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-8 text-center">
            <div class="text-6xl mb-4">📊</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Riwayat Assessment</h3>
            <p class="text-gray-600 mb-6">Mulai lakukan Job Stress Scale Assessment untuk melacak tingkat stres Anda.</p>
            
            <a href="{{ route('job-stress.create') }}" 
               class="inline-flex px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                📝 Mulai Assessment Pertama
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
@if(count($stressScales) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('stressTrendChart').getContext('2d');
    
    const data = @json($stressScales->reverse()->values());
    const labels = data.map(item => `${item.month_name} ${item.year}`);
    const scores = data.map(item => item.total_score);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Skor Stres',
                data: scores,
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: scores.map(score => {
                    if (score >= 36) return 'rgb(239, 68, 68)';
                    if (score >= 21) return 'rgb(245, 158, 11)';
                    return 'rgb(34, 197, 94)';
                }),
                pointBorderColor: 'white',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        afterLabel: function(context) {
                            const score = context.raw;
                            if (score >= 36) return 'Stres Tinggi';
                            if (score >= 21) return 'Stres Sedang';
                            return 'Stres Rendah';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 50,
                    ticks: {
                        stepSize: 10
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: 'white'
                }
            }
        }
    });
});
</script>
@endif
@endpush
@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="relative overflow-hidden 
        @if($jobStressScale->stress_level === 'high') bg-gradient-to-r from-red-600 via-orange-600 to-pink-600
        @elseif($jobStressScale->stress_level === 'moderate') bg-gradient-to-r from-yellow-600 via-orange-600 to-red-600
        @else bg-gradient-to-r from-green-600 via-teal-600 to-blue-600
        @endif text-white rounded-3xl p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/0 to-white/10"></div>
        <div class="absolute -top-10 -right-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10">
            <h1 class="text-3xl font-extrabold tracking-tight">Hasil Job Stress Assessment</h1>
            <p class="mt-2 text-white/90">{{ $jobStressScale->month_name }} {{ $jobStressScale->year }}</p>
            <p class="mt-1 text-sm text-white/80">{{ $jobStressScale->user->name }}</p>
        </div>
    </div>

    {{-- Result Summary --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-24 h-24 
                @if($jobStressScale->stress_level === 'high') bg-red-100
                @elseif($jobStressScale->stress_level === 'moderate') bg-yellow-100
                @else bg-green-100
                @endif rounded-full mb-4">
                <span class="text-4xl">
                    @if($jobStressScale->stress_level === 'high') 😰
                    @elseif($jobStressScale->stress_level === 'moderate') 😐
                    @else 😌
                    @endif
                </span>
            </div>
            
            <h2 class="text-3xl font-bold 
                @if($jobStressScale->stress_level === 'high') text-red-600
                @elseif($jobStressScale->stress_level === 'moderate') text-yellow-600
                @else text-green-600
                @endif mb-2">
                {{ $jobStressScale->total_score }}/50
            </h2>
            
            <h3 class="text-xl font-semibold 
                @if($jobStressScale->stress_level === 'high') text-red-800
                @elseif($jobStressScale->stress_level === 'moderate') text-yellow-800
                @else text-green-800
                @endif mb-4">
                {{ $jobStressScale->getStressLevelIndonesian() }}
            </h3>
            
            <div class="
                @if($jobStressScale->stress_level === 'high') bg-red-50 border-red-200 text-red-800
                @elseif($jobStressScale->stress_level === 'moderate') bg-yellow-50 border-yellow-200 text-yellow-800
                @else bg-green-50 border-green-200 text-green-800
                @endif border rounded-lg p-4 inline-block">
                <p class="text-sm font-medium">
                    @if($jobStressScale->stress_level === 'high')
                        Tingkat stres Anda tinggi (36-50). Perlu perhatian dan tindakan untuk mengurangi stres.
                    @elseif($jobStressScale->stress_level === 'moderate')
                        Tingkat stres Anda sedang (21-35). Pantau kondisi dan lakukan manajemen stres.
                    @else
                        Tingkat stres Anda rendah (10-20). Kondisi kerja Anda cukup baik.
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Detailed Breakdown --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">📊 Breakdown Jawaban Anda</h3>
        
        <div class="space-y-4">
            @foreach($questions as $number => $question)
                @php
                    $questionField = 'question_' . $number;
                    $score = $jobStressScale->$questionField;
                @endphp
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 mb-1">{{ $number }}. {{ $question['en'] }}</h4>
                            <p class="text-sm text-gray-600 italic">{{ $question['id'] }}</p>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <span class="text-lg font-bold 
                                @if($score >= 4) text-red-600
                                @elseif($score >= 3) text-yellow-600
                                @else text-green-600
                                @endif">{{ $score }}</span>
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white
                                @if($score >= 4) bg-red-500
                                @elseif($score >= 3) bg-yellow-500
                                @else bg-green-500
                                @endif">{{ $score }}</div>
                        </div>
                    </div>
                    
                    {{-- Visual scale indicator --}}
                    <div class="flex space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <div class="flex-1 h-2 rounded 
                                @if($i <= $score)
                                    @if($score >= 4) bg-red-400
                                    @elseif($score >= 3) bg-yellow-400
                                    @else bg-green-400
                                    @endif
                                @else bg-gray-200
                                @endif"></div>
                        @endfor
                    </div>
                    
                    <div class="flex justify-between mt-2 text-xs text-gray-500">
                        <span>Sangat Tidak Setuju</span>
                        <span>Sangat Setuju</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Recommendations --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">💡 Rekomendasi untuk Anda</h3>
        
        @if($jobStressScale->stress_level === 'high')
            <div class="space-y-4">
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h4 class="font-semibold text-red-800 mb-2">🚨 Tindakan Segera</h4>
                    <ul class="text-red-700 text-sm space-y-1">
                        <li>• Bicarakan dengan atasan atau HR mengenai beban kerja Anda</li>
                        <li>• Pertimbangkan untuk mengambil cuti atau istirahat yang cukup</li>
                        <li>• Cari dukungan dari rekan kerja, keluarga, atau konselor</li>
                        <li>• Identifikasi sumber stres utama dan buat rencana untuk mengatasinya</li>
                    </ul>
                </div>
                
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-semibold text-blue-800 mb-2">🧘 Teknik Manajemen Stres</h4>
                    <ul class="text-blue-700 text-sm space-y-1">
                        <li>• Lakukan teknik pernapasan dalam atau meditasi 10-15 menit setiap hari</li>
                        <li>• Atur prioritas tugas dan buat to-do list yang realistis</li>
                        <li>• Ambil break pendek setiap 1-2 jam sekali</li>
                        <li>• Olahraga ringan seperti jalan kaki atau stretching</li>
                    </ul>
                </div>
            </div>
        @elseif($jobStressScale->stress_level === 'moderate')
            <div class="space-y-4">
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h4 class="font-semibold text-yellow-800 mb-2">⚠️ Pencegahan Eskalasi</h4>
                    <ul class="text-yellow-700 text-sm space-y-1">
                        <li>• Monitor tingkat stres Anda secara berkala</li>
                        <li>• Jaga work-life balance dengan baik</li>
                        <li>• Komunikasikan tantangan kerja dengan atasan</li>
                        <li>• Gunakan waktu istirahat dengan efektif</li>
                    </ul>
                </div>
                
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h4 class="font-semibold text-green-800 mb-2">🌟 Tingkatkan Wellness</h4>
                    <ul class="text-green-700 text-sm space-y-1">
                        <li>• Maintain rutinitas sehat (tidur 7-8 jam, makan teratur)</li>
                        <li>• Kembangkan hobi atau aktivitas yang menyenangkan</li>
                        <li>• Bangun relasi positif dengan rekan kerja</li>
                        <li>• Belajar teknik time management yang lebih baik</li>
                    </ul>
                </div>
            </div>
        @else
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                <h4 class="font-semibold text-green-800 mb-2">✅ Pertahankan Kondisi Baik</h4>
                <ul class="text-green-700 text-sm space-y-1">
                    <li>• Terus pertahankan work-life balance yang baik</li>
                    <li>• Bagikan tips manajemen stres Anda dengan rekan kerja</li>
                    <li>• Tetap monitoring kondisi Anda secara berkala</li>
                    <li>• Bantu rekan kerja yang mungkin mengalami stres tinggi</li>
                    <li>• Lanjutkan praktik wellness yang sudah Anda lakukan</li>
                </ul>
            </div>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('dashboard') }}" 
               class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                🏠 Kembali ke Dashboard
            </a>
            
            <a href="{{ route('job-stress.history') }}" 
               class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                📈 Lihat Riwayat
            </a>
            
            @if($jobStressScale->stress_level === 'high')
                <button onclick="shareResult()" 
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    🔗 Bagikan dengan Atasan
                </button>
            @endif
            
            <button onclick="window.print()" 
                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                🖨️ Print Hasil
            </button>
        </div>
    </div>

    {{-- Timeline info --}}
    <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6 text-center">
        <p class="text-sm text-gray-600">
            Assessment ini diisi pada <strong>{{ $jobStressScale->created_at->format('d M Y H:i') }}</strong><br>
            Assessment berikutnya: <strong>{{ $jobStressScale->created_at->addMonth()->format('F Y') }}</strong>
        </p>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .bg-gradient-to-r { background: #6366f1 !important; }
}
</style>
@endsection

@push('scripts')
<script>
function shareResult() {
    if (navigator.share) {
        navigator.share({
            title: 'Hasil Job Stress Scale Assessment',
            text: 'Saya memerlukan perhatian terkait tingkat stres kerja yang tinggi.',
            url: window.location.href
        });
    } else {
        // Fallback - copy link to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link hasil assessment telah disalin. Anda dapat membagikannya dengan atasan.');
        });
    }
}

// Show congratulations for low stress or encouragement for high stress
document.addEventListener('DOMContentLoaded', function() {
    const stressLevel = '{{ $jobStressScale->stress_level }}';
    const score = {{ $jobStressScale->total_score }};
    
    if (stressLevel === 'low') {
        setTimeout(() => {
            showNotification('Selamat! Tingkat stres Anda rendah. Pertahankan kondisi ini! 🎉', 'success');
        }, 1000);
    } else if (stressLevel === 'high') {
        setTimeout(() => {
            showNotification('Perhatian: Tingkat stres Anda tinggi. Jangan ragu untuk mencari bantuan. 💪', 'warning');
        }, 1000);
    }
});

function showNotification(message, type) {
    const color = type === 'success' ? 'green' : type === 'warning' ? 'yellow' : 'blue';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg bg-${color}-100 border border-${color}-200 max-w-sm`;
    notification.innerHTML = `
        <div class="flex items-start space-x-2">
            <div class="flex-shrink-0">
                ${type === 'success' ? 
                    '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
                    '<svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>'
                }
            </div>
            <p class="text-${color}-800 text-sm">${message}</p>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endpush
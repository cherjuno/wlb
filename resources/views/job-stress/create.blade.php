@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white rounded-3xl p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/0 to-white/10"></div>
        <div class="absolute -top-10 -right-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10">
            <h1 class="text-3xl font-extrabold tracking-tight">Job Stress Scale Assessment</h1>
            <p class="mt-2 text-blue-100">{{ DateTime::createFromFormat('!m', $currentMonth)->format('F') }} {{ $currentYear }}</p>
            <p class="mt-1 text-sm text-blue-200">Evaluasi tingkat stres kerja Anda bulan ini</p>
        </div>
    </div>

    {{-- Instructions --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Petunjuk Pengisian</h3>
        <div class="space-y-3 text-sm text-gray-700">
            <p>• Bacalah setiap pernyataan dengan seksama dan berikan penilaian yang jujur sesuai kondisi Anda.</p>
            <p>• Gunakan skala 1-5 dimana:</p>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-2 mt-2">
                <div class="text-center p-2 bg-green-50 rounded-lg border border-green-200">
                    <div class="font-semibold text-green-800">1</div>
                    <div class="text-xs text-green-600">Sangat Tidak Setuju</div>
                </div>
                <div class="text-center p-2 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="font-semibold text-yellow-800">2</div>
                    <div class="text-xs text-yellow-600">Tidak Setuju</div>
                </div>
                <div class="text-center p-2 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="font-semibold text-gray-800">3</div>
                    <div class="text-xs text-gray-600">Netral</div>
                </div>
                <div class="text-center p-2 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="font-semibold text-orange-800">4</div>
                    <div class="text-xs text-orange-600">Setuju</div>
                </div>
                <div class="text-center p-2 bg-red-50 rounded-lg border border-red-200">
                    <div class="font-semibold text-red-800">5</div>
                    <div class="text-xs text-red-600">Sangat Setuju</div>
                </div>
            </div>
            <p class="mt-3">• Tidak ada jawaban yang benar atau salah, jawablah sesuai pengalaman Anda.</p>
            <p>• Data ini akan membantu manajemen untuk meningkatkan kondisi kerja yang lebih baik.</p>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('job-stress.store') }}" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">10 Item Job Stress Scale (Parker, 1983)</h3>
            
            <div class="space-y-6">
                @foreach($questions as $number => $question)
                    <div class="border border-gray-200 rounded-xl p-6 hover:border-blue-300 transition-colors">
                        <div class="mb-4">
                            <h4 class="font-semibold text-gray-900 mb-2">{{ $number }}. {{ $question['en'] }}</h4>
                            <p class="text-gray-600 text-sm italic">{{ $question['id'] }}</p>
                        </div>
                        
                        <div class="grid grid-cols-5 gap-3">
                            @for($score = 1; $score <= 5; $score++)
                                <label class="flex flex-col items-center cursor-pointer group">
                                    <input type="radio" 
                                           name="question_{{ $number }}" 
                                           value="{{ $score }}" 
                                           class="sr-only peer" 
                                           required>
                                    <div class="w-full p-3 text-center border-2 border-gray-200 rounded-lg 
                                                peer-checked:border-blue-500 peer-checked:bg-blue-50 
                                                hover:border-blue-300 hover:bg-blue-25 transition-all
                                                @if($score == 1) hover:border-green-300 hover:bg-green-50 peer-checked:border-green-500 peer-checked:bg-green-50
                                                @elseif($score == 2) hover:border-yellow-300 hover:bg-yellow-50 peer-checked:border-yellow-500 peer-checked:bg-yellow-50
                                                @elseif($score == 3) hover:border-gray-300 hover:bg-gray-50 peer-checked:border-gray-500 peer-checked:bg-gray-50
                                                @elseif($score == 4) hover:border-orange-300 hover:bg-orange-50 peer-checked:border-orange-500 peer-checked:bg-orange-50
                                                @else hover:border-red-300 hover:bg-red-50 peer-checked:border-red-500 peer-checked:bg-red-50
                                                @endif">
                                        <div class="text-xl font-bold 
                                                    @if($score == 1) text-green-600
                                                    @elseif($score == 2) text-yellow-600
                                                    @elseif($score == 3) text-gray-600
                                                    @elseif($score == 4) text-orange-600
                                                    @else text-red-600
                                                    @endif">{{ $score }}</div>
                                        <div class="text-xs mt-1 
                                                    @if($score == 1) text-green-600
                                                    @elseif($score == 2) text-yellow-600
                                                    @elseif($score == 3) text-gray-600
                                                    @elseif($score == 4) text-orange-600
                                                    @else text-red-600
                                                    @endif">
                                            @if($score == 1) Sangat Tidak Setuju
                                            @elseif($score == 2) Tidak Setuju
                                            @elseif($score == 3) Netral
                                            @elseif($score == 4) Setuju
                                            @else Sangat Setuju
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endfor
                        </div>
                        
                        @error('question_' . $number)
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Submit Section --}}
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-semibold text-gray-900">Siap untuk mengirim?</h4>
                    <p class="text-sm text-gray-600">Pastikan semua pertanyaan telah dijawab dengan jujur.</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('dashboard') }}" 
                       class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        📊 Kirim Assessment
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- Information Box --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
        <div class="flex items-start space-x-3">
            <div class="text-2xl">🔒</div>
            <div>
                <h4 class="font-semibold text-blue-900 mb-2">Kerahasiaan Data</h4>
                <p class="text-blue-800 text-sm">
                    Data yang Anda berikan akan dijaga kerahasiaannya dan hanya digunakan untuk tujuan 
                    evaluasi kesejahteraan karyawan. Hasil agregat akan membantu manajemen dalam 
                    menciptakan lingkungan kerja yang lebih baik untuk semua.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-save to localStorage as user fills the form
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[type="radio"]');
    
    // Load saved data
    inputs.forEach(input => {
        const saved = localStorage.getItem(input.name);
        if (saved && input.value === saved) {
            input.checked = true;
        }
    });
    
    // Save data on change
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            localStorage.setItem(this.name, this.value);
            updateProgress();
        });
    });
    
    // Clear localStorage on form submit
    form.addEventListener('submit', function() {
        for (let i = 1; i <= 10; i++) {
            localStorage.removeItem(`question_${i}`);
        }
    });
    
    function updateProgress() {
        const totalQuestions = 10;
        const answeredQuestions = Array.from(inputs).filter(input => input.checked).length;
        const progress = (answeredQuestions / totalQuestions) * 100;
        
        // Update progress indicator if exists
        const progressBar = document.getElementById('progress');
        if (progressBar) {
            progressBar.style.width = progress + '%';
        }
    }
    
    updateProgress();
});

// Smooth scroll to next question after answering
document.querySelectorAll('input[type="radio"]').forEach(input => {
    input.addEventListener('change', function() {
        const currentQuestion = this.closest('.border');
        const nextQuestion = currentQuestion.nextElementSibling;
        
        setTimeout(() => {
            if (nextQuestion && nextQuestion.classList.contains('border')) {
                nextQuestion.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
        }, 300);
    });
});
</script>
@endpush
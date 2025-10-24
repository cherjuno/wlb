@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white rounded-3xl p-8 shadow-xl">
        <div class="flex items-center space-x-4">
            <div class="h-16 w-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold">✏️ Edit Overtime Request</h1>
                <p class="text-purple-100 mt-2">Update your overtime application</p>
            </div>
        </div>
    </div>

    {{-- Edit Form --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <form action="{{ route('overtime.update', $overtime) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Overtime Date --}}
                <div>
                    <label for="date" class="block text-lg font-bold text-gray-900 mb-3">
                        📅 Overtime Date
                    </label>
                    <input type="date" name="date" id="date" 
                           value="{{ old('date', $overtime->date) }}"
                           class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-lg transition-all duration-300" 
                           min="{{ now()->format('Y-m-d') }}" required>
                    @error('date')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Hours --}}
                <div>
                    <label for="hours" class="block text-lg font-bold text-gray-900 mb-3">
                        ⏱️ Overtime Hours
                    </label>
                    <select name="hours" id="hours"
                            class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-lg transition-all duration-300" required>
                        <option value="">Select hours...</option>
                        <option value="1" {{ old('hours', $overtime->hours) == 1 ? 'selected' : '' }}>1 hour</option>
                        <option value="1.5" {{ old('hours', $overtime->hours) == 1.5 ? 'selected' : '' }}>1.5 hours</option>
                        <option value="2" {{ old('hours', $overtime->hours) == 2 ? 'selected' : '' }}>2 hours</option>
                        <option value="2.5" {{ old('hours', $overtime->hours) == 2.5 ? 'selected' : '' }}>2.5 hours</option>
                        <option value="3" {{ old('hours', $overtime->hours) == 3 ? 'selected' : '' }}>3 hours</option>
                        <option value="3.5" {{ old('hours', $overtime->hours) == 3.5 ? 'selected' : '' }}>3.5 hours</option>
                        <option value="4" {{ old('hours', $overtime->hours) == 4 ? 'selected' : '' }}>4 hours (maximum)</option>
                    </select>
                    @error('hours')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Time Selection --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_time" class="block text-lg font-bold text-gray-900 mb-3">
                        🕐 Start Time
                    </label>
                    <select name="start_time" id="start_time"
                            class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-lg transition-all duration-300" required>
                        <option value="">Select start time...</option>
                        <option value="17:00" {{ old('start_time', $overtime->start_time) == '17:00' ? 'selected' : '' }}>17:00 (After regular hours)</option>
                        <option value="18:00" {{ old('start_time', $overtime->start_time) == '18:00' ? 'selected' : '' }}>18:00</option>
                        <option value="19:00" {{ old('start_time', $overtime->start_time) == '19:00' ? 'selected' : '' }}>19:00</option>
                        <option value="20:00" {{ old('start_time', $overtime->start_time) == '20:00' ? 'selected' : '' }}>20:00</option>
                        <option value="06:00" {{ old('start_time', $overtime->start_time) == '06:00' ? 'selected' : '' }}>06:00 (Early morning)</option>
                        <option value="07:00" {{ old('start_time', $overtime->start_time) == '07:00' ? 'selected' : '' }}>07:00</option>
                    </select>
                    @error('start_time')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-lg font-bold text-gray-900 mb-3">
                        🕔 End Time
                    </label>
                    <input type="time" name="end_time" id="end_time" 
                           value="{{ old('end_time', $overtime->end_time) }}"
                           class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-lg transition-all duration-300" required>
                    @error('end_time')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Type --}}
            <div>
                <label for="type" class="block text-lg font-bold text-gray-900 mb-3">
                    📋 Overtime Type
                </label>
                <select name="type" id="type"
                        class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-lg transition-all duration-300" required>
                    <option value="">Select type...</option>
                    <option value="regular" {{ old('type', $overtime->type) == 'regular' ? 'selected' : '' }}>Regular Overtime</option>
                    <option value="project" {{ old('type', $overtime->type) == 'project' ? 'selected' : '' }}>Project Deadline</option>
                    <option value="emergency" {{ old('type', $overtime->type) == 'emergency' ? 'selected' : '' }}>Emergency</option>
                    <option value="weekend" {{ old('type', $overtime->type) == 'weekend' ? 'selected' : '' }}>Weekend Work</option>
                </select>
                @error('type')
                    <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-lg font-bold text-gray-900 mb-3">
                    📝 Description & Reason
                </label>
                <textarea name="description" id="description" rows="4" 
                          placeholder="Explain the reason for overtime and tasks to be completed..."
                          class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-lg transition-all duration-300" required>{{ old('description', $overtime->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- Current Status Info --}}
            <div class="bg-gray-50 rounded-2xl p-6">
                <h4 class="font-bold text-gray-800 mb-2">📊 Current Request Status</h4>
                <div class="flex items-center space-x-4">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($overtime->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($overtime->status == 'approved') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($overtime->status) }}
                    </span>
                    <span class="text-gray-600">
                        Submitted: {{ $overtime->created_at->format('M d, Y H:i') }}
                    </span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end space-x-4 pt-6">
                <a href="{{ route('overtime.index') }}" 
                   class="px-8 py-4 bg-gray-200 text-gray-700 rounded-2xl font-bold text-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-8 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-2xl font-bold text-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105">
                    💾 Update Request
                </button>
            </div>
        </form>
    </div>

    {{-- Notice --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
        <div class="flex items-start space-x-3">
            <div class="text-2xl">⚠️</div>
            <div>
                <h4 class="font-bold text-amber-800">Important Notice</h4>
                <p class="text-amber-700 text-sm mt-1">
                    You can only edit overtime requests that are still pending approval. 
                    Once approved or rejected, the request cannot be modified.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
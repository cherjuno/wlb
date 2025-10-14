@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Edit Attendance</h2>
        @if(session('error'))
            <div class="mb-4 text-red-600">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('attendance.update', $attendance) }}">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="check_in" class="block text-sm font-medium text-gray-700">Check In Time</label>
                <input type="time" name="check_in" id="check_in" value="{{ old('check_in', $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('check_in') border-red-300 @enderror">
                @error('check_in')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="check_out" class="block text-sm font-medium text-gray-700">Check Out Time</label>
                <input type="time" name="check_out" id="check_out" value="{{ old('check_out', $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('check_out') border-red-300 @enderror">
                @error('check_out')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="break_duration" class="block text-sm font-medium text-gray-700">Break Duration (hours)</label>
                <input type="number" name="break_duration" id="break_duration" value="{{ old('break_duration', $attendance->break_duration ?? 1.0) }}" min="0" max="8" step="0.5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('break_duration') border-red-300 @enderror">
                @error('break_duration')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes (optional)</label>
                <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $attendance->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end">
                <a href="{{ route('attendance.index') }}" class="mr-3 rounded-md bg-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-200">Cancel</a>
                <button type="submit" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">Update Attendance</button>
            </div>
        </form>
    </div>
</div>
@endsection

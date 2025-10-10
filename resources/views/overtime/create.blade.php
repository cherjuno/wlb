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
                <h1 class="text-3xl font-bold">⏰ Submit Overtime Request</h1>
                <p class="text-purple-100 mt-2">Request approval for overtime work</p>
            </div>
        </div>
    </div>

    {{-- Overtime Limits Info --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <h3 class="text-xl font-bold text-gray-900 mb-6">📊 Overtime Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl">
                <div class="h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-blue-600">40</div>
                <div class="text-sm text-gray-600">Monthly Limit (hours)</div>
            </div>
            
            <div class="text-center p-6 bg-gradient-to-br from-red-50 to-red-100 rounded-2xl">
                <div class="h-12 w-12 bg-red-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-red-600">{{ $monthlyOvertime ?? 0 }}</div>
                <div class="text-sm text-gray-600">Used This Month</div>
            </div>
            
            <div class="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl">
                <div class="h-12 w-12 bg-green-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-green-600">{{ 40 - ($monthlyOvertime ?? 0) }}</div>
                <div class="text-sm text-gray-600">Remaining Hours</div>
            </div>
        </div>

        {{-- Overtime Policies --}}
        <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-2xl p-6">
            <h4 class="font-bold text-amber-800 mb-3">📋 Overtime Policies</h4>
            <ul class="text-sm text-amber-700 space-y-2">
                <li class="flex items-start space-x-2">
                    <span class="text-amber-500 mt-1">•</span>
                    <span>Maximum 4 hours overtime per day</span>
                </li>
                <li class="flex items-start space-x-2">
                    <span class="text-amber-500 mt-1">•</span>
                    <span>Overtime must be approved by your manager</span>
                </li>
                <li class="flex items-start space-x-2">
                    <span class="text-amber-500 mt-1">•</span>
                    <span>Submit request at least 1 day in advance (except emergency)</span>
                </li>
                <li class="flex items-start space-x-2">
                    <span class="text-amber-500 mt-1">•</span>
                    <span>Overtime rate: 1.5x regular hourly rate</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- Overtime Request Form --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <form action="{{ route('overtime.store') }}" method="POST" x-data="overtimeForm()" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Overtime Date --}}
                <div>
                    <label for="date" class="block text-lg font-bold text-gray-900 mb-3">
                        📅 Overtime Date
                    </label>
                    <input type="date" name="date" id="date" x-model="form.date" @change="checkWeekend()"
                           class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-lg transition-all duration-300" 
                           :min="new Date().toISOString().split('T')[0]" required>
                    @error('date')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                    <div x-show="form.isWeekend" x-transition class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-yellow-800 text-sm">⚠️ Weekend overtime requires special approval</p>
                    </div>
                </div>

                {{-- Hours --}}
                <div>
                    <label for="hours" class="block text-lg font-bold text-gray-900 mb-3">
                        ⏱️ Overtime Hours
                    </label>
                    <select name="hours" id="hours" x-model="form.hours" @change="calculateEndTime()"
                            class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-lg transition-all duration-300" required>
                        <option value="">Select hours...</option>
                        <option value="1">1 hour</option>
                        <option value="1.5">1.5 hours</option>
                        <option value="2">2 hours</option>
                        <option value="2.5">2.5 hours</option>
                        <option value="3">3 hours</option>
                        <option value="3.5">3.5 hours</option>
                        <option value="4">4 hours (maximum)</option>
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
                    <select name="start_time" id="start_time" x-model="form.startTime" @change="calculateEndTime()"
                            class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-lg transition-all duration-300" required>
                        <option value="">Select start time...</option>
                        <option value="17:00">17:00 (After regular hours)</option>
                        <option value="18:00">18:00</option>
                        <option value="19:00">19:00</option>
                        <option value="20:00">20:00</option>
                        <option value="06:00">06:00 (Early morning)</option>
                        <option value="07:00">07:00</option>
                    </select>
                    @error('start_time')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-lg font-bold text-gray-900 mb-3">
                        🕔 Estimated End Time
                    </label>
                    <div class="w-full px-4 py-4 rounded-2xl border border-gray-200 bg-gray-50 text-lg text-gray-700">
                        <span x-text="form.endTime || 'Select start time and hours'"></span>
                    </div>
                </div>
            </div>

            {{-- Overtime Type --}}
            <div>
                <label class="block text-lg font-bold text-gray-900 mb-3">
                    📋 Overtime Type
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-center p-4 border border-gray-300 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all duration-300"
                           :class="form.type === 'regular' ? 'bg-purple-50 border-purple-500' : ''">
                        <input type="radio" name="type" x-model="form.type" value="regular" class="sr-only">
                        <div class="flex items-center space-x-3">
                            <div class="h-4 w-4 rounded-full border-2 transition-all duration-300"
                                 :class="form.type === 'regular' ? 'bg-purple-500 border-purple-500' : 'border-gray-300'"></div>
                            <div>
                                <div class="font-medium">🏢 Regular Overtime</div>
                                <div class="text-sm text-gray-500">Planned work extension</div>
                            </div>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border border-gray-300 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all duration-300"
                           :class="form.type === 'project' ? 'bg-purple-50 border-purple-500' : ''">
                        <input type="radio" name="type" x-model="form.type" value="project" class="sr-only">
                        <div class="flex items-center space-x-3">
                            <div class="h-4 w-4 rounded-full border-2 transition-all duration-300"
                                 :class="form.type === 'project' ? 'bg-purple-500 border-purple-500' : 'border-gray-300'"></div>
                            <div>
                                <div class="font-medium">🚀 Project Deadline</div>
                                <div class="text-sm text-gray-500">Urgent project work</div>
                            </div>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border border-gray-300 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all duration-300"
                           :class="form.type === 'emergency' ? 'bg-purple-50 border-purple-500' : ''">
                        <input type="radio" name="type" x-model="form.type" value="emergency" class="sr-only">
                        <div class="flex items-center space-x-3">
                            <div class="h-4 w-4 rounded-full border-2 transition-all duration-300"
                                 :class="form.type === 'emergency' ? 'bg-purple-500 border-purple-500' : 'border-gray-300'"></div>
                            <div>
                                <div class="font-medium">🚨 Emergency</div>
                                <div class="text-sm text-gray-500">Urgent issue resolution</div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Reason/Description --}}
            <div>
                <label for="reason" class="block text-lg font-bold text-gray-900 mb-3">
                    📝 Reason for Overtime
                </label>
                <textarea name="reason" id="reason" rows="4" 
                          class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-lg transition-all duration-300 resize-none" 
                          placeholder="Please provide detailed information about the work to be done during overtime..." required></textarea>
                @error('reason')
                    <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- Project/Task Details --}}
            <div>
                <label for="task_description" class="block text-lg font-bold text-gray-900 mb-3">
                    📋 Task Description
                </label>
                <textarea name="task_description" id="task_description" rows="3" 
                          class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-lg transition-all duration-300 resize-none" 
                          placeholder="Describe the specific tasks you will be working on..."></textarea>
            </div>

            {{-- Expected Deliverables --}}
            <div>
                <label for="deliverables" class="block text-lg font-bold text-gray-900 mb-3">
                    🎯 Expected Deliverables
                </label>
                <textarea name="deliverables" id="deliverables" rows="3" 
                          class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-lg transition-all duration-300 resize-none" 
                          placeholder="What outcomes do you expect to achieve from this overtime work?"></textarea>
            </div>

            {{-- Overtime Summary --}}
            <div x-show="form.hours && form.startTime" x-transition class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl p-6">
                <h4 class="font-bold text-purple-900 mb-4">📊 Overtime Summary</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-white/50 rounded-xl">
                        <div class="text-lg font-bold text-purple-600" x-text="form.hours + ' hours'"></div>
                        <div class="text-sm text-gray-600">Duration</div>
                    </div>
                    <div class="text-center p-4 bg-white/50 rounded-xl">
                        <div class="text-lg font-bold text-purple-600" x-text="form.startTime + ' - ' + form.endTime"></div>
                        <div class="text-sm text-gray-600">Time Range</div>
                    </div>
                    <div class="text-center p-4 bg-white/50 rounded-xl">
                        <div class="text-lg font-bold text-purple-600" x-text="'Rp ' + (parseFloat(form.hours || 0) * 50000).toLocaleString('id-ID')"></div>
                        <div class="text-sm text-gray-600">Estimated Pay (1.5x)</div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 pt-6">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-purple-600 hover:to-indigo-600 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                    <div class="flex items-center justify-center space-x-3">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>🚀 Submit Overtime Request</span>
                    </div>
                </button>
                
                <a href="{{ route('dashboard') }}" 
                   class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 text-center">
                    <div class="flex items-center justify-center space-x-3">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Cancel</span>
                    </div>
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function overtimeForm() {
    return {
        form: {
            date: '',
            hours: '',
            startTime: '',
            endTime: '',
            type: 'regular',
            isWeekend: false
        },
        
        checkWeekend() {
            if (this.form.date) {
                const date = new Date(this.form.date);
                const dayOfWeek = date.getDay();
                this.form.isWeekend = dayOfWeek === 0 || dayOfWeek === 6; // Sunday = 0, Saturday = 6
            }
        },
        
        calculateEndTime() {
            if (this.form.startTime && this.form.hours) {
                const [hours, minutes] = this.form.startTime.split(':').map(Number);
                const overtimeHours = parseFloat(this.form.hours);
                const overtimeMinutes = (overtimeHours % 1) * 60;
                
                const endHours = hours + Math.floor(overtimeHours);
                const endMinutes = minutes + overtimeMinutes;
                
                const finalHours = endHours + Math.floor(endMinutes / 60);
                const finalMinutes = Math.round(endMinutes % 60);
                
                this.form.endTime = String(finalHours).padStart(2, '0') + ':' + String(finalMinutes).padStart(2, '0');
            }
        }
    }
}
</script>
@endsection
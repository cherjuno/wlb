@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-3xl p-8 shadow-xl">
        <div class="flex items-center space-x-4">
            <div class="h-16 w-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold">📅 Request Leave</h1>
                <p class="text-yellow-100 mt-2">Submit your leave application here</p>
            </div>
        </div>
    </div>

    {{-- Leave Balance Info --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <h3 class="text-xl font-bold text-gray-900 mb-6">📊 Your Leave Balance</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl">
                <div class="h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-blue-600">{{ $leaveBalance['annual_quota'] ?? 12 }}</div>
                <div class="text-sm text-gray-600">Annual Quota</div>
            </div>
            
            <div class="text-center p-6 bg-gradient-to-br from-red-50 to-red-100 rounded-2xl">
                <div class="h-12 w-12 bg-red-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-red-600">{{ $leaveBalance['used'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Used</div>
            </div>
            
            <div class="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl">
                <div class="h-12 w-12 bg-green-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-green-600">{{ $leaveBalance['remaining'] ?? 12 }}</div>
                <div class="text-sm text-gray-600">Available</div>
            </div>
        </div>
    </div>

    {{-- Leave Request Form --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <form action="{{ route('leave.store') }}" method="POST" x-data="leaveForm()" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Leave Type --}}
                <div>
                    <label for="type" class="block text-lg font-bold text-gray-900 mb-3">
                        📋 Leave Type
                    </label>
                    <select name="type" id="type" x-model="form.type" 
                            class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-yellow-200 focus:border-yellow-500 text-lg text-gray-900 bg-white transition-all duration-300" required>
                        <option value="">Select leave type...</option>
                        <option value="annual">🏖️ Annual Leave</option>
                        <option value="sick">🏥 Sick Leave</option>
                        <option value="emergency">🚨 Emergency Leave</option>
                        <option value="maternity">👶 Maternity/Paternity</option>
                        <option value="personal">👤 Personal Leave</option>
                    </select>
                    @error('type')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Duration Selector --}}
                <div>
                    <label class="block text-lg font-bold text-gray-900 mb-3">
                        ⏱️ Duration Type
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center p-4 border border-gray-300 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all duration-300" 
                               :class="form.durationType === 'full' ? 'bg-yellow-50 border-yellow-500' : ''">
                            <input type="radio" x-model="form.durationType" value="full" class="sr-only">
                            <div class="flex items-center space-x-3">
                                <div class="h-4 w-4 rounded-full border-2 transition-all duration-300"
                                     :class="form.durationType === 'full' ? 'bg-yellow-500 border-yellow-500' : 'border-gray-300'"></div>
                                <span class="font-medium">Full Day</span>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-4 border border-gray-300 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all duration-300"
                               :class="form.durationType === 'half' ? 'bg-yellow-50 border-yellow-500' : ''">
                            <input type="radio" x-model="form.durationType" value="half" class="sr-only">
                            <div class="flex items-center space-x-3">
                                <div class="h-4 w-4 rounded-full border-2 transition-all duration-300"
                                     :class="form.durationType === 'half' ? 'bg-yellow-500 border-yellow-500' : 'border-gray-300'"></div>
                                <span class="font-medium">Half Day</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Date Selection --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_date" class="block text-lg font-bold text-gray-900 mb-3">
                        📅 Start Date
                    </label>
                    <input type="date" name="start_date" id="start_date" x-model="form.startDate" @change="calculateDays()"
                           class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-yellow-200 focus:border-yellow-500 text-lg text-gray-900 bg-white transition-all duration-300" 
                           :min="new Date().toISOString().split('T')[0]" required>
                    @error('start_date')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="form.durationType === 'full'">
                    <label for="end_date" class="block text-lg font-bold text-gray-900 mb-3">
                        📅 End Date
                    </label>
                    <input type="date" name="end_date" id="end_date" x-model="form.endDate" @change="calculateDays()"
                           class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-yellow-200 focus:border-yellow-500 text-lg text-gray-900 bg-white transition-all duration-300" 
                           :min="form.startDate" :required="form.durationType === 'full'">
                    @error('end_date')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Half Day Options --}}
            <div x-show="form.durationType === 'half'" x-transition class="space-y-4">
                <label class="block text-lg font-bold text-gray-900">
                    🕐 Half Day Period
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center p-4 border border-gray-300 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all duration-300"
                           :class="form.halfDayPeriod === 'morning' ? 'bg-yellow-50 border-yellow-500' : ''">
                        <input type="radio" name="half_day_period" x-model="form.halfDayPeriod" value="morning" class="sr-only">
                        <div class="flex items-center space-x-3">
                            <div class="h-4 w-4 rounded-full border-2 transition-all duration-300"
                                 :class="form.halfDayPeriod === 'morning' ? 'bg-yellow-500 border-yellow-500' : 'border-gray-300'"></div>
                            <span class="font-medium">🌅 Morning (8:00 - 12:00)</span>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border border-gray-300 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all duration-300"
                           :class="form.halfDayPeriod === 'afternoon' ? 'bg-yellow-50 border-yellow-500' : ''">
                        <input type="radio" name="half_day_period" x-model="form.halfDayPeriod" value="afternoon" class="sr-only">
                        <div class="flex items-center space-x-3">
                            <div class="h-4 w-4 rounded-full border-2 transition-all duration-300"
                                 :class="form.halfDayPeriod === 'afternoon' ? 'bg-yellow-500 border-yellow-500' : 'border-gray-300'"></div>
                            <span class="font-medium">🌇 Afternoon (13:00 - 17:00)</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Days Calculation --}}
            <div x-show="form.totalDays > 0" x-transition class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6">
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-blue-900">Total Leave Days</div>
                        <div class="text-2xl font-bold text-blue-600" x-text="form.totalDays + (form.totalDays === 1 ? ' day' : ' days')"></div>
                    </div>
                </div>
            </div>

            {{-- Reason --}}
            <div>
                <label for="reason" class="block text-lg font-bold text-gray-900 mb-3">
                    📝 Reason for Leave
                </label>
                <textarea name="reason" id="reason" rows="4" 
                          class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-yellow-200 focus:border-yellow-500 text-lg text-gray-900 bg-white placeholder-gray-500 transition-all duration-300 resize-none" 
                          placeholder="Please provide a detailed reason for your leave request..." required></textarea>
                @error('reason')
                    <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- Emergency Contact (for longer leaves) --}}
            <div x-show="form.totalDays >= 3" x-transition class="space-y-4">
                <label class="block text-lg font-bold text-gray-900">
                    📞 Emergency Contact (Optional)
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="emergency_contact_name" 
                           class="w-full px-4 py-3 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-yellow-200 focus:border-yellow-500 text-gray-900 bg-white placeholder-gray-500" 
                           placeholder="Contact name">
                    <input type="tel" name="emergency_contact_phone" 
                           class="w-full px-4 py-3 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-yellow-200 focus:border-yellow-500 text-gray-900 bg-white placeholder-gray-500" 
                           placeholder="Phone number">
                </div>
            </div>

            {{-- Handover Notes --}}
            <div>
                <label for="handover_notes" class="block text-lg font-bold text-gray-900 mb-3">
                    🔄 Work Handover Notes (Optional)
                </label>
                <textarea name="handover_notes" id="handover_notes" rows="3" 
                          class="w-full px-4 py-4 rounded-2xl border border-gray-300 focus:ring-4 focus:ring-yellow-200 focus:border-yellow-500 text-lg text-gray-900 bg-white placeholder-gray-500 transition-all duration-300 resize-none" 
                          placeholder="Any work instructions or tasks to be handed over during your absence..."></textarea>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 pt-6">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                    <div class="flex items-center justify-center space-x-3">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>🚀 Submit Leave Request</span>
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
function leaveForm() {
    return {
        form: {
            type: '',
            durationType: 'full',
            startDate: '',
            endDate: '',
            halfDayPeriod: 'morning',
            totalDays: 0
        },
        
        calculateDays() {
            if (!this.form.startDate) {
                this.form.totalDays = 0;
                return;
            }
            
            if (this.form.durationType === 'half') {
                this.form.totalDays = 0.5;
                this.form.endDate = this.form.startDate;
                return;
            }
            
            if (this.form.startDate && this.form.endDate) {
                const start = new Date(this.form.startDate);
                const end = new Date(this.form.endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                this.form.totalDays = diffDays;
            } else {
                this.form.totalDays = 1;
                this.form.endDate = this.form.startDate;
            }
        }
    }
}
</script>
@endsection
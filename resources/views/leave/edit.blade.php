@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-orange-600 via-red-600 to-pink-600 text-white rounded-3xl p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/0 to-white/10"></div>
        <div class="absolute -top-10 -right-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Edit Pengajuan Cuti</h1>
                <p class="mt-2 text-orange-100">Perbarui informasi pengajuan cuti Anda</p>
            </div>
            <div class="text-7xl opacity-20 select-none">✏️</div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="flex items-center space-x-4">
        <a href="{{ route('leave.show', $leave) }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Detail
        </a>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('leave.update', $leave) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Informasi Cuti</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Leave Type --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Cuti *</label>
                    <select name="type" id="type" required 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Jenis Cuti</option>
                        <option value="annual" {{ old('type', $leave->type) === 'annual' ? 'selected' : '' }}>Cuti Tahunan</option>
                        <option value="sick" {{ old('type', $leave->type) === 'sick' ? 'selected' : '' }}>Cuti Sakit</option>
                        <option value="emergency" {{ old('type', $leave->type) === 'emergency' ? 'selected' : '' }}>Cuti Darurat</option>
                        <option value="maternity" {{ old('type', $leave->type) === 'maternity' ? 'selected' : '' }}>Cuti Melahirkan</option>
                        <option value="paternity" {{ old('type', $leave->type) === 'paternity' ? 'selected' : '' }}>Cuti Ayah</option>
                        <option value="unpaid" {{ old('type', $leave->type) === 'unpaid' ? 'selected' : '' }}>Cuti Tanpa Gaji</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Days Requested (calculated automatically) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Durasi Cuti</label>
                    <div class="flex items-center space-x-2">
                        <input type="number" id="days_display" readonly
                               class="w-20 border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-center font-semibold"
                               value="{{ old('days_requested', $leave->days_requested) }}">
                        <span class="text-gray-600">hari</span>
                    </div>
                    <input type="hidden" name="days_requested" id="days_requested" value="{{ old('days_requested', $leave->days_requested) }}">
                </div>

                {{-- Start Date --}}
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai *</label>
                    <input type="date" name="start_date" id="start_date" required
                           value="{{ old('start_date', $leave->start_date) }}"
                           min="{{ now()->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- End Date --}}
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai *</label>
                    <input type="date" name="end_date" id="end_date" required
                           value="{{ old('end_date', $leave->end_date) }}"
                           min="{{ now()->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Reason --}}
            <div class="mt-6">
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan Cuti *</label>
                <textarea name="reason" id="reason" rows="4" required
                          placeholder="Jelaskan alasan mengajukan cuti..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('reason', $leave->reason) }}</textarea>
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Current Attachment --}}
            @if($leave->attachment)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran Saat Ini</label>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ basename($leave->attachment) }}</p>
                            <a href="{{ Storage::url($leave->attachment) }}" 
                               target="_blank"
                               class="text-blue-600 hover:text-blue-800 text-xs">
                                Lihat File →
                            </a>
                        </div>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="remove_attachment" value="1" class="rounded">
                            <span class="text-sm text-red-600">Hapus</span>
                        </label>
                    </div>
                </div>
            @endif

            {{-- New Attachment --}}
            <div class="mt-6">
                <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $leave->attachment ? 'Ganti Lampiran' : 'Lampiran' }} (Opsional)
                </label>
                <input type="file" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">
                    Format yang diizinkan: PDF, JPG, PNG, DOC, DOCX. Maksimal 2MB.
                </p>
                @error('attachment')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Submit Section --}}
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-semibold text-gray-900">Perbarui Pengajuan Cuti</h4>
                    <p class="text-sm text-gray-600">Pastikan semua informasi sudah benar sebelum memperbarui.</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('leave.show', $leave) }}" 
                       class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors">
                        💾 Perbarui Pengajuan
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- Information Box --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6">
        <div class="flex items-start space-x-3">
            <div class="text-2xl">⚠️</div>
            <div>
                <h4 class="font-semibold text-yellow-900 mb-2">Catatan Penting</h4>
                <ul class="text-yellow-800 text-sm space-y-1">
                    <li>• Pengajuan hanya dapat diubah selama masih berstatus "Pending"</li>
                    <li>• Setelah diperbarui, pengajuan akan tetap dalam status "Pending" untuk ditinjau ulang</li>
                    <li>• Pastikan tanggal dan alasan cuti sudah sesuai kebutuhan</li>
                    <li>• Lampiran baru akan menggantikan lampiran sebelumnya jika ada</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Calculate days between start and end date
function calculateDays() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        if (end >= start) {
            // Calculate business days (excluding weekends)
            let days = 0;
            let current = new Date(start);
            
            while (current <= end) {
                const dayOfWeek = current.getDay();
                if (dayOfWeek !== 0 && dayOfWeek !== 6) { // Not Sunday (0) or Saturday (6)
                    days++;
                }
                current.setDate(current.getDate() + 1);
            }
            
            document.getElementById('days_display').value = days;
            document.getElementById('days_requested').value = days;
        } else {
            document.getElementById('days_display').value = 0;
            document.getElementById('days_requested').value = 0;
        }
    }
}

// Add event listeners
document.getElementById('start_date').addEventListener('change', calculateDays);
document.getElementById('end_date').addEventListener('change', calculateDays);

// Set minimum end date when start date changes
document.getElementById('start_date').addEventListener('change', function() {
    document.getElementById('end_date').min = this.value;
});

// Calculate initial days on page load
document.addEventListener('DOMContentLoaded', calculateDays);

// File size validation
document.getElementById('attachment').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const maxSize = 2 * 1024 * 1024; // 2MB in bytes
        if (file.size > maxSize) {
            alert('Ukuran file terlalu besar. Maksimal 2MB.');
            this.value = '';
        }
    }
});
</script>
@endpush
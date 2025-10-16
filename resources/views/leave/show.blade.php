@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white rounded-3xl p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/0 to-white/10"></div>
        <div class="absolute -top-10 -right-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Detail Pengajuan Cuti</h1>
                <p class="mt-2 text-blue-100">{{ $leave->user->name }}</p>
            </div>
            <div class="text-7xl opacity-20 select-none">📝</div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="flex items-center space-x-4">
        <a href="{{ route('leave.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
        
        @if($leave->status === 'pending' && (Auth::user()->hasRole('admin') || (Auth::user()->hasRole('manager') && Auth::user()->subordinates()->pluck('id')->contains($leave->user_id))))
            <div class="flex space-x-2">
                <form action="{{ route('leave.approve', $leave) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        ✅ Setujui
                    </button>
                </form>
                
                <form action="{{ route('leave.reject', $leave) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        ❌ Tolak
                    </button>
                </form>
            </div>
        @endif
        
        @if($leave->user_id === Auth::id() && $leave->status === 'pending')
            <a href="{{ route('leave.edit', $leave) }}" 
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                ✏️ Edit
            </a>
        @endif
    </div>

    {{-- Leave Details Card --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Left Column --}}
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Pemohon</label>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-medium">{{ substr($leave->user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $leave->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $leave->user->department->name ?? 'No Department' }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Jenis Cuti</label>
                    <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full 
                        @if($leave->type === 'annual') bg-blue-100 text-blue-800
                        @elseif($leave->type === 'sick') bg-red-100 text-red-800
                        @elseif($leave->type === 'emergency') bg-orange-100 text-orange-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($leave->type) }}
                    </span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Tanggal Cuti</label>
                    <div class="space-y-2">
                        <p class="text-gray-900">
                            <span class="font-medium">Mulai:</span> 
                            {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}
                        </p>
                        <p class="text-gray-900">
                            <span class="font-medium">Selesai:</span> 
                            {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Durasi</label>
                    <p class="text-2xl font-bold text-indigo-600">{{ $leave->days_requested }} hari</p>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Status</label>
                    <span class="inline-flex px-4 py-2 text-lg font-semibold rounded-full 
                        @if($leave->status === 'approved') bg-green-100 text-green-800
                        @elseif($leave->status === 'rejected') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        @if($leave->status === 'approved') ✅ Disetujui
                        @elseif($leave->status === 'rejected') ❌ Ditolak
                        @else ⏳ Menunggu Persetujuan
                        @endif
                    </span>
                </div>

                @if($leave->approved_by)
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Disetujui Oleh</label>
                        <p class="text-gray-900 font-medium">{{ $leave->approver->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600">{{ $leave->approved_at ? \Carbon\Carbon::parse($leave->approved_at)->format('d M Y H:i') : '' }}</p>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Tanggal Pengajuan</label>
                    <p class="text-gray-900">{{ $leave->created_at->format('d M Y H:i') }}</p>
                </div>

                @if($leave->approval_notes)
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Catatan Persetujuan</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-900">{{ $leave->approval_notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Reason Section --}}
        @if($leave->reason)
            <div class="mt-8 pt-8 border-t border-gray-200">
                <label class="block text-sm font-medium text-gray-600 mb-3">Alasan Cuti</label>
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <p class="text-gray-900 leading-relaxed">{{ $leave->reason }}</p>
                </div>
            </div>
        @endif

        {{-- Attachment Section --}}
        @if($leave->attachment)
            <div class="mt-8 pt-8 border-t border-gray-200">
                <label class="block text-sm font-medium text-gray-600 mb-3">Lampiran</label>
                <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-900">{{ basename($leave->attachment) }}</p>
                        <a href="{{ Storage::url($leave->attachment) }}" 
                           target="_blank"
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            Lihat File →
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Additional Information --}}
    @if($leave->type === 'annual')
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
            <div class="flex items-start space-x-3">
                <div class="text-2xl">ℹ️</div>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2">Informasi Cuti Tahunan</h4>
                    <p class="text-blue-800 text-sm">
                        @if($leave->status === 'approved')
                            Cuti tahunan ini telah mengurangi sisa jatah cuti Anda sebanyak {{ $leave->days_requested }} hari.
                        @else
                            Jika disetujui, cuti ini akan mengurangi sisa jatah cuti sebanyak {{ $leave->days_requested }} hari.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
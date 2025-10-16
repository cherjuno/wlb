@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-blue-600 text-white rounded-3xl p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/0 to-white/10"></div>
        <div class="absolute -top-10 -right-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white">Detail Lembur</h1>
                <p class="mt-2 text-indigo-100">Informasi detail pengajuan lembur</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('overtime.index') }}" 
                   class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl font-semibold hover:bg-white/30 transition-colors">
                    ← Kembali
                </a>
                <div class="text-7xl opacity-20 select-none">⏰</div>
            </div>
        </div>
    </div>

    {{-- Overtime Details --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-8">
        {{-- Header Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            {{-- Employee Info --}}
            <div class="space-y-4">
                <h3 class="text-xl font-semibold text-gray-900">Informasi Karyawan</h3>
                <div class="bg-gray-50 rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-lg font-bold">{{ substr($overtime->user->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-4">
                            <div class="font-semibold text-gray-900">{{ $overtime->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $overtime->user->employee_id }}</div>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Department:</span>
                            <span class="font-medium">{{ $overtime->user->department->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Position:</span>
                            <span class="font-medium">{{ $overtime->user->position->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status & Approval Info --}}
            <div class="space-y-4">
                <h3 class="text-xl font-semibold text-gray-900">Status & Persetujuan</h3>
                <div class="bg-gray-50 rounded-xl p-6">
                    <div class="mb-4">
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                            @if($overtime->status === 'approved') bg-green-100 text-green-800
                            @elseif($overtime->status === 'rejected') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            @if($overtime->status === 'approved') ✅ Disetujui
                            @elseif($overtime->status === 'rejected') ❌ Ditolak
                            @else ⏳ Pending
                            @endif
                        </span>
                    </div>
                    
                    @if($overtime->approver)
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Disetujui Oleh:</span>
                                <span class="font-medium">{{ $overtime->approver->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal Persetujuan:</span>
                                <span class="font-medium">{{ $overtime->approved_at ? \Carbon\Carbon::parse($overtime->approved_at)->format('d M Y H:i') : '-' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-sm text-gray-500">Menunggu persetujuan</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Overtime Details --}}
        <div class="border-t border-gray-200 pt-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Detail Lembur</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                {{-- Date --}}
                <div class="bg-blue-50 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600">Tanggal</p>
                            <p class="text-2xl font-bold text-blue-900">{{ \Carbon\Carbon::parse($overtime->date)->format('d') }}</p>
                            <p class="text-sm text-blue-700">{{ \Carbon\Carbon::parse($overtime->date)->format('M Y') }}</p>
                        </div>
                        <div class="h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">📅</span>
                        </div>
                    </div>
                </div>

                {{-- Hours --}}
                <div class="bg-purple-50 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-600">Total Jam</p>
                            <p class="text-2xl font-bold text-purple-900">{{ number_format($overtime->hours, 1, '.', '') }}h</p>
                            <p class="text-sm text-purple-700">{{ $overtime->start_time }} - {{ $overtime->end_time }}</p>
                        </div>
                        <div class="h-12 w-12 bg-purple-500 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">⏰</span>
                        </div>
                    </div>
                </div>

                {{-- Type --}}
                <div class="bg-indigo-50 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-indigo-600">Jenis Lembur</p>
                            <p class="text-2xl font-bold text-indigo-900">{{ ucfirst($overtime->type) }}</p>
                            <p class="text-sm text-indigo-700">
                                @if($overtime->type === 'weekend') Akhir Pekan
                                @elseif($overtime->type === 'holiday') Hari Libur
                                @else Hari Kerja
                                @endif
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-indigo-500 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">
                                @if($overtime->type === 'weekend') 🏖️
                                @elseif($overtime->type === 'holiday') 🎉
                                @else 💼
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reason --}}
            <div class="bg-gray-50 rounded-xl p-6 mb-8">
                <h4 class="font-semibold text-gray-900 mb-3">Alasan Lembur</h4>
                <p class="text-gray-700 leading-relaxed">{{ $overtime->reason }}</p>
            </div>

            {{-- Approval Notes --}}
            @if($overtime->approval_notes)
                <div class="bg-blue-50 rounded-xl p-6 mb-8">
                    <h4 class="font-semibold text-blue-900 mb-3">Catatan Persetujuan</h4>
                    <p class="text-blue-800 leading-relaxed">{{ $overtime->approval_notes }}</p>
                </div>
            @endif
        </div>

        {{-- Action Buttons --}}
        <div class="border-t border-gray-200 pt-8">
            <div class="flex items-center justify-between">
                <div class="flex space-x-4">
                    @if($overtime->status === 'pending' && $overtime->user_id === Auth::id())
                        <a href="{{ route('overtime.edit', $overtime) }}" 
                           class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                            ✏️ Edit
                        </a>
                        
                        <form action="{{ route('overtime.destroy', $overtime) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Yakin ingin menghapus pengajuan lembur ini?')"
                                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                                🗑️ Hapus
                            </button>
                        </form>
                    @endif

                    @if($overtime->status === 'pending' && (Auth::user()->hasRole('admin') || (Auth::user()->hasRole('manager') && Auth::user()->subordinates()->pluck('id')->contains($overtime->user_id))))
                        <form action="{{ route('overtime.approve', $overtime) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                                ✅ Setujui
                            </button>
                        </form>
                        
                        <form action="{{ route('overtime.reject', $overtime) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                                ❌ Tolak
                            </button>
                        </form>
                    @endif
                </div>
                
                <div class="text-sm text-gray-500">
                    Created {{ $overtime->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-emerald-600 via-blue-600 to-indigo-600 text-white rounded-3xl p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/0 to-white/10"></div>
        <div class="absolute -top-10 -right-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white">Manajemen Cuti</h1>
                <p class="mt-2 text-emerald-100">Kelola pengajuan cuti dan riwayat cuti</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('leave.create') }}" 
                   class="bg-white text-emerald-600 px-6 py-3 rounded-xl font-semibold hover:bg-emerald-50 transition-colors">
                    📝 Ajukan Cuti Baru
                </a>
                @if(auth()->user()->hasRole(['admin', 'manager']))
                    <a href="{{ route('leave.pending') }}" 
                       class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl font-semibold hover:bg-white/30 transition-colors inline-flex items-center">
                        ⏳ Pending Approvals
                        @php
                            $pendingLeaves = \App\Models\Leave::where('status', 'pending');
                            if(auth()->user()->hasRole('manager')) {
                                $subordinateIds = auth()->user()->subordinates()->pluck('id');
                                $pendingLeaves = $pendingLeaves->whereIn('user_id', $subordinateIds);
                            }
                            $pendingCount = $pendingLeaves->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="ml-2 bg-orange-500 text-white rounded-full px-2 py-1 text-xs font-bold">{{ $pendingCount }}</span>
                        @endif
                    </a>
                @endif
                <div class="text-7xl opacity-20 select-none">🏖️</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <form method="GET" action="{{ route('leave.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            @if(auth()->user()->hasRole(['admin', 'manager']))
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-600 mb-2">Karyawan</label>
                    <select name="user_id" id="user_id" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900">
                        <option value="">Semua Karyawan</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            
            <div>
                <label for="type" class="block text-sm font-medium text-gray-600 mb-2">Jenis Cuti</label>
                <select name="type" id="type" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900">
                    <option value="">Semua Jenis</option>
                    <option value="annual" {{ request('type') == 'annual' ? 'selected' : '' }}>Cuti Tahunan</option>
                    <option value="sick" {{ request('type') == 'sick' ? 'selected' : '' }}>Cuti Sakit</option>
                    <option value="emergency" {{ request('type') == 'emergency' ? 'selected' : '' }}>Cuti Darurat</option>
                    <option value="maternity" {{ request('type') == 'maternity' ? 'selected' : '' }}>Cuti Melahirkan</option>
                    <option value="paternity" {{ request('type') == 'paternity' ? 'selected' : '' }}>Cuti Ayah</option>
                    <option value="unpaid" {{ request('type') == 'unpaid' ? 'selected' : '' }}>Cuti Tanpa Gaji</option>
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-600 mb-2">Status</label>
                <select name="status" id="status" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-600 mb-2">Dari Tanggal</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900">
            </div>
            
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors">
                    🔍 Filter
                </button>
                <a href="{{ route('leave.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Leave Table --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Pengajuan Cuti</h3>
        </div>
        
        @if(count($leaves) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @if(auth()->user()->hasRole(['admin', 'manager']))
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disetujui Oleh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($leaves as $leave)
                            <tr class="hover:bg-gray-50">
                                @if(auth()->user()->hasRole(['admin', 'manager']))
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-medium">{{ substr($leave->user->name, 0, 1) }}</span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $leave->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $leave->user->employee_id ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($leave->type === 'annual') bg-blue-100 text-blue-800
                                        @elseif($leave->type === 'sick') bg-red-100 text-red-800
                                        @elseif($leave->type === 'emergency') bg-orange-100 text-orange-800
                                        @elseif($leave->type === 'maternity') bg-pink-100 text-pink-800
                                        @elseif($leave->type === 'paternity') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($leave->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>
                                        <div class="font-medium">{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</div>
                                        <div class="text-gray-500">s/d {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-lg font-bold text-indigo-600">{{ $leave->days_requested }}</span>
                                    <span class="text-sm text-gray-500">hari</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($leave->status === 'approved') bg-green-100 text-green-800
                                        @elseif($leave->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        @if($leave->status === 'approved') ✅ Disetujui
                                        @elseif($leave->status === 'rejected') ❌ Ditolak
                                        @else ⏳ Pending
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $leave->approver->name ?? '-' }}
                                    @if($leave->approved_at)
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($leave->approved_at)->format('d M Y') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('leave.show', $leave) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">View</a>
                                    
                                    @if($leave->user_id === auth()->id() && $leave->status === 'pending')
                                        <a href="{{ route('leave.edit', $leave) }}" 
                                           class="text-orange-600 hover:text-orange-900">Edit</a>
                                        
                                        <form action="{{ route('leave.destroy', $leave) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Yakin ingin menghapus pengajuan cuti ini?')"
                                                    class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    @endif
                                    
                                    @if($leave->status === 'pending' && (auth()->user()->hasRole('admin') || (auth()->user()->hasRole('manager') && auth()->user()->subordinates()->pluck('id')->contains($leave->user_id))))
                                        <form action="{{ route('leave.approve', $leave) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                        </form>
                                        
                                        <form action="{{ route('leave.reject', $leave) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🏖️</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Pengajuan Cuti</h3>
                <p class="text-gray-600 mb-4">Mulai ajukan cuti untuk mendapatkan work-life balance yang lebih baik.</p>
                <a href="{{ route('leave.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                    📝 Ajukan Cuti Sekarang
                </a>
            </div>
        @endif
        
        @if($leaves->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $leaves->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
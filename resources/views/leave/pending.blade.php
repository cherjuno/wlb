@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data>
    <!-- Header -->
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-2xl p-6 shadow">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Persetujuan Cuti</h1>
                <p class="text-amber-100">Tinjau dan proses pengajuan cuti yang belum disetujui</p>
            </div>
            <a href="{{ route('overtime.pending') }}" class="inline-flex items-center gap-2 rounded-lg bg-white/20 hover:bg-white/30 px-4 py-2 text-sm font-semibold text-white">
                <span>⏰ Lembur Pending</span>
            </a>
        </div>
    </div>

    <!-- Empty state -->
    @if($pendingLeaves->count() === 0)
        <div class="bg-white rounded-2xl border border-gray-100 shadow p-12 text-center">
            <div class="text-5xl mb-3">🎉</div>
            <p class="text-gray-700 font-semibold">Tidak ada pengajuan cuti yang menunggu persetujuan.</p>
        </div>
    @else
        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Karyawan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Periode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Hari</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Alasan</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pendingLeaves as $leave)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900">{{ $leave->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $leave->user->department->name ?? '—' }}</div>
                                </td>
                                <td class="px-4 py-3 capitalize">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">{{ $leave->type }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }} — {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-900 font-semibold">{{ $leave->days_requested }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 max-w-md">{{ $leave->reason }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('leave.reject', $leave) }}" method="POST" onsubmit="return confirm('Tolak pengajuan ini?')">
                                            @csrf
                                            <input type="hidden" name="approval_notes" value="Ditolak oleh atasan">
                                            <button class="px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-sm font-semibold">Tolak</button>
                                        </form>
                                        <form action="{{ route('leave.approve', $leave) }}" method="POST" onsubmit="return confirm('Setujui pengajuan ini?')">
                                            @csrf
                                            <input type="hidden" name="approval_notes" value="Disetujui oleh atasan">
                                            <button class="px-3 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-semibold">Setujui</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $pendingLeaves->links() }}</div>
        </div>
    @endif
</div>
@endsection
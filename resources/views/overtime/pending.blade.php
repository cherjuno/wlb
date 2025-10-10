@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-violet-500 to-indigo-500 text-white rounded-2xl p-6 shadow">
        <h1 class="text-2xl font-bold">Persetujuan Lembur</h1>
        <p class="text-indigo-100">Tinjau pengajuan lembur yang menunggu persetujuan</p>
    </div>

    @if($pendingOvertimes->count() === 0)
        <div class="bg-white rounded-2xl border border-gray-100 shadow p-12 text-center">
            <div class="text-5xl mb-3">✅</div>
            <p class="text-gray-700 font-semibold">Tidak ada pengajuan lembur pending.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Karyawan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Durasi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Alasan</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pendingOvertimes as $ot)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $ot->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $ot->user->department->name ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ \Carbon\Carbon::parse($ot->date)->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $ot->hours }} jam</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $ot->type == 'weekend' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($ot->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 max-w-md">{{ $ot->reason }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('overtime.reject', $ot) }}" method="POST" onsubmit="return confirm('Tolak pengajuan lembur ini?')">
                                        @csrf
                                        <input type="hidden" name="approval_notes" value="Ditolak oleh atasan">
                                        <button class="px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-sm font-semibold">Tolak</button>
                                    </form>
                                    <form action="{{ route('overtime.approve', $ot) }}" method="POST" onsubmit="return confirm('Setujui pengajuan lembur ini?')">
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

            <div class="mt-4">{{ $pendingOvertimes->links() }}</div>
        </div>
    @endif
</div>
@endsection
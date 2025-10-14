@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-3xl p-8 shadow-xl">
        <div class="flex items-center space-x-4">
            <div class="h-16 w-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 10c-4.418 0-8-1.79-8-4V7c0-2.21 3.582-4 8-4s8 1.79 8 4v7c0 2.21-3.582 4-8 4z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold">💰 Informasi Gaji</h1>
                <p class="text-green-100 mt-2">Detail gaji dan perhitungan bulanan Anda</p>
            </div>
        </div>
    </div>

    {{-- Employee Info --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <h3 class="text-xl font-bold text-gray-900 mb-6">👤 Informasi Karyawan</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-600">Nama Lengkap</label>
                    <div class="text-lg font-semibold text-gray-900">{{ $user->name }}</div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Employee ID</label>
                    <div class="text-lg font-semibold text-gray-900">{{ $user->employee_id }}</div>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-600">Departemen</label>
                    <div class="text-lg font-semibold text-gray-900">{{ $user->department->name ?? '-' }}</div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Posisi</label>
                    <div class="text-lg font-semibold text-gray-900">{{ $user->position->name ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Salary Breakdown --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Monthly Salary --}}
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-3xl p-6 border border-blue-200">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-bold text-blue-900">Gaji Pokok</h4>
                <div class="h-10 w-10 bg-blue-500 rounded-xl flex items-center justify-center">
                    <span class="text-white text-lg">💼</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-blue-700 mb-2">
                Rp {{ number_format($monthlySalary, 0, ',', '.') }}
            </div>
            <div class="text-sm text-blue-600">Per bulan</div>
        </div>

        {{-- Annual Salary --}}
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-3xl p-6 border border-green-200">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-bold text-green-900">Gaji Tahunan</h4>
                <div class="h-10 w-10 bg-green-500 rounded-xl flex items-center justify-center">
                    <span class="text-white text-lg">🗓️</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-green-700 mb-2">
                Rp {{ number_format($annualSalary, 0, ',', '.') }}
            </div>
            <div class="text-sm text-green-600">Per tahun</div>
        </div>

        {{-- Daily Salary --}}
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-3xl p-6 border border-purple-200">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-bold text-purple-900">Gaji Harian</h4>
                <div class="h-10 w-10 bg-purple-500 rounded-xl flex items-center justify-center">
                    <span class="text-white text-lg">📅</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-purple-700 mb-2">
                Rp {{ number_format($dailySalary, 0, ',', '.') }}
            </div>
            <div class="text-sm text-purple-600">Per hari</div>
        </div>
    </div>

    {{-- Current Month Calculation --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <h3 class="text-xl font-bold text-gray-900 mb-6">📊 Perhitungan Bulan {{ now()->format('F Y') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Additions --}}
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-green-700 flex items-center gap-2">
                    <span>➕</span> Tambahan
                </h4>
                
                <div class="bg-green-50 rounded-2xl p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Gaji Pokok</span>
                        <span class="font-semibold text-green-700">
                            + Rp {{ number_format($monthlySalary, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                
                <div class="bg-green-50 rounded-2xl p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Lembur Bulan Ini</span>
                        <span class="font-semibold text-green-700">
                            + Rp {{ number_format($overtimeEarnings, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Deductions --}}
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-red-700 flex items-center gap-2">
                    <span>➖</span> Potongan
                </h4>
                
                <div class="bg-red-50 rounded-2xl p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Cuti Tidak Dibayar</span>
                        <span class="font-semibold text-red-700">
                            - Rp {{ number_format($leaveDeductions, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-2xl p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Pajak & Potongan Lain</span>
                        <span class="font-semibold text-gray-700">
                            Lihat Payslip
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Net Salary --}}
        <div class="mt-8 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h4 class="text-lg font-semibold mb-2">💰 Estimasi Gaji Bersih Bulan Ini</h4>
                    <p class="text-indigo-100 text-sm">*Sebelum pajak dan potongan lainnya</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold">
                        Rp {{ number_format($netSalary, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Info --}}
    <div class="bg-blue-50 rounded-2xl p-6 border border-blue-200">
        <div class="flex items-start space-x-3">
            <div class="text-2xl">ℹ️</div>
            <div>
                <h4 class="font-semibold text-blue-900 mb-2">Informasi Penting</h4>
                <ul class="text-blue-700 text-sm space-y-1">
                    <li>• Perhitungan di atas adalah estimasi berdasarkan data yang tersedia</li>
                    <li>• Gaji final dapat berbeda setelah dipotong pajak, BPJS, dan tunjangan lainnya</li>
                    <li>• Untuk detail lengkap, silakan lihat payslip resmi dari HR</li>
                    <li>• Lembur dihitung berdasarkan jam kerja normal 173 jam/bulan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
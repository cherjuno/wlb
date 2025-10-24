@extends('layouts.app')

@section('title', 'Detail Analisis - ' . $employee->name)

@push('styles')
<style>
.metric-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-left: 4px solid;
}

.metric-card.optimal { border-left-color: #28a745; }
.metric-card.warning { border-left-color: #ffc107; }
.metric-card.danger { border-left-color: #dc3545; }
.metric-card.secondary { border-left-color: #6c757d; }

.progress-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2em;
    margin: 0 auto;
}

.quadrant-position {
    max-width: 400px;
    margin: 20px auto;
    position: relative;
    border: 2px solid #dee2e6;
    border-radius: 12px;
    padding: 20px;
}

.quadrant-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    height: 200px;
}

.quadrant-cell {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875em;
    text-align: center;
    opacity: 0.3;
    transition: all 0.3s;
}

.quadrant-cell.active {
    opacity: 1;
    font-weight: bold;
    transform: scale(1.05);
}

.quadrant-cell.q1 { background-color: #d4edda; border-color: #28a745; }
.quadrant-cell.q2 { background-color: #fff3cd; border-color: #ffc107; }
.quadrant-cell.q3 { background-color: #e2e3e5; border-color: #6c757d; }
.quadrant-cell.q4 { background-color: #f8d7da; border-color: #dc3545; }

.employee-position {
    position: absolute;
    width: 20px;
    height: 20px;
    background: #007bff;
    border: 3px solid white;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    z-index: 10;
}

.metric-item {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
}

.recommendation-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    padding: 20px;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <div>
            <h1>Detail Analisis WLB-Stress</h1>
            <p class="text-muted mb-0">{{ $employee->name }} - {{ $employee->department->name ?? 'N/A' }}</p>
        </div>
        <a href="{{ route('analytics.employee-matrix') }}?period={{ $period }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Analisis
        </a>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('analytics.employee-matrix') }}">Analisis Matrik</a></li>
        <li class="breadcrumb-item active">{{ $employee->name }}</li>
    </ol>

    <!-- Employee Info & Quadrant Position -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card metric-card">
                <div class="card-body text-center">
                    <div class="avatar bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 80px; height: 80px;">
                        <span class="text-white h3 mb-0">
                            {{ strtoupper(substr($employee->name, 0, 2)) }}
                        </span>
                    </div>
                    <h5 class="mb-1">{{ $employee->name }}</h5>
                    <p class="text-muted mb-2">{{ $employee->position->name ?? 'N/A' }}</p>
                    <p class="text-muted mb-0">{{ $employee->department->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card metric-card 
                @if($matrixData['quadrant'] == 1) optimal 
                @elseif($matrixData['quadrant'] == 2) warning 
                @elseif($matrixData['quadrant'] == 3) secondary 
                @else danger @endif">
                <div class="card-header">
                    <h6 class="mb-0">Posisi dalam Matrik WLB-Stress</h6>
                </div>
                <div class="card-body">
                    <div class="quadrant-position">
                        <div class="quadrant-grid">
                            <div class="quadrant-cell q1 {{ $matrixData['quadrant'] == 1 ? 'active' : '' }}">
                                <div>
                                    <strong>Q1</strong><br>
                                    <small>WLB Tinggi<br>Stress Rendah</small>
                                </div>
                            </div>
                            <div class="quadrant-cell q2 {{ $matrixData['quadrant'] == 2 ? 'active' : '' }}">
                                <div>
                                    <strong>Q2</strong><br>
                                    <small>WLB Tinggi<br>Stress Tinggi</small>
                                </div>
                            </div>
                            <div class="quadrant-cell q3 {{ $matrixData['quadrant'] == 3 ? 'active' : '' }}">
                                <div>
                                    <strong>Q3</strong><br>
                                    <small>WLB Rendah<br>Stress Rendah</small>
                                </div>
                            </div>
                            <div class="quadrant-cell q4 {{ $matrixData['quadrant'] == 4 ? 'active' : '' }}">
                                <div>
                                    <strong>Q4</strong><br>
                                    <small>WLB Rendah<br>Stress Tinggi</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Labels -->
                        <div class="position-absolute" style="top: -20px; left: 50%; transform: translateX(-50%); font-size: 0.875em; font-weight: bold;">
                            Tingkat Stress
                        </div>
                        <div class="position-absolute" style="top: -10px; left: 25%; font-size: 0.75em; color: #28a745;">Rendah</div>
                        <div class="position-absolute" style="top: -10px; right: 25%; font-size: 0.75em; color: #dc3545;">Tinggi</div>
                        
                        <div class="position-absolute" style="left: -80px; top: 50%; transform: translateY(-50%) rotate(-90deg); font-size: 0.875em; font-weight: bold;">
                            Work-Life Balance
                        </div>
                        <div class="position-absolute" style="left: -50px; top: 25%; font-size: 0.75em; color: #28a745; transform: rotate(-90deg);">Tinggi</div>
                        <div class="position-absolute" style="left: -50px; bottom: 25%; font-size: 0.75em; color: #dc3545; transform: rotate(-90deg);">Rendah</div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <span class="badge 
                            @if($matrixData['quadrant'] == 1) bg-success 
                            @elseif($matrixData['quadrant'] == 2) bg-warning 
                            @elseif($matrixData['quadrant'] == 3) bg-secondary 
                            @else bg-danger @endif
                            fs-6 px-3 py-2">
                            {{ $matrixData['quadrant_name'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Metrics Overview -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card metric-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-balance-scale text-primary"></i> Skor Work-Life Balance
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <div class="progress-circle 
                                {{ $matrixData['wlb_level'] === 'high' ? 'bg-success' : 'bg-danger' }} text-white">
                                {{ number_format($matrixData['wlb_score'], 0) }}
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar 
                                    {{ $matrixData['wlb_level'] === 'high' ? 'bg-success' : 'bg-danger' }}" 
                                    style="width: {{ $matrixData['wlb_score'] }}%"></div>
                            </div>
                            <p class="mb-1">
                                <strong>Status:</strong> 
                                <span class="{{ $matrixData['wlb_level'] === 'high' ? 'text-success' : 'text-danger' }}">
                                    {{ $matrixData['wlb_level'] === 'high' ? 'Baik' : 'Perlu Perbaikan' }}
                                </span>
                            </p>
                            <small class="text-muted">
                                Berdasarkan data lembur, keterlambatan, dan penggunaan cuti
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card metric-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-heartbeat text-warning"></i> Skor Job Stress Scale (JSS)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <div class="progress-circle 
                                {{ $matrixData['stress_level'] === 'low' ? 'bg-success' : 'bg-warning' }} text-white">
                                {{ number_format($matrixData['jss_score'], 1) }}
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar 
                                    {{ $matrixData['stress_level'] === 'low' ? 'bg-success' : 'bg-warning' }}" 
                                    style="width: {{ ($matrixData['jss_score'] / 5) * 100 }}%"></div>
                            </div>
                            <p class="mb-1">
                                <strong>Level:</strong> 
                                <span class="{{ $matrixData['stress_level'] === 'low' ? 'text-success' : 'text-warning' }}">
                                    {{ $matrixData['stress_level'] === 'low' ? 'Normal' : 'Tinggi' }}
                                </span>
                            </p>
                            <small class="text-muted">
                                Skala 1-5 berdasarkan assessment terbaru
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Metrics -->
    @if(isset($matrixData['detailed_metrics']))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Detail Metrik Kinerja
                        <small class="text-muted">{{ $matrixData['period']['label'] ?? '' }}</small>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="metric-item text-center">
                                <h4 class="text-primary mb-1">{{ $matrixData['detailed_metrics']['overtime_hours'] }}</h4>
                                <small class="text-muted">Jam Lembur</small>
                                <div class="mt-2">
                                    @if($matrixData['detailed_metrics']['overtime_hours'] > 40)
                                        <span class="badge bg-danger">Tinggi</span>
                                    @elseif($matrixData['detailed_metrics']['overtime_hours'] > 20)
                                        <span class="badge bg-warning">Sedang</span>
                                    @else
                                        <span class="badge bg-success">Normal</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-item text-center">
                                <h4 class="text-warning mb-1">{{ $matrixData['detailed_metrics']['late_count'] }}</h4>
                                <small class="text-muted">Keterlambatan</small>
                                <div class="mt-2">
                                    @if($matrixData['detailed_metrics']['late_count'] > 5)
                                        <span class="badge bg-danger">Sering</span>
                                    @elseif($matrixData['detailed_metrics']['late_count'] > 2)
                                        <span class="badge bg-warning">Kadang</span>
                                    @else
                                        <span class="badge bg-success">Jarang</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-item text-center">
                                <h4 class="text-info mb-1">{{ $matrixData['detailed_metrics']['leave_days'] }}</h4>
                                <small class="text-muted">Hari Cuti</small>
                                <div class="mt-2">
                                    @if($matrixData['detailed_metrics']['leave_days'] >= 3)
                                        <span class="badge bg-success">Optimal</span>
                                    @elseif($matrixData['detailed_metrics']['leave_days'] >= 1)
                                        <span class="badge bg-warning">Cukup</span>
                                    @else
                                        <span class="badge bg-danger">Kurang</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-item text-center">
                                <h4 class="text-success mb-1">{{ number_format($matrixData['detailed_metrics']['attendance_rate'], 1) }}%</h4>
                                <small class="text-muted">Tingkat Kehadiran</small>
                                <div class="mt-2">
                                    @if($matrixData['detailed_metrics']['attendance_rate'] >= 95)
                                        <span class="badge bg-success">Excellent</span>
                                    @elseif($matrixData['detailed_metrics']['attendance_rate'] >= 90)
                                        <span class="badge bg-info">Baik</span>
                                    @elseif($matrixData['detailed_metrics']['attendance_rate'] >= 80)
                                        <span class="badge bg-warning">Cukup</span>
                                    @else
                                        <span class="badge bg-danger">Kurang</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recommendations -->
    <div class="row">
        <div class="col-12">
            <div class="recommendation-box">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-lightbulb fa-2x me-3"></i>
                    <h5 class="mb-0">Rekomendasi Tindak Lanjut</h5>
                </div>
                <p class="mb-0">{{ $matrixData['recommendation'] }}</p>
                
                @if($matrixData['quadrant'] == 4)
                <div class="mt-3 p-3 bg-danger bg-opacity-25 rounded">
                    <strong>⚠️ Perhatian Khusus:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Segera evaluasi beban kerja karyawan</li>
                        <li>Pertimbangkan konseling atau dukungan psikologis</li>
                        <li>Review distribusi tugas dalam tim</li>
                        <li>Monitor perkembangan mingguan</li>
                    </ul>
                </div>
                @elseif($matrixData['quadrant'] == 2)
                <div class="mt-3 p-3 bg-warning bg-opacity-25 rounded">
                    <strong>⚠️ Tindakan Preventif:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Implementasikan teknik manajemen stress</li>
                        <li>Pastikan karyawan mengambil cuti secara teratur</li>
                        <li>Evaluasi deadline dan ekspektasi kerja</li>
                    </ul>
                </div>
                @elseif($matrixData['quadrant'] == 1)
                <div class="mt-3 p-3 bg-success bg-opacity-25 rounded">
                    <strong>✅ Kelebihan yang Dapat Dimanfaatkan:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Dapat menjadi mentor untuk rekan kerja</li>
                        <li>Kandidat untuk proyek pengembangan</li>
                        <li>Role model work-life balance</li>
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Animate progress circles on load
    $('.progress-circle').each(function() {
        $(this).animate({
            opacity: 1
        }, 1000);
    });
});
</script>
@endpush
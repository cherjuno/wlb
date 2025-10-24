@extends('layouts.app')

@section('title', 'Analisis Matrik Karyawan - WLB & Stress')

@push('styles')
<style>
.matrix-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    max-width: 600px;
    margin: 20px auto;
}

.quadrant-card {
    border: 2px solid;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    transition: transform 0.2s;
    color: #2d3748 !important;
}

.quadrant-card:hover {
    transform: translateY(-2px);
}

.quadrant-1 {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-color: #28a745;
    color: #155724 !important;
}

.quadrant-2 {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-color: #ffc107;
    color: #856404 !important;
}

.quadrant-3 {
    background: linear-gradient(135deg, #e2e3e5 0%, #d1ecf1 100%);
    border-color: #6c757d;
    color: #383d41 !important;
}

.quadrant-4 {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border-color: #dc3545;
    color: #721c24 !important;
}

/* Enhanced table styling for better visibility */
.employee-table {
    background-color: #ffffff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.employee-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.employee-table thead th {
    color: #ffffff !important;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
    border: none;
}

.employee-table tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.employee-table tbody tr:hover {
    background-color: #f7fafc;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.employee-table tbody td {
    color: #000000 !important;
    font-weight: 500;
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border: none;
}

.employee-table tbody td * {
    color: #000000 !important;
}

.employee-table tbody td .text-muted {
    color: #6c757d !important;
}

.employee-table tbody td .fw-semibold,
.employee-table tbody td .fw-bold {
    color: #000000 !important;
}

.employee-table .badge {
    font-size: 0.875em;
    padding: 0.375rem 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.25px;
}

.metric-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.metric-card .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 1.5rem;
}

.metric-card .card-header h5 {
    color: #2d3748 !important;
    font-weight: 700;
    margin: 0;
}

.metric-card .card-body {
    padding: 1.5rem;
}

.filter-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid #dee2e6;
}

.filter-section .form-label {
    color: #2d3748 !important;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.filter-section .form-select {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    color: #2d3748 !important;
    background-color: #ffffff;
    transition: all 0.2s ease;
}

.filter-section .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.stress-indicator, .wlb-indicator {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.stress-low { background-color: #28a745; }
.stress-high { background-color: #dc3545; }
.wlb-high { background-color: #28a745; }
.wlb-low { background-color: #dc3545; }

/* Avatar styling */
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 0.875rem;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Button styling */
.btn-outline-primary {
    border-color: #667eea;
    color: #667eea;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-outline-primary:hover {
    background-color: #667eea;
    border-color: #667eea;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

/* Breadcrumb styling */
.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 1.5rem;
}

.breadcrumb-item {
    color: #6c757d;
}

.breadcrumb-item.active {
    color: #2d3748;
    font-weight: 600;
}

.breadcrumb-item a {
    color: #667eea;
    text-decoration: none;
    transition: color 0.2s ease;
}

.breadcrumb-item a:hover {
    color: #5a67d8;
}

/* Page title */
h1.mt-4 {
    color: #2d3748 !important;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

/* Empty state styling */
.text-center.py-4 {
    padding: 3rem 1rem !important;
}

.text-center.py-4 i {
    opacity: 0.3;
    margin-bottom: 1rem;
}

.text-center.py-4 p {
    color: #6c757d !important;
    font-size: 1.1rem;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .matrix-container {
        max-width: 100%;
        gap: 10px;
    }
    
    .quadrant-card {
        padding: 15px;
    }
    
    .employee-table thead th,
    .employee-table tbody td {
        padding: 0.75rem 0.5rem;
        font-size: 0.875rem;
    }
}

/* FORCE ALL TEXT TO BE BLACK AND VISIBLE */
.card-body *,
.metric-card *,
.stats-card *,
.employee-table tbody td *,
.bg-light *,
.bg-success *,
.bg-danger *,
.bg-warning *,
.bg-opacity-10 *,
.text-muted,
small,
h4,
h5,
h6,
.fw-semibold,
.fw-bold {
    color: #000000 !important;
    text-shadow: none !important;
}

/* Keep colored badges and primary elements */
.badge {
    color: white !important;
}

.text-primary {
    color: #007bff !important;
}

.text-success {
    color: #28a745 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.text-warning {
    color: #ffc107 !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Analisis Matrik WLB-Stress Karyawan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Analisis Matrik Karyawan</li>
    </ol>

    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="{{ route('analytics.employee-matrix') }}">
            <div class="row">
                <div class="col-md-3">
                    <label for="period" class="form-label">Periode Analisis</label>
                    <select name="period" id="period" class="form-select">
                        <option value="current_month" {{ request('period') == 'current_month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Bulan Lalu</option>
                        <option value="current_quarter" {{ request('period') == 'current_quarter' ? 'selected' : '' }}>Kuartal Ini</option>
                        <option value="current_year" {{ request('period') == 'current_year' ? 'selected' : '' }}>Tahun Ini</option>
                    </select>
                </div>
                @if(auth()->user()->hasRole('admin'))
                <div class="col-md-3">
                    <label for="department_id" class="form-label">Departemen</label>
                    <select name="department_id" id="department_id" class="form-select">
                        <option value="">Semua Departemen</option>
                        @foreach(\App\Models\Department::where('is_active', true)->get() as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Analisis
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Matrix Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card metric-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie"></i> Distribusi Matrik WLB-Stress
                        <small class="text-muted">({{ $dateRange['label'] }})</small>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="matrix-container">
                        <!-- Quadrant 1: High WLB, Low Stress -->
                        <div class="quadrant-card quadrant-1">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <span class="wlb-indicator wlb-high"></span>
                                <span class="stress-indicator stress-low"></span>
                            </div>
                            <h3 class="mb-1">{{ $quadrantSummary['q1'] }}</h3>
                            <h6>WLB Tinggi, Stress Rendah</h6>
                            <small>Kondisi Optimal</small>
                        </div>

                        <!-- Quadrant 2: High WLB, High Stress -->
                        <div class="quadrant-card quadrant-2">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <span class="wlb-indicator wlb-high"></span>
                                <span class="stress-indicator stress-high"></span>
                            </div>
                            <h3 class="mb-1">{{ $quadrantSummary['q2'] }}</h3>
                            <h6>WLB Tinggi, Stress Tinggi</h6>
                            <small>Berpotensi Burnout</small>
                        </div>

                        <!-- Quadrant 3: Low WLB, Low Stress -->
                        <div class="quadrant-card quadrant-3">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <span class="wlb-indicator wlb-low"></span>
                                <span class="stress-indicator stress-low"></span>
                            </div>
                            <h3 class="mb-1">{{ $quadrantSummary['q3'] }}</h3>
                            <h6>WLB Rendah, Stress Rendah</h6>
                            <small>Kurang Engaged</small>
                        </div>

                        <!-- Quadrant 4: Low WLB, High Stress -->
                        <div class="quadrant-card quadrant-4">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <span class="wlb-indicator wlb-low"></span>
                                <span class="stress-indicator stress-high"></span>
                            </div>
                            <h3 class="mb-1">{{ $quadrantSummary['q4'] }}</h3>
                            <h6>WLB Rendah, Stress Tinggi</h6>
                            <small>Kondisi Kritis</small>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-3 text-center">
                            <div class="p-3 bg-light rounded">
                                <h4 class="fw-bold mb-1" style="color: #007bff !important;">{{ array_sum($quadrantSummary) }}</h4>
                                <small style="color: #2d3748 !important; font-weight: 600;">Total Karyawan</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <h4 class="fw-bold mb-1" style="color: #28a745 !important;">{{ round(($quadrantSummary['q1'] / max(array_sum($quadrantSummary), 1)) * 100, 1) }}%</h4>
                                <small style="color: #2d3748 !important; font-weight: 600;">Kondisi Optimal</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="p-3 bg-danger bg-opacity-10 rounded">
                                <h4 class="fw-bold mb-1" style="color: #dc3545 !important;">{{ round(($quadrantSummary['q4'] / max(array_sum($quadrantSummary), 1)) * 100, 1) }}%</h4>
                                <small style="color: #2d3748 !important; font-weight: 600;">Perlu Perhatian</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="p-3 bg-warning bg-opacity-10 rounded">
                                <h4 class="fw-bold mb-1" style="color: #ffc107 !important;">{{ round((($quadrantSummary['q2'] + $quadrantSummary['q3']) / max(array_sum($quadrantSummary), 1)) * 100, 1) }}%</h4>
                                <small style="color: #2d3748 !important; font-weight: 600;">Perlu Monitoring</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Details Table -->
    <div class="row">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> Detail Posisi Karyawan dalam Matrik
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover employee-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Karyawan</th>
                                    <th style="width: 15%;">Departemen</th>
                                    <th style="width: 15%;">Skor WLB</th>
                                    <th style="width: 15%;">Skor JSS</th>
                                    <th style="width: 12%;">Kuadran</th>
                                    <th style="width: 18%;">Status</th>
                                    <th style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employeeMatrix as $data)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <span class="text-white fw-bold">
                                                    {{ strtoupper(substr($data['employee']->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $data['employee']->name }}</div>
                                                <small class="text-muted">{{ $data['employee']->position->name ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $data['employee']->department->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="wlb-indicator {{ $data['matrix_data']['wlb_level'] === 'high' ? 'wlb-high' : 'wlb-low' }}"></span>
                                            <div>
                                                <span class="fw-bold text-dark">{{ number_format($data['matrix_data']['wlb_score'], 1) }}</span>
                                                <br>
                                                <small class="text-muted">{{ $data['matrix_data']['wlb_level'] === 'high' ? 'Tinggi' : 'Rendah' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="stress-indicator {{ $data['matrix_data']['stress_level'] === 'low' ? 'stress-low' : 'stress-high' }}"></span>
                                            <div>
                                                <span class="fw-bold text-dark">{{ number_format($data['matrix_data']['jss_score'], 1) }}</span>
                                                <br>
                                                <small class="text-muted">{{ $data['matrix_data']['stress_level'] === 'low' ? 'Rendah' : 'Tinggi' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill
                                            @if($data['matrix_data']['quadrant'] == 1) bg-success 
                                            @elseif($data['matrix_data']['quadrant'] == 2) bg-warning text-dark
                                            @elseif($data['matrix_data']['quadrant'] == 3) bg-secondary 
                                            @else bg-danger @endif">
                                            Q{{ $data['matrix_data']['quadrant'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold
                                            @if($data['matrix_data']['quadrant'] == 1) text-success 
                                            @elseif($data['matrix_data']['quadrant'] == 2) text-warning 
                                            @elseif($data['matrix_data']['quadrant'] == 3) text-secondary 
                                            @else text-danger @endif">
                                            @if($data['matrix_data']['quadrant'] == 1) 
                                                Optimal
                                            @elseif($data['matrix_data']['quadrant'] == 2) 
                                                Perlu Monitoring
                                            @elseif($data['matrix_data']['quadrant'] == 3) 
                                                Kurang Engaged
                                            @else 
                                                Kritis
                                            @endif
                                        </div>
                                        <small class="text-muted d-block">{{ 
                                            $data['matrix_data']['quadrant'] == 1 ? 'Kondisi Ideal' :
                                            ($data['matrix_data']['quadrant'] == 2 ? 'Risiko Burnout' :
                                            ($data['matrix_data']['quadrant'] == 3 ? 'Butuh Motivasi' : 'Perlu Intervensi'))
                                        }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('analytics.employee-detail', $data['employee']->id) }}?period={{ $period }}" 
                                           class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-users fa-4x text-muted mb-3 opacity-50"></i>
                                            <h5 class="text-muted">Tidak ada data karyawan</h5>
                                            <p class="text-muted mb-0">Tidak ada data karyawan untuk periode yang dipilih.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header">
                    <h6 class="mb-0 text-dark fw-bold">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Keterangan Indikator
                    </h6>
                </div>
                <div class="card-body" style="background: white; color: black;">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3" style="color: #2d3748 !important;">Indikator WLB (Work-Life Balance):</h6>
                            <div class="d-flex align-items-center mb-2">
                                <span class="wlb-indicator wlb-high me-3"></span>
                                <div style="color: #2d3748 !important;">
                                    <strong style="color: #1f2937 !important;">Tinggi (≥70):</strong>
                                    <span style="color: #374151 !important;">Keseimbangan kerja-hidup baik</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="wlb-indicator wlb-low me-3"></span>
                                <div style="color: #2d3748 !important;">
                                    <strong style="color: #1f2937 !important;">Rendah (<70):</strong>
                                    <span style="color: #374151 !important;">Keseimbangan kerja-hidup kurang</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3" style="color: #2d3748 !important;">Indikator Stress (JSS Score):</h6>
                            <div class="d-flex align-items-center mb-2">
                                <span class="stress-indicator stress-low me-3"></span>
                                <div style="color: #2d3748 !important;">
                                    <strong style="color: #1f2937 !important;">Rendah (<3.0):</strong>
                                    <span style="color: #374151 !important;">Tingkat stress normal</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="stress-indicator stress-high me-3"></span>
                                <div style="color: #2d3748 !important;">
                                    <strong style="color: #1f2937 !important;">Tinggi (≥3.0):</strong>
                                    <span style="color: #374151 !important;">Tingkat stress tinggi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                                <div>
                                    <strong class="text-dark">Tinggi (≥3.0):</strong>
                                    <span class="text-muted">Tingkat stress tinggi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quadrant Legend -->
                    <hr class="my-4">
                    <h6 class="text-dark fw-bold mb-3">Penjelasan Kuadran:</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="p-3 border border-success rounded bg-success bg-opacity-10">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-success rounded-pill me-2">Q1</span>
                                    <strong class="text-success">Optimal</strong>
                                </div>
                                <small class="text-dark">WLB tinggi + Stress rendah</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border border-warning rounded bg-warning bg-opacity-10">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-warning text-dark rounded-pill me-2">Q2</span>
                                    <strong class="text-warning">Burnout Risk</strong>
                                </div>
                                <small class="text-dark">WLB tinggi + Stress tinggi</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border border-secondary rounded bg-secondary bg-opacity-10">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-secondary rounded-pill me-2">Q3</span>
                                    <strong class="text-secondary">Low Engagement</strong>
                                </div>
                                <small class="text-dark">WLB rendah + Stress rendah</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border border-danger rounded bg-danger bg-opacity-10">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-danger rounded-pill me-2">Q4</span>
                                    <strong class="text-danger">Kritis</strong>
                                </div>
                                <small class="text-dark">WLB rendah + Stress tinggi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form when period changes
    $('#period, #department_id').change(function() {
        $(this).closest('form').submit();
    });
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush
@extends('layouts.app')

@section('title', 'WLB-Stress Matrix Overview')

@push('styles')
<style>
.matrix-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    max-width: 500px;
    margin: 20px auto;
}

.quadrant-card {
    border: 3px solid;
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, var(--bg-from), var(--bg-to));
    color: #2d3748 !important;
}

.quadrant-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.quadrant-1 {
    --bg-from: #d4edda;
    --bg-to: #c3e6cb;
    border-color: #28a745;
    color: #155724 !important;
}

.quadrant-2 {
    --bg-from: #fff3cd;
    --bg-to: #ffeaa7;
    border-color: #ffc107;
    color: #856404 !important;
}

.quadrant-3 {
    --bg-from: #e2e3e5;
    --bg-to: #d1ecf1;
    border-color: #6c757d;
    color: #383d41 !important;
}

.quadrant-4 {
    --bg-from: #f8d7da;
    --bg-to: #f5c6cb;
    border-color: #dc3545;
    color: #721c24 !important;
}

.indicator {
    width: 14px;
    height: 14px;
    border-radius: 3px;
    display: inline-block;
    margin-right: 8px;
    border: 1px solid rgba(255, 255, 255, 0.4);
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.wlb-high { 
    background: linear-gradient(135deg, #28a745, #20c997);
}
.wlb-low { 
    background: linear-gradient(135deg, #dc3545, #e74c3c);
}
.stress-low { 
    background: linear-gradient(135deg, #007bff, #0056b3);
}
.stress-high { 
    background: linear-gradient(135deg, #ffc107, #fd7e14);
}

.stats-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.stats-card h3, .stats-card h4, .stats-card h5, .stats-card h6 {
    color: #2d3748 !important;
}

.stats-card p, .stats-card small {
    color: #6c757d !important;
}

.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 16px;
    padding: 32px;
    margin-bottom: 32px;
}

/* Legend section styling - FORCE BLACK TEXT */
.legend-section {
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    background: white !important;
}

.legend-section *,
.legend-section h5,
.legend-section h6,
.legend-section div,
.legend-section span:not(.indicator),
.legend-section strong {
    color: #000000 !important;
    text-shadow: none !important;
}

.legend-section .text-primary {
    color: #007bff !important;
}

.legend-item {
    padding: 10px 0;
    color: #000000 !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.legend-item:last-child {
    border-bottom: none;
}

.legend-item strong {
    color: #000000 !important;
    font-weight: 600;
}

.legend-item span:not(.indicator) {
    color: #000000 !important;
}

.legend-item .d-flex {
    align-items: center;
}

.legend-item * {
    color: #000000 !important;
}

/* Enhanced legend items */
.summary-stats {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 2rem;
    margin-top: 2rem;
}

.stat-item {
    background: #ffffff;
    border-radius: 8px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: 2px solid transparent;
    transition: all 0.2s ease;
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.stat-item.primary { border-color: #007bff; }
.stat-item.success { border-color: #28a745; }
.stat-item.danger { border-color: #dc3545; }
.stat-item.warning { border-color: #ffc107; }

.stat-item h4 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-item small {
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Legend improvements */
.legend-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 1.5rem;
}

.legend-item {
    background: #ffffff;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-left: 4px solid;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.legend-item.wlb-high { border-left-color: #28a745; }
.legend-item.wlb-low { border-left-color: #6c757d; }
.legend-item.stress-low { border-left-color: #28a745; }
.legend-item.stress-high { border-left-color: #dc3545; }

.legend-item strong {
    color: #000000 !important;
    font-weight: 600;
}

.legend-item span {
    color: #000000 !important;
}

/* Action buttons */
.action-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 1.5rem;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

.btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    transform: translateY(-1px);
}

/* Employee info box */
.employee-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-radius: 8px;
    padding: 1rem;
    border-left: 4px solid #2196f3;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold mb-3">WLB-Stress Matrix</h1>
                <p class="lead mb-0">Visualisasi distribusi karyawan berdasarkan Work-Life Balance dan tingkat stress</p>
            </div>
            <div class="col-md-4 text-end">
                <div style="font-size: 4rem; opacity: 0.3;">📊</div>
            </div>
        </div>
    </div>

    <!-- Matrix Overview -->
    <div class="row">
        <div class="col-12">
            <div class="stats-card p-4 mb-4">
                <div class="text-center mb-4">
                    <h3 class="h4 mb-2">Distribusi Karyawan dalam Matrix</h3>
                    <p class="text-muted">Periode: <span class="fw-semibold">Bulan Ini</span></p>
                </div>
                
                <div class="matrix-container">
                    <!-- Quadrant 1: High WLB, Low Stress -->
                    <div class="quadrant-card quadrant-1">
                        <div class="mb-3">
                            <span class="indicator wlb-high"></span>
                            <span class="indicator stress-low"></span>
                        </div>
                        <h2 class="fw-bold mb-2" id="q1-count">-</h2>
                        <h6 class="fw-semibold mb-1">WLB Tinggi, Stress Rendah</h6>
                        <small class="fw-medium">Kondisi Optimal</small>
                    </div>

                    <!-- Quadrant 2: High WLB, High Stress -->
                    <div class="quadrant-card quadrant-2">
                        <div class="mb-3">
                            <span class="indicator wlb-high"></span>
                            <span class="indicator stress-high"></span>
                        </div>
                        <h2 class="fw-bold mb-2" id="q2-count">-</h2>
                        <h6 class="fw-semibold mb-1">WLB Tinggi, Stress Tinggi</h6>
                        <small class="fw-medium">Berpotensi Burnout</small>
                    </div>

                    <!-- Quadrant 3: Low WLB, Low Stress -->
                    <div class="quadrant-card quadrant-3">
                        <div class="mb-3">
                            <span class="indicator wlb-low"></span>
                            <span class="indicator stress-low"></span>
                        </div>
                        <h2 class="fw-bold mb-2" id="q3-count">-</h2>
                        <h6 class="fw-semibold mb-1">WLB Rendah, Stress Rendah</h6>
                        <small class="fw-medium">Kurang Engaged</small>
                    </div>

                    <!-- Quadrant 4: Low WLB, High Stress -->
                    <div class="quadrant-card quadrant-4">
                        <div class="mb-3">
                            <span class="indicator wlb-low"></span>
                            <span class="indicator stress-high"></span>
                        </div>
                        <h2 class="fw-bold mb-2" id="q4-count">-</h2>
                        <h6 class="fw-semibold mb-1">WLB Rendah, Stress Tinggi</h6>
                        <small class="fw-medium">Kondisi Kritis</small>
                    </div>
                </div>

                <!-- Summary Statistics -->
                <div class="summary-stats">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="stat-item primary">
                                <h4 class="text-primary fw-bold" id="total-employees">-</h4>
                                <small class="text-dark">Total Karyawan</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item success">
                                <h4 class="text-success fw-bold" id="optimal-percentage">-%</h4>
                                <small class="text-dark">Kondisi Optimal</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item danger">
                                <h4 class="text-danger fw-bold" id="critical-percentage">-%</h4>
                                <small class="text-dark">Perlu Perhatian</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item warning">
                                <h4 class="text-warning fw-bold" id="monitoring-percentage">-%</h4>
                                <small class="text-dark">Perlu Monitoring</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend and Information -->
    <div class="row">
        <div class="col-md-6">
            <div class="stats-card p-4 legend-section" style="background: white; color: black;">
                <h5 class="fw-semibold mb-3" style="color: #2d3748 !important;">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Keterangan Indikator
                </h5>
                <div class="mb-3">
                    <h6 class="fw-semibold mb-2" style="color: #2d3748 !important;">Indikator WLB (Work-Life Balance):</h6>
                    <div class="legend-item wlb-high" style="color: #2d3748 !important;">
                        <div class="d-flex align-items-center">
                            <span class="indicator wlb-high me-2"></span>
                            <div style="color: #2d3748 !important;">
                                <strong style="color: #1f2937 !important;">Tinggi (≥70):</strong>
                                <span style="color: #374151 !important;">Keseimbangan kerja-hidup baik</span>
                            </div>
                        </div>
                    </div>
                    <div class="legend-item wlb-low" style="color: #2d3748 !important;">
                        <div class="d-flex align-items-center">
                            <span class="indicator wlb-low me-2"></span>
                            <div style="color: #2d3748 !important;">
                                <strong style="color: #1f2937 !important;">Rendah (<70):</strong>
                                <span style="color: #374151 !important;">Keseimbangan kerja-hidup kurang</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h6 class="fw-semibold mb-2" style="color: #2d3748 !important;">Indikator Stress (JSS Score):</h6>
                    <div class="legend-item stress-low" style="color: #2d3748 !important;">
                        <div class="d-flex align-items-center">
                            <span class="indicator stress-low me-2"></span>
                            <div style="color: #2d3748 !important;">
                                <strong style="color: #1f2937 !important;">Rendah (<3.0):</strong>
                                <span style="color: #374151 !important;">Tingkat stress normal</span>
                            </div>
                        </div>
                    </div>
                    <div class="legend-item stress-high" style="color: #2d3748 !important;">
                        <div class="d-flex align-items-center">
                            <span class="indicator stress-high me-2"></span>
                            <div style="color: #2d3748 !important;">
                                <strong style="color: #1f2937 !important;">Tinggi (≥3.0):</strong>
                                <span style="color: #374151 !important;">Tingkat stress tinggi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="stats-card p-4">
                <h5 class="fw-semibold mb-3">
                    <i class="fas fa-chart-line text-success me-2"></i>
                    Aksi Lanjutan
                </h5>
                <div class="d-grid gap-2">
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                    <a href="{{ route('analytics.employee-matrix') }}" class="btn btn-primary">
                        <i class="fas fa-users me-2"></i>
                        Lihat Detail per Karyawan
                    </a>
                    @endif
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
                
                @if(auth()->user()->hasRole('employee'))
                <div class="mt-3 p-3 bg-light rounded">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Untuk melihat posisi WLB personal Anda, kunjungi Dashboard utama.
                    </small>
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
    // Load matrix data
    loadMatrixData();
    
    function loadMatrixData() {
        fetch('{{ route("analytics.matrix-overview") }}?period=current_month')
            .then(response => response.json())
            .then(data => {
                updateMatrixDisplay(data);
            })
            .catch(error => {
                console.error('Error loading matrix data:', error);
                showDefaultData();
            });
    }
    
    function updateMatrixDisplay(data) {
        const quadrants = data.quadrants;
        const total = data.total_employees;
        
        // Update quadrant counts
        $('#q1-count').text(quadrants[1]);
        $('#q2-count').text(quadrants[2]);
        $('#q3-count').text(quadrants[3]);
        $('#q4-count').text(quadrants[4]);
        
        // Update summary statistics
        $('#total-employees').text(total);
        $('#optimal-percentage').text(total > 0 ? Math.round((quadrants[1] / total) * 100) + '%' : '0%');
        $('#critical-percentage').text(total > 0 ? Math.round((quadrants[4] / total) * 100) + '%' : '0%');
        $('#monitoring-percentage').text(total > 0 ? Math.round(((quadrants[2] + quadrants[3]) / total) * 100) + '%' : '0%');
    }
    
    function showDefaultData() {
        // Show default data if API fails
        $('#q1-count, #q2-count, #q3-count, #q4-count').text('0');
        $('#total-employees').text('0');
        $('#optimal-percentage, #critical-percentage, #monitoring-percentage').text('0%');
    }
    
    // Add hover effects
    $('.quadrant-card').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );
});
</script>
@endpush
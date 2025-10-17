@extends('layouts.app')

@section('content')
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-chart-line text-info me-2"></i>
                    Dashboard Manajemen
                </h2>
                <p class="text-muted">Laporan, analisis, dan monitoring kinerja - Selamat datang, {{ auth()->user()->name }}</p>
            </div>
            <div>
                <span class="badge bg-info fs-6">Management Access</span>
            </div>
        </div>
    </div>
</div>

    <!-- Key Performance Indicators -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background: #667eea;">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-tint fa-2x" style="opacity: 0.8;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-white">Total Konsumsi</h6>
                            <h4 class="mb-0 text-white fw-bold">12,345 m³</h4>
                            <small style="opacity: 0.8;">Bulan ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background: #e74c3c;">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-money-bill-wave fa-2x" style="opacity: 0.8;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-white">Pendapatan</h6>
                            <h4 class="mb-0 text-white fw-bold">Rp 245.8 M</h4>
                            <small style="opacity: 0.8;">Bulan ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background: #3498db;">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-percentage fa-2x" style="opacity: 0.8;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-white">Tingkat Bayar</h6>
                            <h4 class="mb-0 text-white fw-bold">87.5%</h4>
                            <small style="opacity: 0.8;">Collection rate</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background: #27ae60;">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users fa-2x" style="opacity: 0.8;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-white">Pelanggan Aktif</h6>
                            <h4 class="mb-0 text-white fw-bold">8,432</h4>
                            <small style="opacity: 0.8;">Total customers</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="row g-4 mb-4">
        <!-- Financial Reports -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Laporan Keuangan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('manajemen.reports') }}?type=revenue" class="btn btn-primary w-100">
                                <i class="fas fa-money-bill-wave me-2"></i>
                                Laporan Pendapatan
                            </a>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-outline-primary w-100" onclick="generateReport('collection')">
                                <i class="fas fa-percentage me-2"></i>
                                Collection Rate
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-success w-100" onclick="generateReport('monthly')">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Laporan Bulanan
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-info w-100" onclick="generateReport('yearly')">
                                <i class="fas fa-calendar me-2"></i>
                                Laporan Tahunan
                            </button>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-success mb-1">-- IDR</h5>
                                <small class="text-muted">Pendapatan Bulan Ini</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-warning mb-1">-- IDR</h5>
                                <small class="text-muted">Target Bulanan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operational Reports -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Laporan Operasional
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button class="btn btn-info w-100" onclick="generateReport('consumption')">
                                <i class="fas fa-tint me-2"></i>
                                Konsumsi Air
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-outline-info w-100" onclick="generateReport('meter')">
                                <i class="fas fa-gauge-high me-2"></i>
                                Pembacaan Meter
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-warning w-100" onclick="generateReport('customers')">
                                <i class="fas fa-users me-2"></i>
                                Data Pelanggan
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-secondary w-100" onclick="generateReport('zones')">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Distribusi Zona
                            </button>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-info mb-1">-- m³</h5>
                                <small class="text-muted">Konsumsi Bulan Ini</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-secondary mb-1">-- Meter</h5>
                                <small class="text-muted">Total Meter Aktif</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Dashboard -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-area text-primary me-2"></i>
                            Trend Pendapatan & Konsumsi
                        </h6>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary active" onclick="showChart('monthly')">Bulanan</button>
                            <button class="btn btn-outline-primary" onclick="showChart('yearly')">Tahunan</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">Grafik Trend akan ditampilkan di sini</h6>
                                <p class="text-muted mb-0">Integrasi dengan Chart.js untuk visualisasi data</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0">
                        <i class="fas fa-download text-success me-2"></i>
                        Export Laporan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Periode Laporan:</label>
                        <select class="form-select" id="reportPeriod">
                            <option value="">Pilih Periode</option>
                            <option value="current_month">Bulan Ini</option>
                            <option value="last_month">Bulan Lalu</option>
                            <option value="quarterly">Triwulan</option>
                            <option value="yearly">Tahunan</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Format Export:</label>
                        <select class="form-select" id="exportFormat">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel (.xlsx)</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="exportReport()">
                            <i class="fas fa-download me-2"></i>
                            Download Laporan
                        </button>
                        <button class="btn btn-outline-info" onclick="emailReport()">
                            <i class="fas fa-envelope me-2"></i>
                            Email Laporan
                        </button>
                    </div>

                    <hr>

                    <h6 class="mb-2">Quick Export:</h6>
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action py-2" onclick="quickExport('revenue')">
                            <i class="fas fa-file-pdf text-danger me-2"></i>
                            Laporan Pendapatan
                        </a>
                        <a href="#" class="list-group-item list-group-item-action py-2" onclick="quickExport('consumption')">
                            <i class="fas fa-file-excel text-success me-2"></i>
                            Data Konsumsi
                        </a>
                        <a href="#" class="list-group-item list-group-item-action py-2" onclick="quickExport('customers')">
                            <i class="fas fa-file-csv text-info me-2"></i>
                            Database Pelanggan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateReport(type) {
    switch(type) {
        case 'collection':
            alert('Generating Collection Rate Report...');
            break;
        case 'monthly':
            alert('Generating Monthly Financial Report...');
            break;
        case 'yearly':
            alert('Generating Annual Report...');
            break;
        case 'consumption':
            alert('Generating Water Consumption Report...');
            break;
        case 'meter':
            alert('Generating Meter Reading Report...');
            break;
        case 'customers':
            alert('Generating Customer Data Report...');
            break;
        case 'zones':
            alert('Generating Zone Distribution Report...');
            break;
        default:
            alert('Report type: ' + type);
    }
}

function showChart(period) {
    // Update active button
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    alert('Showing ' + period + ' chart data');
}

function exportReport() {
    const period = document.getElementById('reportPeriod').value;
    const format = document.getElementById('exportFormat').value;
    
    if (!period) {
        alert('Pilih periode laporan terlebih dahulu');
        return;
    }
    
    alert(`Exporting ${period} report as ${format}...`);
}

function emailReport() {
    const period = document.getElementById('reportPeriod').value;
    
    if (!period) {
        alert('Pilih periode laporan terlebih dahulu');
        return;
    }
    
    alert(`Sending ${period} report via email...`);
}

function quickExport(type) {
    alert(`Quick export: ${type} report`);
}
</script>
@endsection

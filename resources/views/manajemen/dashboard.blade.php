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
                            <h4 class="mb-0 text-white fw-bold" id="total-consumption">--</h4>
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
                            <h4 class="mb-0 text-white fw-bold" id="total-revenue">--</h4>
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
                            <h4 class="mb-0 text-white fw-bold" id="collection-rate">--</h4>
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
                            <h4 class="mb-0 text-white fw-bold" id="active-customers">--</h4>
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
                                <h5 class="text-success mb-1" id="monthly-revenue">--</h5>
                                <small class="text-muted">Pendapatan Bulan Ini</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-warning mb-1" id="monthly-target">--</h5>
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
                                <h5 class="text-info mb-1" id="monthly-consumption">--</h5>
                                <small class="text-muted">Konsumsi Bulan Ini</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-secondary mb-1" id="active-meters">--</h5>
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
// Global variables
let dashboardData = {};

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
});

// Load dashboard data from API
async function loadDashboardData() {
    try {
        showLoadingState();

        const response = await fetch('/api/dashboard/management', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        dashboardData = await response.json();
        updateDashboardUI();
        hideLoadingState();

    } catch (error) {
        console.error('Error loading dashboard data:', error);
        showError('Data Tidak Ada');
    } catch (error) {
        hideLoadingState();
    }
}

// Update dashboard UI with loaded data
function updateDashboardUI() {
    const data = dashboardData.overview || {};

    // Update KPI cards
    document.getElementById('total-consumption').textContent = formatNumber(data.total_consumption || 0) + ' m³';
    document.getElementById('total-revenue').textContent = 'Rp ' + formatCurrency(data.total_revenue || 0);
    document.getElementById('collection-rate').textContent = (data.collection_rate || 0) + '%';
    document.getElementById('active-customers').textContent = formatNumber(data.active_customers || 0);

    // Update financial reports section
    document.getElementById('monthly-revenue').textContent = 'Rp ' + formatCurrency(data.monthly_revenue || 0);
    document.getElementById('monthly-target').textContent = 'Rp ' + formatCurrency(data.monthly_target || 0);

    // Update operational reports section
    document.getElementById('monthly-consumption').textContent = formatNumber(data.monthly_consumption || 0) + ' m³';
    document.getElementById('active-meters').textContent = formatNumber(data.active_meters || 0);
}

// Generate report function
async function generateReport(type) {
    try {
        showToast('Memproses laporan...', 'info');

        const params = new URLSearchParams({
            type: type,
            date_from: getCurrentMonthStart(),
            date_to: getCurrentMonthEnd()
        });

        const response = await fetch(`/api/reports/generate?${params.toString()}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`Failed to generate ${type} report`);
        }

        const reportData = await response.json();
        showToast(`Laporan ${type} berhasil dibuat`, 'success');

        // Here you could open the report in a new window or show it in a modal
        // For now, just show success message

    } catch (error) {
        console.error('Error generating report:', error);
        showToast('Gagal membuat laporan: ' + error.message, 'error');
    }
}

function showChart(period) {
    // Update active button
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    // Here you would update the chart with different data based on period
    showToast(`Menampilkan data ${period}`, 'info');
}

async function exportReport() {
    const period = document.getElementById('reportPeriod').value;
    const format = document.getElementById('exportFormat').value;

    if (!period) {
        showToast('Pilih periode laporan terlebih dahulu', 'warning');
        return;
    }

    try {
        showToast(`Memproses export laporan...`, 'info');

        const params = new URLSearchParams({
            period: period,
            format: format
        });

        const response = await fetch(`/api/reports/export?${params.toString()}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error('Failed to export report');
        }

        // Trigger download
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `report_${period}.${format}`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        showToast(`Laporan berhasil di-download`, 'success');

    } catch (error) {
        console.error('Error exporting report:', error);
        showToast('Gagal export laporan: ' + error.message, 'error');
    }
}

async function emailReport() {
    const period = document.getElementById('reportPeriod').value;

    if (!period) {
        showToast('Pilih periode laporan terlebih dahulu', 'warning');
        return;
    }

    try {
        showToast('Mengirim laporan via email...', 'info');

        const response = await fetch('/api/reports/email', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                period: period
            })
        });

        if (!response.ok) {
            throw new Error('Failed to send email');
        }

        showToast('Laporan berhasil dikirim via email', 'success');

    } catch (error) {
        console.error('Error sending email:', error);
        showToast('Gagal mengirim email: ' + error.message, 'error');
    }
}

async function quickExport(type) {
    try {
        showToast(`Memproses quick export ${type}...`, 'info');

        const response = await fetch(`/api/reports/quick-export/${type}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error('Failed to quick export');
        }

        // Trigger download
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${type}_report.${getFileExtension(type)}`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        showToast(`Quick export ${type} berhasil`, 'success');

    } catch (error) {
        console.error('Error quick exporting:', error);
        showToast('Gagal quick export: ' + error.message, 'error');
    }
}

// Utility functions
function showLoadingState() {
    // Add loading indicators to KPI cards
    const kpiElements = ['total-consumption', 'total-revenue', 'collection-rate', 'active-customers',
                        'monthly-revenue', 'monthly-target', 'monthly-consumption', 'active-meters'];
    kpiElements.forEach(id => {
        const element = document.getElementById(id);
        if (element) element.textContent = '--';
    });
}

function hideLoadingState() {
    // Loading states are hidden when data is updated
}

function showError(message) {
    showToast(message, 'error');
}

function showToast(message, type = 'info') {
    // Create toast container if not exists
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast show align-items-center text-white bg-${type === 'error' ? 'danger' : type}`;

    const iconMap = {
        success: 'check-circle',
        error: 'exclamation-triangle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${iconMap[type] || 'info-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

function formatNumber(num) {
    return parseInt(num).toLocaleString('id-ID');
}

function formatCurrency(num) {
    return parseInt(num).toLocaleString('id-ID');
}

function getCurrentMonthStart() {
    const now = new Date();
    return new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
}

function getCurrentMonthEnd() {
    const now = new Date();
    return new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().split('T')[0];
}

function getFileExtension(type) {
    const extensions = {
        'revenue': 'xlsx',
        'consumption': 'xlsx',
        'customers': 'csv'
    };
    return extensions[type] || 'xlsx';
}
</script>
@endsection

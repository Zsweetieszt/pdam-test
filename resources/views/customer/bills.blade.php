@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    Tagihan Saya
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h4 id="pending-count">--</h4>
                                <small>Tagihan Pending</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4 id="paid-count">--</h4>
                                <small>Sudah Lunas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4 id="total-year">--</h4>
                                <small>Total Tahun Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h4 id="avg-monthly">--</h4>
                                <small>Rata-rata/Bulan</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="text-muted">Riwayat tagihan air Anda</h6>
                    <div>
                        <button class="btn btn-primary me-2">
                            <i class="fas fa-download me-2"></i>
                            Download Semua
                        </button>
                        <button class="btn btn-success">
                            <i class="fas fa-credit-card me-2"></i>
                            Bayar Sekarang
                        </button>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" placeholder="Cari berdasarkan periode...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option>Semua Status</option>
                            <option>Pending</option>
                            <option>Lunas</option>
                            <option>Terlambat</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>
                            Filter
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Periode</th>
                                <th>Pemakaian</th>
                                <th>Meter Awal</th>
                                <th>Meter Akhir</th>
                                <th>Total Tagihan</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bills-table-body">
                            <!-- Bills will be loaded here -->
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 mb-0 text-muted">Memuat data tagihan...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="alert alert-info mt-4">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Penting
                    </h6>
                    <ul class="mb-0">
                        <li>Tagihan jatuh tempo setiap tanggal 25</li>
                        <li>Denda keterlambatan 10% setelah jatuh tempo</li>
                        <li>Pembayaran dapat dilakukan melalui transfer bank atau cash</li>
                        <li>Notifikasi WhatsApp akan dikirim 3 hari sebelum jatuh tempo</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
// Global variables
let billsData = [];
let currentFilters = {};

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadCustomerBills();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Filter button
    document.querySelector('.btn[onclick*="Filter"]').addEventListener('click', applyFilters);

    // Search input
    document.querySelector('input[placeholder*="Cari"]').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });
}

// Load customer bills from API
async function loadCustomerBills() {
    try {
        showLoadingState();

        const response = await fetch('/api/customer/bills', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        billsData = data.bills || [];
        updateBillsUI(data);
        hideLoadingState();

    } catch (error) {
        console.error('Error loading customer bills:', error);
        showError('Data Belum Ada');
        hideLoadingState();
    }
}

// Update bills UI with loaded data
function updateBillsUI(data) {
    // Update summary cards
    const summary = data.summary || {};
    document.getElementById('pending-count').textContent = summary.pending_count || 0;
    document.getElementById('paid-count').textContent = summary.paid_count || 0;
    document.getElementById('total-year').textContent = 'Rp ' + formatCurrency(summary.total_year || 0);
    document.getElementById('avg-monthly').textContent = formatNumber(summary.avg_monthly || 0) + ' m³';

    // Update bills table
    displayBills(billsData);
}

// Display bills in table
function displayBills(bills) {
    const tbody = document.getElementById('bills-table-body');

    if (!bills || bills.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="mb-0 text-muted">Tidak ada data tagihan</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = bills.map(bill => {
        const isPending = bill.status === 'pending';
        const rowClass = isPending ? 'table-warning' : '';
        const periodText = isPending ? `<strong>${getMonthName(bill.period_month)} ${bill.period_year}</strong>` : `${getMonthName(bill.period_month)} ${bill.period_year}`;
        const usageText = isPending ? `<strong>${formatNumber(bill.usage)} m³</strong>` : `${formatNumber(bill.usage)} m³`;
        const amountText = isPending ? `<strong class="text-warning">Rp ${formatCurrency(bill.total_amount)}</strong>` : `Rp ${formatCurrency(bill.total_amount)}`;

        const actionButtons = isPending ?
            `<div class="btn-group btn-group-sm">
                <button class="btn btn-info" title="Detail" onclick="viewBillDetail(${bill.id})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-success" title="Bayar" onclick="payBill(${bill.id})">
                    <i class="fas fa-credit-card"></i>
                </button>
                <button class="btn btn-secondary" title="Download" onclick="downloadBill(${bill.id})">
                    <i class="fas fa-download"></i>
                </button>
            </div>` :
            `<div class="btn-group btn-group-sm">
                <button class="btn btn-info" title="Detail" onclick="viewBillDetail(${bill.id})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-secondary" title="Download" onclick="downloadBill(${bill.id})">
                    <i class="fas fa-download"></i>
                </button>
                <button class="btn btn-primary" title="Bukti Bayar" onclick="viewPaymentProof(${bill.id})">
                    <i class="fas fa-receipt"></i>
                </button>
            </div>`;

        return `
            <tr class="${rowClass}">
                <td>${periodText}</td>
                <td>${usageText}</td>
                <td>${formatNumber(bill.meter_start || 0)}</td>
                <td>${formatNumber(bill.meter_end || 0)}</td>
                <td>${amountText}</td>
                <td>${formatDate(bill.due_date)}</td>
                <td><span class="badge bg-${getStatusColor(bill.status)}">${getStatusText(bill.status)}</span></td>
                <td>${actionButtons}</td>
            </tr>
        `;
    }).join('');
}

// Apply filters
function applyFilters() {
    const searchTerm = document.querySelector('input[placeholder*="Cari"]').value;
    const statusFilter = document.querySelector('select').value;

    currentFilters = {
        search: searchTerm,
        status: statusFilter !== 'Semua Status' ? statusFilter : ''
    };

    // Filter bills locally (or make API call with filters)
    let filteredBills = billsData;

    if (currentFilters.search) {
        filteredBills = filteredBills.filter(bill =>
            `${getMonthName(bill.period_month)} ${bill.period_year}`.toLowerCase().includes(currentFilters.search.toLowerCase())
        );
    }

    if (currentFilters.status) {
        filteredBills = filteredBills.filter(bill => bill.status === currentFilters.status.toLowerCase());
    }

    displayBills(filteredBills);
}

// Action functions
async function viewBillDetail(billId) {
    try {
        const response = await fetch(`/api/customer/bills/${billId}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch bill details');
        }

        const billData = await response.json();
        // Here you could show a modal with bill details
        showToast('Menampilkan detail tagihan...', 'info');

    } catch (error) {
        console.error('Error fetching bill details:', error);
        showToast('Data Tidak Ada', 'error');
    } catch (error) {
    }
}

async function payBill(billId) {
    if (confirm('Lanjutkan ke halaman pembayaran?')) {
        try {
            // Here you could redirect to payment page or open payment modal
            showToast('Redirect ke halaman pembayaran...', 'info');
            // window.location.href = `/customer/payment/${billId}`;

        } catch (error) {
            console.error('Error initiating payment:', error);
            showToast('Gagal memulai pembayaran', 'error');
        }
    }
}

async function downloadBill(billId) {
    try {
        showToast('Memproses download tagihan...', 'info');

        const response = await fetch(`/api/customer/bills/${billId}/download`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error('Failed to download bill');
        }

        // Trigger download
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `tagihan_${billId}.pdf`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        showToast('Tagihan berhasil didownload', 'success');

    } catch (error) {
        console.error('Error downloading bill:', error);
        showToast('Gagal download tagihan', 'error');
    }
}

async function viewPaymentProof(billId) {
    try {
        const response = await fetch(`/api/customer/bills/${billId}/payment-proof`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch payment proof');
        }

        const proofData = await response.json();
        // Here you could show a modal with payment proof
        showToast('Menampilkan bukti pembayaran...', 'info');

    } catch (error) {
        console.error('Error fetching payment proof:', error);
        showToast('Data Tidak Ada', 'error');
    }
}

// Utility functions
function showLoadingState() {
    // Add loading indicators
    const summaryElements = ['pending-count', 'paid-count', 'total-year', 'avg-monthly'];
    summaryElements.forEach(id => {
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
    return parseInt(num || 0).toLocaleString('id-ID');
}

function formatCurrency(num) {
    return parseInt(num || 0).toLocaleString('id-ID');
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID');
}

function getMonthName(month) {
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return months[month - 1] || month;
}

function getStatusText(status) {
    const statuses = {
        'pending': 'Pending',
        'paid': 'Lunas',
        'overdue': 'Terlambat'
    };
    return statuses[status] || status;
}

function getStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'paid': 'success',
        'overdue': 'danger'
    };
    return colors[status] || 'secondary';
}
</script>

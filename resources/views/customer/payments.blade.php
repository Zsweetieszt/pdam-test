@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-receipt me-2"></i>
                    Riwayat Pembayaran
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4 id="total-payments">--</h4>
                                <small>Total Pembayaran</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4 id="total-amount">--</h4>
                                <small>Total Nominal</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h4 id="this-month">--</h4>
                                <small>Bulan Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h4 id="avg-payment">--</h4>
                                <small>Rata-rata/Bulan</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="text-muted">Riwayat pembayaran tagihan air Anda</h6>
                    <div>
                        <button class="btn btn-primary me-2" onclick="refreshPayments()">
                            <i class="fas fa-sync-alt me-2"></i>
                            Refresh
                        </button>
                        <button class="btn btn-success" onclick="downloadAllReceipts()">
                            <i class="fas fa-download me-2"></i>
                            Download Semua
                        </button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Cari berdasarkan nomor pembayaran..." id="search-payment">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="status-filter">
                            <option value="">Semua Status</option>
                            <option value="verified">Terverifikasi</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="month-filter">
                            <option value="">Semua Bulan</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" onclick="applyFilters()">
                            <i class="fas fa-search me-2"></i>
                            Filter
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Pembayaran</th>
                                <th>Periode</th>
                                <th>Nominal</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="payments-table-body">
                            <!-- Payments will be loaded here -->
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 mb-0 text-muted">Memuat data pembayaran...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Payment pagination">
                        <ul class="pagination" id="pagination-container">
                            <!-- Pagination will be generated here -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Detail Modal -->
<div class="modal fade" id="paymentDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-receipt me-2"></i>
                    Detail Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="payment-detail-content">
                <!-- Payment details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="downloadReceipt()">
                    <i class="fas fa-download me-2"></i>
                    Download Bukti
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
// Global variables
let paymentsData = [];
let currentPage = 1;
let totalPages = 1;
let currentFilters = {};

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadCustomerPayments();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Search input
    document.getElementById('search-payment').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });

    // Filters
    document.getElementById('status-filter').addEventListener('change', applyFilters);
    document.getElementById('month-filter').addEventListener('change', applyFilters);
}

// Load customer payments from API
async function loadCustomerPayments(page = 1) {
    try {
        showLoadingState();

        const params = new URLSearchParams({
            page: page,
            per_page: 10,
            ...currentFilters
        });

        const response = await fetch(`/api/customer/payments?${params}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        paymentsData = data.payments || [];
        currentPage = data.current_page || 1;
        totalPages = data.last_page || 1;

        updatePaymentsUI(data);
        hideLoadingState();

    } catch (error) {
        console.error('Error loading customer payments:', error);
        showError('Data Belum Ada');
        hideLoadingState();
    }
}

// Update payments UI with loaded data
function updatePaymentsUI(data) {
    // Update summary cards
    const summary = data.summary || {};
    document.getElementById('total-payments').textContent = summary.total_payments || 0;
    document.getElementById('total-amount').textContent = 'Rp ' + formatCurrency(summary.total_amount || 0);
    document.getElementById('this-month').textContent = summary.this_month || 0;
    document.getElementById('avg-payment').textContent = 'Rp ' + formatCurrency(summary.avg_payment || 0);

    // Update payments table
    displayPayments(paymentsData);

    // Update pagination
    updatePagination();
}

// Display payments in table
function displayPayments(payments) {
    const tbody = document.getElementById('payments-table-body');

    if (!payments || payments.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="mb-0 text-muted">Tidak ada data pembayaran</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = payments.map(payment => {
        const statusBadge = getStatusBadge(payment.status);
        const methodText = getPaymentMethodText(payment.payment_method);

        return `
            <tr>
                <td>${formatDate(payment.payment_date)}</td>
                <td><strong>${payment.payment_number}</strong></td>
                <td>${getMonthName(payment.period_month)} ${payment.period_year}</td>
                <td><strong>Rp ${formatCurrency(payment.amount)}</strong></td>
                <td>${methodText}</td>
                <td>${statusBadge}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" title="Detail" onclick="viewPaymentDetail(${payment.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-success" title="Download" onclick="downloadReceipt(${payment.id})">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Update pagination
function updatePagination() {
    const container = document.getElementById('pagination-container');
    container.innerHTML = '';

    if (totalPages <= 1) return;

    // Previous button
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a>`;
    container.appendChild(prevLi);

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    for (let i = startPage; i <= endPage; i++) {
        const pageLi = document.createElement('li');
        pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
        pageLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
        container.appendChild(pageLi);
    }

    // Next button
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a>`;
    container.appendChild(nextLi);
}

// Change page
function changePage(page) {
    if (page >= 1 && page <= totalPages) {
        loadCustomerPayments(page);
    }
}

// Apply filters
function applyFilters() {
    const searchTerm = document.getElementById('search-payment').value;
    const statusFilter = document.getElementById('status-filter').value;
    const monthFilter = document.getElementById('month-filter').value;

    currentFilters = {
        search: searchTerm,
        status: statusFilter,
        month: monthFilter
    };

    loadCustomerPayments(1); // Reset to first page
}

// View payment detail
async function viewPaymentDetail(paymentId) {
    try {
        const response = await fetch(`/api/customer/payments/${paymentId}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch payment details');
        }

        const paymentData = await response.json();

        // Show modal with payment details
        const modal = new bootstrap.Modal(document.getElementById('paymentDetailModal'));
        const content = document.getElementById('payment-detail-content');

        content.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Informasi Pembayaran</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>No. Pembayaran:</strong></td>
                            <td>${paymentData.payment_number}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal:</strong></td>
                            <td>${formatDate(paymentData.payment_date)}</td>
                        </tr>
                        <tr>
                            <td><strong>Periode:</strong></td>
                            <td>${getMonthName(paymentData.period_month)} ${paymentData.period_year}</td>
                        </tr>
                        <tr>
                            <td><strong>Nominal:</strong></td>
                            <td>Rp ${formatCurrency(paymentData.amount)}</td>
                        </tr>
                        <tr>
                            <td><strong>Metode:</strong></td>
                            <td>${getPaymentMethodText(paymentData.payment_method)}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>${getStatusBadge(paymentData.status)}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Bukti Pembayaran</h6>
                    ${paymentData.payment_proof ? `
                        <div class="text-center">
                            <img src="${paymentData.payment_proof}" class="img-fluid rounded" alt="Bukti Pembayaran" style="max-height: 300px;">
                        </div>
                    ` : `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Bukti pembayaran tidak tersedia
                        </div>
                    `}
                </div>
            </div>
        `;

        modal.show();

    } catch (error) {
        console.error('Error fetching payment details:', error);
        showToast('Data Tidak Ada', 'error');
    } catch (error) {
    }
}

// Download receipt
async function downloadReceipt(paymentId = null) {
    try {
        const id = paymentId || currentPaymentId; // currentPaymentId should be set when viewing details
        if (!id) {
            showToast('Pilih pembayaran terlebih dahulu', 'warning');
            return;
        }

        showToast('Memproses download bukti pembayaran...', 'info');

        const response = await fetch(`/api/customer/payments/${id}/receipt`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error('Failed to download receipt');
        }

        // Trigger download
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `bukti_pembayaran_${id}.pdf`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        showToast('Bukti pembayaran berhasil didownload', 'success');

    } catch (error) {
        console.error('Error downloading receipt:', error);
        showToast('Gagal download bukti pembayaran', 'error');
    }
}

// Download all receipts
async function downloadAllReceipts() {
    try {
        showToast('Memproses download semua bukti pembayaran...', 'info');

        const response = await fetch('/api/customer/payments/download-all', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error('Failed to download all receipts');
        }

        // Trigger download
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'semua_bukti_pembayaran.zip';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        showToast('Semua bukti pembayaran berhasil didownload', 'success');

    } catch (error) {
        console.error('Error downloading all receipts:', error);
        showToast('Gagal download semua bukti pembayaran', 'error');
    }
}

// Refresh payments
function refreshPayments() {
    loadCustomerPayments(currentPage);
}

// Utility functions
function showLoadingState() {
    // Add loading indicators
    const summaryElements = ['total-payments', 'total-amount', 'this-month', 'avg-payment'];
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

function getStatusBadge(status) {
    const statusMap = {
        'verified': { text: 'Terverifikasi', class: 'bg-success' },
        'pending': { text: 'Pending', class: 'bg-warning' },
        'rejected': { text: 'Ditolak', class: 'bg-danger' }
    };

    const statusInfo = statusMap[status] || { text: status, class: 'bg-secondary' };
    return `<span class="badge ${statusInfo.class}">${statusInfo.text}</span>`;
}

function getPaymentMethodText(method) {
    const methods = {
        'cash': 'Tunai',
        'transfer': 'Transfer Bank',
        'qris': 'QRIS',
        'ewallet': 'E-Wallet'
    };
    return methods[method] || method;
}
</script>
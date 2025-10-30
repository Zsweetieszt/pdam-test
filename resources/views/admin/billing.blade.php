@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8 col-sm-6">
                        <h4 class="mb-1">
                            <i class="fas fa-calculator text-primary me-2"></i>
                            Manajemen Tagihan
                        </h4>
                        <p class="text-muted mb-0">Kelola tagihan pelanggan PDAM dengan generate dan monitoring pembayaran</p>
                    </div>
                    <div class="col-md-4 col-sm-6 text-end">
                        <div class="btn-group" role="group">
                            <button class="btn btn-success" id="btnInputMeter">
                                <i class="fas fa-plus me-2"></i>
                                <span class="d-none d-sm-inline">Generate </span>Tagihan
                            </button>
                            <button class="btn btn-primary" id="btnExport">
                                <i class="fas fa-download me-2"></i>
                                Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="card bg-primary text-white border-0 h-100">
                    <div class="card-body text-center py-3">
                        <h4 id="totalBillsCount" class="mb-1">-</h4>
                        <small>Total Tagihan</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="card bg-success text-white border-0 h-100">
                    <div class="card-body text-center py-3">
                        <h4 id="paidBillsCount" class="mb-1">-</h4>
                        <small>Sudah Lunas</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 mb-md-0">
                <div class="card bg-warning text-dark border-0 h-100">
                    <div class="card-body text-center py-3">
                        <h4 id="pendingBillsCount" class="mb-1">-</h4>
                        <small>Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-danger text-white border-0 h-100">
                    <div class="card-body text-center py-3">
                        <h4 id="overdueBillsCount" class="mb-1">-</h4>
                        <small>Terlambat</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Panel (Updated with single date filter) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card" style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
                    <div class="card-header bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter & Pencarian</h6>
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                <i class="fas fa-chevron-down" id="filterToggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="collapse show" id="filterCollapse">
                        <div class="card-body">
                            <form id="filterForm">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" id="statusFilter">
                                            <option value="">Semua Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="sent">Terkirim</option>
                                            <option value="paid">Lunas</option>
                                            <option value="overdue">Terlambat</option>
                                            <option value="cancelled">Dibatalkan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Periode</label>
                                        <select class="form-select" id="periodFilter">
                                            <option value="">Semua Periode</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Jatuh Tempo</label>
                                        <input type="date" class="form-control" id="dueDateFilter">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Cari Pelanggan</label>
                                        <input type="text" class="form-control" id="customerSearchFilter" placeholder="Nama, nomor pelanggan, atau nomor telepon">
                                    </div>
                                    <div class="col-md-12 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fas fa-search me-2"></i>Cari
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                            <i class="fas fa-undo me-2"></i>Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <!-- Loading State -->
                <div id="loadingState" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data tagihan...</p>
                </div>

                <!-- Data Table - REQ-F-10 -->
                <div class="table-responsive" id="billsTableContainer">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th onclick="sortTable('bill_number')" class="sortable-header" style="min-width: 140px;">
                                    <div class="d-flex align-items-center">
                                        <span>No. Tagihan</span>
                                        <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="bill_number"></i>
                                    </div>
                                </th>
                                <th onclick="sortTable('customer_name')" class="sortable-header" style="min-width: 200px;">
                                    <div class="d-flex align-items-center">
                                        <span>Pelanggan</span>
                                        <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="customer_name"></i>
                                    </div>
                                </th>
                                <th class="d-none d-lg-table-cell" style="min-width: 120px;">No. Meter</th>
                                <th class="d-none d-md-table-cell text-center" style="min-width: 100px;">Periode</th>
                                <th class="d-none d-lg-table-cell text-center" style="min-width: 100px;">Pemakaian</th>
                                <th onclick="sortTable('total_amount')" class="sortable-header text-center" style="min-width: 140px;">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span>Total Tagihan</span>
                                        <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="total_amount"></i>
                                    </div>
                                </th>
                                <th onclick="sortTable('due_date')" class="sortable-header d-none d-lg-table-cell text-center" style="min-width: 120px;">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span>Jatuh Tempo</span>
                                        <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="due_date"></i>
                                    </div>
                                </th>
                                <th class="text-center" style="min-width: 100px;">Status</th>
                                <th class="text-center" style="min-width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="billsTableBody">
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted mb-2">Belum ada data tagihan</h6>
                                        <p class="text-muted small mb-0">Klik tombol "Cari" untuk memuat data tagihan</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="row align-items-center mt-4">
                    <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
                        <select class="form-select form-select-sm" id="perPageSelect" onchange="changePerPage()">
                            <option value="10">10 per halaman</option>
                            <option value="15" selected>15 per halaman</option>
                            <option value="25">25 per halaman</option>
                            <option value="50">50 per halaman</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-sm-6 text-center mb-3 mb-md-0">
                        <small id="paginationInfo" class="text-muted">
                            Menampilkan 0 dari 0 data
                        </small>
                    </div>
                    <div class="col-md-4">
                        <nav id="paginationNav">
                            <ul class="pagination pagination-sm justify-content-center justify-content-md-end mb-0" id="paginationList">
                                <!-- Pagination will be generated here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Generate Tagihan -->
<div class="modal fade" id="meterReadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Generate Tagihan Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="meterReadingForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pelanggan <span class="text-danger">*</span></label>
                            <select class="form-select" id="customerSelect" required>
                                <option value="">Pilih Pelanggan...</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Periode Tagihan <span class="text-danger">*</span></label>
                            <select class="form-select" id="billingPeriodSelect" required>
                                <option value="">Pilih Periode...</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pembacaan Sebelumnya</label>
                            <input type="number" class="form-control" id="previousReading" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pembacaan Saat Ini <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="currentReading" min="0" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pemakaian (m³)</label>
                            <input type="number" class="form-control" id="usageDisplay" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tarif Dasar <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="baseAmount" min="0" step="0.01" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Biaya Tambahan</label>
                            <input type="number" class="form-control" id="additionalCharges" min="0" step="0.01" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pajak</label>
                            <input type="number" class="form-control" id="taxAmount" min="0" step="0.01" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="dueDate" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Tagihan</label>
                            <input type="text" class="form-control fw-bold text-success" id="totalAmount" readonly>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Penting
                        </h6>
                        <ul class="mb-0 small">
                            <li>Pastikan pembacaan meter saat ini lebih besar dari pembacaan sebelumnya</li>
                            <li>Total tagihan akan dihitung otomatis berdasarkan tarif dan biaya tambahan</li>
                            <li>Jatuh tempo default adalah 30 hari dari tanggal generate</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Batal
                </button>
                <button type="button" class="btn btn-success" id="generateBillBtn">
                    <i class="fas fa-save me-2"></i>
                    Generate Tagihan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Tagihan -->
<div class="modal fade" id="billDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice me-2"></i>
                    Detail Tagihan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="billDetailContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat detail tagihan...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="printBillBtn" onclick="printBill()">
                    <i class="fas fa-print me-2"></i>
                    Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah Status -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
                    Ubah Status Tagihan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <input type="hidden" id="billIdStatus">
                    <div class="mb-3">
                        <label class="form-label">Status Baru <span class="text-danger">*</span></label>
                        <select class="form-select" id="newStatus" required>
                            <option value="pending">Pending</option>
                            <option value="sent">Terkirim</option>
                            <option value="paid">Lunas</option>
                            <option value="overdue">Terlambat</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" id="statusNotes" rows="3" placeholder="Catatan perubahan status (opsional)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Batal
                </button>
                <button type="button" class="btn btn-warning" id="updateStatusBtn">
                    <i class="fas fa-save me-2"></i>
                    Update Status
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Styles -->
<style>
    /* Enhanced table styling for REQ-F-10 */
    .table-responsive {
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .sortable-header {
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
        position: relative;
    }
    
    .sortable-header:hover {
        background-color: rgba(255,255,255,0.1) !important;
        transform: translateY(-1px);
    }
    
    .sortable-header:active {
        transform: translateY(0);
    }
    
    .sort-icon {
        font-size: 0.75rem;
        transition: all 0.3s ease;
    }
    
    .hover-shadow {
        transition: all 0.2s ease;
    }
    
    .hover-shadow:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-1px);
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-right: 2px;
        transition: all 0.2s ease;
    }
    
    .btn-group .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    /* Enhanced pagination */
    .pagination .page-link {
        border: none;
        margin: 0 2px;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }
    
    .pagination .page-link:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .pagination .page-item.active .page-link {
        background: var(--gradient-primary);
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    }
    
    /* Mobile responsive enhancements */
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }
        
        .btn-group-sm .btn {
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
        }
        
        .table td, .table th {
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
        }
    }
    
    @media (max-width: 576px) {
        .modal-xl, .modal-lg {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }
        
        .card-header .row {
            text-align: center;
        }
        
        .table td, .table th {
            padding: 0.5rem 0.25rem;
            font-size: 0.875rem;
        }
    }
    
    /* Enhanced search and form inputs */
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }
    
    /* Button enhancements */
    .btn:hover {
        transform: translateY(-1px);
    }
    
    .btn:active {
        transform: translateY(0);
    }
    
    /* Badge enhancements */
    .badge {
        font-weight: 500;
        letter-spacing: 0.02em;
    }
</style>

<script>
// Global variables - REQ-F-10 compliant
let currentPage = 1;
let perPage = 15;
let sortField = 'created_at';
let sortDirection = 'desc';
let totalBills = 0;
let billingPeriods = [];
let customers = [];
let currentBillId = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    setupSearchHandlers();
    loadInitialData();
    bindEvents();
});

// Enhanced search handlers - REQ-F-10.2 (Updated for simplified filter)
function setupSearchHandlers() {
    const customerSearchFilter = document.getElementById('customerSearchFilter');
    const statusFilter = document.getElementById('statusFilter');
    const periodFilter = document.getElementById('periodFilter');
    const dueDateFilter = document.getElementById('dueDateFilter');
    
    // Real-time search with debouncing
    let searchTimeout;
    customerSearchFilter.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const value = this.value.trim();
        
        if (value.length === 0 || value.length >= 2) {
            searchTimeout = setTimeout(() => {
                searchBills(1);
            }, 300);
        }
    });
    
    // Filter change handlers
    [statusFilter, periodFilter, dueDateFilter].forEach(filter => {
        filter.addEventListener('change', () => {
            searchBills(1);
        });
    });
    
    // Enter key search
    customerSearchFilter.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimeout);
            searchBills(1);
        }
    });

    // Filter form submit handler
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        searchBills(1);
    });

    // Filter collapse toggle
    document.querySelector('[data-bs-target="#filterCollapse"]').addEventListener('click', function() {
        const icon = document.getElementById('filterToggleIcon');
        setTimeout(() => {
            if (document.getElementById('filterCollapse').classList.contains('show')) {
                icon.className = 'fas fa-chevron-up';
            } else {
                icon.className = 'fas fa-chevron-down';
            }
        }, 350);
    });
}

function loadInitialData() {
    showLoading();
    Promise.all([
        loadBillingPeriods(),
        loadCustomers(),
        loadBillStats()
    ]).then(() => {
        searchBills();
    }).finally(() => {
        hideLoading();
    });
}

function bindEvents() {
    // Button events
    document.getElementById('btnInputMeter').addEventListener('click', showMeterReadingModal);
    document.getElementById('btnExport').addEventListener('click', exportData);
    document.getElementById('generateBillBtn').addEventListener('click', generateBill);
    document.getElementById('updateStatusBtn').addEventListener('click', updateStatus);

    // Form input events
    document.getElementById('currentReading').addEventListener('input', calculateUsageAndAmount);
    document.getElementById('baseAmount').addEventListener('input', calculateUsageAndAmount);
    document.getElementById('additionalCharges').addEventListener('input', calculateUsageAndAmount);
    document.getElementById('taxAmount').addEventListener('input', calculateUsageAndAmount);
    
    // Customer select change
    document.getElementById('customerSelect').addEventListener('change', loadPreviousReading);
}

// Enhanced search function - REQ-F-10.1 & REQ-F-10.2
async function searchBills(page = 1) {
    showLoading();

    const searchTerm = document.getElementById('customerSearchFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const periodFilter = document.getElementById('periodFilter').value;
    const dueDateFilter = document.getElementById('dueDateFilter').value;

    try {
        // Build query parameters
        const params = new URLSearchParams({
            page: page,
            per_page: perPage,
            sort_field: sortField,
            sort_direction: sortDirection
        });

        if (searchTerm) params.append('search', searchTerm);
        if (statusFilter) params.append('status', statusFilter);
        if (periodFilter) params.append('period_month', periodFilter);
        if (dueDateFilter) params.append('due_date', dueDateFilter);

        const response = await fetch(`/api/datatables/bills?${params.toString()}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        displayBills(data);
        updatePagination(data);
        currentPage = page;
    } catch (error) {
        console.error('Error loading bills:', error);
        handleApiError(error, 'Data Belum Ada');
    } finally {
        hideLoading();
    }
}

// Enhanced filtering and sorting with proper pagination
// Enhanced display bills function - REQ-F-10.1
function displayBills(data) {
    const tbody = document.getElementById('billsTableBody');
    
    if (!data.data || data.data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-search fa-4x text-muted mb-3 opacity-50"></i>
                        <h6 class="text-muted mb-2">Tidak ada data tagihan</h6>
                        <p class="text-muted small mb-0">Coba ubah kriteria pencarian atau generate tagihan baru</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = data.data.map((bill, index) => {
        const rowNumber = ((currentPage - 1) * perPage) + index + 1;
        const usage = bill.current_reading - bill.previous_reading;
        
        return `
            <tr class="hover-shadow">
                <td>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-light text-dark me-2">${rowNumber}</span>
                        <div>
                            <strong class="text-primary">${bill.bill_number}</strong>
                            <br>
                            <small class="text-muted d-md-none">
                                ${getStatusBadge(bill.status)}
                            </small>
                        </div>
                    </div>
                </td>
                <td>
                    <div>
                        <div class="fw-bold text-dark">${bill.customer_name}</div>
                        <small class="text-muted">
                            <i class="fas fa-id-card fa-xs me-1"></i>
                            ${bill.customer_number}
                        </small>
                    </div>
                </td>
                <td class="d-none d-lg-table-cell">
                    <small class="font-monospace text-primary">${bill.meter_number}</small>
                </td>
                <td class="d-none d-md-table-cell text-center">
                    <small>${getMonthName(bill.period_month)} ${bill.period_year}</small>
                </td>
                <td class="d-none d-lg-table-cell text-center">
                    <span class="badge bg-info">${usage} m³</span>
                </td>
                <td class="text-center">
                    <div class="fw-bold text-success">Rp ${formatCurrency(bill.total_amount)}</div>
                    <small class="text-muted d-lg-none">
                        ${getMonthName(bill.period_month)} ${bill.period_year}
                    </small>
                </td>
                <td class="d-none d-lg-table-cell text-center">
                    <small class="text-muted">${formatDate(bill.due_date)}</small>
                </td>
                <td class="text-center">
                    ${getStatusBadge(bill.status)}
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-info btn-sm" onclick="showBillDetail(${bill.id})" 
                                title="Detail Tagihan" data-bs-toggle="tooltip">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="showStatusModal(${bill.id})" 
                                title="Ubah Status" data-bs-toggle="tooltip">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${bill.status !== 'paid' ? `
                            <button class="btn btn-success btn-sm" onclick="markAsPaid(${bill.id})" 
                                    title="Tandai Lunas" data-bs-toggle="tooltip">
                                <i class="fas fa-check"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Load statistics from API
async function loadBillStats() {
    try {
        const response = await fetch('/api/admin/dashboard-stats', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        document.getElementById('totalBillsCount').textContent = data.data.bills.total_this_month;
        document.getElementById('paidBillsCount').textContent = data.data.bills.paid;
        document.getElementById('pendingBillsCount').textContent = data.data.bills.pending;
        document.getElementById('overdueBillsCount').textContent = data.data.bills.overdue;
    } catch (error) {
        console.error('Error loading stats:', error);
        ['totalBillsCount', 'paidBillsCount', 'pendingBillsCount', 'overdueBillsCount'].forEach(id => {
            document.getElementById(id).textContent = '0';
        });
    }
}

// Load billing periods from API
async function loadBillingPeriods() {
    try {
        const response = await fetch('/api/bills/billing-periods', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        billingPeriods = data.data || [];
        populatePeriodSelects();
        return billingPeriods;
    } catch (error) {
        console.error('Error loading billing periods:', error);
        // Fallback to mock data if API fails
        billingPeriods = Array.from({length: 8}, (_, i) => ({
            id: i + 1,
            period_month: i + 1,
            period_year: 2025
        }));
        populatePeriodSelects();
        return billingPeriods;
    }
}

// Load customers from API
async function loadCustomers() {
    try {
        const response = await fetch('/api/customers?per_page=100', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        customers = data.data || [];
        populateCustomerSelect();
        return customers;
    } catch (error) {
        console.error('Error loading customers:', error);
        // Fallback to mock data if API fails
        customers = Array.from({length: 20}, (_, i) => ({
            id: i + 1,
            customer_number: `CUST-${String(i + 1).padStart(3, '0')}`,
            user: {
                name: `Customer ${i + 1}`,
                phone: `0811${String(i + 1).padStart(4, '0')}`
            },
            meters: [{
                id: i + 1,
                meter_number: `MTR-${String(i + 1).padStart(4, '0')}`
            }]
        }));
        populateCustomerSelect();
        return customers;
    }
}

function populatePeriodSelects() {
    const selects = ['periodFilter', 'billingPeriodSelect'];
    
    selects.forEach(selectId => {
        const select = document.getElementById(selectId);
        const firstOption = select.querySelector('option').outerHTML;
        
        select.innerHTML = firstOption + billingPeriods.map(period => 
            `<option value="${period.period_month}">${getMonthName(period.period_month)} ${period.period_year}</option>`
        ).join('');
    });
}

function populateCustomerSelect() {
    const select = document.getElementById('customerSelect');
    const firstOption = select.querySelector('option').outerHTML;

    select.innerHTML = firstOption + customers.flatMap(customer =>
        (customer.meters || []).map(meter =>
            `<option value="${meter.id}" data-customer-id="${customer.id}" data-customer-name="${customer.user?.name || 'Unknown'}" data-customer-number="${customer.customer_number}">
                ${customer.user?.name || 'Unknown'} - ${customer.customer_number} (${meter.meter_number})
            </option>`
        )
    ).join('');
}

// REQ-F-10.3: Enhanced sorting functionality
function sortTable(field) {
    // Update sort parameters
    if (sortField === field) {
        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        sortField = field;
        sortDirection = 'asc';
    }
    
    // Show loading state for sorting
    showTableLoading(true);
    
    // Apply sorting and refresh data
    searchBills(1);
    
    // Update visual indicators
    updateSortIcons(field);
}

function updateSortIcons(activeField) {
    // Reset all sort icons
    document.querySelectorAll('.sort-icon').forEach(icon => {
        icon.className = 'fas fa-sort ms-2 text-muted sort-icon';
    });
    
    // Set active sort icon
    const activeIcon = document.querySelector(`.sort-icon[data-field="${activeField}"]`);
    if (activeIcon) {
        activeIcon.className = `fas fa-sort-${sortDirection === 'asc' ? 'up' : 'down'} ms-2 text-primary sort-icon`;
    }
}

// REQ-F-10.4: Enhanced pagination functionality
function changePerPage() {
    perPage = parseInt(document.getElementById('perPageSelect').value);
    searchBills(1);
}

function updatePagination(data) {
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationList = document.getElementById('paginationList');
    
    paginationInfo.textContent = `Menampilkan ${data.from || 0} - ${data.to || 0} dari ${data.total || 0} data`;
    
    if (!data.last_page || data.last_page <= 1) {
        paginationList.innerHTML = '';
        return;
    }
    
    let paginationHtml = '';
    
    // Previous button
    if (data.current_page > 1) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="searchBills(${data.current_page - 1})">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
    }
    
    // Page numbers
    const startPage = Math.max(1, data.current_page - 2);
    const endPage = Math.min(data.last_page, data.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        paginationHtml += `
            <li class="page-item ${i === data.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="searchBills(${i})">${i}</a>
            </li>
        `;
    }
    
    // Next button
    if (data.current_page < data.last_page) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="searchBills(${data.current_page + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
    }
    
    paginationList.innerHTML = paginationHtml;
}

// Enhanced loading states
function showTableLoading(show) {
    const tbody = document.getElementById('billsTableBody');
    
    if (show) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                        <div class="spinner-border text-primary mb-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <small class="text-muted">Memproses data...</small>
                    </div>
                </td>
            </tr>
        `;
    }
}

function showLoading() {
    const loadingState = document.getElementById('loadingState');
    const tableContainer = document.getElementById('billsTableContainer');
    
    loadingState.classList.remove('d-none');
    tableContainer.classList.add('d-none');
}

function hideLoading() {
    const loadingState = document.getElementById('loadingState');
    const tableContainer = document.getElementById('billsTableContainer');
    
    loadingState.classList.add('d-none');
    tableContainer.classList.remove('d-none');
}

// Modal and CRUD functions
function showMeterReadingModal() {
    const modal = new bootstrap.Modal(document.getElementById('meterReadingModal'));
    
    // Reset form
    document.getElementById('meterReadingForm').reset();
    document.getElementById('previousReading').value = '';
    document.getElementById('usageDisplay').value = '';
    document.getElementById('totalAmount').value = '';
    
    // Set default due date (30 days from now)
    const dueDate = new Date();
    dueDate.setDate(dueDate.getDate() + 30);
    document.getElementById('dueDate').value = dueDate.toISOString().split('T')[0];
    
    modal.show();
}

async function loadPreviousReading() {
    const meterId = document.getElementById('customerSelect').value;
    if (!meterId) {
        document.getElementById('previousReading').value = '';
        return;
    }

    try {
        const response = await fetch(`/api/meters/${meterId}/details`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        document.getElementById('previousReading').value = data.data.last_reading || 0;
    } catch (error) {
        console.error('Error loading previous reading:', error);
        // Fallback to 0 if API fails
        document.getElementById('previousReading').value = '0';
    }
}

function calculateUsageAndAmount() {
    const previousReading = parseFloat(document.getElementById('previousReading').value) || 0;
    const currentReading = parseFloat(document.getElementById('currentReading').value) || 0;
    const baseAmount = parseFloat(document.getElementById('baseAmount').value) || 0;
    const additionalCharges = parseFloat(document.getElementById('additionalCharges').value) || 0;
    const taxAmount = parseFloat(document.getElementById('taxAmount').value) || 0;

    const usage = Math.max(0, currentReading - previousReading);
    document.getElementById('usageDisplay').value = usage;

    const total = baseAmount + additionalCharges + taxAmount;
    document.getElementById('totalAmount').value = 'Rp ' + formatCurrency(total);
}

async function generateBill() {
    const form = document.getElementById('meterReadingForm');
    if (!validateBillForm()) {
        return;
    }

    const currentReading = parseFloat(document.getElementById('currentReading').value);
    const previousReading = parseFloat(document.getElementById('previousReading').value) || 0;

    if (currentReading < previousReading) {
        showError('Pembacaan saat ini tidak boleh lebih kecil dari pembacaan sebelumnya');
        return;
    }

    const saveButton = document.getElementById('generateBillBtn');
    saveButton.disabled = true;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';

    try {
        const billData = {
            meter_id: parseInt(document.getElementById('customerSelect').value),
            billing_period_id: parseInt(document.getElementById('billingPeriodSelect').value),
            previous_reading: previousReading,
            current_reading: currentReading,
            base_amount: parseFloat(document.getElementById('baseAmount').value),
            additional_charges: parseFloat(document.getElementById('additionalCharges').value) || 0,
            tax_amount: parseFloat(document.getElementById('taxAmount').value) || 0,
            due_date: document.getElementById('dueDate').value
        };

        const response = await fetch('/api/bills/generate', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(billData)
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        showSuccess('Tagihan berhasil dibuat');
        bootstrap.Modal.getInstance(document.getElementById('meterReadingModal')).hide();
        searchBills(currentPage);
        loadBillStats();
    } catch (error) {
        console.error('Error generating bill:', error);
        showError(error.message || 'Terjadi kesalahan saat membuat tagihan');
    } finally {
        saveButton.disabled = false;
        saveButton.innerHTML = '<i class="fas fa-save me-2"></i>Generate Tagihan';
    }
}

async function showBillDetail(billId) {
    const modal = new bootstrap.Modal(document.getElementById('billDetailModal'));
    modal.show();

    try {
        const response = await fetch(`/api/bills/${billId}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        renderBillDetail(data.data);
        currentBillId = billId;
    } catch (error) {
        console.error('Error loading bill detail:', error);
        document.getElementById('billDetailContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <p class="text-danger">Data Tidak Ada</p>
            </div>
        `;
    }
}

function renderBillDetail(bill) {
    const usage = bill.current_reading - bill.previous_reading;
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="border-bottom pb-2">Informasi Pelanggan</h6>
                <table class="table table-borderless table-sm">
                    <tr><td width="40%">Nama</td><td>: ${bill.customer_name}</td></tr>
                    <tr><td>No. Pelanggan</td><td>: ${bill.customer_number}</td></tr>
                    <tr><td>No. Meter</td><td>: ${bill.meter_number}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="border-bottom pb-2">Informasi Tagihan</h6>
                <table class="table table-borderless table-sm">
                    <tr><td width="40%">No. Tagihan</td><td>: ${bill.bill_number}</td></tr>
                    <tr><td>Periode</td><td>: ${getMonthName(bill.period_month)} ${bill.period_year}</td></tr>
                    <tr><td>Tanggal Terbit</td><td>: ${formatDate(bill.issued_date)}</td></tr>
                    <tr><td>Jatuh Tempo</td><td>: ${formatDate(bill.due_date)}</td></tr>
                    <tr><td>Status</td><td>: ${getStatusBadge(bill.status)}</td></tr>
                </table>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <h6 class="border-bottom pb-2">Pemakaian Air</h6>
                <table class="table table-bordered">
                    <tr>
                        <th width="25%">Pembacaan Sebelumnya</th>
                        <th width="25%">Pembacaan Saat Ini</th>
                        <th width="25%">Pemakaian (m³)</th>
                        <th width="25%">Tarif Dasar</th>
                    </tr>
                    <tr class="text-center">
                        <td>${bill.previous_reading}</td>
                        <td>${bill.current_reading}</td>
                        <td class="fw-bold">${usage}</td>
                        <td>Rp ${formatCurrency(bill.base_amount)}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-8">
                <h6 class="border-bottom pb-2">Riwayat Pembayaran</h6>
                <div class="text-center py-3">
                    <i class="fas fa-credit-card fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">Belum ada pembayaran untuk tagihan ini</p>
                </div>
            </div>
            <div class="col-md-4">
                <h6 class="border-bottom pb-2">Rincian Biaya</h6>
                <table class="table table-borderless table-sm">
                    <tr><td>Tarif Dasar</td><td class="text-end">Rp ${formatCurrency(bill.base_amount)}</td></tr>
                    ${bill.additional_charges > 0 ? `<tr><td>Biaya Tambahan</td><td class="text-end">Rp ${formatCurrency(bill.additional_charges)}</td></tr>` : ''}
                    ${bill.tax_amount > 0 ? `<tr><td>Pajak</td><td class="text-end">Rp ${formatCurrency(bill.tax_amount)}</td></tr>` : ''}
                    <tr class="border-top"><th>Total Tagihan</th><th class="text-end">Rp ${formatCurrency(bill.total_amount)}</th></tr>
                </table>
            </div>
        </div>
    `;
    
    document.getElementById('billDetailContent').innerHTML = content;
}

function showStatusModal(billId) {
    currentBillId = billId;
    document.getElementById('billIdStatus').value = billId;
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

async function updateStatus() {
    const billId = parseInt(document.getElementById('billIdStatus').value);
    const newStatus = document.getElementById('newStatus').value;

    if (!newStatus) {
        showError('Pilih status baru');
        return;
    }

    const saveButton = document.getElementById('updateStatusBtn');
    saveButton.disabled = true;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

    try {
        const response = await fetch(`/api/bills/${billId}/status`, {
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status: newStatus })
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        showSuccess('Status tagihan berhasil diubah');
        bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
        searchBills(currentPage);
        loadBillStats();
    } catch (error) {
        console.error('Error updating status:', error);
        showError(error.message || 'Terjadi kesalahan saat mengubah status');
    } finally {
        saveButton.disabled = false;
        saveButton.innerHTML = '<i class="fas fa-save me-2"></i>Update Status';
    }
}

async function markAsPaid(billId) {
    if (!confirm('Tandai tagihan ini sebagai lunas?')) return;

    try {
        const response = await fetch(`/api/bills/${billId}/status`, {
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status: 'paid' })
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        showSuccess('Tagihan berhasil ditandai sebagai lunas');
        searchBills(currentPage);
        loadBillStats();
    } catch (error) {
        console.error('Error marking as paid:', error);
        showError(error.message || 'Terjadi kesalahan saat menandai sebagai lunas');
    }
}

// Utility functions
function resetFilters() {
    document.getElementById('customerSearchFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('periodFilter').value = '';
    document.getElementById('dueDateFilter').value = '';
    searchBills(1);
}

function exportData() {
    showInfo('Fitur export sedang dalam pengembangan');
}

function printBill() {
    if (!currentBillId) return;
    showInfo(`Printing bill ${currentBillId}...`);
}

function validateBillForm() {
    clearFormErrors();
    let isValid = true;
    
    const requiredFields = [
        { id: 'customerSelect', message: 'Pilih pelanggan' },
        { id: 'billingPeriodSelect', message: 'Pilih periode tagihan' },
        { id: 'currentReading', message: 'Masukkan pembacaan saat ini' },
        { id: 'baseAmount', message: 'Masukkan tarif dasar' },
        { id: 'dueDate', message: 'Tentukan jatuh tempo' }
    ];
    
    requiredFields.forEach(field => {
        const element = document.getElementById(field.id);
        if (!element.value.trim()) {
            showFieldError(element, field.message);
            isValid = false;
        }
    });
    
    return isValid;
}

function clearFormErrors() {
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function showFieldError(field, message) {
    field.classList.add('is-invalid');
    const feedback = field.parentElement.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.textContent = message;
    }
}

function handleApiError(error, defaultMessage) {
    const message = error.response?.data?.message || error.message || defaultMessage;
    showError(message);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID').format(amount);
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit', 
        year: 'numeric'
    });
}

function getMonthName(month) {
    const months = [
        '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return months[month] || month;
}

function getStatusBadge(status) {
    const badges = {
        pending: '<span class="badge bg-warning">Pending</span>',
        sent: '<span class="badge bg-info">Terkirim</span>',
        paid: '<span class="badge bg-success">Lunas</span>',
        overdue: '<span class="badge bg-danger">Terlambat</span>',
        cancelled: '<span class="badge bg-secondary">Dibatalkan</span>'
    };
    return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
}

function showSuccess(message) {
    showToast(message, 'success');
}

function showError(message) {
    showToast(message, 'error');
}

function showInfo(message) {
    showToast(message, 'info');
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
</script>
@endsection
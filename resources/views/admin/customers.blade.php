@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8 col-sm-6">
                            <h5 class="mb-1">
                                <i class="fas fa-address-book me-2"></i>
                                Manajemen Data Pelanggan
                            </h5>
                            <small class="opacity-75">Kelola data pelanggan PDAM dan informasi meter</small>
                        </div>
                        <div class="col-md-4 col-sm-6 text-end">
                            <button type="button" class="btn btn-light btn-sm" onclick="showAddCustomerModal()">
                                <i class="fas fa-plus me-2"></i>
                                <span class="d-none d-sm-inline">Tambah </span>Pelanggan
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                            <div class="card bg-primary text-white border-0 h-100">
                                <div class="card-body text-center py-3">
                                    <h4 id="total-customers" class="mb-1">-</h4>
                                    <small>Total Pelanggan</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                            <div class="card bg-success text-white border-0 h-100">
                                <div class="card-body text-center py-3">
                                    <h4 id="active-customers" class="mb-1">-</h4>
                                    <small>Pelanggan Aktif</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3 mb-md-0">
                            <div class="card bg-info text-white border-0 h-100">
                                <div class="card-body text-center py-3">
                                    <h4 id="total-meters" class="mb-1">-</h4>
                                    <small>Total Meter</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-warning text-dark border-0 h-100">
                                <div class="card-body text-center py-3">
                                    <h4 id="inactive-meters" class="mb-1">-</h4>
                                    <small>Meter Non-Aktif</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchInput" 
                                       placeholder="Cari nomor pelanggan, nama, atau meter...">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6 mb-3 mb-lg-0">
                            <select class="form-select" id="tariffFilter">
                                <option value="">Semua Tarif</option>
                                <option value="R1">R1 - Rumah Tangga</option>
                                <option value="R2">R2 - Rumah Mewah</option>
                                <option value="N1">N1 - Niaga Kecil</option>
                                <option value="N2">N2 - Niaga Besar</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6 mb-3 mb-lg-0">
                            <select class="form-select" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Non-Aktif</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6 mb-3 mb-md-0">
                            <button class="btn btn-primary w-100" onclick="searchCustomers()">
                                <i class="fas fa-search me-1"></i>
                                <span class="d-none d-sm-inline">Cari</span>
                            </button>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                                <i class="fas fa-undo me-1"></i>
                                <span class="d-none d-sm-inline">Reset</span>
                            </button>
                        </div>
                    </div>

                    <div id="loadingState" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data pelanggan...</p>
                    </div>

                    <div class="table-responsive" id="customersTableContainer">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th onclick="sortTable('customer_number')" class="sortable-header" style="min-width: 120px;">
                                        <div class="d-flex align-items-center">
                                            <span>No. Pelanggan</span>
                                            <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="customer_number"></i>
                                        </div>
                                    </th>
                                    <th onclick="sortTable('name')" class="sortable-header d-none d-md-table-cell" style="min-width: 200px;">
                                        <div class="d-flex align-items-center">
                                            <span>Nama Pelanggan</span>
                                            <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="name"></i>
                                        </div>
                                    </th>
                                    <th class="d-none d-lg-table-cell" style="min-width: 250px;">Alamat</th>
                                    <th class="d-none d-xl-table-cell" style="min-width: 140px;">No. KTP</th>
                                    <th class="d-none d-md-table-cell text-center" style="min-width: 80px;">Tarif</th>
                                    <th class="d-none d-lg-table-cell text-center" style="min-width: 120px;">Meter</th>
                                    <th onclick="sortTable('created_at')" class="sortable-header d-none d-lg-table-cell text-center" style="min-width: 110px;">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span>Terdaftar</span>
                                            <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="created_at"></i>
                                        </div>
                                    </th>
                                    <th class="d-none d-sm-table-cell text-center" style="min-width: 80px;">Status</th>
                                    <th class="text-center" style="min-width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="customersTableBody">
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h6 class="text-muted mb-2">Belum ada data pelanggan</h6>
                                            <p class="text-muted small mb-0">Klik tombol "Cari" untuk memuat data pelanggan</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row align-items-center mt-4">
                        <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
                            <select class="form-select form-select-sm" id="perPageSelect" onchange="changePerPage()">
                                <option value="10">10 per halaman</option>
                                <option value="25">25 per halaman</option>
                                <option value="50">50 per halaman</option>
                                <option value="100">100 per halaman</option>
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
                                    </ul>
                            </nav>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<div class="modal fade" id="customerModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="customerModalTitle">
                    <i class="fas fa-user-plus me-2"></i>
                    Tambah Pelanggan Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3 border-bottom pb-2">
                                <i class="fas fa-user me-2"></i>
                                Data Pengguna
                            </h6>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user text-primary"></i>
                                </span>
                                <input type="text" class="form-control" id="customerName" name="name" 
                                       placeholder="Masukkan nama lengkap" required>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label fw-semibold">Nomor Telepon <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-phone text-primary"></i>
                                </span>
                                <input type="tel" class="form-control" id="customerPhone" name="phone" 
                                       placeholder="08xxxxxxxxxx" required>
                            </div>
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">Untuk login dan notifikasi WhatsApp</small>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope text-primary"></i>
                                </span>
                                <input type="email" class="form-control" id="customerEmail" name="email"
                                       placeholder="email@domain.com (opsional)">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock text-primary"></i>
                                </span>
                                <input type="password" class="form-control" id="customerPassword" name="password" 
                                       placeholder="Minimal 8 karakter" minlength="8" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('customerPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">Minimal 8 karakter: huruf besar, kecil, dan angka</small>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3 border-bottom pb-2">
                                <i class="fas fa-address-card me-2"></i>
                                Data Pelanggan
                            </h6>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label fw-semibold">Nomor Pelanggan</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-hashtag text-primary"></i>
                                </span>
                                <input type="text" class="form-control" id="customerNumber" name="customer_number"
                                       placeholder="Auto-generated" readonly>
                            </div>
                            <small class="form-text text-muted">Otomatis di-generate sistem</small>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label fw-semibold">Nomor KTP <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-id-card text-primary"></i>
                                </span>
                                <input type="text" class="form-control" id="ktpNumber" name="ktp_number" 
                                       maxlength="16" placeholder="1234567890123456" required>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text align-items-start pt-3">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                </span>
                                <textarea class="form-control" id="customerAddress" name="address" rows="3" 
                                          placeholder="Masukkan alamat lengkap sesuai KTP" required></textarea>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label fw-semibold">Golongan Tarif <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-tags text-primary"></i>
                                </span>
                                <select class="form-select" id="tariffGroup" name="tariff_group" required>
                                    <option value="">Pilih Golongan Tarif</option>
                                    <option value="R1">R1 - Rumah Tangga (0-10 m³)</option>
                                    <option value="R2">R2 - Rumah Mewah (>10 m³)</option>
                                    <option value="N1">N1 - Niaga Kecil (0-30 m³)</option>
                                    <option value="N2">N2 - Niaga Besar (>30 m³)</option>
                                </select>
                            </div>
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">Pilih sesuai jenis penggunaan air</small>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3 border-bottom pb-2">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Data Meter Air
                            </h6>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label fw-semibold">Nomor Meter <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-barcode text-primary"></i>
                                </span>
                                <input type="text" class="form-control" id="meterNumber" name="meter_number" 
                                       placeholder="MTR001" required>
                            </div>
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">Nomor unik pada meter air</small>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label fw-semibold">Jenis Meter <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-cogs text-primary"></i>
                                </span>
                                <select class="form-select" id="meterType" name="meter_type" required>
                                    <option value="">Pilih Jenis Meter</option>
                                    <option value="analog">Analog</option>
                                    <option value="digital">Digital</option>
                                </select>
                            </div>
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">Tipe meter yang terpasang</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-info-circle me-3 mt-1 text-primary"></i>
                                    <div>
                                        <h6 class="alert-heading mb-2">
                                            Informasi Penting
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="mb-0 small">
                                                    <li>Pastikan semua data telah diisi dengan benar</li>
                                                    <li>Nomor telepon untuk login dan WhatsApp</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="mb-0 small">
                                                    <li>Nomor KTP dan nomor meter harus unik</li>
                                                    <li>Password harus memenuhi kriteria keamanan</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Batal
                </button>
                <button type="button" class="btn btn-primary" id="saveCustomerBtn" onclick="saveCustomer()">
                    <i class="fas fa-save me-2"></i>
                    <span id="saveButtonText">Simpan</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="customerDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user me-2"></i>
                    Detail Pelanggan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="customerDetailContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat detail pelanggan...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="editFromDetailBtn" onclick="editCustomerFromDetail()">
                    <i class="fas fa-edit me-2"></i>
                    Edit Data
                </button>
            </div>
        </div>
    </div>
</div>

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
    
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
        flex-shrink: 0;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
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
    
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* Enhanced loading animation */
    .spinner-border {
        animation: spinner-border 0.75s linear infinite;
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
        
        .avatar-circle {
            width: 32px;
            height: 32px;
            font-size: 12px;
        }
    }
    
    @media (max-width: 576px) {
        .modal-xl {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }
        
        .card-header .row {
            text-align: center;
        }
        
        .card-header .col-md-4 {
            margin-top: 1rem;
        }
        
        .table td, .table th {
            padding: 0.5rem 0.25rem;
            font-size: 0.875rem;
        }
    }
    
    /* Loading overlay improvements */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        backdrop-filter: blur(2px);
    }
    
    /* Badge enhancements */
    .badge {
        font-weight: 500;
        letter-spacing: 0.02em;
    }
    
    /* Search input enhancements */
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }
    
    /* Button hover effects */
    .btn:hover {
        transform: translateY(-1px);
    }
    
    .btn:active {
        transform: translateY(0);
    }
</style>

<script>
// Global variables
let currentPage = 1;
let perPage = 10;
let sortField = 'created_at';
let sortDirection = 'desc';
let currentCustomerId = null;
let isEditMode = false;

// Setup axios defaults
if (typeof window.axios === 'undefined') {
    console.error('Axios tidak ditemukan. Pastikan sudah di-include.');
} else {
    // Set CSRF token
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
    }
    
    // Set API base URL
    axios.defaults.baseURL = '{{ url('/') }}';
    
    axios.defaults.headers.common['Accept'] = 'application/json';
    axios.defaults.headers.common['Content-Type'] = 'application/json';
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupSearchHandlers();
    loadCustomerStats(); // Load stats from API
    searchCustomers(); // Load table data from API
});

// REQ-F-3.1: Search and fetch customers from API
async function searchCustomers(page = 1) {
    showLoading(true);
    
    const searchTerm = document.getElementById('searchInput').value;
    const tariffFilter = document.getElementById('tariffFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    try {
        const response = await axios.get('/api/customers', {
            params: {
                page: page,
                per_page: perPage,
                sort_field: sortField,
                sort_direction: sortDirection,
                search: searchTerm,
                // Parameter filter sesuai dengan API CustomerController@index
                tariff_group: tariffFilter,
                status: statusFilter 
            }
        });
        
        const data = response.data;

        if (data.data) {
            displayCustomers(data);
            updatePagination(data);
            currentPage = page;
        } else {
            handleApiError(null, 'Gagal memuat data pelanggan: format respons tidak sesuai.');
        }

    } catch (error) {
        console.error('Error loading customers:', error);
        handleApiError(error, 'Gagal memuat data pelanggan. Pastikan server aktif dan terautentikasi.');
    } finally {
        showLoading(false);
    }
}

// Display customers in table
function displayCustomers(data) {
    const tbody = document.getElementById('customersTableBody');
    
    if (!data.data || data.data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-search fa-4x text-muted mb-3 opacity-50"></i>
                        <h6 class="text-muted mb-2">Tidak ada data pelanggan</h6>
                        <p class="text-muted small mb-0">Coba ubah kriteria pencarian atau tambah pelanggan baru</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = data.data.map((customer, index) => {
        // Data dari Customer::with(['user', 'meters'])
        const user = customer.user || {};
        const meters = customer.meters || [];
        
        // Ambil tarif dari customer.tariff_group (untuk filter) atau meter pertama
        const firstMeter = meters.length > 0 ? meters[0] : {};
        const meterTariff = customer.tariff_group || firstMeter.customer_group_code || 'N/A';
            
        const meterInfo = meters.length > 0 
            ? meters.map(meter => `
                <div class="mb-1">
                    <div class="d-flex align-items-center justify-content-center">
                        <small class="text-primary fw-bold me-1">${meter.meter_number}</small>
                        <span class="badge ${meter.is_active ? 'bg-success' : 'bg-danger'} badge-sm">
                            ${meter.is_active ? 'Aktif' : 'Off'}
                        </span>
                    </div>
                </div>
              `).join('') 
            : '<span class="text-muted small">No meter</span>';
            
        const rowNumber = ((data.current_page - 1) * data.per_page) + index + 1;
            
        return `
            <tr class="hover-shadow">
                <td>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-light text-dark me-2">${rowNumber}</span>
                        <strong class="text-primary">${customer.customer_number || '-'}</strong>
                    </div>
                </td>
                <td class="d-none d-md-table-cell">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-3">
                            ${user.name ? user.name.charAt(0).toUpperCase() : '?'}
                        </div>
                        <div>
                            <div class="fw-bold text-dark">${user.name || 'N/A'}</div>
                            <small class="text-muted">
                                <i class="fas fa-phone fa-xs me-1"></i>
                                ${user.phone || '-'}
                            </small>
                        </div>
                    </div>
                </td>
                <td class="d-none d-lg-table-cell">
                    <div class="text-truncate" style="max-width: 250px;" title="${customer.address || '-'}">
                        <i class="fas fa-map-marker-alt text-muted me-1"></i>
                        <small>${customer.address || '-'}</small>
                    </div>
                </td>
                <td class="d-none d-xl-table-cell">
                    <small class="font-monospace">${customer.ktp_number || '-'}</small>
                </td>
                <td class="d-none d-md-table-cell text-center">
                    <span class="badge ${getTariffBadgeClass(meterTariff)}">${meterTariff}</span>
                </td>
                <td class="d-none d-lg-table-cell text-center">${meterInfo}</td>
                <td class="d-none d-lg-table-cell text-center">
                    <small class="text-muted">${formatDate(customer.created_at)}</small>
                </td>
                <td class="d-none d-sm-table-cell text-center">
                    <span class="badge ${user.is_active ? 'bg-success' : 'bg-danger'}">
                        ${user.is_active ? 'Aktif' : 'Non-Aktif'}
                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-info btn-sm" onclick="viewCustomerDetail(${customer.id})" 
                                title="Lihat Detail" data-bs-toggle="tooltip">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="editCustomer(${customer.id})" 
                                title="Edit Data" data-bs-toggle="tooltip">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDeleteCustomer(${customer.id})" 
                                title="Hapus" data-bs-toggle="tooltip">
                            <i class="fas fa-trash"></i>
                        </button>
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

// Load customer statistics from API
async function loadCustomerStats() {
    // Set loading state
    ['total-customers', 'active-customers', 'total-meters', 'inactive-meters'].forEach(id => {
        document.getElementById(id).textContent = '...';
    });
    
    try {
        const response = await axios.get('/api/customers/stats'); // Endpoint sudah tersedia
        const stats = response.data.data;
        
        document.getElementById('total-customers').textContent = stats.total_customers || 0;
        document.getElementById('active-customers').textContent = stats.active_customers || 0;
        document.getElementById('total-meters').textContent = stats.total_meters || 0;
        document.getElementById('inactive-meters').textContent = stats.inactive_meters || 0;
    } catch (error) {
        console.error('Error loading stats:', error);
        // Set default values on error
        ['total-customers', 'active-customers', 'total-meters', 'inactive-meters'].forEach(id => {
            document.getElementById(id).textContent = '0';
        });
    }
}

// REQ-F-3.2: Show Add Customer Modal
function showAddCustomerModal() {
    resetCustomerForm();
    isEditMode = false;
    currentCustomerId = null;
    
    document.getElementById('customerModalTitle').innerHTML = '<i class="fas fa-user-plus me-2"></i>Tambah Pelanggan Baru';
    document.getElementById('saveButtonText').textContent = 'Simpan';
    document.getElementById('customerPassword').required = true;
    
    new bootstrap.Modal(document.getElementById('customerModal')).show();
}

// REQ-F-3.2: Edit Customer
async function editCustomer(customerId) {
    isEditMode = true;
    currentCustomerId = customerId;
    
    document.getElementById('customerModalTitle').innerHTML = '<i class="fas fa-user-edit me-2"></i>Edit Data Pelanggan';
    document.getElementById('saveButtonText').textContent = 'Update';
    document.getElementById('customerPassword').required = false;
    
    showLoading(true);
    
    try {
        // Use API: CustomerController@show
        const response = await axios.get(`/api/customers/${customerId}`);
        const customer = response.data.data;
        
        if (!customer) {
            throw new Error('Customer tidak ditemukan');
        }
        
        // Fill form with customer data
        fillCustomerForm(customer);
        
        new bootstrap.Modal(document.getElementById('customerModal')).show();
    } catch (error) {
        console.error('Error loading customer:', error);
        handleApiError(error, 'Gagal memuat data pelanggan');
    } finally {
        showLoading(false);
    }
}

// Fill form with customer data
function fillCustomerForm(customer) {
    // Fill User Data
    document.getElementById('customerName').value = customer.user?.name || '';
    document.getElementById('customerEmail').value = customer.user?.email || '';
    document.getElementById('customerPhone').value = customer.user?.phone || '';
    
    // Fill Customer Data
    document.getElementById('customerNumber').value = customer.customer_number || '';
    document.getElementById('ktpNumber').value = customer.ktp_number || '';
    document.getElementById('customerAddress').value = customer.address || '';
    // Gunakan customer.tariff_group (dari tabel customers)
    document.getElementById('tariffGroup').value = customer.tariff_group || '';
    
    // Fill Meter Data (first meter)
    if (customer.meters && customer.meters.length > 0) {
        const meter = customer.meters[0];
        document.getElementById('meterNumber').value = meter.meter_number || '';
        document.getElementById('meterType').value = meter.meter_type || '';
        // installation_date field tidak ada di form HTML Anda, jadi kita abaikan
    }
}

// Reset form
function resetCustomerForm() {
    document.getElementById('customerForm').reset();
    clearAllValidationErrors();
    document.getElementById('customerNumber').placeholder = 'Auto-generated';
}

// REQ-F-3.2: Save Customer
async function saveCustomer() {
    if (!validateCustomerForm()) {
        return;
    }
    
    const formData = collectFormData();
    const saveButton = document.getElementById('saveCustomerBtn');
    
    // Set loading state
    saveButton.disabled = true;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    
    try {
        let response;
        if (isEditMode) {
            // Edit mode: Use PUT /api/customers/{id}
            response = await axios.put(`/api/customers/${currentCustomerId}`, formData);
            showSuccess('Data pelanggan berhasil diupdate');
        } else {
            // Create mode: Use POST /api/customers
            
            // Format payload sesuai dengan API CustomerController@store yang mengharapkan array 'meters'
            const meterData = {
                meter_number: formData.meter_number,
                meter_type: formData.meter_type,
                customer_group_code: formData.tariff_group, 
                meter_size: '1/2"', // Default size
                installation_date: new Date().toISOString().split('T')[0] // Default today's date
            };
            
            const payload = {
                name: formData.name,
                email: formData.email,
                phone: formData.phone,
                password: formData.password,
                ktp_number: formData.ktp_number,
                address: formData.address,
                meters: [meterData] // Kirim sebagai array
            };
            
            response = await axios.post('/api/customers', payload);
            showSuccess('Pelanggan baru berhasil ditambahkan');
        }
        
        bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
        searchCustomers(currentPage);
        loadCustomerStats();
    } catch (error) {
        console.error('Error saving customer:', error);
        handleApiError(error, 'Gagal menyimpan data pelanggan');
    } finally {
        // Reset button
        saveButton.disabled = false;
        saveButton.innerHTML = `<i class="fas fa-save me-2"></i>${document.getElementById('saveButtonText').textContent}`;
    }
}

// Collect form data
function collectFormData() {
    const formData = {
        name: document.getElementById('customerName').value,
        email: document.getElementById('customerEmail').value,
        phone: document.getElementById('customerPhone').value,
        ktp_number: document.getElementById('ktpNumber').value,
        address: document.getElementById('customerAddress').value,
        tariff_group: document.getElementById('tariffGroup').value,
        meter_number: document.getElementById('meterNumber').value,
        meter_type: document.getElementById('meterType').value,
        // installation_date: document.getElementById('installationDate')?.value,
    };
    
    // Add password only if provided or in create mode
    const password = document.getElementById('customerPassword').value;
    if (password || !isEditMode) {
        formData.password = password;
    }
    
    // Add customer number only in edit mode
    if (isEditMode) {
        formData.customer_number = document.getElementById('customerNumber').value;
    }
    
    return formData;
}

// REQ-F-3.3: View Customer Detail
async function viewCustomerDetail(customerId) {
    currentCustomerId = customerId;
    
    // Show modal immediately with loading
    const modal = new bootstrap.Modal(document.getElementById('customerDetailModal'));
    modal.show();
    
    // Set loading content
    document.getElementById('customerDetailContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat detail pelanggan...</p>
        </div>
    `;

    try {
        // Use API: CustomerController@show
        const response = await axios.get(`/api/customers/${customerId}`);
        const customer = response.data.data;
        
        if (!customer) {
            throw new Error('Customer tidak ditemukan');
        }
        
        displayCustomerDetail(customer);
    } catch (error) {
        console.error('Error loading customer detail:', error);
        document.getElementById('customerDetailContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <p class="text-danger">Gagal memuat detail pelanggan: ${error.response?.data?.message || error.message}</p>
            </div>
        `;
    }
}

// REQ-F-3.3: Display Customer Detail
function displayCustomerDetail(customer) {
    // Ambil tarif dari customer.tariff_group (dari tabel customers)
    const primaryTariff = customer.tariff_group || (customer.meters && customer.meters.length > 0 ? customer.meters[0].customer_group_code : 'N/A');
    
    const meterDetailHtml = customer.meters && customer.meters.length > 0 ? 
        customer.meters.map(meter => `
            <div class="card border mb-3">
                <div class="card-body">
                    <div class="row text-center text-md-start">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <strong>No. Meter:</strong><br>
                            <span class="text-primary h5">${meter.meter_number}</span>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <strong>Jenis:</strong><br>
                            <span class="badge bg-info">${meter.meter_type}</span>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <strong>Tgl. Instalasi:</strong><br>
                            ${formatDate(meter.installation_date)}
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong><br>
                            <span class="badge ${meter.is_active ? 'bg-success' : 'bg-danger'}">
                                ${meter.is_active ? 'Aktif' : 'Non-Aktif'}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `).join('') : 
        '<div class="text-center py-3"><p class="text-muted">Tidak ada data meter</p></div>';

    const content = `
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Pengguna</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="35%" class="text-muted">Nama:</td>
                                <td><strong>${customer.user?.name || '-'}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email:</td>
                                <td>${customer.user?.email || '-'}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Telepon:</td>
                                <td><strong>${customer.user?.phone || '-'}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status:</td>
                                <td>
                                    <span class="badge ${customer.user?.is_active ? 'bg-success' : 'bg-danger'}">
                                        ${customer.user?.is_active ? 'Aktif' : 'Non-Aktif'}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Terdaftar:</td>
                                <td>${formatDate(customer.created_at)}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-address-card me-2"></i>Data Pelanggan</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="35%" class="text-muted">No. Pelanggan:</td>
                                <td><strong class="text-primary">${customer.customer_number || '-'}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">No. KTP:</td>
                                <td>${customer.ktp_number || '-'}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Alamat:</td>
                                <td>${customer.address || '-'}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Golongan Tarif Utama:</td>
                                <td>
                                    <span class="badge bg-secondary">${primaryTariff}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Informasi Meter</h6>
                    </div>
                    <div class="card-body">
                        ${meterDetailHtml}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-6 mb-3 mb-lg-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Riwayat Tagihan Terakhir</h6>
                    </div>
                    <div class="card-body text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Data tagihan akan ditampilkan di sini</p>
                        <small class="text-muted">Integrasi dengan modul billing</small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Riwayat Pembayaran Terakhir</h6>
                    </div>
                    <div class="card-body text-center py-4">
                        <i class="fas fa-money-bill-wave fa-3x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Data pembayaran akan ditampilkan di sini</p>
                        <small class="text-muted">Integrasi dengan modul payment</small>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('customerDetailContent').innerHTML = content;
}

// Edit from detail modal
function editCustomerFromDetail() {
    bootstrap.Modal.getInstance(document.getElementById('customerDetailModal')).hide();
    setTimeout(() => editCustomer(currentCustomerId), 300);
}

// Delete customer
async function confirmDeleteCustomer(customerId) {
    if (!confirm('Apakah Anda yakin ingin menghapus pelanggan ini?\n\nPerhatian: Semua data terkait akan ikut terhapus.')) {
        return;
    }
    
    try {
        // Use API: CustomerController@destroy
        await axios.delete(`/api/customers/${customerId}`);
        
        showSuccess('Pelanggan berhasil dihapus');
        searchCustomers(currentPage);
        loadCustomerStats();
    } catch (error) {
        console.error('Error deleting customer:', error);
        handleApiError(error, 'Gagal menghapus pelanggan');
    }
}

// Form validation
function validateCustomerForm() {
    clearAllValidationErrors();
    let isValid = true;
    
    // Required fields
    const requiredFields = [
        { id: 'customerName', message: 'Nama lengkap wajib diisi' },
        { id: 'customerPhone', message: 'Nomor telepon wajib diisi' },
        { id: 'ktpNumber', message: 'Nomor KTP wajib diisi' },
        { id: 'customerAddress', message: 'Alamat wajib diisi' },
        { id: 'tariffGroup', message: 'Golongan tarif wajib dipilih' },
        { id: 'meterNumber', message: 'Nomor meter wajib diisi' },
        { id: 'meterType', message: 'Jenis meter wajib dipilih' },
    ];
    
    if (!isEditMode || document.getElementById('customerPassword').value) {
        requiredFields.push({ id: 'customerPassword', message: 'Password wajib diisi' });
    }
    
    requiredFields.forEach(field => {
        const element = document.getElementById(field.id);
        if (!element.value.trim()) {
            showFieldError(element, field.message);
            isValid = false;
        }
    });
    
    // Phone validation
    const phone = document.getElementById('customerPhone').value;
    if (phone && !phone.match(/^08\d{8,11}$/)) {
        showFieldError(document.getElementById('customerPhone'), 'Format: 08xxxxxxxxxx (8-12 digit)');
        isValid = false;
    }
    
    // KTP validation
    const ktp = document.getElementById('ktpNumber').value;
    if (ktp && !ktp.match(/^\d{16}$/)) {
        showFieldError(document.getElementById('ktpNumber'), 'KTP harus 16 digit angka');
        isValid = false;
    }
    
    // Password validation
    const password = document.getElementById('customerPassword').value;
    if (password && !password.match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/)) {
        showFieldError(document.getElementById('customerPassword'), 
            'Minimal 8 karakter: huruf besar, kecil, dan angka');
        isValid = false;
    }
    
    return isValid;
}

// Helper function for tariff badge colors
function getTariffBadgeClass(tariff) {
    switch(tariff) {
        case 'R1': return 'bg-primary';
        case 'R2': return 'bg-info';
        case 'N1': return 'bg-success';
        case 'N2': return 'bg-warning';
        default: return 'bg-secondary';
    }
}

// REQ-F-10: Enhanced sorting functionality
function sortTable(field) {
    // Update sort parameters
    if (sortField === field) {
        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        sortField = field;
        sortDirection = 'asc';
    }
    
    showTableLoading(true);
    searchCustomers(1);
    updateSortIcons(field);
}

function updateSortIcons(activeField) {
    document.querySelectorAll('.sort-icon').forEach(icon => {
        icon.className = 'fas fa-sort ms-2 text-muted sort-icon';
    });
    
    const activeIcon = document.querySelector(`.sort-icon[data-field="${activeField}"]`);
    if (activeIcon) {
        activeIcon.className = `fas fa-sort-${sortDirection === 'asc' ? 'up' : 'down'} ms-2 text-primary sort-icon`;
    }
}

// Enhanced search with real-time feedback
let searchTimeout;
function setupSearchHandlers() {
    const searchInput = document.getElementById('searchInput');
    const tariffFilter = document.getElementById('tariffFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    // Real-time search with debouncing
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const value = this.value.trim();
        
        if (value.length === 0 || value.length >= 2) {
            searchTimeout = setTimeout(() => {
                searchCustomers(1);
            }, 300);
        }
    });
    
    // Filter change handlers
    [tariffFilter, statusFilter].forEach(filter => {
        filter.addEventListener('change', () => {
            searchCustomers(1);
        });
    });
    
    // Enter key search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimeout);
            searchCustomers(1);
        }
    });
}

function changePerPage() {
    perPage = parseInt(document.getElementById('perPageSelect').value);
    searchCustomers(1);
}

function updatePagination(data) {
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationList = document.getElementById('paginationList');
    
    paginationInfo.textContent = `Menampilkan ${data.from || 0} - ${data.to || 0} dari ${data.total || 0} data`;
    
    if (data.last_page <= 1) {
        paginationList.innerHTML = '';
        return;
    }
    
    let paginationHtml = '';
    
    // Previous button
    if (data.current_page > 1) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="searchCustomers(${data.current_page - 1})">
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
                <a class="page-link" href="#" onclick="searchCustomers(${i})">${i}</a>
            </li>
        `;
    }
    
    // Next button
    if (data.current_page < data.last_page) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="searchCustomers(${data.current_page + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
    }
    
    paginationList.innerHTML = paginationHtml;
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('tariffFilter').value = '';
    document.getElementById('statusFilter').value = '';
    searchCustomers(1);
}

// Utility functions
function showLoading(show) {
    const loadingState = document.getElementById('loadingState');
    const tableContainer = document.getElementById('customersTableContainer');
    
    if (show) {
        loadingState.classList.remove('d-none');
        tableContainer.classList.add('d-none');
    } else {
        loadingState.classList.add('d-none');
        tableContainer.classList.remove('d-none');
    }
    if (!show) {
        showTableLoading(false);
    }
}

function showTableLoading(show) {
    const tbody = document.getElementById('customersTableBody');
    const loadingState = document.getElementById('loadingState');
    
    if (loadingState.classList.contains('d-none')) {
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
}

function showSuccess(message) {
    alert('✅ ' + message);
}

function showError(message) {
    alert('❌ ' + message);
}

function showFieldError(field, message) {
    field.classList.add('is-invalid');
    const feedback = field.parentElement.querySelector('.invalid-feedback') || 
                    field.parentElement.parentElement.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.textContent = message;
    }
}

function clearFieldError(field) {
    field.classList.remove('is-invalid');
}

function clearAllValidationErrors() {
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function handleApiError(error, defaultMessage) {
    if (error?.response?.data?.errors) {
        // Handle validation errors
        Object.keys(error.response.data.errors).forEach(field => {
            // Mapping fields untuk error API ke ID HTML
            const formFieldId = {
                'name': 'customerName',
                'phone': 'customerPhone',
                'email': 'customerEmail',
                'ktp_number': 'ktpNumber',
                'address': 'customerAddress',
                'meters.0.meter_number': 'meterNumber',
                'meters.0.customer_group_code': 'tariffGroup',
                'meters.0.meter_type': 'meterType',
                'password': 'customerPassword',
                'tariff_group': 'tariffGroup'
            }[field] || field;

            const element = document.getElementById(formFieldId);
            if (element && error.response.data.errors[field][0]) {
                showFieldError(element, error.response.data.errors[field][0]);
            }
        });
    } else {
        // Handle general errors
        const message = error?.response?.data?.message || defaultMessage;
        showError(message);
    }
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}

function truncateText(text, maxLength) {
    if (!text || text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Event listeners
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchCustomers(1);
    }
});

// Add CSS for avatar circle
document.head.insertAdjacentHTML('beforeend', `
    <style>
        .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-blue);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            flex-shrink: 0;
        }
    </style>
`);
</script>

@endsection
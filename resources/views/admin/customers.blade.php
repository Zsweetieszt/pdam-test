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
                                    {{-- 1. No. Pelanggan / Status (Gabungan) --}}
                                    <th onclick="sortTable('customer_number')" class="sortable-header" style="min-width: 150px;">
                                        <div class="d-flex align-items-center">
                                            <span>No. Pelanggan / Status</span>
                                            <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="customer_number"></i>
                                        </div>
                                    </th>
                                    {{-- 2. Nama Pelanggan --}}
                                    <th onclick="sortTable('name')" class="sortable-header d-none d-md-table-cell" style="min-width: 180px;">
                                        <div class="d-flex align-items-center">
                                            <span>Nama Pelanggan</span>
                                            <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="name"></i>
                                        </div>
                                    </th>
                                    {{-- 3. Alamat --}}
                                    <th class="d-none d-lg-table-cell" style="min-width: 250px;">Alamat</th>
                                    {{-- 4. No. KTP --}}
                                    <th class="d-none d-xl-table-cell" style="min-width: 140px;">No. KTP</th>
                                    {{-- 5. Tarif --}}
                                    <th class="d-none d-md-table-cell text-center" style="min-width: 100px;">Tarif</th>
                                    {{-- 6. Meter --}}
                                    <th class="d-none d-lg-table-cell text-center" style="min-width: 120px;">Meter</th>
                                    {{-- 7. Terdaftar --}}
                                    <th onclick="sortTable('created_at')" class="sortable-header d-none d-lg-table-cell text-center" style="min-width: 110px;">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span>Terdaftar</span>
                                            <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="created_at"></i>
                                        </div>
                                    </th>
                                    {{-- 8. Aksi --}}
                                    <th class="text-center" style="min-width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="customersTableBody">
                                <tr>
                                    <td colspan="8" class="text-center py-5">
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
                            <div class="invalid-feedback" id="nameError"></div>
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
                            <div class="invalid-feedback" id="phoneError"></div>
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
                            <div class="invalid-feedback" id="emailError"></div>
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
                            <div class="invalid-feedback" id="passwordError"></div>
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
                                <input type="text" class="form-control" id="customerKtp" name="ktp_number" 
                                       maxlength="16" placeholder="1234567890123456" required>
                            </div>
                            <div class="invalid-feedback" id="ktp_numberError"></div>
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
                            <div class="invalid-feedback" id="addressError"></div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label fw-semibold">Golongan Tarif <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-tags text-primary"></i>
                                </span>
                                <select class="form-select" id="customerGroup" name="customer_group_id" required>
                                    <option value="">Pilih Golongan Tarif</option>
                                    {{-- Opsi akan diisi oleh JavaScript dari API --}}
                                </select>
                            </div>
                            <div class="invalid-feedback" id="customer_group_codeError"></div>
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
                                <input type="text" class="form-control" id="meterId" name="meter_number" 
                                       placeholder="MTR001" required>
                            </div>
                            <div class="invalid-feedback" id="meter_numberError"></div>
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
                            <div class="invalid-feedback" id="meter_typeError"></div>
                            <small class="form-text text-muted">Tipe meter yang terpasang</small>
                        </div>
                        <input type="hidden" id="meterSize" name="meter_size" value="1/2&quot;" />
                        <input type="hidden" id="installationDate" name="installation_date" value="{{ date('Y-m-d') }}" />
                        <input type="hidden" id="initialReading" name="initial_reading" value="0" />
                        <input type="hidden" id="customerIsActive" name="is_active" value="1" />
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
                <button type="button" class="btn btn-primary" id="saveCustomerBtn" onclick="document.getElementById('customerForm').dispatchEvent(new Event('submit'))">
                    <span id="saveCustomerSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                    <i class="fas fa-save me-2"></i>
                    <span id="saveButtonText">Simpan</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash-alt me-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus pelanggan **<span id="deleteCustomerName" class="fw-bold"></span>**? Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" onclick="confirmDelete()">
                    <span id="deleteSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                    <span id="deleteText">Hapus Permanen</span>
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
                <button type="button" class="btn btn-primary" id="editFromDetailBtn" onclick="editCustomer(currentCustomerId)">
                    <i class="fas fa-edit me-2"></i>
                    Edit Data
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* ... (CSS yang sama) ... */
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
let deleteCustomerId = null;
let customerGroups = []; 
let isEditMode = false;
let currentCustomerId = null;

// ===================================
// START UTILITY FUNCTIONS
// ===================================

function getAuthHeaders() {
    const token = document.querySelector('meta[name="api-token"]')?.getAttribute('content');
    const headers = {};
    
    headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }
    
    return headers;
}

function showLoading(show) {
    const spinner = document.getElementById('mainSpinner');
    if (spinner) {
        spinner.style.display = show ? 'block' : 'none';
    }
    showTableLoading(show);
}

function showTableLoading(show) {
    const tbody = document.getElementById('customersTableBody');
    if (tbody) {
        if (show) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        Memuat data...
                    </td>
                </tr>
            `;
        }
    }
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
// ===================================
// END UTILITY FUNCTIONS
// ===================================


// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.axios === 'undefined') {
        console.error('Axios tidak ditemukan. Pastikan sudah di-include.');
    }
    
    axios.defaults.headers.common = getAuthHeaders();
    
    setupSearchHandlers();
    loadCustomerGroups(); 
    loadCustomerStats(); 
    searchCustomers(); 
    setupFormSubmission();
});

function setupSearchHandlers() {
    const searchInput = document.getElementById('searchInput');
    const groupFilter = document.getElementById('tariffFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const value = this.value.trim();
            
            if (value.length === 0 || value.length >= 2) {
                searchTimeout = setTimeout(() => {
                    searchCustomers(1);
                }, 300);
            }
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(searchTimeout);
                searchCustomers(1);
            }
        });
    }

    const filterElements = [groupFilter, statusFilter].filter(el => el !== null);
    
    filterElements.forEach(filter => {
        filter.addEventListener('change', () => {
            searchCustomers(1);
        });
    });
}

// ===================================
// START API INTEGRATION & ADD CUSTOMER FEATURE
// ===================================

// Load Customer Groups (untuk filter dan modal form)
async function loadCustomerGroups() {
    try {
        const response = await axios.get('/api/tariff/customer-groups', {
            headers: getAuthHeaders()
        });
        
        customerGroups = Array.isArray(response.data.data) ? response.data.data : [];
        
        const groupSelect = document.getElementById('customerGroup');
        const groupFilter = document.getElementById('tariffFilter');
        
        // Memastikan dropdown modal terisi
        if (groupSelect) {
            groupSelect.innerHTML = '<option value="">Pilih Golongan Tarif</option>';
            customerGroups.forEach(group => {
                // Menggunakan ID group sebagai value untuk form submission
                groupSelect.innerHTML += `<option value="${group.id}">${group.name} (${group.code})</option>`; 
            });
        }
        
        // Memastikan dropdown filter terisi
        if (groupFilter) {
            groupFilter.innerHTML = '<option value="">Semua Tarif</option>';
            customerGroups.forEach(group => {
                // Menggunakan CODE group sebagai value untuk filter API
                groupFilter.innerHTML += `<option value="${group.code}">${group.name} (${group.code})</option>`; 
            });
        }
        
        // Console log data yang diambil (untuk debugging jika masih kosong)
        if (customerGroups.length === 0) {
            console.warn('loadCustomerGroups: Data golongan tarif kosong atau tidak valid.', response.data);
        }

    } catch (error) {
        console.error('Error loading customer groups:', error);
        handleApiError(error, 'Gagal memuat data Grup Pelanggan!');
    }
}

async function loadCustomerStats() {
    const statIds = ['total-customers', 'active-customers', 'total-meters', 'inactive-meters'];
    statIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = '...';
    });
    
    try {
        const response = await axios.get('/api/customers/stats', {
            headers: getAuthHeaders()
        });
        const stats = response.data.data;
        
        const totalEl = document.getElementById('total-customers');
        const activeEl = document.getElementById('active-customers');
        const totalMetersEl = document.getElementById('total-meters');
        const inactiveMetersEl = document.getElementById('inactive-meters');
        
        if (totalEl) totalEl.textContent = stats.total_customers;
        if (activeEl) activeEl.textContent = stats.active_customers;
        if (totalMetersEl) totalMetersEl.textContent = stats.total_meters || 0;
        if (inactiveMetersEl) inactiveMetersEl.textContent = stats.inactive_meters || 0;


    } catch (error) {
        console.error('Error loading stats:', error);
        handleApiError(error, 'Gagal memuat statistik pelanggan!');
        statIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = '0';
        });
    }
}

async function searchCustomers(page = 1) {
    showLoading(true);
    
    const searchTerm = document.getElementById('searchInput')?.value || '';
    const groupFilter = document.getElementById('tariffFilter')?.value || '';
    const statusFilter = document.getElementById('statusFilter')?.value || '';
    
    try {
        const response = await axios.get('/api/customers', {
            headers: getAuthHeaders(),
            params: {
                page: page,
                per_page: perPage,
                sort_field: sortField,
                sort_direction: sortDirection,
                search: searchTerm,
                tariff_group: groupFilter, 
                status: statusFilter,
            }
        });
        
        const pagination = response.data;
        const data = pagination.data; 

        displayCustomers({ 
            data: data, 
            current_page: pagination.current_page, 
            per_page: pagination.per_page, 
            total: pagination.total, 
            last_page: pagination.last_page, 
            from: pagination.from, 
            to: pagination.to 
        });
        updatePagination(pagination); 
        
        currentPage = page;
    } catch (error) {
        console.error('Error loading customers:', error);
        handleApiError(error, 'Gagal memuat data pelanggan. Pastikan API berfungsi dan terotentikasi.');
    } finally {
        showLoading(false);
    }
}

function showAddCustomerModal() {
    isEditMode = false;
    currentCustomerId = null;
    
    const form = document.getElementById('customerForm');
    form.reset();
    clearFormErrors();

    document.getElementById('customerModalTitle').innerHTML = '<i class="fas fa-user-plus me-2"></i>Tambah Pelanggan Baru';
    document.getElementById('saveButtonText').textContent = 'Simpan';
    
    document.getElementById('customerPassword').required = true;
    
    const modal = new bootstrap.Modal(document.getElementById('customerModal'));
    modal.show();
}

async function editCustomer(customerId) {
    currentCustomerId = customerId;
    isEditMode = true;

    document.getElementById('customerModalTitle').innerHTML = '<i class="fas fa-user-edit me-2"></i>Edit Data Pelanggan';
    document.getElementById('saveButtonText').textContent = 'Update';
    document.getElementById('customerPassword').required = false;

    showLoading(true);
    
    try {
        const response = await axios.get(`/api/customers/${customerId}`, {
            headers: getAuthHeaders()
        });
        const customer = response.data.data;
        
        if (!customer) {
            showAlert('Pelanggan tidak ditemukan!', 'danger');
            return;
        }
        
        openCustomerModal('edit', customer);
        
    } catch (error) {
        console.error('Error fetching customer data:', error);
        handleApiError(error, 'Error mengambil data pelanggan!');
    } finally {
        showLoading(false);
    }
}

function setupFormSubmission() {
    document.getElementById('customerForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('saveCustomerBtn');
        const text = document.getElementById('saveButtonText');
        const spinner = document.getElementById('saveCustomerSpinner');
        
        btn.disabled = true;
        text.style.display = 'none';
        spinner.style.display = 'inline-block';
        clearFormErrors();
        
        const selectedGroupId = document.getElementById('customerGroup').value;
        const customerGroupCode = customerGroups.find(g => g.id == selectedGroupId)?.code;

        const isEdit = currentCustomerId !== null;
        let formData;

        if (isEdit) {
            formData = {
                name: document.getElementById('customerName').value,
                address: document.getElementById('customerAddress').value,
                phone: document.getElementById('customerPhone').value,
                email: document.getElementById('customerEmail').value,
                
                ktp_number: document.getElementById('customerKtp')?.value || '1111111111111111', 
                tariff_group: customerGroupCode || 'R1', 
                
                meter_number: document.getElementById('meterId')?.value, 
                meter_type: document.getElementById('meterType')?.value || 'analog', 
                installation_date: document.getElementById('installationDate')?.value || new Date().toISOString().slice(0, 10),
                is_active: true, 
                password: document.getElementById('customerPassword')?.value || undefined,
            };
            if(formData.password === undefined || formData.password === '') delete formData.password;

        } else {
             formData = {
                name: document.getElementById('customerName').value,
                email: document.getElementById('customerEmail').value,
                phone: document.getElementById('customerPhone').value,
                password: document.getElementById('customerPassword').value,
                
                ktp_number: document.getElementById('customerKtp').value, 
                address: document.getElementById('customerAddress').value,
                
                meters: [{
                    meter_number: document.getElementById('meterId').value,
                    meter_type: document.getElementById('meterType').value, 
                    customer_group_code: customerGroupCode, 
                    meter_size: document.getElementById('meterSize').value,
                    installation_date: document.getElementById('installationDate').value,
                }]
            };
        }

        const method = isEdit ? 'PUT' : 'POST';
        const url = isEdit ? `/api/customers/${currentCustomerId}` : '/api/customers';
        
        try {
            const response = await axios({ method: method, url: url, data: formData, headers: getAuthHeaders() });
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('customerModal'));
            modal.hide();
            
            showAlert(isEdit ? 'Pelanggan berhasil diperbarui!' : 'Pelanggan baru berhasil ditambahkan!', 'success');
            searchCustomers(currentPage);
            loadCustomerStats();
            
        } catch (error) {
            handleApiError(error, 'Gagal menyimpan data pelanggan');
        } finally {
            btn.disabled = false;
            text.style.display = 'inline';
            spinner.style.display = 'none';
        }
    });
}

async function confirmDelete() {
    const btn = document.getElementById('confirmDeleteBtn');
    const text = document.getElementById('deleteText');
    const spinner = document.getElementById('deleteSpinner');
    
    try {
        btn.disabled = true;
        text.style.display = 'none';
        spinner.style.display = 'inline-block';
        
        await axios.delete(`/api/customers/${deleteCustomerId}`, {
            headers: getAuthHeaders()
        });
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();
        
        showAlert('Pelanggan berhasil dihapus!', 'success');
        searchCustomers(currentPage);
        loadCustomerStats();
        
    } catch (error) {
        console.error('Error deleting customer:', error);
        handleApiError(error, 'Error menghapus pelanggan!');
    } finally {
        btn.disabled = false;
        text.style.display = 'inline';
        spinner.style.display = 'none';
    }
}
// ===================================
// END API INTEGRATION & ADD CUSTOMER FEATURE
// ===================================


function displayCustomers(data) {
    const tbody = document.getElementById('customersTableBody');
    
    if (!data.data || data.data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5">
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
        const rowNumber = ((currentPage - 1) * perPage) + index + 1;
        
        const tariffGroupCode = customer.meters?.[0]?.customer_group_code || customer.tariff_group;
        const groupName = customerGroups.find(g => g.code === tariffGroupCode)?.name || tariffGroupCode || 'N/A';
        
        const meterNumber = customer.meters?.[0]?.meter_number || 'N/A';

        return `
            <tr class="hover-shadow">
                {{-- 1. No. Pelanggan + Status --}}
                <td>
                    <div class="d-flex align-items-center">
                        <small class="text-muted me-2">${rowNumber}.</small>
                        <strong class="text-primary me-2">${customer.customer_number}</strong>
                        <span class="badge ${customer.user?.is_active ? 'bg-success' : 'bg-danger'}">
                            ${customer.user?.is_active ? 'Aktif' : 'Non-Aktif'}
                        </span>
                    </div>
                </td>
                {{-- 2. Nama Pelanggan --}}
                <td class="d-none d-md-table-cell">
                    <div class="fw-bold">${customer.user?.name || customer.name}</div>
                    <small class="text-muted">${customer.user?.phone || 'N/A'}</small>
                </td>
                {{-- 3. Alamat --}}
                <td class="d-none d-lg-table-cell">
                    <div class="text-truncate" style="max-width: 250px;">
                        ${customer.address}
                    </div>
                </td>
                {{-- 4. No. KTP --}}
                <td class="d-none d-xl-table-cell">
                    <small class="font-monospace">${customer.ktp_number || 'N/A'}</small>
                </td>
                {{-- 5. Tarif --}}
                <td class="d-none d-md-table-cell text-center">
                    <span class="badge bg-secondary">${groupName}</span>
                </td>
                {{-- 6. Meter --}}
                <td class="d-none d-lg-table-cell text-center">${meterNumber}</td>
                {{-- 7. Terdaftar --}}
                <td class="d-none d-lg-table-cell text-center">
                    <small class="text-muted">${formatDate(customer.created_at)}</small>
                </td>
                {{-- 8. Aksi --}}
                <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-warning btn-sm" onclick="editCustomer(${customer.id})" 
                                title="Edit Pelanggan" data-bs-toggle="tooltip">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="viewMeterDetails(${customer.id})" 
                                title="Detail Meter" data-bs-toggle="tooltip">
                            <i class="fas fa-water"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteCustomer(${customer.id}, '${customer.user?.name || customer.name}')" 
                                title="Hapus Pelanggan" data-bs-toggle="tooltip">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function openCustomerModal(action, customerData = null) {
    const modal = new bootstrap.Modal(document.getElementById('customerModal'));
    const form = document.getElementById('customerForm');
    const title = document.getElementById('customerModalTitle');
    
    form.reset();
    clearFormErrors();
    isEditMode = action === 'edit';
    currentCustomerId = customerData ? customerData.id : null;

    title.innerHTML = action === 'edit' 
        ? '<i class="fas fa-user-edit me-2"></i>Edit Pelanggan' 
        : '<i class="fas fa-user-plus me-2"></i>Tambah Pelanggan Baru';
    
    
    document.getElementById('customerNumber').value = '';
    document.getElementById('customerKtp').required = !isEdit;
    document.getElementById('customerPassword').required = !isEdit;

    if (action === 'edit' && customerData) {
        document.getElementById('customerNumber').value = customerData.customer_number;
        document.getElementById('customerAddress').value = customerData.address;
        document.getElementById('customerKtp').value = customerData.ktp_number;

        document.getElementById('customerName').value = customerData.user?.name || '';
        document.getElementById('customerPhone').value = customerData.user?.phone || '';
        document.getElementById('customerEmail').value = customerData.user?.email || '';
        
        const meter = customerData.meters?.[0];
        if (meter) {
             document.getElementById('meterId').value = meter.meter_number || '';
             document.getElementById('meterType').value = meter.meter_type || '';
             // Mencari ID group (yang merupakan value dari dropdown) berdasarkan code group meter
             document.getElementById('customerGroup').value = customerGroups.find(g => g.code === meter.customer_group_code)?.id || ''; 
        } else {
             document.getElementById('meterId').value = '';
             document.getElementById('meterType').value = '';
             document.getElementById('customerGroup').value = '';
        }

    } 
    
    modal.show();
}


function viewMeterDetails(customerId) {
    showAlert(`Menampilkan detail meter untuk Pelanggan ID: ${customerId}`, 'info');
}

function getBillStatusBadge(status) {
    switch(status?.toUpperCase()) {
        case 'UNPAID':
        case 'BELUM LUNAS':
            return 'bg-warning text-dark';
        case 'PENDING':
            return 'bg-info text-dark';
        case 'PAID':
        case 'LUNAS':
            return 'bg-success';
        default:
            return 'bg-secondary';
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

function resetFilters() {
    const searchInput = document.getElementById('searchInput');
    const groupFilter = document.getElementById('tariffFilter');
    const statusFilter = document.getElementById('statusFilter');

    if (searchInput) searchInput.value = '';
    if (groupFilter) groupFilter.value = '';
    if (statusFilter) statusFilter.value = '';
    
    searchCustomers(1);
}

function clearFormErrors() {
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
}

function handleApiError(error, defaultMessage) {
    if (error?.response?.status === 401) {
        showAlert('Sesi Anda telah berakhir atau tidak sah. Mohon login ulang.', 'warning');
        console.error('Unauthorized Access (401):', error.response.data);
    } else if (error?.response?.data?.errors) {
        Object.keys(error.response.data.errors).forEach(field => {
            const formFieldId = {
                'name': 'customerName',
                'address': 'customerAddress',
                'phone': 'customerPhone',
                'email': 'customerEmail',
                'ktp_number': 'customerKtp',
                'meter_number': 'meterId', 
                'meter_type': 'meterType',
                'tariff_group': 'customerGroup', 
                'customer_group_code': 'customerGroup', 
                'meters.0.meter_number': 'meterId', 
                'meters.0.meter_type': 'meterType',
                'meters.0.customer_group_code': 'customerGroup',
            }[field] || field;

            const element = document.getElementById(formFieldId);
            const message = error.response.data.errors[field][0];
            
            if (element) {
                element.classList.add('is-invalid');
                const feedback = document.getElementById(field + 'Error');
                if (feedback) {
                    feedback.textContent = message;
                }
            } else {
                showAlert(`Validasi gagal: ${message} (${field})`, 'warning');
            }
        });
    } else {
        const message = error?.response?.data?.message || error.message || defaultMessage;
        showAlert(message, 'danger');
    }
}

function changePerPage() {
    const select = document.getElementById('perPageSelect');
    if (select) {
        perPage = parseInt(select.value);
        searchCustomers(1);
    }
}

function updatePagination(pagination) {
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationList = document.getElementById('paginationList');
    
    if (paginationInfo) paginationInfo.textContent = `Menampilkan ${pagination.from || 0} - ${pagination.to || 0} dari ${pagination.total || 0} data`;
    
    if (!paginationList) return;

    if (!pagination.last_page || pagination.last_page <= 1) {
        paginationList.innerHTML = '';
        return;
    }
    
    let paginationHtml = '';
    
    if (pagination.current_page > 1) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="searchCustomers(${pagination.current_page - 1})">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
    }
    
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        paginationHtml += `
            <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="searchCustomers(${i})">${i}</a>
            </li>
        `;
    }
    
    if (pagination.current_page < pagination.last_page) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="searchCustomers(${pagination.current_page + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
    }
    
    paginationList.innerHTML = paginationHtml;
}

function sortTable(field) {
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
</script>

@endsection
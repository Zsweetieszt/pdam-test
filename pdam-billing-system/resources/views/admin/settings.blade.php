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
                            <i class="fas fa-cogs text-primary me-2"></i>
                            Pengaturan Sistem & Template Management
                        </h4>
                        <p class="text-muted mb-0">Kelola template notifikasi WhatsApp dan konfigurasi sistem</p>
                    </div>
                    <div class="col-md-4 col-sm-6 text-end">
                        <button class="btn btn-primary" onclick="refreshTemplates()">
                            <i class="fas fa-sync me-2"></i>
                            <span class="d-none d-sm-inline">Refresh </span>Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Tabs -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <ul class="nav nav-tabs card-header-tabs" id="settingsTabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="templates-tab" data-bs-toggle="tab" href="#templates" role="tab">
                            <i class="fas fa-file-alt me-2"></i>
                            Template WhatsApp
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="system-tab" data-bs-toggle="tab" href="#system" role="tab">
                            <i class="fas fa-server me-2"></i>
                            System Config
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tariff-tab" data-bs-toggle="tab" href="#tariff" role="tab">
                            <i class="fas fa-calculator me-2"></i>
                            Tarif Air
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="card-body">
                <div class="tab-content" id="settingsTabContent">
                    
                    <!-- REQ-F-8: Template Management Tab -->
                    <div class="tab-pane fade show active" id="templates" role="tabpanel">
                        <!-- Template Actions -->
                        <div class="row align-items-center mb-4">
                            <div class="col-md-8 col-sm-6">
                                <h6 class="mb-1">Manajemen Template WhatsApp</h6>
                                <p class="text-muted mb-0">Template notifikasi untuk tagihan, reminder, dan konfirmasi pembayaran</p>
                            </div>
                            <div class="col-md-4 col-sm-6 text-end">
                                <button class="btn btn-primary" onclick="openTemplateModal()">
                                    <i class="fas fa-plus me-2"></i>
                                    <span class="d-none d-sm-inline">Tambah </span>Template
                                </button>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="row mb-4">
                            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                <div class="card bg-primary text-white border-0 h-100">
                                    <div class="card-body text-center py-3">
                                        <h4 id="total-templates" class="mb-1">-</h4>
                                        <small>Total Template</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                <div class="card bg-success text-white border-0 h-100">
                                    <div class="card-body text-center py-3">
                                        <h4 id="active-templates" class="mb-1">-</h4>
                                        <small>Template Aktif</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3 mb-md-0">
                                <div class="card bg-info text-white border-0 h-100">
                                    <div class="card-body text-center py-3">
                                        <h4 id="bill-templates" class="mb-1">-</h4>
                                        <small>Template Tagihan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card bg-warning text-dark border-0 h-100">
                                    <div class="card-body text-center py-3">
                                        <h4 id="payment-templates" class="mb-1">-</h4>
                                        <small>Template Pembayaran</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search and Filter Section -->
                        <div class="row mb-4">
                            <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Cari nama template atau isi...">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3 mb-lg-0">
                                <select class="form-select" id="templateTypeFilter">
                                    <option value="">Semua Tipe</option>
                                    <option value="bill_reminder">Reminder Tagihan</option>
                                    <option value="overdue_notice">Pemberitahuan Tunggakan</option>
                                    <option value="payment_confirmation">Konfirmasi Pembayaran</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3 mb-lg-0">
                                <select class="form-select" id="statusFilter">
                                    <option value="">Semua Status</option>
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3 mb-md-0">
                                <button class="btn btn-primary w-100" onclick="searchTemplates()">
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

                        <!-- Loading State -->
                        <div id="loadingState" class="text-center py-5 d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Memuat data template...</p>
                        </div>

                        <!-- REQ-F-8.1: Templates Table - REQ-F-10 Enhanced -->
                        <div class="table-responsive" id="templatesTableContainer">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th onclick="sortTable('template_name')" class="sortable-header" style="min-width: 200px;">
                                            <div class="d-flex align-items-center">
                                                <span>Template</span>
                                                <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="template_name"></i>
                                            </div>
                                        </th>
                                        <th onclick="sortTable('template_type')" class="sortable-header d-none d-md-table-cell text-center" style="min-width: 140px;">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span>Tipe</span>
                                                <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="template_type"></i>
                                            </div>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Status</th>
                                        <th class="d-none d-lg-table-cell text-center" style="min-width: 100px;">Variabel</th>
                                        <th onclick="sortTable('created_at')" class="sortable-header d-none d-lg-table-cell text-center" style="min-width: 120px;">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span>Dibuat</span>
                                                <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="created_at"></i>
                                            </div>
                                        </th>
                                        <th class="text-center" style="min-width: 160px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="templatesTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                                <h6 class="text-muted mb-2">Belum ada data template</h6>
                                                <p class="text-muted small mb-0">Klik tombol "Cari" untuk memuat data template</p>
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

                    <!-- System Config Tab -->
                    <div class="tab-pane fade" id="system" role="tabpanel">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Konfigurasi Sistem
                            </h6>
                            <p class="mb-0">Pengaturan sistem akan diimplementasi pada fase selanjutnya.</p>
                        </div>
                    </div>

                    <!-- Tariff Config Tab -->
                    <div class="tab-pane fade" id="tariff" role="tabpanel">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Pengaturan Tarif Air
                            </h6>
                            <p class="mb-0">Manajemen tarif air akan diimplementasi pada fase selanjutnya.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REQ-F-8.1 & REQ-F-8.2: Template Modal (Create/Edit) -->
<div class="modal fade" id="templateModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt me-2"></i>
                    <span id="modal-title">Tambah Template</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="templateForm">
                    <input type="hidden" id="template_id" name="template_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Template *</label>
                            <input type="text" class="form-control" id="template_name" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tipe Template *</label>
                            <select class="form-select" id="template_type" name="type" required>
                                <option value="">Pilih tipe template...</option>
                                <option value="bill_reminder">Reminder Tagihan</option>
                                <option value="overdue_notice">Pemberitahuan Tunggakan</option>
                                <option value="payment_confirmation">Konfirmasi Pembayaran</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label fw-bold" for="is_active">
                                    Template Aktif
                                </label>
                            </div>
                            <small class="text-muted">Template aktif dapat digunakan untuk mengirim notifikasi</small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- REQ-F-8.2: Template Editor -->
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Isi Template *</label>
                            <textarea class="form-control" id="template_content" name="content" rows="12" 
                                      placeholder="Ketik template pesan WhatsApp di sini..." required></textarea>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Gunakan variabel dalam kurung kurawal, contoh: {customer_name}, {amount}
                            </small>
                            <div class="invalid-feedback"></div>
                            
                            <!-- Character Counter -->
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">Karakter: <span id="char-count">0</span>/1000</small>
                                <small class="text-muted">WhatsApp limit: ~4096 karakter</small>
                            </div>
                        </div>

                        <!-- REQ-F-8.2: Available Variables -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Variabel Tersedia</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div id="available-variables">
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Pilih tipe template untuk melihat variabel yang tersedia
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Insert Buttons -->
                            <div class="mt-3">
                                <label class="form-label fw-bold">Quick Insert</label>
                                <div class="d-grid gap-1" id="quick-insert-buttons">
                                    <!-- Buttons will be generated based on template type -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- REQ-F-8.3: Preview Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-eye me-2"></i>
                                        Preview Template dengan Data Sample
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Template Original:</h6>
                                            <div class="bg-light p-3 rounded" style="white-space: pre-wrap;" id="original-preview">
                                                Template akan ditampilkan di sini...
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>
                                                Hasil dengan Data Sample:
                                                <button type="button" class="btn btn-sm btn-outline-success ms-2" onclick="refreshPreview()">
                                                    <i class="fas fa-sync me-1"></i>
                                                    Refresh
                                                </button>
                                            </h6>
                                            <div class="bg-success bg-opacity-10 p-3 rounded border border-success" style="white-space: pre-wrap;" id="processed-preview">
                                                Preview akan ditampilkan di sini...
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Sample Data Display -->
                                    <div class="mt-3">
                                        <h6>Data Sample yang Digunakan:</h6>
                                        <div class="row" id="sample-data-display">
                                            <!-- Sample data will be displayed here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Batal
                </button>
                <button type="button" class="btn btn-success" onclick="testTemplate()">
                    <i class="fas fa-play me-1"></i>
                    Test Template
                </button>
                <button type="button" class="btn btn-primary" id="saveTemplateBtn" onclick="saveTemplate()">
                    <i class="fas fa-save me-1"></i>
                    Simpan Template
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Template Detail Modal -->
<div class="modal fade" id="templateDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Detail Template
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="templateDetailContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat detail template...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Tutup
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

    .variable-badge {
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 2px;
        font-size: 0.75rem;
    }

    .variable-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .template-content {
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    #template_content {
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .char-limit-warning {
        color: #ff6b6b !important;
    }

    .char-limit-danger {
        color: #dc3545 !important;
        font-weight: bold;
    }

    .quick-insert-btn {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
    }

    .sample-data-item {
        background: #f8f9fa;
        border-radius: 4px;
        padding: 0.5rem;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
    }

    .template-preview {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
        border-left: 4px solid #4caf50;
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
        .modal-xl {
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
let templatesData = [];
let currentTemplate = {};
let availableVariables = {};
let currentPage = 1;
let perPage = 15;
let sortField = 'created_at';
let sortDirection = 'desc';
let currentFilters = {};
let isEditMode = false;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    setupSearchHandlers();
    loadAvailableVariables();
    loadTemplateStats();
    loadDummyTemplates();
    setupEventListeners();
});

// Enhanced search handlers - REQ-F-10.2
function setupSearchHandlers() {
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('templateTypeFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    // Real-time search with debouncing
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const value = this.value.trim();
        
        if (value.length === 0 || value.length >= 2) {
            searchTimeout = setTimeout(() => {
                searchTemplates(1);
            }, 300);
        }
    });
    
    // Filter change handlers
    [typeFilter, statusFilter].forEach(filter => {
        filter.addEventListener('change', () => {
            searchTemplates(1);
        });
    });
    
    // Enter key search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimeout);
            searchTemplates(1);
        }
    });
}

// Setup additional event listeners
function setupEventListeners() {
    // Template type change
    document.getElementById('template_type').addEventListener('change', function() {
        updateAvailableVariables(this.value);
        updatePreview();
    });
    
    // Content change
    document.getElementById('template_content').addEventListener('input', function() {
        updateCharCount();
        updatePreview();
    });
}

// Load template statistics
async function loadTemplateStats() {
    // Set loading state
    ['total-templates', 'active-templates', 'bill-templates', 'payment-templates'].forEach(id => {
        document.getElementById(id).textContent = '...';
    });
    
    try {
        // Simulate delay
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Calculate stats from mock data
        const totalTemplates = templatesData.length;
        const activeTemplates = templatesData.filter(t => t.is_active).length;
        const billTemplates = templatesData.filter(t => t.template_type === 'bill_reminder').length;
        const paymentTemplates = templatesData.filter(t => t.template_type === 'payment_confirmation').length;
        
        document.getElementById('total-templates').textContent = totalTemplates;
        document.getElementById('active-templates').textContent = activeTemplates;
        document.getElementById('bill-templates').textContent = billTemplates;
        document.getElementById('payment-templates').textContent = paymentTemplates;
    } catch (error) {
        console.error('Error loading stats:', error);
        // Set default values
        ['total-templates', 'active-templates', 'bill-templates', 'payment-templates'].forEach(id => {
            document.getElementById(id).textContent = '0';
        });
    }
}

// Enhanced search function - REQ-F-10.1 & REQ-F-10.2
async function searchTemplates(page = 1) {
    showLoading(true);
    
    const searchTerm = document.getElementById('searchInput').value;
    const typeFilter = document.getElementById('templateTypeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    currentFilters = {
        search: searchTerm,
        type: typeFilter,
        status: statusFilter
    };
    
    try {
        // Simulate API delay
        await new Promise(resolve => setTimeout(resolve, 800));
        
        // Use mock data with enhanced filtering and sorting
        const mockData = generateMockTemplates(page, perPage, searchTerm, typeFilter, statusFilter);
        
        displayTemplates(mockData);
        updatePagination(mockData);
        currentPage = page;
    } catch (error) {
        console.error('Error loading templates:', error);
        handleApiError(error, 'Gagal memuat data template');
    } finally {
        showLoading(false);
    }
}

// Load dummy templates data
function loadDummyTemplates() {
    // Dummy templates data
    templatesData = [
        {
            id: 1,
            template_name: 'Tagihan Bulanan Standard',
            template_type: 'bill_reminder',
            subject: null,
            message_content: `Halo {customer_name},

Tagihan air PDAM bulan {period} sudah tersedia:

🧾 No. Tagihan: {bill_number}
💧 Pemakaian: {usage} m³
💰 Total: Rp {amount}
📅 Jatuh Tempo: {due_date}

Silakan lakukan pembayaran sebelum tanggal jatuh tempo.

Terima kasih,
PDAM Billing System`,
            variables: ['customer_name', 'period', 'bill_number', 'usage', 'amount', 'due_date'],
            is_active: true,
            created_at: '2025-08-15 10:30:00'
        },
        {
            id: 2,
            template_name: 'Reminder Tunggakan Urgent',
            template_type: 'overdue_notice',
            subject: null,
            message_content: `⚠️ PERINGATAN TUNGGAKAN ⚠️

Halo {customer_name},

Tagihan air PDAM Anda telah melewati jatuh tempo:

🧾 No. Tagihan: {bill_number}
💰 Jumlah: Rp {amount}
📅 Jatuh Tempo: {original_due_date}
⏰ Terlambat: {overdue_days} hari

Harap segera lakukan pembayaran untuk menghindari pemutusan layanan.

Info: 021-XXXXXX
PDAM Billing System`,
            variables: ['customer_name', 'bill_number', 'amount', 'original_due_date', 'overdue_days'],
            is_active: true,
            created_at: '2025-08-14 14:15:00'
        },
        {
            id: 3,
            template_name: 'Konfirmasi Pembayaran Berhasil',
            template_type: 'payment_confirmation',
            subject: null,
            message_content: `✅ PEMBAYARAN BERHASIL

Terima kasih {customer_name},

Pembayaran tagihan air PDAM Anda telah kami terima:

🧾 No. Tagihan: {bill_number}
💰 Jumlah: Rp {payment_amount}
💳 Metode: {payment_method}
📅 Tanggal: {payment_date}
🔢 No. Ref: {payment_number}

Pembayaran akan diproses dalam 1x24 jam.

Terima kasih atas kepercayaan Anda,
PDAM Billing System`,
            variables: ['customer_name', 'bill_number', 'payment_amount', 'payment_method', 'payment_date', 'payment_number'],
            is_active: true,
            created_at: '2025-08-13 09:45:00'
        },
        {
            id: 4,
            template_name: 'Reminder Jatuh Tempo H-3',
            template_type: 'bill_reminder',
            subject: null,
            message_content: `⏰ REMINDER: 3 Hari Menuju Jatuh Tempo

Halo {customer_name},

Tagihan air PDAM Anda akan jatuh tempo dalam 3 hari:

🧾 No. Tagihan: {bill_number}
💰 Total: Rp {amount}
📅 Jatuh Tempo: {due_date}
🏠 Meter: {meter_number}

Jangan lupa untuk melakukan pembayaran tepat waktu.

PDAM Billing System`,
            variables: ['customer_name', 'bill_number', 'amount', 'due_date', 'meter_number'],
            is_active: false,
            created_at: '2025-08-12 16:20:00'
        },
        {
            id: 5,
            template_name: 'Selamat Datang Customer Baru',
            template_type: 'bill_reminder',
            subject: null,
            message_content: `🎉 Selamat Datang di PDAM!

Halo {customer_name},

Selamat! Pendaftaran Anda sebagai pelanggan PDAM telah berhasil.

🏠 No. Pelanggan: {customer_number}
📍 Alamat: Sesuai data registrasi
💧 No. Meter: {meter_number}

Tagihan pertama akan dikirim pada awal bulan depan.

Terima kasih telah mempercayai layanan kami,
PDAM Billing System`,
            variables: ['customer_name', 'customer_number', 'meter_number'],
            is_active: true,
            created_at: '2025-08-10 11:05:00'
        }
    ];
    
    loadTemplateStats();
    searchTemplates();
}

// Enhanced mock data generator with sorting support - REQ-F-10.3
function generateMockTemplates(page, perPage, search, typeFilter, statusFilter) {
    // Apply filters first
    let filteredTemplates = templatesData;
    
    if (search) {
        const searchLower = search.toLowerCase();
        filteredTemplates = filteredTemplates.filter(template => 
            template.template_name.toLowerCase().includes(searchLower) ||
            template.message_content.toLowerCase().includes(searchLower)
        );
    }
    
    if (typeFilter) {
        filteredTemplates = filteredTemplates.filter(template => template.template_type === typeFilter);
    }
    
    if (statusFilter !== '') {
        const isActive = statusFilter === '1';
        filteredTemplates = filteredTemplates.filter(template => template.is_active === isActive);
    }
    
    // Apply sorting - REQ-F-10.3
    filteredTemplates.sort((a, b) => {
        let valueA, valueB;
        
        switch(sortField) {
            case 'template_name':
                valueA = a.template_name || '';
                valueB = b.template_name || '';
                break;
            case 'template_type':
                valueA = a.template_type || '';
                valueB = b.template_type || '';
                break;
            case 'created_at':
                valueA = new Date(a.created_at);
                valueB = new Date(b.created_at);
                break;
            default:
                valueA = a.id;
                valueB = b.id;
        }
        
        // Handle different data types
        if (valueA instanceof Date && valueB instanceof Date) {
            return sortDirection === 'asc' 
                ? valueA.getTime() - valueB.getTime()
                : valueB.getTime() - valueA.getTime();
        } else {
            const comparison = valueA.toString().localeCompare(valueB.toString(), 'id-ID');
            return sortDirection === 'asc' ? comparison : -comparison;
        }
    });
    
    // Apply pagination - REQ-F-10.4
    const startIndex = (page - 1) * parseInt(perPage);
    const endIndex = startIndex + parseInt(perPage);
    const paginatedTemplates = filteredTemplates.slice(startIndex, endIndex);
    
    return {
        data: paginatedTemplates,
        current_page: parseInt(page),
        per_page: parseInt(perPage),
        total: filteredTemplates.length,
        last_page: Math.ceil(filteredTemplates.length / parseInt(perPage)),
        from: filteredTemplates.length === 0 ? 0 : startIndex + 1,
        to: Math.min(endIndex, filteredTemplates.length)
    };
}

// Enhanced display templates function - REQ-F-10.1
function displayTemplates(data) {
    const tbody = document.getElementById('templatesTableBody');
    
    if (!data.data || data.data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-search fa-4x text-muted mb-3 opacity-50"></i>
                        <h6 class="text-muted mb-2">Tidak ada data template</h6>
                        <p class="text-muted small mb-0">Coba ubah kriteria pencarian atau tambah template baru</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = data.data.map((template, index) => {
        const typeText = getTemplateTypeText(template.template_type);
        const statusBadge = template.is_active ? 
            '<span class="badge bg-success">Aktif</span>' : 
            '<span class="badge bg-secondary">Tidak Aktif</span>';
        
        const variablesCount = template.variables ? template.variables.length : 0;
        const rowNumber = ((currentPage - 1) * perPage) + index + 1;
        
        return `
            <tr class="hover-shadow">
                <td>
                    <div class="d-flex align-items-start">
                        <span class="badge bg-light text-dark me-2 mt-1">${rowNumber}</span>
                        <div>
                            <div class="fw-bold text-dark">${template.template_name}</div>
                            <small class="text-muted">
                                <i class="fas fa-file-alt fa-xs me-1"></i>
                                ${template.message_content.substring(0, 60)}...
                            </small>
                        </div>
                    </div>
                </td>
                <td class="d-none d-md-table-cell text-center">
                    <span class="badge ${getTypeBadgeClass(template.template_type)}">${typeText}</span>
                </td>
                <td class="text-center">${statusBadge}</td>
                <td class="d-none d-lg-table-cell text-center">
                    <span class="badge bg-info">${variablesCount} variabel</span>
                </td>
                <td class="d-none d-lg-table-cell text-center">
                    <small class="text-muted">${formatDate(template.created_at)}</small>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-info btn-sm" onclick="viewTemplate(${template.id})" 
                                title="Detail Template" data-bs-toggle="tooltip">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="editTemplate(${template.id})" 
                                title="Edit Template" data-bs-toggle="tooltip">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-success btn-sm" onclick="duplicateTemplate(${template.id})" 
                                title="Duplikat" data-bs-toggle="tooltip">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteTemplate(${template.id})" 
                                title="Hapus Template" data-bs-toggle="tooltip">
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

// Helper functions
function getTypeBadgeClass(type) {
    switch(type) {
        case 'bill_reminder': return 'bg-primary';
        case 'overdue_notice': return 'bg-danger';
        case 'payment_confirmation': return 'bg-success';
        default: return 'bg-secondary';
    }
}

function getTemplateTypeText(type) {
    const types = {
        'bill_reminder': 'Reminder Tagihan',
        'overdue_notice': 'Pemberitahuan Tunggakan',
        'payment_confirmation': 'Konfirmasi Pembayaran'
    };
    return types[type] || type;
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
    searchTemplates(1);
    
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
    searchTemplates(1);
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
                <a class="page-link" href="#" onclick="searchTemplates(${data.current_page - 1})">
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
                <a class="page-link" href="#" onclick="searchTemplates(${i})">${i}</a>
            </li>
        `;
    }
    
    // Next button
    if (data.current_page < data.last_page) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="searchTemplates(${data.current_page + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
    }
    
    paginationList.innerHTML = paginationHtml;
}

// Enhanced loading states
function showTableLoading(show) {
    const tbody = document.getElementById('templatesTableBody');
    
    if (show) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
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

function showLoading(show) {
    const loadingState = document.getElementById('loadingState');
    const tableContainer = document.getElementById('templatesTableContainer');
    
    if (show) {
        loadingState.classList.remove('d-none');
        tableContainer.classList.add('d-none');
    } else {
        loadingState.classList.add('d-none');
        tableContainer.classList.remove('d-none');
    }
}

// Utility functions
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('templateTypeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    searchTemplates(1);
}

function refreshTemplates() {
    currentFilters = {};
    currentPage = 1;
    document.getElementById('searchInput').value = '';
    document.getElementById('templateTypeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    
    showToast('Data template berhasil direfresh', 'info');
    searchTemplates();
    loadTemplateStats();
}

function handleApiError(error, defaultMessage) {
    if (error.response?.data?.errors) {
        // Handle validation errors
        Object.keys(error.response.data.errors).forEach(field => {
            const element = document.querySelector(`[name="${field}"], #${field}`);
            if (element && error.response.data.errors[field][0]) {
                showFieldError(element, error.response.data.errors[field][0]);
            }
        });
    } else {
        // Handle general errors
        const message = error.response?.data?.message || error.message || defaultMessage;
        showToast(message, 'error');
    }
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

// Keep all existing template-specific functions here...
// (loadAvailableVariables, openTemplateModal, updateAvailableVariables, etc.)

// Load available variables
function loadAvailableVariables() {
    availableVariables = {
        'bill_reminder': {
            'customer_name': 'Nama pelanggan',
            'bill_number': 'Nomor tagihan',
            'amount': 'Jumlah tagihan',
            'due_date': 'Tanggal jatuh tempo',
            'meter_number': 'Nomor meter',
            'period': 'Periode tagihan',
            'current_reading': 'Angka meter saat ini',
            'previous_reading': 'Angka meter sebelumnya',
            'usage': 'Pemakaian (m³)',
            'customer_number': 'Nomor pelanggan'
        },
        'overdue_notice': {
            'customer_name': 'Nama pelanggan',
            'bill_number': 'Nomor tagihan',
            'amount': 'Jumlah tagihan',
            'overdue_days': 'Hari terlambat',
            'meter_number': 'Nomor meter',
            'original_due_date': 'Tanggal jatuh tempo asli'
        },
        'payment_confirmation': {
            'customer_name': 'Nama pelanggan',
            'bill_number': 'Nomor tagihan',
            'payment_amount': 'Jumlah pembayaran',
            'payment_date': 'Tanggal pembayaran',
            'payment_method': 'Metode pembayaran',
            'payment_number': 'Nomor pembayaran'
        }
    };
}

// REQ-F-8.1: Open template modal
function openTemplateModal(template = null) {
    const modal = new bootstrap.Modal(document.getElementById('templateModal'));
    const form = document.getElementById('templateForm');
    
    // Reset form
    form.reset();
    clearFormErrors();
    isEditMode = template !== null;
    
    if (template) {
        // Edit mode
        document.getElementById('modal-title').textContent = 'Edit Template';
        document.getElementById('template_id').value = template.id;
        document.getElementById('template_name').value = template.template_name;
        document.getElementById('template_type').value = template.template_type;
        document.getElementById('template_content').value = template.message_content;
        document.getElementById('is_active').checked = template.is_active;
        
        currentTemplate = template;
        updateAvailableVariables(template.template_type);
    } else {
        // Create mode
        document.getElementById('modal-title').textContent = 'Tambah Template';
        currentTemplate = {};
        document.getElementById('available-variables').innerHTML = `
            <p class="text-muted small mb-2">
                <i class="fas fa-info-circle me-1"></i>
                Pilih tipe template untuk melihat variabel yang tersedia
            </p>
        `;
    }
    
    updateCharCount();
    updatePreview();
    modal.show();
}

// REQ-F-8.2: Update available variables based on template type
function updateAvailableVariables(templateType) {
    const variablesContainer = document.getElementById('available-variables');
    const quickInsertContainer = document.getElementById('quick-insert-buttons');
    
    if (!templateType || !availableVariables[templateType]) {
        variablesContainer.innerHTML = `
            <p class="text-muted small mb-2">
                <i class="fas fa-info-circle me-1"></i>
                Pilih tipe template untuk melihat variabel yang tersedia
            </p>
        `;
        quickInsertContainer.innerHTML = '';
        return;
    }
    
    const variables = availableVariables[templateType];
    
    // Display available variables
    let variablesHtml = '<h6 class="small fw-bold mb-2">Klik untuk menyalin:</h6>';
    Object.keys(variables).forEach(key => {
        variablesHtml += `
            <span class="badge bg-primary variable-badge" onclick="insertVariable('{${key}}')" title="${variables[key]}">
                {${key}}
            </span>
        `;
    });
    
    variablesHtml += '<hr class="my-2">';
    variablesHtml += '<h6 class="small fw-bold mb-2">Keterangan:</h6>';
    Object.keys(variables).forEach(key => {
        variablesHtml += `<div class="small text-muted mb-1"><code>{${key}}</code> = ${variables[key]}</div>`;
    });
    
    variablesContainer.innerHTML = variablesHtml;
    
    // Generate quick insert buttons
    let buttonsHtml = '';
    const commonVariables = ['customer_name', 'bill_number', 'amount'];
    commonVariables.forEach(key => {
        if (variables[key]) {
            buttonsHtml += `
                <button type="button" class="btn btn-sm btn-outline-primary quick-insert-btn" onclick="insertVariable('{${key}}')">
                    + {${key}}
                </button>
            `;
        }
    });
    
    quickInsertContainer.innerHTML = buttonsHtml;
}

// Insert variable into template content
function insertVariable(variable) {
    const contentTextarea = document.getElementById('template_content');
    const cursorPosition = contentTextarea.selectionStart;
    const textBefore = contentTextarea.value.substring(0, cursorPosition);
    const textAfter = contentTextarea.value.substring(contentTextarea.selectionEnd);
    
    contentTextarea.value = textBefore + variable + textAfter;
    contentTextarea.focus();
    contentTextarea.setSelectionRange(cursorPosition + variable.length, cursorPosition + variable.length);
    
    updateCharCount();
    updatePreview();
}

// Update character count
function updateCharCount() {
    const content = document.getElementById('template_content').value;
    const charCountElement = document.getElementById('char-count');
    const length = content.length;
    
    charCountElement.textContent = length;
    
    if (length > 800) {
        charCountElement.className = 'char-limit-danger';
    } else if (length > 600) {
        charCountElement.className = 'char-limit-warning';
    } else {
        charCountElement.className = '';
    }
}

// REQ-F-8.3: Update preview with sample data
function updatePreview() {
    const content = document.getElementById('template_content').value;
    const templateType = document.getElementById('template_type').value;
    const originalPreview = document.getElementById('original-preview');
    const processedPreview = document.getElementById('processed-preview');
    const sampleDataDisplay = document.getElementById('sample-data-display');
    
    // Show original template
    originalPreview.textContent = content || 'Template akan ditampilkan di sini...';
    
    if (!content || !templateType) {
        processedPreview.textContent = 'Preview akan ditampilkan di sini...';
        sampleDataDisplay.innerHTML = '';
        return;
    }
    
    // Generate sample data based on template type
    const sampleData = generateSampleData(templateType);
    
    // Process template with sample data
    let processedContent = content;
    Object.keys(sampleData).forEach(key => {
        const regex = new RegExp(`{${key}}`, 'g');
        processedContent = processedContent.replace(regex, sampleData[key]);
    });
    
    processedPreview.textContent = processedContent;
    
    // Display sample data
    let sampleDataHtml = '';
    Object.keys(sampleData).forEach(key => {
        sampleDataHtml += `
            <div class="col-md-4 col-sm-6 mb-2">
                <div class="sample-data-item">
                    <strong>{${key}}</strong><br>
                    <span class="text-primary">${sampleData[key]}</span>
                </div>
            </div>
        `;
    });
    
    sampleDataDisplay.innerHTML = sampleDataHtml;
}

// Generate sample data
function generateSampleData(templateType) {
    const baseSample = {
        customer_name: 'Ahmad Yani',
        bill_number: 'BILL-202508-001',
        amount: 'Rp 225.000',
        meter_number: 'MTR-001234',
        period: 'Agustus 2025',
        due_date: '25/08/2025',
        customer_number: 'CUST-001',
        current_reading: '125',
        previous_reading: '110',
        usage: '15'
    };
    
    const overdueData = {
        ...baseSample,
        overdue_days: '5',
        original_due_date: '20/08/2025'
    };
    
    const paymentData = {
        ...baseSample,
        payment_amount: 'Rp 225.000',
        payment_date: '23/08/2025',
        payment_method: 'Transfer Bank',
        payment_number: 'PAY-202508-001'
    };
    
    switch(templateType) {
        case 'overdue_notice': return overdueData;
        case 'payment_confirmation': return paymentData;
        default: return baseSample;
    }
}

// Test template with API
async function testTemplate() {
    const templateType = document.getElementById('template_type').value;
    const content = document.getElementById('template_content').value;
    
    if (!templateType || !content) {
        showToast('Pilih tipe template dan isi content terlebih dahulu', 'error');
        return;
    }
    
    const sampleData = generateSampleData(templateType);
    
    showToast('Testing template dengan data sample...', 'info');
    
    // Simulate API call for testing
    setTimeout(() => {
        showToast('Template berhasil ditest! Lihat hasil di preview.', 'success');
        refreshPreview();
    }, 1000);
}

// Refresh preview
function refreshPreview() {
    updatePreview();
    showToast('Preview berhasil diperbarui', 'info');
}

// Save template
async function saveTemplate() {
    const form = document.getElementById('templateForm');
    const formData = new FormData(form);
    const templateId = document.getElementById('template_id').value;
    
    // Validation
    if (!validateTemplateForm()) {
        return;
    }
    
    const templateData = {
        name: document.getElementById('template_name').value,
        type: document.getElementById('template_type').value,
        content: document.getElementById('template_content').value,
        is_active: document.getElementById('is_active').checked
    };
    
    const saveButton = document.getElementById('saveTemplateBtn');
    saveButton.disabled = true;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
    
    try {
        // Simulate API call with delay
        await new Promise(resolve => setTimeout(resolve, 1500));
        
        // Simulate validation errors occasionally for demo
        if (Math.random() < 0.1) { // 10% chance of error for demo
            throw {
                response: {
                    data: {
                        errors: {
                            name: ['Nama template sudah digunakan'],
                            content: ['Content template terlalu pendek']
                        }
                    }
                }
            };
        }
        
        if (templateId) {
            // Update existing template
            const index = templatesData.findIndex(t => t.id === parseInt(templateId));
            if (index !== -1) {
                templatesData[index] = {
                    ...templatesData[index],
                    template_name: templateData.name,
                    template_type: templateData.type,
                    message_content: templateData.content,
                    is_active: templateData.is_active,
                    variables: extractVariables(templateData.content)
                };
            }
            showToast('Template berhasil diupdate!', 'success');
        } else {
            // Add new template
            const newTemplate = {
                id: Math.max(...templatesData.map(t => t.id)) + 1,
                template_name: templateData.name,
                template_type: templateData.type,
                message_content: templateData.content,
                is_active: templateData.is_active,
                variables: extractVariables(templateData.content),
                created_at: new Date().toISOString()
            };
            templatesData.unshift(newTemplate);
            showToast('Template baru berhasil dibuat!', 'success');
        }
        
        bootstrap.Modal.getInstance(document.getElementById('templateModal')).hide();
        searchTemplates(currentPage);
        loadTemplateStats();
        
    } catch (error) {
        console.error('Error saving template:', error);
        handleApiError(error, 'Gagal menyimpan template');
    } finally {
        saveButton.disabled = false;
        saveButton.innerHTML = '<i class="fas fa-save me-1"></i>Simpan Template';
    }
}

// View template details
async function viewTemplate(id) {
    const template = templatesData.find(t => t.id === id);
    if (!template) return;
    
    const modal = new bootstrap.Modal(document.getElementById('templateDetailModal'));
    modal.show();
    
    try {
        // Simulate API delay
        await new Promise(resolve => setTimeout(resolve, 500));
        
        const content = document.getElementById('templateDetailContent');
        const sampleData = generateSampleData(template.template_type);
        let processedContent = template.message_content;
        
        Object.keys(sampleData).forEach(key => {
            const regex = new RegExp(`{${key}}`, 'g');
            processedContent = processedContent.replace(regex, sampleData[key]);
        });
        
        content.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Informasi Template</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Nama:</strong></td><td>${template.template_name}</td></tr>
                        <tr><td><strong>Tipe:</strong></td><td><span class="badge ${getTypeBadgeClass(template.template_type)}">${getTemplateTypeText(template.template_type)}</span></td></tr>
                        <tr><td><strong>Status:</strong></td><td>${template.is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Tidak Aktif</span>'}</td></tr>
                        <tr><td><strong>Variabel:</strong></td><td>${template.variables ? template.variables.length : 0} variabel</td></tr>
                        <tr><td><strong>Dibuat:</strong></td><td>${formatDate(template.created_at)}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Template Content</h6>
                    <div class="bg-light p-3 rounded template-content" style="max-height: 200px; overflow-y: auto;">
                        ${template.message_content.replace(/\n/g, '<br>')}
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <h6>Preview dengan Data Sample</h6>
                <div class="template-preview p-3 rounded">
                    ${processedContent.replace(/\n/g, '<br>')}
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error loading template detail:', error);
        document.getElementById('templateDetailContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <p class="text-danger">Gagal memuat detail template</p>
            </div>
        `;
    }
}

// Edit template
function editTemplate(id) {
    const template = templatesData.find(t => t.id === id);
    if (template) {
        openTemplateModal(template);
    }
}

// Duplicate template
function duplicateTemplate(id) {
    const template = templatesData.find(t => t.id === id);
    if (!template) return;
    
    const duplicated = {
        ...template,
        id: Math.max(...templatesData.map(t => t.id)) + 1,
        template_name: template.template_name + ' (Copy)',
        is_active: false,
        created_at: new Date().toISOString()
    };
    
    templatesData.unshift(duplicated);
    searchTemplates(currentPage);
    loadTemplateStats();
    showToast('Template berhasil diduplikat!', 'success');
}

// Delete template
function deleteTemplate(id) {
    const template = templatesData.find(t => t.id === id);
    if (!template) return;
    
    if (confirm(`Apakah Anda yakin ingin menghapus template "${template.template_name}"?`)) {
        templatesData = templatesData.filter(t => t.id !== id);
        searchTemplates(currentPage);
        loadTemplateStats();
        showToast('Template berhasil dihapus!', 'success');
    }
}

// Utility functions
function validateTemplateForm() {
    clearFormErrors();
    let isValid = true;
    
    const name = document.getElementById('template_name').value.trim();
    const type = document.getElementById('template_type').value;
    const content = document.getElementById('template_content').value.trim();
    
    if (!name) {
        showFieldError('template_name', 'Nama template wajib diisi');
        isValid = false;
    }
    
    if (!type) {
        showFieldError('template_type', 'Tipe template wajib dipilih');
        isValid = false;
    }
    
    if (!content) {
        showFieldError('template_content', 'Content template wajib diisi');
        isValid = false;
    }
    
    return isValid;
}

function clearFormErrors() {
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
    document.querySelectorAll('.invalid-feedback').forEach(el => {
        el.textContent = '';
    });
}

function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const feedback = field.nextElementSibling;
    
    field.classList.add('is-invalid');
    if (feedback && feedback.classList.contains('invalid-feedback')) {
        feedback.textContent = message;
    }
}

function extractVariables(content) {
    const regex = /{([^}]+)}/g;
    const variables = [];
    let match;
    
    while ((match = regex.exec(content)) !== null) {
        if (!variables.includes(match[1])) {
            variables.push(match[1]);
        }
    }
    
    return variables;
}
</script>
@endsection
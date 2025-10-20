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
                            <i class="fas fa-users-cog text-primary me-2"></i>
                            Manajemen User
                        </h4>
                        <p class="text-muted mb-0">Kelola semua user sistem: Admin, Keuangan, Manajemen, Customer</p>
                    </div>
                    <div class="col-md-4 col-sm-6 text-end">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openUserModal('add')">
                            <i class="fas fa-plus me-2"></i>
                            <span class="d-none d-sm-inline">Tambah </span>User
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="card bg-primary text-white border-0 h-100">
                    <div class="card-body text-center py-3">
                        <h4 id="total-users" class="mb-1">-</h4>
                        <small>Total User</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="card bg-success text-white border-0 h-100">
                    <div class="card-body text-center py-3">
                        <h4 id="active-users" class="mb-1">-</h4>
                        <small>User Aktif</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 mb-md-0">
                <div class="card bg-info text-white border-0 h-100">
                    <div class="card-body text-center py-3">
                        <h4 id="admin-count" class="mb-1">-</h4>
                        <small>Admin</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-warning text-dark border-0 h-100">
                    <div class="card-body text-center py-3">
                        <h4 id="customer-count" class="mb-1">-</h4>
                        <small>Customer</small>
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
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari nama, phone, email...">
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-6 mb-3 mb-lg-0">
                <select class="form-select" id="roleFilter">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="keuangan">Keuangan</option>
                    <option value="manajemen">Manajemen</option>
                    <option value="customer">Customer</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-6 mb-3 mb-lg-0">
                <select class="form-select" id="statusFilter">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Non-Aktif</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-6 mb-3 mb-md-0">
                <button class="btn btn-primary w-100" onclick="searchUsers()">
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

        <!-- Users Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <!-- Loading State -->
                <div id="loadingState" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data user...</p>
                </div>

                <!-- Data Table - REQ-F-10 -->
                <div class="table-responsive" id="usersTableContainer">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th onclick="sortTable('id')" class="sortable-header" style="min-width: 80px;">
                                    <div class="d-flex align-items-center">
                                        <span>ID</span>
                                        <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="id"></i>
                                    </div>
                                </th>
                                <th onclick="sortTable('name')" class="sortable-header" style="min-width: 200px;">
                                    <div class="d-flex align-items-center">
                                        <span>Nama User</span>
                                        <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="name"></i>
                                    </div>
                                </th>
                                <th class="d-none d-md-table-cell" style="min-width: 140px;">Phone</th>
                                <th class="d-none d-lg-table-cell" style="min-width: 200px;">Email</th>
                                <th class="d-none d-sm-table-cell text-center" style="min-width: 120px;">Role</th>
                                <th class="text-center" style="min-width: 100px;">Status</th>
                                <th onclick="sortTable('created_at')" class="sortable-header d-none d-lg-table-cell text-center" style="min-width: 120px;">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span>Bergabung</span>
                                        <i class="fas fa-sort ms-2 text-muted sort-icon" data-field="created_at"></i>
                                    </div>
                                </th>
                                <th class="text-center" style="min-width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted mb-2">Belum ada data user</h6>
                                        <p class="text-muted small mb-0">Klik tombol "Cari" untuk memuat data user</p>
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
                                <!-- Pagination will be generated here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-user-plus me-2"></i>
                    Tambah User Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="userId" name="user_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="userName" name="name" required>
                            <div class="invalid-feedback" id="nameError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="userPhone" name="phone" required placeholder="08xxxxxxxxx">
                            <div class="invalid-feedback" id="phoneError"></div>
                            <small class="text-muted">Format: 08xxxxxxxxx</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="userEmail" name="email">
                            <div class="invalid-feedback" id="emailError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="userRole" name="role_id" required>
                                <option value="">Pilih Role</option>
                                <!-- Options akan dimuat via JavaScript -->
                            </select>
                            <div class="invalid-feedback" id="roleError"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password <span class="text-danger" id="passwordRequired">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="userPassword" name="password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('userPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="passwordError"></div>
                            <small class="text-muted">Min 8 karakter, kombinasi huruf besar, kecil, dan angka</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konfirmasi Password <span class="text-danger" id="confirmPasswordRequired">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="userPasswordConfirm" name="password_confirmation">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('userPasswordConfirm')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="passwordConfirmError"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="userIsActive" name="is_active" checked>
                                <label class="form-check-label" for="userIsActive">
                                    User Aktif
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Penting
                        </h6>
                        <ul class="mb-0 small">
                            <li>Pastikan semua data telah diisi dengan benar</li>
                            <li>Nomor telepon harus unik dalam sistem</li>
                            <li>Password harus memenuhi kriteria keamanan</li>
                            <li>Role menentukan hak akses user di sistem</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveUserBtn">
                        <i class="fas fa-save me-2"></i>
                        <span id="saveUserText">Simpan</span>
                        <span id="saveUserSpinner" class="spinner-border spinner-border-sm ms-1" style="display: none;"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-user-times fa-3x text-danger mb-2"></i>
                    <p class="mb-2">Yakin ingin menghapus user <strong id="deleteUserName"></strong>?</p>
                    <p class="text-danger small mb-0">Tindakan ini tidak dapat dibatalkan!</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Batal
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>
                    <span id="deleteText">Hapus</span>
                    <span id="deleteSpinner" class="spinner-border spinner-border-sm ms-1" style="display: none;"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Styles -->
<style>

    /* Admin protection styling */
    .btn-secondary[disabled] {
        background-color: #6c757d;
        border-color: #6c757d;
        opacity: 0.65;
        cursor: not-allowed;
    }

    .btn-secondary[disabled]:hover {
        transform: none;
        box-shadow: none;
    }
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
        .modal-lg {
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
let perPage = 10;
let sortField = 'created_at';
let sortDirection = 'desc';
let deleteUserId = null;
let roles = [];
let isEditMode = false;
let currentUserId = null;

// ===================================
// START PERBAIKAN: Utility Functions (Missing functions)
// ===================================

/**
 * Mendapatkan headers otentikasi.
 * Mengasumsikan token JWT/Sanctum disimpan di meta tag 'api-token'
 * atau menggunakan X-CSRF-TOKEN untuk otentikasi session.
 */
function getAuthHeaders() {
    const token = document.querySelector('meta[name="api-token"]')?.getAttribute('content');
    const headers = {};
    
    // Fallback: Jika menggunakan session/web guard, kirim X-CSRF-TOKEN
    headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // API Authorization (Bearer Token - Sesuai error 401)
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }
    
    return headers;
}

// Fungsi dummy/placeholder untuk showLoading dan showTableLoading
function showLoading(show) {
    const spinner = document.getElementById('mainSpinner');
    if (spinner) {
        spinner.style.display = show ? 'block' : 'none';
    }
    // Implementasi real: bisa mematikan/menghidupkan overlay loading global
    showTableLoading(show);
}

function showTableLoading(show) {
    const tbody = document.getElementById('usersTableBody');
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
        } else {
            // Biarkan data ditampilkan atau akan diisi oleh displayUsers
        }
    }
}
// ===================================
// END PERBAIKAN: Utility Functions
// ===================================

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.axios === 'undefined') {
        console.error('Axios tidak ditemukan. Pastikan sudah di-include.');
    }
    
    // Mengatur header default Axios untuk token otentikasi (opsional, tapi bagus)
    // Walaupun kita akan mengirim secara eksplisit per API call di sini.
    axios.defaults.headers.common = getAuthHeaders();
    
    setupSearchHandlers();
    loadRoles();
    loadUserStats();
    searchUsers(); // Load table data from API on start
    setupFormSubmission();
});

// Enhanced search handlers - REQ-F-10.2
function setupSearchHandlers() {
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    // Real-time search with debouncing
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const value = this.value.trim();
        
        if (value.length === 0 || value.length >= 2) {
            searchTimeout = setTimeout(() => {
                searchUsers(1);
            }, 300);
        }
    });
    
    // Filter change handlers
    [roleFilter, statusFilter].forEach(filter => {
        filter.addEventListener('change', () => {
            searchUsers(1);
        });
    });
    
    // Enter key search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimeout);
            searchUsers(1);
        }
    });
}

// Load user statistics dari API /api/admin/dashboard-stats
async function loadUserStats() {
    // Set loading state
    ['total-users', 'active-users', 'admin-count', 'customer-count'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = '...';
    });
    
    try {
        // START PERBAIKAN: Tambahkan headers otentikasi
        const response = await axios.get('/api/admin/dashboard-stats', {
            headers: getAuthHeaders()
        });
        // END PERBAIKAN
        
        const stats = response.data.data;
        
        document.getElementById('total-users').textContent = stats.users.total;
        document.getElementById('active-users').textContent = stats.users.active;
        document.getElementById('admin-count').textContent = stats.users.admin;
        document.getElementById('customer-count').textContent = stats.users.customer;
    } catch (error) {
        console.error('Error loading stats:', error);
        handleApiError(error, 'Gagal memuat statistik dashboard!');
        // Set default values on error
        ['total-users', 'active-users', 'admin-count', 'customer-count'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = '0';
        });
    }
}

// Load roles for dropdown dari API /api/admin/roles
async function loadRoles() {
    try {
        // START PERBAIKAN: Tambahkan headers otentikasi
        const response = await axios.get('/api/admin/roles', {
            headers: getAuthHeaders()
        });
        // END PERBAIKAN
        
        roles = response.data.data;
        
        const roleSelect = document.getElementById('userRole');
        const roleFilter = document.getElementById('roleFilter');
        
        if (!roleSelect || !roleFilter) return;
        
        // Populate modal role select
        roleSelect.innerHTML = '<option value="">Pilih Role</option>';
        roles.forEach(role => {
            const description = role.name.charAt(0).toUpperCase() + role.name.slice(1);
            roleSelect.innerHTML += `<option value="${role.id}">${description}</option>`;
        });

        // Populate filter role select
        roleFilter.innerHTML = '<option value="">Semua Role</option>';
        roles.forEach(role => {
            const description = role.name.charAt(0).toUpperCase() + role.name.slice(1);
            roleFilter.innerHTML += `<option value="${role.name}">${description}</option>`;
        });

    } catch (error) {
        console.error('Error loading roles:', error);
        showAlert('Gagal memuat data roles!', 'danger');
    }
}

// Enhanced search function - REQ-F-10.1 & REQ-F-10.2 menggunakan API /api/datatables/users
async function searchUsers(page = 1) {
    showLoading(true);
    
    const searchTerm = document.getElementById('searchInput').value;
    const roleFilterName = document.getElementById('roleFilter').value; // Ambil role name dari filter
    const statusFilter = document.getElementById('statusFilter').value;
    
    // Cari role_id berdasarkan role name yang dipilih di filter
    const roleFilterId = roles.find(r => r.name === roleFilterName)?.id;

    try {
        // START PERBAIKAN: Tambahkan headers otentikasi
        const response = await axios.get('/api/datatables/users', {
            headers: getAuthHeaders(),
            params: {
                page: page,
                per_page: perPage,
                sort_field: sortField,
                sort_direction: sortDirection,
                search: searchTerm,
                // Filters sesuai API DataTableController.php
                filters: JSON.stringify({
                    role_id: roleFilterId, // Kirim ID role
                    is_active: statusFilter === '1' ? true : (statusFilter === '0' ? false : undefined)
                })
            }
        });
        // END PERBAIKAN

        const data = response.data.data; 
        const pagination = response.data.meta.pagination;

        displayUsers({ data: data, current_page: pagination.current_page, per_page: pagination.per_page, total: pagination.total, last_page: pagination.last_page, from: pagination.from, to: pagination.to });
        updatePagination({ current_page: pagination.current_page, last_page: pagination.last_page, total: pagination.total, per_page: pagination.per_page, from: pagination.from, to: pagination.to });
        
        currentPage = page;
    } catch (error) {
        console.error('Error loading users:', error);
        handleApiError(error, 'Gagal memuat data user. Pastikan API datatables/users berfungsi dan terotentikasi.');
    } finally {
        showLoading(false);
    }
}

// Display users function (disesuaikan untuk struktur data API)
function displayUsers(data) {
    const tbody = document.getElementById('usersTableBody');
    
    if (!data.data || data.data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-search fa-4x text-muted mb-3 opacity-50"></i>
                        <h6 class="text-muted mb-2">Tidak ada data user</h6>
                        <p class="text-muted small mb-0">Coba ubah kriteria pencarian atau tambah user baru</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = data.data.map((user, index) => {
        const rowNumber = ((currentPage - 1) * perPage) + index + 1;
        
        const roleName = user.role?.name || 'unknown';
        const roleDescription = roles.find(r => r.id === user.role_id)?.description || roleName.charAt(0).toUpperCase() + roleName.slice(1);

        return `
            <tr class="hover-shadow">
                <td>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-light text-dark me-2">${rowNumber}</span>
                        <strong class="text-primary">${user.id}</strong>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-3">
                            ${user.name ? user.name.charAt(0).toUpperCase() : '?'}
                        </div>
                        <div>
                            <div class="fw-bold text-dark">${user.name}</div>
                            <small class="text-muted">
                                <i class="fas fa-phone fa-xs me-1"></i>
                                ${user.phone}
                            </small>
                        </div>
                    </div>
                </td>
                <td class="d-none d-md-table-cell">
                    <small class="font-monospace">${user.phone}</small>
                </td>
                <td class="d-none d-lg-table-cell">
                    <div class="text-truncate" style="max-width: 200px;" title="${user.email || '-'}">
                        ${user.email ? `<i class="fas fa-envelope text-muted me-1"></i><small>${user.email}</small>` : '<span class="text-muted">-</span>'}
                    </div>
                </td>
                <td class="d-none d-sm-table-cell text-center">
                    <span class="badge ${getRoleBadgeClass(roleName)}">${roleDescription}</span>
                </td>
                <td class="text-center">
                    <span class="badge ${user.is_active ? 'bg-success' : 'bg-danger'}">
                        ${user.is_active ? 'Aktif' : 'Non-Aktif'}
                    </span>
                </td>
                <td class="d-none d-lg-table-cell text-center">
                    <small class="text-muted">${formatDate(user.created_at)}</small>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-warning btn-sm" onclick="editUser(${user.id})" 
                                title="Edit User" data-bs-toggle="tooltip">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${user.role.name === 'admin' ? 
                            `<button class="btn btn-secondary btn-sm" disabled 
                                     title="Administrator tidak dapat dihapus" data-bs-toggle="tooltip">
                                <i class="fas fa-shield-alt"></i>
                             </button>` : 
                            `<button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id}, '${user.name}')" 
                                     title="Hapus User" data-bs-toggle="tooltip">
                                <i class="fas fa-trash"></i>
                             </button>`
                        }
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

// Load single user data for edit modal - menggunakan API /api/admin/users/{id}
async function editUser(userId) {
    currentUserId = userId;
    
    showLoading(true);
    
    try {
        // START PERBAIKAN: Tambahkan headers otentikasi
        const response = await axios.get(`/api/admin/users/${userId}`, {
            headers: getAuthHeaders()
        });
        // END PERBAIKAN
        
        const user = response.data.data;
        
        if (!user) {
            showAlert('User tidak ditemukan!', 'danger');
            return;
        }
        
        openUserModal('edit', user);
        
    } catch (error) {
        console.error('Error fetching user data:', error);
        handleApiError(error, 'Error mengambil data user!');
    } finally {
        showLoading(false);
    }
}

// Function untuk submit form (Create/Update)
function setupFormSubmission() {
    document.getElementById('userForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('saveUserBtn');
        const text = document.getElementById('saveUserText');
        const spinner = document.getElementById('saveUserSpinner');
        
        btn.disabled = true;
        text.style.display = 'none';
        spinner.style.display = 'inline-block';
        clearFormErrors();
        
        const formData = {
            name: document.getElementById('userName').value,
            phone: document.getElementById('userPhone').value,
            email: document.getElementById('userEmail').value,
            password: document.getElementById('userPassword').value,
            password_confirmation: document.getElementById('userPasswordConfirm').value,
            role_id: document.getElementById('userRole').value,
            is_active: document.getElementById('userIsActive').checked
        };
        
        const isEdit = currentUserId !== null;
        const method = isEdit ? 'PUT' : 'POST';
        const url = isEdit ? `/api/admin/users/${currentUserId}` : '/api/admin/users';
        
        try {
            // Jika edit mode dan password kosong, hapus field password dari request
            if (isEdit && !formData.password) {
                delete formData.password;
                delete formData.password_confirmation;
            }
            
            // START PERBAIKAN: Tambahkan headers otentikasi
            const response = await axios({ method: method, url: url, data: formData, headers: getAuthHeaders() });
            // END PERBAIKAN
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
            modal.hide();
            
            showAlert(isEdit ? 'User berhasil diperbarui!' : 'User berhasil ditambah!', 'success');
            searchUsers(currentPage);
            loadUserStats();
            
        } catch (error) {
            handleApiError(error, 'Gagal menyimpan data user');
        } finally {
            btn.disabled = false;
            text.style.display = 'inline';
            spinner.style.display = 'none';
        }
    });
}

// Delete user API call - menggunakan API /api/admin/users/{id}
async function confirmDelete() {
    const btn = document.getElementById('confirmDeleteBtn');
    const text = document.getElementById('deleteText');
    const spinner = document.getElementById('deleteSpinner');
    
    try {
        btn.disabled = true;
        text.style.display = 'none';
        spinner.style.display = 'inline-block';
        
        // START PERBAIKAN: Tambahkan headers otentikasi
        await axios.delete(`/api/admin/users/${deleteUserId}`, {
            headers: getAuthHeaders()
        });
        // END PERBAIKAN
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();
        
        showAlert('User berhasil dihapus!', 'success');
        searchUsers(currentPage);
        loadUserStats();
        
    } catch (error) {
        console.error('Error deleting user:', error);
        handleApiError(error, 'Error menghapus user!');
    } finally {
        btn.disabled = false;
        text.style.display = 'inline';
        spinner.style.display = 'none';
    }
}

// Fungsi openUserModal disesuaikan untuk mode edit
function openUserModal(action, userData = null) {
    const modal = new bootstrap.Modal(document.getElementById('userModal'));
    const form = document.getElementById('userForm');
    const title = document.getElementById('modalTitle');
    
    form.reset();
    clearFormErrors();
    isEditMode = action === 'edit';
    currentUserId = userData ? userData.id : null;

    const passwordRequired = document.getElementById('passwordRequired');
    const confirmPasswordRequired = document.getElementById('confirmPasswordRequired');
    const userPassword = document.getElementById('userPassword');
    const userPasswordConfirm = document.getElementById('userPasswordConfirm');
    
    // Default mode: Add User
    title.innerHTML = '<i class="fas fa-user-plus me-2"></i>Tambah User Baru';
    document.getElementById('userId').value = '';
    passwordRequired.style.display = 'inline';
    confirmPasswordRequired.style.display = 'inline';
    userPassword.required = true;
    userPasswordConfirm.required = true;
    document.getElementById('userIsActive').checked = true;

    if (action === 'edit' && userData) {
        title.innerHTML = '<i class="fas fa-user-edit me-2"></i>Edit User';
        
        // Hide password required indicator, make fields optional
        passwordRequired.style.display = 'none';
        confirmPasswordRequired.style.display = 'none';
        userPassword.required = false;
        userPasswordConfirm.required = false;

        // Populate form
        document.getElementById('userId').value = userData.id;
        document.getElementById('userName').value = userData.name;
        document.getElementById('userPhone').value = userData.phone;
        document.getElementById('userEmail').value = userData.email || '';
        document.getElementById('userIsActive').checked = userData.is_active;
        
        // Set role dropdown
        const roleId = roles.find(r => r.name === userData.role.name)?.id;
        if (roleId) {
            document.getElementById('userRole').value = roleId;
        }
        
        // Clear password fields (do not display old password)
        userPassword.value = '';
        userPasswordConfirm.value = '';
    }
    
    modal.show();
}

// Fungsi deleteUser tetap, hanya memastikan admin tidak bisa dihapus
function deleteUser(userId, userName) {
    deleteUserId = userId;
    document.getElementById('deleteUserName').textContent = userName;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}


// Utility functions (sedikit disesuaikan)
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('roleFilter').value = '';
    document.getElementById('statusFilter').value = '';
    searchUsers(1);
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
        // Handle validation errors for modal
        Object.keys(error.response.data.errors).forEach(field => {
            const formFieldId = {
                'name': 'userName',
                'phone': 'userPhone',
                'email': 'userEmail',
                'password': 'userPassword',
                'password_confirmation': 'userPasswordConfirm',
                'role_id': 'userRole',
                'is_active': 'userIsActive'
            }[field] || field;

            const element = document.getElementById(formFieldId);
            const message = error.response.data.errors[field][0];
            
            if (element) {
                element.classList.add('is-invalid');
                const feedback = document.getElementById(field + 'Error');
                if (feedback) {
                    feedback.textContent = message;
                }
            }
        });
    } else {
        // Handle general errors
        const message = error?.response?.data?.message || error.message || defaultMessage;
        showAlert(message, 'danger');
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

function getRoleBadgeClass(role) {
    switch(role) {
        case 'admin': return 'bg-danger';
        case 'keuangan': return 'bg-success';
        case 'manajemen': return 'bg-info';
        case 'customer': return 'bg-primary';
        default: return 'bg-secondary';
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

function changePerPage() {
    perPage = parseInt(document.getElementById('perPageSelect').value);
    searchUsers(1);
}

// Update pagination untuk format response DataTableController
function updatePagination(pagination) {
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationList = document.getElementById('paginationList');
    
    paginationInfo.textContent = `Menampilkan ${pagination.from || 0} - ${pagination.to || 0} dari ${pagination.total || 0} data`;
    
    if (!pagination.last_page || pagination.last_page <= 1) {
        paginationList.innerHTML = '';
        return;
    }
    
    let paginationHtml = '';
    
    // Previous button
    if (pagination.current_page > 1) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="searchUsers(${pagination.current_page - 1})">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
    }
    
    // Page numbers
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        paginationHtml += `
            <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="searchUsers(${i})">${i}</a>
            </li>
        `;
    }
    
    // Next button
    if (pagination.current_page < pagination.last_page) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="searchUsers(${pagination.current_page + 1})">
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
    searchUsers(1);
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
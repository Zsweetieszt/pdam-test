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

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupSearchHandlers();
    loadRoles();
    loadUserStats();
    searchUsers();
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

// Load user statistics
async function loadUserStats() {
    // Set loading state
    ['total-users', 'active-users', 'admin-count', 'customer-count'].forEach(id => {
        document.getElementById(id).textContent = '...';
    });
    
    try {
        // Simulate delay
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Calculate stats from mock data
        const allData = generateMockUsers(1, 1000, '', '', '');
        const totalUsers = allData.data.length;
        const activeUsers = allData.data.filter(u => u.is_active).length;
        const adminCount = allData.data.filter(u => u.role.name === 'admin').length;
        const customerCount = allData.data.filter(u => u.role.name === 'customer').length;
        
        document.getElementById('total-users').textContent = totalUsers;
        document.getElementById('active-users').textContent = activeUsers;
        document.getElementById('admin-count').textContent = adminCount;
        document.getElementById('customer-count').textContent = customerCount;
    } catch (error) {
        console.error('Error loading stats:', error);
        // Set default values
        ['total-users', 'active-users', 'admin-count', 'customer-count'].forEach(id => {
            document.getElementById(id).textContent = '0';
        });
    }
}

// Load roles for dropdown
async function loadRoles() {
    try {
        roles = [
            {id: 1, name: 'admin', description: 'Administrator'},
            {id: 2, name: 'keuangan', description: 'Staff Keuangan'},
            {id: 3, name: 'manajemen', description: 'Manajemen'},
            {id: 4, name: 'customer', description: 'Customer'}
        ];
        
        const roleSelect = document.getElementById('userRole');
        roleSelect.innerHTML = '<option value="">Pilih Role</option>';
        roles.forEach(role => {
            roleSelect.innerHTML += `<option value="${role.id}">${role.description}</option>`;
        });
    } catch (error) {
        console.error('Error loading roles:', error);
    }
}

// Enhanced search function - REQ-F-10.1 & REQ-F-10.2
async function searchUsers(page = 1) {
    showLoading(true);
    
    const searchTerm = document.getElementById('searchInput').value;
    const roleFilter = document.getElementById('roleFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    try {
        // Simulate API delay
        await new Promise(resolve => setTimeout(resolve, 800));
        
        // Use mock data with enhanced filtering and sorting
        const mockData = generateMockUsers(page, perPage, searchTerm, roleFilter, statusFilter);
        
        displayUsers(mockData);
        updatePagination(mockData);
        currentPage = page;
    } catch (error) {
        console.error('Error loading users:', error);
        handleApiError(error, 'Gagal memuat data user');
    } finally {
        showLoading(false);
    }
}

// Enhanced mock data generator with sorting support - REQ-F-10.3
function generateMockUsers(page, perPage, search, roleFilter, statusFilter) {
    const allUsers = [
        {id: 1, name: 'Admin PDAM', phone: '08111111111', email: 'admin@pdam.com', role: {name: 'admin', description: 'Administrator'}, is_active: true, created_at: '2024-01-01'},
        {id: 2, name: 'Staff Keuangan 1', phone: '08222222222', email: 'keuangan1@pdam.com', role: {name: 'keuangan', description: 'Staff Keuangan'}, is_active: true, created_at: '2024-01-02'},
        {id: 3, name: 'Staff Keuangan 2', phone: '08222222223', email: 'keuangan2@pdam.com', role: {name: 'keuangan', description: 'Staff Keuangan'}, is_active: false, created_at: '2024-01-03'},
        {id: 4, name: 'Manager PDAM', phone: '08333333333', email: 'manager@pdam.com', role: {name: 'manajemen', description: 'Manajemen'}, is_active: true, created_at: '2024-01-04'},
        {id: 5, name: 'Budi Santoso', phone: '08444444444', email: 'budi.santoso@gmail.com', role: {name: 'customer', description: 'Customer'}, is_active: true, created_at: '2024-01-05'},
        {id: 6, name: 'Siti Rahayu', phone: '08555555555', email: 'siti.rahayu@gmail.com', role: {name: 'customer', description: 'Customer'}, is_active: true, created_at: '2024-01-06'},
        {id: 7, name: 'Ahmad Hidayat', phone: '08666666666', email: 'ahmad.hidayat@gmail.com', role: {name: 'customer', description: 'Customer'}, is_active: false, created_at: '2024-01-07'},
        {id: 8, name: 'Maria Gonzalez', phone: '08777777777', email: 'maria.gonzalez@outlook.com', role: {name: 'customer', description: 'Customer'}, is_active: true, created_at: '2024-01-08'},
        {id: 9, name: 'Dewi Sartika', phone: '08888888888', email: 'dewi.sartika@yahoo.com', role: {name: 'customer', description: 'Customer'}, is_active: true, created_at: '2024-01-09'},
        {id: 10, name: 'Bambang Setiawan', phone: '08999999999', email: null, role: {name: 'customer', description: 'Customer'}, is_active: false, created_at: '2024-01-10'}
    ];
    
    // Apply filters first
    let filteredUsers = allUsers;
    
    if (search) {
        const searchLower = search.toLowerCase();
        filteredUsers = filteredUsers.filter(user => 
            user.name.toLowerCase().includes(searchLower) ||
            user.phone.includes(search) ||
            (user.email && user.email.toLowerCase().includes(searchLower))
        );
    }
    
    if (roleFilter) {
        filteredUsers = filteredUsers.filter(user => user.role.name === roleFilter);
    }
    
    if (statusFilter !== '') {
        const isActive = statusFilter === '1';
        filteredUsers = filteredUsers.filter(user => user.is_active === isActive);
    }
    
    // Apply sorting - REQ-F-10.3
    filteredUsers.sort((a, b) => {
        let valueA, valueB;
        
        switch(sortField) {
            case 'id':
                valueA = a.id;
                valueB = b.id;
                break;
            case 'name':
                valueA = a.name || '';
                valueB = b.name || '';
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
        } else if (typeof valueA === 'number' && typeof valueB === 'number') {
            return sortDirection === 'asc' ? valueA - valueB : valueB - valueA;
        } else {
            const comparison = valueA.toString().localeCompare(valueB.toString(), 'id-ID');
            return sortDirection === 'asc' ? comparison : -comparison;
        }
    });
    
    // Apply pagination - REQ-F-10.4
    const startIndex = (page - 1) * parseInt(perPage);
    const endIndex = startIndex + parseInt(perPage);
    const paginatedUsers = filteredUsers.slice(startIndex, endIndex);
    
    return {
        data: paginatedUsers,
        current_page: parseInt(page),
        per_page: parseInt(perPage),
        total: filteredUsers.length,
        last_page: Math.ceil(filteredUsers.length / parseInt(perPage)),
        from: filteredUsers.length === 0 ? 0 : startIndex + 1,
        to: Math.min(endIndex, filteredUsers.length)
    };
}

// Enhanced display users function - REQ-F-10.1
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
                            ${user.name.charAt(0).toUpperCase()}
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
                    <span class="badge ${getRoleBadgeClass(user.role.name)}">${user.role.description}</span>
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

// Helper functions
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
    searchUsers(1);
    
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
    searchUsers(1);
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
                <a class="page-link" href="#" onclick="searchUsers(${data.current_page - 1})">
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
                <a class="page-link" href="#" onclick="searchUsers(${i})">${i}</a>
            </li>
        `;
    }
    
    // Next button
    if (data.current_page < data.last_page) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="searchUsers(${data.current_page + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
    }
    
    paginationList.innerHTML = paginationHtml;
}

// Enhanced loading states
function showTableLoading(show) {
    const tbody = document.getElementById('usersTableBody');
    
    if (show) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
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
    const tableContainer = document.getElementById('usersTableContainer');
    
    if (show) {
        loadingState.classList.remove('d-none');
        tableContainer.classList.add('d-none');
    } else {
        loadingState.classList.add('d-none');
        tableContainer.classList.remove('d-none');
    }
}

// Modal and CRUD functions (keeping existing functionality)
function openUserModal(action, userData = null) {
    const modal = new bootstrap.Modal(document.getElementById('userModal'));
    const form = document.getElementById('userForm');
    const title = document.getElementById('modalTitle');
    
    // Reset form
    form.reset();
    clearFormErrors();
    isEditMode = action === 'edit';
    
    if (action === 'add') {
        title.innerHTML = '<i class="fas fa-user-plus me-2"></i>Tambah User Baru';
        document.getElementById('userIsActive').checked = true;
        document.getElementById('passwordRequired').style.display = 'inline';
        document.getElementById('confirmPasswordRequired').style.display = 'inline';
        document.getElementById('userPassword').required = true;
        document.getElementById('userPasswordConfirm').required = true;
        
        // Clear hidden field
        document.getElementById('userId').value = '';
        
    } else if (action === 'edit' && userData) {
        title.innerHTML = '<i class="fas fa-user-edit me-2"></i>Edit User';
        document.getElementById('passwordRequired').style.display = 'none';
        document.getElementById('confirmPasswordRequired').style.display = 'none';
        document.getElementById('userPassword').required = false;
        document.getElementById('userPasswordConfirm').required = false;
        
        // Populate form dengan data user
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
        
        // Clear password fields
        document.getElementById('userPassword').value = '';
        document.getElementById('userPasswordConfirm').value = '';
    }
    
    modal.show();
}

async function editUser(userId) {
    try {
        // Simulate API delay
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Get user from mock data
        const allData = generateMockUsers(1, 1000, '', '', '');
        const user = allData.data.find(u => u.id === userId);
        
        if (!user) {
            showAlert('User tidak ditemukan!', 'danger');
            return;
        }
        
        openUserModal('edit', user);
        
    } catch (error) {
        console.error('Error fetching user data:', error);
        showAlert('Error mengambil data user!', 'danger');
    }
}

function deleteUser(userId, userName) {
    // Check if trying to delete admin
    const allData = generateMockUsers(1, 1000, '', '', '');
    const user = allData.data.find(u => u.id === userId);
    
    if (user && user.role.name === 'admin') {
        showAlert('Administrator tidak dapat dihapus dari sistem!', 'warning');
        return;
    }
    
    deleteUserId = userId;
    document.getElementById('deleteUserName').textContent = userName;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

async function confirmDelete() {
    const btn = document.getElementById('confirmDeleteBtn');
    const text = document.getElementById('deleteText');
    const spinner = document.getElementById('deleteSpinner');
    
    try {
        btn.disabled = true;
        text.style.display = 'none';
        spinner.style.display = 'inline-block';
        
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();
        
        showAlert('User berhasil dihapus!', 'success');
        searchUsers(currentPage);
        loadUserStats();
        
    } catch (error) {
        showAlert('Error menghapus user!', 'danger');
    } finally {
        btn.disabled = false;
        text.style.display = 'inline';
        spinner.style.display = 'none';
    }
}

// Form submission handler
document.getElementById('userForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('saveUserBtn');
    const text = document.getElementById('saveUserText');
    const spinner = document.getElementById('saveUserSpinner');
    
    try {
        btn.disabled = true;
        text.style.display = 'none';
        spinner.style.display = 'inline-block';
        clearFormErrors();
        
        // Get form data
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        // Check if this is edit mode
        const isEdit = data.user_id && data.user_id !== '';
        
        // Basic validation
        if (!data.name || !data.phone || !data.role_id) {
            throw new Error('Mohon lengkapi data yang wajib diisi');
        }
        
        // Password validation - hanya required untuk user baru
        if (!isEdit && (!data.password || !data.password_confirmation)) {
            throw new Error('Password wajib diisi untuk user baru');
        }
        
        if (data.password && data.password !== data.password_confirmation) {
            throw new Error('Konfirmasi password tidak cocok');
        }
        
        // Validasi format phone
        if (!/^08\d{8,11}$/.test(data.phone)) {
            throw new Error('Format nomor telepon tidak valid. Gunakan format: 08xxxxxxxxx');
        }
        
        // Validasi password format (jika diisi)
        if (data.password) {
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
            if (!passwordRegex.test(data.password)) {
                throw new Error('Password harus minimal 8 karakter dengan kombinasi huruf besar, kecil, dan angka');
            }
        }
        
        // Simulate API call with occasional errors
        await new Promise(resolve => setTimeout(resolve, 1500));
        
        // Simulate validation errors occasionally for demo
        if (Math.random() < 0.1) { // 10% chance of error for demo
            throw {
                response: {
                    data: {
                        errors: {
                            phone: ['Nomor telepon sudah digunakan'],
                            email: ['Email sudah terdaftar']
                        }
                    }
                }
            };
        }
        
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

// Utility functions
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
        showAlert(message, 'danger');
    }
}

function showFieldError(field, message) {
    field.classList.add('is-invalid');
    const feedback = field.parentElement.querySelector('.invalid-feedback') || 
                    field.parentElement.parentElement.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.textContent = message;
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
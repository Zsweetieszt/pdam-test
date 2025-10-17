@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-history text-warning me-2"></i>
                            Audit Logs
                        </h4>
                        <p class="text-muted mb-0">Catatan aktivitas sistem dan pengguna</p>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary" onclick="exportAuditLogs()">
                            <i class="fas fa-download me-2"></i>
                            Export
                        </button>
                        <button class="btn btn-primary" onclick="refreshAuditLogs()">
                            <i class="fas fa-sync-alt me-2"></i>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">User</label>
                        <select class="form-select" id="userFilter">
                            <option value="">Semua User</option>
                            <!-- Options akan dimuat via JavaScript -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Aksi</label>
                        <select class="form-select" id="actionFilter">
                            <option value="">Semua Aksi</option>
                            <option value="LOGIN">Login</option>
                            <option value="LOGOUT">Logout</option>
                            <option value="LOGIN_FAILED">Login Failed</option>
                            <option value="REGISTER">Register</option>
                            <option value="PASSWORD_RESET">Password Reset</option>
                            <option value="CREATE">Create</option>
                            <option value="UPDATE">Update</option>
                            <option value="DELETE">Delete</option>
                            <option value="VIEW">View</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="dateFrom">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="dateTo">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-1">
                            <button class="btn btn-outline-primary btn-sm" onclick="applyFilters()">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Logs Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2">Memuat audit logs...</p>
                </div>

                <!-- Table -->
                <div id="auditTableContainer" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-hover" id="auditTable">
                            <thead class="table-warning">
                                <tr>
                                    <th width="60">ID</th>
                                    <th width="150">Tanggal/Waktu</th>
                                    <th width="120">User</th>
                                    <th width="100">Aksi</th>
                                    <th width="100">Tabel</th>
                                    <th width="80">Record ID</th>
                                    <th>Detail</th>
                                    <th width="120">IP Address</th>
                                    <th width="60">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="auditTableBody">
                                <!-- Data akan dimuat via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <select class="form-select form-select-sm w-auto" id="perPageSelect" onchange="loadAuditLogs(1)">
                                <option value="10">10 per halaman</option>
                                <option value="25">25 per halaman</option>
                                <option value="50">50 per halaman</option>
                                <option value="100">100 per halaman</option>
                            </select>
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0" id="pagination">
                                <!-- Pagination buttons akan dimuat via JavaScript -->
                            </ul>
                        </nav>
                        <div class="text-muted small" id="recordInfo">
                            <!-- Info record akan dimuat via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Audit Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="auditDetail">
                    <!-- Detail akan dimuat via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    initializePage();
});

function initializePage() {
    // Set default date range (last 7 days)
    const today = new Date();
    const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
    
    document.getElementById('dateTo').value = today.toISOString().split('T')[0];
    document.getElementById('dateFrom').value = weekAgo.toISOString().split('T')[0];
    
    loadUsers();
    loadAuditLogs(1);
}

// Load users for filter dropdown
async function loadUsers() {
    try {
        // Simulasi data users - dalam implementasi real gunakan API
        const users = [
            {id: 1, name: 'Admin PDAM'},
            {id: 2, name: 'Staff Keuangan 1'},
            {id: 3, name: 'Staff Keuangan 2'},
            {id: 4, name: 'Manager PDAM'},
            {id: 5, name: 'Customer 1'},
            {id: 6, name: 'Customer 2'}
        ];
        
        const userSelect = document.getElementById('userFilter');
        userSelect.innerHTML = '<option value="">Semua User</option>';
        users.forEach(user => {
            userSelect.innerHTML += `<option value="${user.id}">${user.name}</option>`;
        });
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

// Load audit logs with pagination and filters
async function loadAuditLogs(page = 1) {
    currentPage = page;
    
    try {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('auditTableContainer').style.display = 'none';
        
        // Build query parameters
        const params = new URLSearchParams();
        params.append('page', page);
        params.append('per_page', document.getElementById('perPageSelect').value);
        
        const userFilter = document.getElementById('userFilter').value;
        if (userFilter) params.append('user_id', userFilter);
        
        const actionFilter = document.getElementById('actionFilter').value;
        if (actionFilter) params.append('action', actionFilter);
        
        const dateFrom = document.getElementById('dateFrom').value;
        if (dateFrom) params.append('date_from', dateFrom);
        
        const dateTo = document.getElementById('dateTo').value;
        if (dateTo) params.append('date_to', dateTo);
        
        // Simulasi API call - dalam implementasi real gunakan API backend
        // const response = await fetch(`/api/admin/audit-logs?${params.toString()}`);
        
        await new Promise(resolve => setTimeout(resolve, 800));
        const mockData = generateMockAuditLogs(page, document.getElementById('perPageSelect').value, userFilter, actionFilter, dateFrom, dateTo);
        
        renderAuditTable(mockData);
        renderPagination(mockData.pagination);
        
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('auditTableContainer').style.display = 'block';
        
    } catch (error) {
        console.error('Error loading audit logs:', error);
        document.getElementById('loadingSpinner').style.display = 'none';
        showAlert('Error memuat audit logs!', 'danger');
    }
}

// Generate mock audit logs data
function generateMockAuditLogs(page, perPage, userFilter, actionFilter, dateFrom, dateTo) {
    const allLogs = [
        {id: 1, user: {id: 1, name: 'Admin PDAM'}, action: 'LOGIN', table_name: 'users', record_id: 1, old_values: null, new_values: null, ip_address: '192.168.1.100', user_agent: 'Mozilla/5.0...', created_at: '2024-01-15 08:30:00'},
        {id: 2, user: {id: 1, name: 'Admin PDAM'}, action: 'CREATE', table_name: 'customers', record_id: 5, old_values: null, new_values: {name: 'Customer Baru', phone: '08777777777'}, ip_address: '192.168.1.100', user_agent: 'Mozilla/5.0...', created_at: '2024-01-15 09:15:00'},
        {id: 3, user: {id: 2, name: 'Staff Keuangan 1'}, action: 'LOGIN', table_name: 'users', record_id: 2, old_values: null, new_values: null, ip_address: '192.168.1.101', user_agent: 'Mozilla/5.0...', created_at: '2024-01-15 10:00:00'},
        {id: 4, user: {id: 2, name: 'Staff Keuangan 1'}, action: 'UPDATE', table_name: 'bills', record_id: 123, old_values: {status: 'pending'}, new_values: {status: 'sent'}, ip_address: '192.168.1.101', user_agent: 'Mozilla/5.0...', created_at: '2024-01-15 10:30:00'},
        {id: 5, user: null, action: 'LOGIN_FAILED', table_name: 'users', record_id: null, old_values: null, new_values: {phone: '08999999999'}, ip_address: '203.142.12.45', user_agent: 'Mozilla/5.0...', created_at: '2024-01-15 11:00:00'},
        {id: 6, user: {id: 5, name: 'Customer 1'}, action: 'REGISTER', table_name: 'users', record_id: 5, old_values: null, new_values: {name: 'Customer 1', phone: '08444444444'}, ip_address: '180.241.155.22', user_agent: 'Mozilla/5.0...', created_at: '2024-01-15 11:30:00'},
        {id: 7, user: {id: 1, name: 'Admin PDAM'}, action: 'DELETE', table_name: 'customers', record_id: 3, old_values: {name: 'Customer Lama', status: 'inactive'}, new_values: null, ip_address: '192.168.1.100', user_agent: 'Mozilla/5.0...', created_at: '2024-01-15 14:00:00'},
        {id: 8, user: {id: 3, name: 'Staff Keuangan 2'}, action: 'PASSWORD_RESET', table_name: 'users', record_id: 3, old_values: {password_changed: false}, new_values: {password_changed: true}, ip_address: '192.168.1.102', user_agent: 'Mozilla/5.0...', created_at: '2024-01-15 15:30:00'},
        {id: 9, user: {id: 4, name: 'Manager PDAM'}, action: 'VIEW', table_name: 'reports', record_id: null, old_values: null, new_values: {report_type: 'revenue_monthly'}, ip_address: '192.168.1.103', user_agent: 'Mozilla/5.0...', created_at: '2024-01-15 16:00:00'},
        {id: 10, user: {id: 2, name: 'Staff Keuangan 1'}, action: 'LOGOUT', table_name: 'users', record_id: 2, old_values: null, new_values: null, ip_address: '192.168.1.101', user_agent: 'Mozilla/5.0...', created_at: '2024-01-15 17:00:00'}
    ];
    
    // Apply filters
    let filteredLogs = allLogs;
    
    if (userFilter) {
        filteredLogs = filteredLogs.filter(log => log.user && log.user.id == userFilter);
    }
    
    if (actionFilter) {
        filteredLogs = filteredLogs.filter(log => log.action === actionFilter);
    }
    
    if (dateFrom) {
        filteredLogs = filteredLogs.filter(log => log.created_at >= dateFrom);
    }
    
    if (dateTo) {
        const dateToEnd = dateTo + ' 23:59:59';
        filteredLogs = filteredLogs.filter(log => log.created_at <= dateToEnd);
    }
    
    // Apply pagination
    const startIndex = (page - 1) * parseInt(perPage);
    const endIndex = startIndex + parseInt(perPage);
    const paginatedLogs = filteredLogs.slice(startIndex, endIndex);
    
    return {
        data: paginatedLogs,
        pagination: {
            current_page: parseInt(page),
            per_page: parseInt(perPage),
            total: filteredLogs.length,
            last_page: Math.ceil(filteredLogs.length / parseInt(perPage)),
            from: startIndex + 1,
            to: Math.min(endIndex, filteredLogs.length)
        }
    };
}

// Render audit logs table
function renderAuditTable(data) {
    const tbody = document.getElementById('auditTableBody');
    
    if (data.data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fas fa-history fa-3x text-muted mb-2"></i>
                    <p class="text-muted">Tidak ada audit log yang ditemukan</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = data.data.map(log => `
        <tr>
            <td>${log.id}</td>
            <td>
                <small class="text-muted">
                    ${formatDateTime(log.created_at)}
                </small>
            </td>
            <td>
                ${log.user ? `
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-1" style="width: 24px; height: 24px; background: linear-gradient(45deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 10px;">
                            ${log.user.name.charAt(0).toUpperCase()}
                        </div>
                        <small>${log.user.name}</small>
                    </div>
                ` : '<span class="text-muted">System</span>'}
            </td>
            <td>
                <span class="badge ${getActionBadgeClass(log.action)}">${log.action}</span>
            </td>
            <td><code class="small">${log.table_name}</code></td>
            <td>${log.record_id || '-'}</td>
            <td>
                <button class="btn btn-sm btn-outline-info" onclick="showDetail(${JSON.stringify(log).replace(/"/g, '&quot;')})" title="Lihat Detail">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
            <td><small class="text-muted">${log.ip_address}</small></td>
            <td>
                <button class="btn btn-sm btn-outline-secondary" onclick="showUserAgent('${log.user_agent}')" title="User Agent">
                    <i class="fas fa-info"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Get action badge class
function getActionBadgeClass(action) {
    switch(action) {
        case 'LOGIN': return 'bg-success';
        case 'LOGOUT': return 'bg-secondary';
        case 'LOGIN_FAILED': return 'bg-danger';
        case 'REGISTER': return 'bg-primary';
        case 'PASSWORD_RESET': return 'bg-warning';
        case 'CREATE': return 'bg-info';
        case 'UPDATE': return 'bg-warning';
        case 'DELETE': return 'bg-danger';
        case 'VIEW': return 'bg-light text-dark';
        default: return 'bg-secondary';
    }
}

// Format date time
function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString('id-ID');
}

// Show detail modal
function showDetail(log) {
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    const detailContainer = document.getElementById('auditDetail');
    
    let detailHTML = `
        <div class="row mb-3">
            <div class="col-sm-3"><strong>ID:</strong></div>
            <div class="col-sm-9">${log.id}</div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-3"><strong>User:</strong></div>
            <div class="col-sm-9">${log.user ? log.user.name : 'System'}</div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-3"><strong>Aksi:</strong></div>
            <div class="col-sm-9"><span class="badge ${getActionBadgeClass(log.action)}">${log.action}</span></div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-3"><strong>Tabel:</strong></div>
            <div class="col-sm-9"><code>${log.table_name}</code></div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-3"><strong>Record ID:</strong></div>
            <div class="col-sm-9">${log.record_id || '-'}</div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-3"><strong>Waktu:</strong></div>
            <div class="col-sm-9">${formatDateTime(log.created_at)}</div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-3"><strong>IP Address:</strong></div>
            <div class="col-sm-9">${log.ip_address}</div>
        </div>
    `;
    
    if (log.old_values) {
        detailHTML += `
            <div class="row mb-3">
                <div class="col-sm-3"><strong>Data Lama:</strong></div>
                <div class="col-sm-9"><pre class="small bg-light p-2 rounded">${JSON.stringify(log.old_values, null, 2)}</pre></div>
            </div>
        `;
    }
    
    if (log.new_values) {
        detailHTML += `
            <div class="row mb-3">
                <div class="col-sm-3"><strong>Data Baru:</strong></div>
                <div class="col-sm-9"><pre class="small bg-light p-2 rounded">${JSON.stringify(log.new_values, null, 2)}</pre></div>
            </div>
        `;
    }
    
    detailHTML += `
        <div class="row mb-3">
            <div class="col-sm-3"><strong>User Agent:</strong></div>
            <div class="col-sm-9"><small class="text-muted">${log.user_agent}</small></div>
        </div>
    `;
    
    detailContainer.innerHTML = detailHTML;
    modal.show();
}

// Show user agent
function showUserAgent(userAgent) {
    alert('User Agent:\n' + userAgent);
}

// Apply filters
function applyFilters() {
    loadAuditLogs(1);
}

// Reset filters
function resetFilters() {
    document.getElementById('userFilter').value = '';
    document.getElementById('actionFilter').value = '';
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';
    loadAuditLogs(1);
}

// Refresh audit logs
function refreshAuditLogs() {
    loadAuditLogs(currentPage);
}

// Export audit logs
function exportAuditLogs() {
    // Implementasi export - bisa ke PDF, Excel, atau CSV
    alert('Export functionality akan diimplementasikan');
}

// Render pagination
function renderPagination(pagination) {
    const paginationEl = document.getElementById('pagination');
    const recordInfo = document.getElementById('recordInfo');
    
    // Record info
    recordInfo.textContent = `Menampilkan ${pagination.from}-${pagination.to} dari ${pagination.total} audit logs`;
    
    // Pagination buttons
    let paginationHTML = '';
    
    // Previous button
    if (pagination.current_page > 1) {
        paginationHTML += `<li class="page-item"><a class="page-link" onclick="loadAuditLogs(${pagination.current_page - 1})">‹</a></li>`;
    } else {
        paginationHTML += `<li class="page-item disabled"><span class="page-link">‹</span></li>`;
    }
    
    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, pagination.current_page - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(pagination.last_page, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === pagination.current_page) {
            paginationHTML += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            paginationHTML += `<li class="page-item"><a class="page-link" onclick="loadAuditLogs(${i})">${i}</a></li>`;
        }
    }
    
    // Next button
    if (pagination.current_page < pagination.last_page) {
        paginationHTML += `<li class="page-item"><a class="page-link" onclick="loadAuditLogs(${pagination.current_page + 1})">›</a></li>`;
    } else {
        paginationHTML += `<li class="page-item disabled"><span class="page-link">›</span></li>`;
    }
    
    paginationEl.innerHTML = paginationHTML;
}

// Show alert
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
</script>
@endsection
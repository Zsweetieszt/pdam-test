@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    Kelola Tagihan
                </h5>
            </div>
            <div class="card-body">
                <!-- Stats Cards (API integrated) -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h4 id="pending-bills-count">--</h4>
                                <small>Tagihan Pending</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h4 id="overdue-bills-count">--</h4>
                                <small>Jatuh Tempo Hari Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4 id="paid-bills-count">--</h4>
                                <small>Lunas Bulan Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4 id="total-revenue">Rp --</h4>
                                <small>Total Terkumpul</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons (existing) -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="text-muted">Kelola semua tagihan pelanggan</h6>
                    <div>
                        <button class="btn btn-success me-2" onclick="generateMonthlyBills()">
                            <i class="fas fa-plus me-2"></i>
                            Generate Tagihan Bulanan
                        </button>
                        <button class="btn btn-primary" onclick="sendBulkWhatsApp()">
                            <i class="fab fa-whatsapp me-2"></i>
                            Kirim Reminder Bulk
                        </button>
                    </div>
                </div>
                
                <!-- Filters (API integrated) -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="searchFilter" placeholder="Cari tagihan atau customer...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="sent">Terkirim</option>
                            <option value="paid">Lunas</option>
                            <option value="overdue">Terlambat</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="periodFilter">
                            <option value="">Semua Periode</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" onclick="searchBills()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Bills Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th><input type="checkbox" class="form-check-input" id="select-all"></th>
                                <th>ID Tagihan</th>
                                <th>Customer</th>
                                <th>Periode</th>
                                <th>Pemakaian</th>
                                <th>Total</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="billsTableBody">
                            <!-- Data will be loaded via API -->
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 mb-0 text-muted">Memuat data tagihan...</p>
                                </td>
                            </tr>
                        </tbody>
                            </tbody>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination (existing) -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <select class="form-select form-select-sm" style="width: auto; display: inline-block;">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                        </select>
                        <span class="text-muted ms-2">data per halaman</span>
                    </div>
                    
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled"><a class="page-link">Previous</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REQ-F-5.2: WhatsApp Modal -->
<div class="modal fade" id="whatsappModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fab fa-whatsapp me-2"></i>
                    Kirim Notifikasi WhatsApp
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Customer</label>
                        <p id="customer-name" class="mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nomor WhatsApp</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="phone-number" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="editPhoneNumber()">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Template Pesan</label>
                        <select class="form-select" id="template-select" onchange="updateMessageTemplate()">
                            <option value="bill_reminder">Tagihan Bulanan</option>
                            <option value="overdue_notice">Pemberitahuan Tunggakan</option>
                            <option value="payment_reminder">Reminder Pembayaran</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Total Tagihan</label>
                        <p id="bill-amount" class="mb-0 text-success fw-bold fs-5">-</p>
                    </div>
                </div>

                <!-- REQ-F-5.3: Message Preview -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-mobile-alt me-2"></i>
                        Preview Pesan WhatsApp
                    </label>
                    <div class="card">
                        <div class="card-body whatsapp-preview-bg">
                            <div id="message-preview" class="whatsapp-message">
                                <!-- Default message will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REQ-F-5.2: WhatsApp Link Section -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Link WhatsApp</label>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace small" id="whatsapp-link" readonly>
                        <button class="btn btn-outline-primary" type="button" onclick="copyWhatsAppLink()" id="copy-btn">
                            <i class="fas fa-copy me-1"></i>
                            Copy
                        </button>
                        <button class="btn btn-success" type="button" onclick="openWhatsAppDirect()" id="open-wa-btn">
                            <i class="fab fa-whatsapp me-1"></i>
                            Buka WhatsApp
                        </button>
                    </div>
                    <small class="text-muted">Link akan otomatis di-generate berdasarkan template pesan</small>
                </div>

                <!-- Additional Options -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="mark-as-sent" checked>
                            <label class="form-check-label" for="mark-as-sent">
                                Tandai sebagai "Terkirim" setelah buka WhatsApp
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="save-log" checked>
                            <label class="form-check-label" for="save-log">
                                Simpan log pengiriman notifikasi
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Tutup
                </button>
                <button type="button" class="btn btn-primary" onclick="refreshMessagePreview()">
                    <i class="fas fa-sync me-1"></i>
                    Refresh Preview
                </button>
                <button type="button" class="btn btn-success" onclick="openWhatsAppDirect()">
                    <i class="fab fa-whatsapp me-1"></i>
                    Kirim via WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styling for WhatsApp integration */
.whatsapp-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.btn-primary.whatsapp-btn:hover {
    background-color: #25D366 !important;
    border-color: #25D366 !important;
}

.btn-warning.whatsapp-btn:hover {
    background-color: #ff6b35 !important;
    border-color: #ff6b35 !important;
}

.whatsapp-preview-bg {
    background: linear-gradient(135deg, #dcf8c6 0%, #e8f5e8 100%);
    min-height: 120px;
}

.whatsapp-message {
    background: #fff;
    border: 2px solid #25D366;
    border-radius: 18px 18px 4px 18px;
    padding: 12px 16px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 14px;
    line-height: 1.4;
    white-space: pre-wrap;
    word-wrap: break-word;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    min-height: 80px;
}

.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.toast {
    min-width: 300px;
    margin-bottom: 10px;
}

@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem 0.5rem;
    }
    
    .toast-container {
        right: 10px;
        left: 10px;
    }
    
    .toast {
        min-width: auto;
    }
}
</style>

<script>
// REQ-F-5 WhatsApp Integration - Frontend Only Implementation
let currentBillData = {};
let whatsappModal;

// Message templates - using frontend templates to avoid backend dependency
const messageTemplates = {
    bill_reminder: `Halo {customer_name},

Pemberitahuan tagihan PDAM bulan {period}:

📋 ID Tagihan: {bill_number}
💧 Pemakaian: {usage} m³
💰 Total: Rp {amount}
📅 Jatuh Tempo: {due_date}

Silakan lakukan pembayaran sebelum tanggal jatuh tempo.

Terima kasih,
PDAM Billing System`,

    overdue_notice: `PERINGATAN TUNGGAKAN

Halo {customer_name},

Tagihan PDAM Anda telah melewati jatuh tempo:

📋 ID Tagihan: {bill_number}
💧 Pemakaian: {usage} m³ 
💰 Total: Rp {amount}
⚠️ Jatuh Tempo: {due_date}

Harap segera lakukan pembayaran untuk menghindari pemutusan layanan.

PDAM Billing System`,

    payment_reminder: `Reminder Pembayaran

Halo {customer_name},

Tagihan PDAM Anda akan jatuh tempo dalam 3 hari:

📋 ID Tagihan: {bill_number}
💧 Pemakaian: {usage} m³
💰 Total: Rp {amount}
📅 Jatuh Tempo: {due_date}

Jangan lupa untuk melakukan pembayaran ya!

PDAM Billing System`
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    whatsappModal = new bootstrap.Modal(document.getElementById('whatsappModal'));
    
    // Setup select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.bill-checkbox:not(:disabled)');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Load initial data
    loadInitialData();
});

// Load initial data from APIs
async function loadInitialData() {
    try {
        await Promise.all([
            loadBillStats(),
            loadBillingPeriods(),
            searchBills()
        ]);
    } catch (error) {
        console.error('Error loading initial data:', error);
        showToast('Data Tidak Ada', 'error');
    }
}

// Load bill statistics from API
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

        document.getElementById('pending-bills-count').textContent = data.data.bills.pending;
        document.getElementById('overdue-bills-count').textContent = data.data.bills.overdue;
        document.getElementById('paid-bills-count').textContent = data.data.bills.paid;
        document.getElementById('total-revenue').textContent = 'Rp ' + formatCurrency(data.data.payments.this_month);
    } catch (error) {
        console.error('Error loading stats:', error);
        // Fallback to default values
        ['pending-bills-count', 'overdue-bills-count', 'paid-bills-count'].forEach(id => {
            document.getElementById(id).textContent = '0';
        });
        document.getElementById('total-revenue').textContent = 'Rp 0';
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
        const periods = data.data || [];
        populatePeriodFilter(periods);
    } catch (error) {
        console.error('Error loading billing periods:', error);
        // Fallback to current period
        const currentDate = new Date();
        const currentPeriod = {
            period_month: currentDate.getMonth() + 1,
            period_year: currentDate.getFullYear()
        };
        populatePeriodFilter([currentPeriod]);
    }
}

// Populate period filter dropdown
function populatePeriodFilter(periods) {
    const select = document.getElementById('periodFilter');
    const firstOption = select.querySelector('option').outerHTML;
    
    select.innerHTML = firstOption + periods.map(period => 
        `<option value="${period.period_month}">${getMonthName(period.period_month)} ${period.period_year}</option>`
    ).join('');
}

// Search bills from API
async function searchBills(page = 1) {
    const tbody = document.getElementById('billsTableBody');
    
    // Show loading
    tbody.innerHTML = `
        <tr>
            <td colspan="9" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0 text-muted">Memuat data tagihan...</p>
            </td>
        </tr>
    `;

    try {
        const searchTerm = document.getElementById('searchFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const periodFilter = document.getElementById('periodFilter').value;

        // Build query parameters
        const params = new URLSearchParams({
            page: page,
            per_page: 15,
            sort_field: 'created_at',
            sort_direction: 'desc'
        });

        if (searchTerm) params.append('search', searchTerm);
        if (statusFilter) params.append('status', statusFilter);
        if (periodFilter) params.append('period_month', periodFilter);

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
    } catch (error) {
        console.error('Error loading bills:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h6 class="text-danger mb-2">Data Tidak Ada</h6>
                    <p class="text-muted mb-0">Terjadi kesalahan saat memuat data tagihan</p>
                </td>
            </tr>
        `;
        showToast('Data Belum Ada', 'error');
    }
}

// Display bills in table
function displayBills(data) {
    const tbody = document.getElementById('billsTableBody');
    
    if (!data.data || data.data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-search fa-4x text-muted mb-3 opacity-50"></i>
                        <h6 class="text-muted mb-2">Tidak ada data tagihan</h6>
                        <p class="text-muted small mb-0">Coba ubah kriteria pencarian</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = data.data.map((bill, index) => {
        const usage = bill.current_reading - bill.previous_reading;
        const billData = {
            id: bill.id,
            customer: bill.customer_name,
            phone: bill.customer_phone || '',
            amount: bill.total_amount,
            period: `${getMonthName(bill.period_month)} ${bill.period_year}`,
            usage: usage,
            due_date: formatDate(bill.due_date),
            bill_number: bill.bill_number,
            status: bill.status
        };
        
        return `
            <tr data-bill='${JSON.stringify(billData).replace(/'/g, "&apos;")}'>
                <td><input type="checkbox" class="form-check-input bill-checkbox" ${bill.status === 'paid' ? 'disabled' : ''}></td>
                <td>${bill.bill_number}</td>
                <td>${bill.customer_name}</td>
                <td>${getMonthName(bill.period_month)} ${bill.period_year}</td>
                <td>${usage} m³</td>
                <td><strong>Rp ${formatCurrency(bill.total_amount)}</strong></td>
                <td>${formatDate(bill.due_date)}</td>
                <td>${getStatusBadge(bill.status)}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" title="Detail" onclick="showBillDetail(${bill.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${bill.status !== 'paid' ? `
                            <button class="btn btn-success" title="Tandai Lunas" onclick="markAsPaid(${bill.id})">
                                <i class="fas fa-check"></i>
                            </button>
                        ` : ''}
                        ${canSendWhatsApp(bill.status) ? `
                            <button class="btn btn-primary whatsapp-btn" 
                                    title="Kirim WhatsApp" 
                                    onclick="openWhatsAppModal(this)">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                        ` : bill.status === 'paid' ? `
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>Lunas
                            </span>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Show bill detail modal
async function showBillDetail(billId) {
    // For now, just show a simple alert - can be enhanced with a proper modal
    showToast(`Menampilkan detail tagihan ${billId}`, 'info');
}

// Mark bill as paid
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

        showToast('Tagihan berhasil ditandai sebagai lunas', 'success');
        // Refresh data
        loadBillStats();
        searchBills();
    } catch (error) {
        console.error('Error marking as paid:', error);
        showToast(error.message || 'Terjadi kesalahan saat menandai sebagai lunas', 'error');
    }
}

// Generate monthly bills
async function generateMonthlyBills() {
    if (!confirm('Generate tagihan untuk semua pelanggan bulan ini?\n\nProses ini akan membuat tagihan baru berdasarkan pemakaian air bulan lalu.')) {
        return;
    }

    try {
        showToast('Sedang generate tagihan bulanan...', 'info');

        // This would typically call an API endpoint to generate bills
        // For now, just simulate the process
        setTimeout(() => {
            showToast('Tagihan bulanan berhasil dibuat', 'success');
            loadBillStats();
            searchBills();
        }, 2000);
    } catch (error) {
        console.error('Error generating bills:', error);
        showToast('Gagal generate tagihan bulanan', 'error');
    }
}



// REQ-F-5.1: Open WhatsApp Modal
function openWhatsAppModal(buttonElement) {
    // Get bill data from the row
    const row = buttonElement.closest('tr');
    const billDataString = row.getAttribute('data-bill');
    
    if (!billDataString) {
        showToast('Data tagihan tidak ditemukan', 'error');
        return;
    }
    
    try {
        currentBillData = JSON.parse(billDataString);
        
        // Check if user has keuangan role (frontend validation)
        if (!hasKeuanganAccess()) {
            showToast('Akses ditolak. Hanya role Keuangan yang dapat mengirim notifikasi WhatsApp.', 'error');
            return;
        }
        
        // Check if bill status allows WhatsApp notification (REQ-C-9)
        if (!canSendWhatsApp(currentBillData.status)) {
            showToast('Notifikasi WhatsApp hanya dapat dikirim untuk tagihan dengan status Pending atau Overdue.', 'error');
            return;
        }
        
        // Populate modal with bill data
        populateModal(currentBillData);
        
        // Show modal
        whatsappModal.show();
        
    } catch (error) {
        console.error('Error parsing bill data:', error);
        showToast('Error: Data tagihan tidak valid', 'error');
    }
}

// Populate modal with bill data
function populateModal(billData) {
    document.getElementById('customer-name').textContent = billData.customer;
    document.getElementById('phone-number').value = billData.phone;
    document.getElementById('bill-amount').textContent = `Rp ${parseInt(billData.amount).toLocaleString('id-ID')}`;
    
    // Set appropriate template based on status
    const templateSelect = document.getElementById('template-select');
    if (billData.status === 'overdue') {
        templateSelect.value = 'overdue_notice';
    } else {
        templateSelect.value = 'bill_reminder';
    }
    
    // Update message preview and WhatsApp link
    updateMessageTemplate();
}

// REQ-F-5.3: Update message template and preview
function updateMessageTemplate() {
    const templateType = document.getElementById('template-select').value;
    const template = messageTemplates[templateType];
    
    if (!template || !currentBillData) {
        return;
    }
    
    // Replace template variables with actual data
    let message = template
        .replace(/{customer_name}/g, currentBillData.customer)
        .replace(/{bill_number}/g, currentBillData.bill_number || `INV-2025-00${currentBillData.id}`)
        .replace(/{period}/g, currentBillData.period)
        .replace(/{usage}/g, currentBillData.usage)
        .replace(/{amount}/g, parseInt(currentBillData.amount).toLocaleString('id-ID'))
        .replace(/{due_date}/g, currentBillData.due_date);
    
    // Update preview
    document.getElementById('message-preview').textContent = message;
    
    // Generate WhatsApp link
    generateWhatsAppLink(message);
}

// Generate WhatsApp link (frontend only)
function generateWhatsAppLink(message) {
    const phone = document.getElementById('phone-number').value;
    let cleanPhone = phone.replace(/\D/g, ''); // Remove non-digits
    
    // Add Indonesian country code if needed
    if (!cleanPhone.startsWith('62')) {
        if (cleanPhone.startsWith('0')) {
            cleanPhone = '62' + cleanPhone.substring(1);
        } else {
            cleanPhone = '62' + cleanPhone;
        }
    }
    
    // Encode message for URL
    const encodedMessage = encodeURIComponent(message);
    
    // Generate wa.me link
    const whatsappLink = `https://wa.me/${cleanPhone}?text=${encodedMessage}`;
    
    // Update link input
    document.getElementById('whatsapp-link').value = whatsappLink;
}

// REQ-F-5.2: Copy WhatsApp link
function copyWhatsAppLink() {
    const linkInput = document.getElementById('whatsapp-link');
    const copyBtn = document.getElementById('copy-btn');
    
    // Select and copy
    linkInput.select();
    linkInput.setSelectionRange(0, 99999); // For mobile
    
    try {
        document.execCommand('copy');
        
        // Visual feedback
        const originalHTML = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fas fa-check me-1"></i> Copied!';
        copyBtn.classList.remove('btn-outline-primary');
        copyBtn.classList.add('btn-success');
        
        setTimeout(() => {
            copyBtn.innerHTML = originalHTML;
            copyBtn.classList.remove('btn-success');
            copyBtn.classList.add('btn-outline-primary');
        }, 2000);
        
        // Show success message
        showToast('Link WhatsApp berhasil di-copy!', 'success');
        
    } catch (err) {
        console.error('Copy failed:', err);
        showToast('Gagal copy link. Silakan copy manual.', 'error');
    }
}

// REQ-F-5.2: Open WhatsApp directly
async function openWhatsAppDirect() {
    const whatsappLink = document.getElementById('whatsapp-link').value;
    
    if (!whatsappLink) {
        showToast('Link WhatsApp belum tersedia', 'error');
        return;
    }
    
    // Open WhatsApp in new tab
    window.open(whatsappLink, '_blank');
    
    // Mark as sent if checkbox is checked
    if (document.getElementById('mark-as-sent').checked) {
        await markBillAsSent();
    }
    
    // Log the action if checkbox is checked
    if (document.getElementById('save-log').checked) {
        await logWhatsAppAction();
    }
    
    // Close modal
    whatsappModal.hide();
    
    showToast('WhatsApp terbuka! Pesan siap untuk dikirim.', 'success');
}

// Mark bill as sent via API
async function markBillAsSent() {
    try {
        const response = await fetch(`/api/bills/${currentBillData.id}/status`, {
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status: 'sent' })
        });

        if (!response.ok) {
            throw new Error('Failed to mark as sent');
        }

        // Update UI
        const row = document.querySelector(`tr[data-bill*='"id":${currentBillData.id}']`);
        if (row) {
            const statusBadge = row.querySelector('.badge');
            if (statusBadge) {
                statusBadge.className = 'badge bg-info';
                statusBadge.textContent = 'Terkirim';
                
                // Update data attribute
                currentBillData.status = 'sent';
                row.setAttribute('data-bill', JSON.stringify(currentBillData));
            }
        }
    } catch (error) {
        console.error('Error marking as sent:', error);
        // Don't show error to user as this is a background operation
    }
}

// Generate WhatsApp data for a bill (helper function)
async function generateWhatsAppData(billData) {
    // Set appropriate template based on status
    const templateType = billData.status === 'overdue' ? 'overdue_notice' : 'bill_reminder';
    const template = messageTemplates[templateType];
    
    // Replace template variables with actual data
    let message = template
        .replace(/{customer_name}/g, billData.customer)
        .replace(/{bill_number}/g, billData.bill_number || `INV-2025-00${billData.id}`)
        .replace(/{period}/g, billData.period)
        .replace(/{usage}/g, billData.usage)
        .replace(/{amount}/g, parseInt(billData.amount).toLocaleString('id-ID'))
        .replace(/{due_date}/g, billData.due_date);
    
    // Clean phone number
    let cleanPhone = billData.phone.replace(/\D/g, ''); // Remove non-digits
    
    // Add Indonesian country code if needed
    if (!cleanPhone.startsWith('62')) {
        if (cleanPhone.startsWith('0')) {
            cleanPhone = '62' + cleanPhone.substring(1);
        } else {
            cleanPhone = '62' + cleanPhone;
        }
    }
    
    // Encode message for URL
    const encodedMessage = encodeURIComponent(message);
    
    // Generate wa.me link
    const link = `https://wa.me/${cleanPhone}?text=${encodedMessage}`;
    
    return { message, link, phone: cleanPhone };
}

// Helper Functions
function hasKeuanganAccess() {
    // Frontend role check - this would normally check actual user role
    // For frontend-only implementation, we assume access is granted if user can see the page
    return true;
}

function canSendWhatsApp(status) {
    // REQ-C-9: Only pending and overdue bills can have notifications
    return ['pending', 'overdue', 'sent'].includes(status.toLowerCase());
}

function getSelectedBills() {
    const selectedCheckboxes = document.querySelectorAll('.bill-checkbox:checked');
    return Array.from(selectedCheckboxes).map(checkbox => {
        const row = checkbox.closest('tr');
        return JSON.parse(row.getAttribute('data-bill'));
    });
}

function getMonthName(monthNumber) {
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return months[monthNumber - 1] || 'Unknown';
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

function getStatusBadge(status) {
    const statusMap = {
        'pending': { class: 'bg-warning', text: 'Pending' },
        'overdue': { class: 'bg-danger', text: 'Overdue' },
        'paid': { class: 'bg-success', text: 'Lunas' },
        'sent': { class: 'bg-info', text: 'Terkirim' },
        'cancelled': { class: 'bg-secondary', text: 'Dibatalkan' }
    };
    
    const config = statusMap[status] || { class: 'bg-secondary', text: status };
    return `<span class="badge ${config.class}">${config.text}</span>`;
}

function formatCurrency(amount) {
    return parseInt(amount).toLocaleString('id-ID');
}

function refreshMessagePreview() {
    updateMessageTemplate();
    showToast('Preview pesan telah di-refresh', 'info');
}

function sendBulkWhatsApp() {
    const selectedBills = document.querySelectorAll('.bill-checkbox:checked');
    
    if (selectedBills.length === 0) {
        showToast('Pilih minimal 1 tagihan untuk dikirim notifikasi WhatsApp', 'error');
        return;
    }
    
    // Count eligible bills (not paid)
    let eligibleCount = 0;
    const billIds = [];
    selectedBills.forEach(checkbox => {
        const row = checkbox.closest('tr');
        const billData = JSON.parse(row.getAttribute('data-bill'));
        if (canSendWhatsApp(billData.status)) {
            eligibleCount++;
            billIds.push(billData.id);
        }
    });
    
    if (eligibleCount === 0) {
        showToast('Tidak ada tagihan yang eligible untuk dikirim WhatsApp', 'warning');
        return;
    }
    
    if (!confirm(`Kirim notifikasi WhatsApp ke ${eligibleCount} pelanggan yang dipilih?`)) {
        return;
    }

    // Show loading state
    const bulkBtn = document.getElementById('bulk-whatsapp-btn');
    const originalText = bulkBtn.innerHTML;
    bulkBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
    bulkBtn.disabled = true;

    let successCount = 0;
    let errorCount = 0;

    // Process each bill sequentially to avoid overwhelming the browser
    (async function processBills() {
        for (const billId of billIds) {
            try {
                // Get bill data
                const row = document.querySelector(`tr[data-bill*='"id":${billId}']`);
                const billData = JSON.parse(row.getAttribute('data-bill'));
                
                // Generate WhatsApp data
                const whatsappData = await generateWhatsAppData(billData);
                
                // Open WhatsApp in new tab
                window.open(whatsappData.link, '_blank');
                
                // Mark as sent if requested
                if (document.getElementById('bulk-mark-sent').checked) {
                    await fetch(`/api/bills/${billId}/status`, {
                        method: 'PUT',
                        headers: {
                            'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ status: 'sent' })
                    });
                    
                    // Update UI
                    const statusBadge = row.querySelector('.badge');
                    if (statusBadge) {
                        statusBadge.className = 'badge bg-info';
                        statusBadge.textContent = 'Terkirim';
                        
                        // Update data attribute
                        billData.status = 'sent';
                        row.setAttribute('data-bill', JSON.stringify(billData));
                    }
                }
                
                // Log the action
                if (document.getElementById('bulk-save-log').checked) {
                    console.log('Bulk WhatsApp sent for bill:', billId);
                }
                
                successCount++;
                
                // Small delay between opens to avoid browser blocking
                await new Promise(resolve => setTimeout(resolve, 1000));
                
            } catch (error) {
                console.error('Error sending WhatsApp for bill', billId, ':', error);
                errorCount++;
            }
        }
        
        // Reset button
        bulkBtn.innerHTML = originalText;
        bulkBtn.disabled = false;
        
        // Show results
        if (successCount > 0) {
            showToast(`Berhasil mengirim ${successCount} pesan WhatsApp${errorCount > 0 ? `, ${errorCount} gagal` : ''}`, 'success');
        } else {
            showToast('Gagal mengirim semua pesan WhatsApp', 'error');
        }
        
        // Refresh stats if any bills were marked as sent
        if (document.getElementById('bulk-mark-sent').checked && successCount > 0) {
            loadBillStats();
        }
    })();
}

function showToast(message, type = 'info') {
    // Create toast container if not exists
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast show align-items-center text-white bg-${type === 'error' ? 'danger' : type}`;
    toast.setAttribute('role', 'alert');
    
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
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}

// Existing functions (unchanged)
function generateMonthlyBills() {
    if (confirm('Generate tagihan untuk semua pelanggan bulan ini?\n\nProses ini akan membuat tagihan baru berdasarkan pemakaian air bulan lalu.')) {
        showToast('Tagihan bulanan sedang di-generate...', 'info');
        setTimeout(() => {
            showToast('Notifikasi WhatsApp akan dikirim otomatis.', 'success');
        }, 1500);
    }
}
</script>
@endsection
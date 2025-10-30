@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>
                    Manajemen Pembayaran
                </h5>
            </div>
            <div class="card-body">
                <!-- Stats Cards (API integrated) -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h4 id="pending-payments-count">--</h4>
                                <small>Pembayaran Pending</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4 id="verified-payments-count">--</h4>
                                <small>Terverifikasi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h4 id="rejected-payments-count">--</h4>
                                <small>Ditolak</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4 id="total-revenue">Rp --</h4>
                                <small>Total Bulan Ini</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="text-muted">Kelola pembayaran dan verifikasi transaksi</h6>
                    <div>
                        <button class="btn btn-success me-2" onclick="openAddPaymentModal()">
                            <i class="fas fa-plus me-2"></i>
                            Input Pembayaran Baru
                        </button>
                        <button class="btn btn-primary" onclick="refreshPaymentHistory()">
                            <i class="fas fa-sync me-2"></i>
                            Refresh Data
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="Cari pembayaran..." id="search-payment">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="status-filter">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="verified">Terverifikasi</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="method-filter">
                            <option value="">Semua Metode</option>
                            <option value="transfer">Transfer</option>
                            <option value="cash">Tunai</option>
                            <option value="online">Online</option>
                            <option value="mobile_banking">Mobile Banking</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="date-from" title="Tanggal Dari">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="date-to" title="Tanggal Sampai">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary w-100" onclick="filterPayments()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Payments Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No. Pembayaran</th>
                                <th>Pelanggan</th>
                                <th>No. Tagihan</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Bukti</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="payments-table-body">
                            <!-- Data will be loaded via AJAX -->
                            <tr>
                                <td colspan="9" class="text-center py-4">
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
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <select class="form-select form-select-sm" style="width: auto; display: inline-block;" id="per-page-select">
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="text-muted ms-2">data per halaman</span>
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="pagination-controls">
                            <!-- Pagination will be generated dynamically -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REQ-F-6.1: Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Input Pembayaran Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilih Tagihan</label>
                            <select class="form-select" id="bill_id" name="bill_id" required>
                                <option value="">Pilih tagihan yang akan dibayar...</option>
                            </select>
                            <div class="invalid-feedback" id="bill_id_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jumlah Pembayaran</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       placeholder="0" min="0" step="1000" required>
                            </div>
                            <div class="invalid-feedback" id="amount_error"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Metode Pembayaran</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">Pilih metode...</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="cash">Tunai</option>
                                <option value="online">Pembayaran Online</option>
                                <option value="mobile_banking">Mobile Banking</option>
                            </select>
                            <div class="invalid-feedback" id="payment_method_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Pembayaran</label>
                            <input type="date" class="form-control" id="payment_date" name="payment_date" readonly required>
                            <small class="text-muted">Otomatis diisi tanggal hari ini</small>
                            <div class="invalid-feedback" id="payment_date_error"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nomor Referensi</label>
                            <input type="text" class="form-control" id="reference_number" name="reference_number" 
                                   placeholder="Nomor transaksi/referensi (opsional)">
                            <div class="invalid-feedback" id="reference_number_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bukti Pembayaran</label>
                            <input type="file" class="form-control" id="payment_proof" name="payment_proof" 
                                   accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, JPEG. Maksimal 2MB.</small>
                            <div class="invalid-feedback" id="payment_proof_error"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Catatan tambahan (opsional)"></textarea>
                        <div class="invalid-feedback" id="notes_error"></div>
                    </div>

                    <!-- Bill Details Preview -->
                    <div id="bill-details" class="card bg-light" style="display: none;">
                        <div class="card-body">
                            <h6 class="card-title">Detail Tagihan</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Pelanggan:</strong> <span id="bill-customer">-</span></p>
                                    <p class="mb-1"><strong>Periode:</strong> <span id="bill-period">-</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Total Tagihan:</strong> <span id="bill-total" class="text-success fw-bold">-</span></p>
                                    <p class="mb-1"><strong>Status:</strong> <span id="bill-status">-</span></p>
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
                <button type="button" class="btn btn-success" onclick="submitPayment()">
                    <i class="fas fa-save me-1"></i>
                    Simpan Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<!-- REQ-F-6.2: Payment Confirmation Modal -->
<div class="modal fade" id="paymentConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Konfirmasi Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading">Periksa kembali data pembayaran:</h6>
                    <div id="payment-confirmation-details">
                        <!-- Details will be populated here -->
                    </div>
                </div>
                <p class="mb-0">Apakah Anda yakin data pembayaran sudah benar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Periksa Ulang
                </button>
                <button type="button" class="btn btn-success" onclick="confirmPaymentSubmission()">
                    <i class="fas fa-check me-1"></i>
                    Ya, Simpan Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<!-- REQ-F-6.3: Payment Detail Modal -->
<div class="modal fade" id="paymentDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-receipt me-2"></i>
                    Detail Pembayaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="payment-detail-content">
                    <!-- Payment details will be loaded here -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 mb-0 text-muted">Memuat detail pembayaran...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Tutup
                </button>
                <div id="payment-actions">
                    <!-- Action buttons will be added here dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verify Payment Modal -->
<div class="modal fade" id="verifyPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>
                    Verifikasi Pembayaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="verifyPaymentForm">
                    <input type="hidden" id="verify_payment_id" name="payment_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Verifikasi</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" value="verified" id="status_verified">
                            <label class="form-check-label text-success" for="status_verified">
                                <i class="fas fa-check-circle me-1"></i>
                                Terverifikasi (Pembayaran Valid)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" value="rejected" id="status_rejected">
                            <label class="form-check-label text-danger" for="status_rejected">
                                <i class="fas fa-times-circle me-1"></i>
                                Ditolak (Pembayaran Tidak Valid)
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Verifikasi</label>
                        <textarea class="form-control" name="verification_notes" rows="3" 
                                  placeholder="Masukkan catatan verifikasi (opsional)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Batal
                </button>
                <button type="button" class="btn btn-success" onclick="submitVerification()">
                    <i class="fas fa-check me-1"></i>
                    Simpan Verifikasi
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.payment-status-pending { color: #f39c12; }
.payment-status-verified { color: #27ae60; }
.payment-status-rejected { color: #e74c3c; }

.payment-method-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.proof-thumbnail {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
    cursor: pointer;
}

.proof-thumbnail:hover {
    opacity: 0.8;
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem 0.5rem;
    }
}
</style>

<script>
// REQ-F-6 Payment Management - Frontend Implementation
let paymentData = [];
let currentPage = 1;
let perPage = 15;
let currentFilters = {};
let unpaidBills = [];
let paymentConfirmData = {};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializePaymentPage();
    loadUnpaidBills();
    loadPaymentHistory();
    
    // Set default payment date to today
    document.getElementById('payment_date').value = new Date().toISOString().split('T')[0];
    
    // Setup event listeners
    document.getElementById('bill_id').addEventListener('change', onBillSelectionChange);
    document.getElementById('per-page-select').addEventListener('change', function() {
        perPage = parseInt(this.value);
        currentPage = 1;
        loadPaymentHistory();
    });
});

// Initialize payment page
function initializePaymentPage() {
    console.log('Payment page initialized with API integration');
}

// Load unpaid bills from API
async function loadUnpaidBills() {
    try {
        const response = await fetch('/api/bills?status=pending&status=overdue&status=sent&per_page=50', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        displayUnpaidBills(data.data || []);
    } catch (error) {
        console.error('Error loading unpaid bills:', error);
        showToast('Data Belum Ada', 'error');
        // Fallback to empty list
        displayUnpaidBills([]);
    }
}

// Display unpaid bills in select dropdown
function displayUnpaidBills(bills) {
    const billSelect = document.getElementById('bill_id');
    billSelect.innerHTML = '<option value="">Pilih tagihan yang akan dibayar...</option>';
    
    bills.forEach(bill => {
        const customerName = bill.customer_name || 'Unknown';
        const amount = formatRupiah(bill.total_amount || 0);
        const period = `${getMonthName(bill.period_month)} ${bill.period_year}`;
        
        const option = document.createElement('option');
        option.value = bill.id;
        option.textContent = `${bill.bill_number} - ${customerName} (${amount}) - ${period}`;
        option.dataset.bill = JSON.stringify({
            id: bill.id,
            bill_number: bill.bill_number,
            total_amount: bill.total_amount,
            status: bill.status,
            period_month: bill.period_month,
            period_year: bill.period_year,
            customer_name: bill.customer_name,
            customer_phone: bill.customer_phone
        });
        billSelect.appendChild(option);
    });
    
    // Store for global access
    unpaidBills = bills;
}

// Handle bill selection change
function onBillSelectionChange() {
    const select = document.getElementById('bill_id');
    const billDetails = document.getElementById('bill-details');
    const amountInput = document.getElementById('amount');
    
    if (select.value) {
        const billData = JSON.parse(select.selectedOptions[0].dataset.bill);
        
        // Show bill details
        document.getElementById('bill-customer').textContent = billData.customer_name || 'Unknown';
        document.getElementById('bill-period').textContent = `${getMonthName(billData.period_month)} ${billData.period_year}`;
        document.getElementById('bill-total').textContent = formatRupiah(billData.total_amount || 0);
        document.getElementById('bill-status').innerHTML = `<span class="badge bg-${getStatusColor(billData.status)}">${getStatusText(billData.status)}</span>`;
        
        // Set default amount to bill total
        amountInput.value = billData.total_amount || 0;
        
        billDetails.style.display = 'block';
    } else {
        billDetails.style.display = 'none';
        amountInput.value = '';
    }
}

// Load payment history from API
async function loadPaymentHistory() {
    const tbody = document.getElementById('payments-table-body');

    // Show loading
    tbody.innerHTML = `
        <tr>
            <td colspan="9" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0 text-muted">Memuat data pembayaran...</p>
            </td>
        </tr>
    `;

    try {
        // Build query parameters
        const params = new URLSearchParams({
            page: currentPage,
            per_page: perPage,
            sort_field: 'created_at',
            sort_direction: 'desc'
        });

        // Add filters
        if (currentFilters.status) params.append('status', currentFilters.status);
        if (currentFilters.payment_method) params.append('payment_method', currentFilters.payment_method);
        if (currentFilters.date_from) params.append('date_from', currentFilters.date_from);
        if (currentFilters.date_to) params.append('date_to', currentFilters.date_to);
        if (currentFilters.search) params.append('search', currentFilters.search);

        const response = await fetch(`/api/payments/history?${params.toString()}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        displayPayments(data.data || []);
        updatePagination(data);
        updateStats(data);

        // Store for global access
        paymentData = data.data || [];
    } catch (error) {
        console.error('Error loading payment history:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h6 class="text-danger mb-2">Data Tidak Ada</h6>
                    <p class="text-muted mb-0">Terjadi kesalahan saat memuat data pembayaran</p>
                </td>
            </tr>
        `;
        showToast('Data Belum Ada', 'error');
    }
}

// Display payments in table
function displayPayments(payments) {
    const tbody = document.getElementById('payments-table-body');

    if (!payments || payments.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="mb-0 text-muted">Tidak ada data pembayaran</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = payments.map(payment => {
        const customer = payment.customer_name || 'Unknown';
        const billNumber = payment.bill_number || '-';
        const proofHtml = payment.payment_proof_path ?
            `<button class="btn btn-sm btn-outline-primary" onclick="viewPaymentProof(${payment.id})" title="Lihat Bukti">
                <i class="fas fa-image"></i>
            </button>` :
            '<span class="text-muted">-</span>';

        return `
            <tr data-payment-id="${payment.id}">
                <td><strong>${payment.payment_number}</strong></td>
                <td>${customer}</td>
                <td>${billNumber}</td>
                <td><strong class="text-success">${formatRupiah(payment.amount)}</strong></td>
                <td><span class="badge bg-secondary payment-method-badge">${getPaymentMethodText(payment.payment_method)}</span></td>
                <td>${formatDate(payment.payment_date)}</td>
                <td><span class="badge bg-${getPaymentStatusColor(payment.status)}">${getPaymentStatusText(payment.status)}</span></td>
                <td class="text-center">${proofHtml}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="viewPaymentDetail(${payment.id})" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${payment.status === 'pending' ?
                            `<button class="btn btn-success" onclick="openVerifyModal(${payment.id})" title="Verifikasi">
                                <i class="fas fa-check"></i>
                            </button>` : ''}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// REQ-F-6.1: Open add payment modal
function openAddPaymentModal() {
    const modal = new bootstrap.Modal(document.getElementById('addPaymentModal'));
    document.getElementById('paymentForm').reset();
    document.getElementById('bill-details').style.display = 'none';
    clearFormErrors();
    
    // Set tanggal pembayaran ke hari ini
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('payment_date').value = today;
    
    modal.show();
}

// REQ-F-6.1: Submit payment (with validation)
function submitPayment() {
    // Clear previous errors
    clearFormErrors();
    
    // Get form data
    const formData = new FormData(document.getElementById('paymentForm'));
    
    // Client-side validation
    const validation = validatePaymentForm(formData);
    if (!validation.isValid) {
        displayFormErrors(validation.errors);
        return;
    }
    
    // Prepare confirmation data
    paymentConfirmData = {
        bill_id: formData.get('bill_id'),
        amount: formData.get('amount'),
        payment_method: formData.get('payment_method'),
        payment_date: formData.get('payment_date'),
        reference_number: formData.get('reference_number'),
        notes: formData.get('notes'),
        payment_proof: formData.get('payment_proof')
    };
    
    // Show confirmation modal
    showPaymentConfirmation();
}

// REQ-F-6.2: Show payment confirmation
function showPaymentConfirmation() {
    const billSelect = document.getElementById('bill_id');
    const selectedBill = JSON.parse(billSelect.selectedOptions[0].dataset.bill);
    const customer = selectedBill.meter?.customer?.user?.name || 'Unknown';
    
    const confirmationDetails = `
        <div class="row">
            <div class="col-md-6">
                <p class="mb-2"><strong>Pelanggan:</strong> ${customer}</p>
                <p class="mb-2"><strong>No. Tagihan:</strong> ${selectedBill.bill_number}</p>
                <p class="mb-2"><strong>Jumlah:</strong> <span class="text-success">${formatRupiah(paymentConfirmData.amount)}</span></p>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Metode:</strong> ${getPaymentMethodText(paymentConfirmData.payment_method)}</p>
                <p class="mb-2"><strong>Tanggal:</strong> ${formatDate(paymentConfirmData.payment_date)}</p>
                <p class="mb-2"><strong>Referensi:</strong> ${paymentConfirmData.reference_number || '-'}</p>
            </div>
        </div>
        ${paymentConfirmData.notes ? `<p class="mb-2"><strong>Catatan:</strong> ${paymentConfirmData.notes}</p>` : ''}
    `;
    
    document.getElementById('payment-confirmation-details').innerHTML = confirmationDetails;
    
    const confirmModal = new bootstrap.Modal(document.getElementById('paymentConfirmModal'));
    confirmModal.show();
}

// Confirm and submit payment
async function confirmPaymentSubmission() {
    const confirmModal = bootstrap.Modal.getInstance(document.getElementById('paymentConfirmModal'));
    const addModal = bootstrap.Modal.getInstance(document.getElementById('addPaymentModal'));

    showToast('Menyimpan pembayaran...', 'info');

    try {
        // Prepare form data for API submission
        const formData = new FormData(document.getElementById('paymentForm'));

        const response = await fetch('/api/payments', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            },
            body: formData
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to submit payment');
        }

        const result = await response.json();
        showToast('Pembayaran berhasil disimpan!', 'success');

        // Close modals
        confirmModal.hide();
        addModal.hide();

        // Refresh data
        loadPaymentHistory();
        loadUnpaidBills();
    } catch (error) {
        console.error('Error submitting payment:', error);
        showToast('Gagal menyimpan pembayaran: ' + error.message, 'error');
    }
}

// REQ-F-6.3: View payment detail
async function viewPaymentDetail(paymentId) {
    const modal = new bootstrap.Modal(document.getElementById('paymentDetailModal'));
    const content = document.getElementById('payment-detail-content');

    // Show loading state
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 mb-0 text-muted">Memuat detail pembayaran...</p>
        </div>
    `;

    modal.show();

    try {
        const response = await fetch(`/api/payments/${paymentId}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const payment = await response.json();
        displayPaymentDetail(payment);
    } catch (error) {
        console.error('Error fetching payment details:', error);
        content.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Data Tidak Ada
            </div>
        `;
        showToast('Data Tidak Ada', 'error');
    }
}

// Display payment detail in modal
function displayPaymentDetail(payment) {
    const detailHtml = `
        <div class="row mb-3">
            <div class="col-md-6">
                <h6 class="text-primary">Informasi Pembayaran</h6>
                <table class="table table-sm">
                    <tr><td><strong>No. Pembayaran:</strong></td><td>${payment.payment_number}</td></tr>
                    <tr><td><strong>Jumlah:</strong></td><td class="text-success"><strong>${formatRupiah(payment.amount)}</strong></td></tr>
                    <tr><td><strong>Metode:</strong></td><td>${getPaymentMethodText(payment.payment_method)}</td></tr>
                    <tr><td><strong>Tanggal:</strong></td><td>${formatDate(payment.payment_date)}</td></tr>
                    <tr><td><strong>Status:</strong></td><td><span class="badge bg-${getPaymentStatusColor(payment.status)}">${getPaymentStatusText(payment.status)}</span></td></tr>
                    ${payment.reference_number ? `<tr><td><strong>Referensi:</strong></td><td>${payment.reference_number}</td></tr>` : ''}
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Informasi Tagihan</h6>
                <table class="table table-sm">
                    <tr><td><strong>No. Tagihan:</strong></td><td>${payment.bill_number || '-'}</td></tr>
                    <tr><td><strong>Pelanggan:</strong></td><td>${payment.customer_name || 'Unknown'}</td></tr>
                    <tr><td><strong>No. Telepon:</strong></td><td>${payment.customer_phone || '-'}</td></tr>
                    <tr><td><strong>Periode:</strong></td><td>${payment.period_month ? `${getMonthName(payment.period_month)} ${payment.period_year}` : '-'}</td></tr>
                    <tr><td><strong>Total Tagihan:</strong></td><td>${formatRupiah(payment.total_amount || 0)}</td></tr>
                </table>
            </div>
        </div>

        ${payment.notes ? `
        <div class="mb-3">
            <h6 class="text-primary">Catatan</h6>
            <div class="alert alert-info">${payment.notes}</div>
        </div>` : ''}

        ${payment.verification_notes ? `
        <div class="mb-3">
            <h6 class="text-primary">Catatan Verifikasi</h6>
            <div class="alert alert-secondary">${payment.verification_notes}</div>
        </div>` : ''}

        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Riwayat</h6>
                <small class="text-muted">
                    <div>Dibuat: ${formatDateTime(payment.created_at)} oleh ${payment.created_by_name || 'System'}</div>
                    ${payment.verified_at ? `<div>Diverifikasi: ${formatDateTime(payment.verified_at)} oleh ${payment.verified_by_name || 'System'}</div>` : ''}
                </small>
            </div>
            ${payment.payment_proof_path ? `
            <div class="col-md-6">
                <h6 class="text-primary">Bukti Pembayaran</h6>
                <button class="btn btn-outline-primary btn-sm" onclick="viewPaymentProof(${payment.id})">
                    <i class="fas fa-image me-1"></i> Lihat Bukti
                </button>
            </div>` : ''}
        </div>
    `;

    document.getElementById('payment-detail-content').innerHTML = detailHtml;

    // Setup action buttons
    const actionButtons = document.getElementById('payment-actions');
    if (payment.status === 'pending') {
        actionButtons.innerHTML = `
            <button type="button" class="btn btn-success" onclick="openVerifyModal(${payment.id})">
                <i class="fas fa-check me-1"></i>
                Verifikasi
            </button>
        `;
    } else {
        actionButtons.innerHTML = '';
    }
}

// Open verify payment modal
function openVerifyModal(paymentId) {
    const modal = new bootstrap.Modal(document.getElementById('verifyPaymentModal'));
    document.getElementById('verify_payment_id').value = paymentId;
    document.getElementById('verifyPaymentForm').reset();
    modal.show();
}

// Submit payment verification
async function submitVerification() {
    const form = document.getElementById('verifyPaymentForm');
    const formData = new FormData(form);
    const paymentId = parseInt(formData.get('payment_id'));
    const status = formData.get('status');
    const notes = formData.get('verification_notes');

    if (!status) {
        showToast('Pilih status verifikasi', 'error');
        return;
    }

    showToast('Memproses verifikasi...', 'info');

    try {
        const response = await fetch(`/api/payments/${paymentId}/verify`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                status: status,
                verification_notes: notes
            })
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to verify payment');
        }

        const result = await response.json();
        showToast('Verifikasi pembayaran berhasil!', 'success');

        // Close modals
        bootstrap.Modal.getInstance(document.getElementById('verifyPaymentModal')).hide();
        bootstrap.Modal.getInstance(document.getElementById('paymentDetailModal'))?.hide();

        // Refresh payment history
        loadPaymentHistory();
    } catch (error) {
        console.error('Error verifying payment:', error);
        showToast('Gagal verifikasi pembayaran: ' + error.message, 'error');
    }
}

// Filter payments
function filterPayments() {
    currentFilters = {
        status: document.getElementById('status-filter').value,
        payment_method: document.getElementById('method-filter').value,
        date_from: document.getElementById('date-from').value,
        date_to: document.getElementById('date-to').value
    };

    const searchTerm = document.getElementById('search-payment').value;
    if (searchTerm) {
        currentFilters.search = searchTerm;
    }

    currentPage = 1;
    loadPaymentHistory();
}

// Refresh payment history
function refreshPaymentHistory() {
    currentFilters = {};
    currentPage = 1;
    document.getElementById('status-filter').value = '';
    document.getElementById('method-filter').value = '';
    document.getElementById('date-from').value = '';
    document.getElementById('date-to').value = '';
    document.getElementById('search-payment').value = '';

    loadPaymentHistory();
}

// Update pagination controls
function updatePagination(paginationData) {
    const controls = document.getElementById('pagination-controls');
    const { current_page, last_page, prev_page_url, next_page_url } = paginationData;
    
    let html = '';
    
    // Previous button
    html += `<li class="page-item ${!prev_page_url ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="${prev_page_url ? `changePage(${current_page - 1})` : 'return false'}">Previous</a>
    </li>`;
    
    // Page numbers
    const startPage = Math.max(1, current_page - 2);
    const endPage = Math.min(last_page, current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === current_page ? 'active' : ''}">
            <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
        </li>`;
    }
    
    // Next button
    html += `<li class="page-item ${!next_page_url ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="${next_page_url ? `changePage(${current_page + 1})` : 'return false'}">Next</a>
    </li>`;
    
    controls.innerHTML = html;
}

// Change page
function changePage(page) {
    currentPage = page;
    loadPaymentHistory();
}

// Update stats from API data
function updateStats(paginationData) {
    // If we have pagination data with totals, use it
    if (paginationData && paginationData.total_stats) {
        const stats = paginationData.total_stats;
        document.getElementById('pending-payments-count').textContent = stats.pending || 0;
        document.getElementById('verified-payments-count').textContent = stats.verified || 0;
        document.getElementById('rejected-payments-count').textContent = stats.rejected || 0;
        return;
    }

    // Fallback: calculate from current page data (less accurate but better than nothing)
    const payments = paymentData;
    const pendingCount = payments.filter(p => p.status === 'pending').length;
    const verifiedCount = payments.filter(p => p.status === 'verified').length;
    const rejectedCount = payments.filter(p => p.status === 'rejected').length;

    document.getElementById('pending-payments-count').textContent = pendingCount;
    document.getElementById('verified-payments-count').textContent = verifiedCount;
    document.getElementById('rejected-payments-count').textContent = rejectedCount;
}

// Utility Functions
function validatePaymentForm(formData) {
    const errors = {};
    let isValid = true;
    
    if (!formData.get('bill_id')) {
        errors.bill_id = 'Pilih tagihan yang akan dibayar';
        isValid = false;
    }
    
    const amount = parseFloat(formData.get('amount'));
    if (!amount || amount <= 0) {
        errors.amount = 'Jumlah pembayaran harus lebih dari 0';
        isValid = false;
    }
    
    if (!formData.get('payment_method')) {
        errors.payment_method = 'Pilih metode pembayaran';
        isValid = false;
    }
    
    if (!formData.get('payment_date')) {
        errors.payment_date = 'Tanggal pembayaran harus diisi';
        isValid = false;
    }
    
    return { isValid, errors };
}

function clearFormErrors() {
    const errorElements = document.querySelectorAll('.invalid-feedback');
    errorElements.forEach(el => el.textContent = '');
    
    const inputElements = document.querySelectorAll('.is-invalid');
    inputElements.forEach(el => el.classList.remove('is-invalid'));
}

function displayFormErrors(errors) {
    Object.keys(errors).forEach(field => {
        const input = document.getElementById(field);
        const errorDiv = document.getElementById(field + '_error');
        
        if (input && errorDiv) {
            input.classList.add('is-invalid');
            errorDiv.textContent = errors[field][0] || errors[field];
        }
    });
}

function formatRupiah(amount) {
    return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID');
}

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString('id-ID');
}

function getPaymentMethodText(method) {
    const methods = {
        'transfer': 'Transfer',
        'cash': 'Tunai',
        'online': 'Online',
        'mobile_banking': 'M-Banking'
    };
    return methods[method] || method;
}

function getPaymentStatusText(status) {
    const statuses = {
        'pending': 'Menunggu',
        'verified': 'Terverifikasi',
        'rejected': 'Ditolak'
    };
    return statuses[status] || status;
}

function getPaymentStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'verified': 'success',
        'rejected': 'danger'
    };
    return colors[status] || 'secondary';
}

function getStatusText(status) {
    const statuses = {
        'pending': 'Pending',
        'sent': 'Terkirim',
        'paid': 'Lunas',
        'overdue': 'Terlambat'
    };
    return statuses[status] || status;
}

function getMonthName(month) {
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return months[month - 1] || month;
}

function viewPaymentProof(paymentId) {
    // Open payment proof in new window
    window.open(`/api/payments/${paymentId}/download-proof`, '_blank');
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
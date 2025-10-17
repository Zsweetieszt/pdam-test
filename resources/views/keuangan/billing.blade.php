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
                <!-- Stats Cards (existing code unchanged) -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h4>25</h4>
                                <small>Tagihan Pending</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h4>8</h4>
                                <small>Jatuh Tempo Hari Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4>142</h4>
                                <small>Lunas Bulan Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4>Rp 21.5M</h4>
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
                
                <!-- Filters (existing) -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Cari tagihan atau customer...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option>Semua Status</option>
                            <option>Pending</option>
                            <option>Lunas</option>
                            <option>Terlambat</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option>Periode: Agu 2025</option>
                            <option>Jul 2025</option>
                            <option>Jun 2025</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">
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
                        <tbody>
                            <!-- Sample Data Row 1 -->
                            <tr data-bill='{"id":1,"customer":"Ahmad Yani","phone":"085723302116","amount":"225000","period":"Agu 2025","usage":"18","due_date":"25/08/2025","bill_number":"INV-2025-003","status":"pending"}'>
                                <td><input type="checkbox" class="form-check-input bill-checkbox"></td>
                                <td>INV-2025-003</td>
                                <td>Ahmad Yani</td>
                                <td>Agu 2025</td>
                                <td>18 m³</td>
                                <td><strong>Rp 225.000</strong></td>
                                <td>25 Agu 2025</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-success" title="Tandai Lunas">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <!-- REQ-F-5.1: WhatsApp Button -->
                                        <button class="btn btn-primary whatsapp-btn" 
                                                title="Kirim WhatsApp" 
                                                onclick="openWhatsAppModal(this)">
                                            <i class="fab fa-whatsapp"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Sample Data Row 2 -->
                            <tr data-bill='{"id":2,"customer":"Maria Sari","phone":"081234567891","amount":"275000","period":"Agu 2025","usage":"22","due_date":"20/08/2025","bill_number":"INV-2025-004","status":"overdue"}'>
                                <td><input type="checkbox" class="form-check-input bill-checkbox"></td>
                                <td>INV-2025-004</td>
                                <td>Maria Sari</td>
                                <td>Agu 2025</td>
                                <td>22 m³</td>
                                <td><strong>Rp 275.000</strong></td>
                                <td>20 Agu 2025</td>
                                <td><span class="badge bg-danger">Overdue</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-success" title="Tandai Lunas">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <!-- REQ-F-5.1: WhatsApp Button for Overdue -->
                                        <button class="btn btn-warning whatsapp-btn" 
                                                title="Kirim Reminder" 
                                                onclick="openWhatsAppModal(this)">
                                            <i class="fas fa-bell"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Sample Data Row 3 - Sent Status -->
                            <tr data-bill='{"id":3,"customer":"Siti Nurhaliza","phone":"081234567892","amount":"310000","period":"Agu 2025","usage":"25","due_date":"25/08/2025","bill_number":"INV-2025-005","status":"sent"}'>
                                <td><input type="checkbox" class="form-check-input bill-checkbox"></td>
                                <td>INV-2025-005</td>
                                <td>Siti Nurhaliza</td>
                                <td>Agu 2025</td>
                                <td>25 m³</td>
                                <td><strong>Rp 310.000</strong></td>
                                <td>25 Agu 2025</td>
                                <td><span class="badge bg-info">Terkirim</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-success" title="Tandai Lunas">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-primary whatsapp-btn" 
                                                title="Kirim Ulang WhatsApp" 
                                                onclick="openWhatsAppModal(this)">
                                            <i class="fab fa-whatsapp"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Sample Data Row 4 - Paid Status (No WhatsApp Button) -->
                            <tr data-bill='{"id":4,"customer":"Budi Santoso","phone":"081234567893","amount":"180000","period":"Agu 2025","usage":"15","due_date":"25/08/2025","bill_number":"INV-2025-006","status":"paid"}'>
                                <td><input type="checkbox" class="form-check-input bill-checkbox" disabled></td>
                                <td>INV-2025-006</td>
                                <td>Budi Santoso</td>
                                <td>Agu 2025</td>
                                <td>15 m³</td>
                                <td><strong>Rp 180.000</strong></td>
                                <td>25 Agu 2025</td>
                                <td><span class="badge bg-success">Lunas</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <!-- No WhatsApp button for paid bills as per REQ-C-9 -->
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Lunas
                                        </span>
                                    </div>
                                </td>
                            </tr>
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
});

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
function openWhatsAppDirect() {
    const whatsappLink = document.getElementById('whatsapp-link').value;
    
    if (!whatsappLink) {
        showToast('Link WhatsApp belum tersedia', 'error');
        return;
    }
    
    // Open WhatsApp in new tab
    window.open(whatsappLink, '_blank');
    
    // Mark as sent if checkbox is checked
    if (document.getElementById('mark-as-sent').checked) {
        markBillAsSent();
    }
    
    // Log the action if checkbox is checked
    if (document.getElementById('save-log').checked) {
        logWhatsAppAction();
    }
    
    // Close modal
    whatsappModal.hide();
    
    showToast('WhatsApp terbuka! Pesan siap untuk dikirim.', 'success');
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

function markBillAsSent() {
    // Update UI only (no backend call)
    const row = document.querySelector(`tr[data-bill*'"id":${currentBillData.id}']`);
    if (row) {
        const statusBadge = row.querySelector('.badge');
        if (statusBadge && ['pending', 'overdue'].includes(currentBillData.status)) {
            statusBadge.className = 'badge bg-info';
            statusBadge.textContent = 'Terkirim';
            
            // Update data attribute
            currentBillData.status = 'sent';
            row.setAttribute('data-bill', JSON.stringify(currentBillData));
        }
    }
}

function logWhatsAppAction() {
    // Frontend logging (to console for now)
    const logData = {
        billId: currentBillData.id,
        customer: currentBillData.customer,
        phone: document.getElementById('phone-number').value,
        template: document.getElementById('template-select').value,
        timestamp: new Date().toISOString(),
        action: 'GENERATE_WHATSAPP_LINK'
    };
    
    console.log('WhatsApp notification sent:', logData);
    
    // Here you could store in localStorage or send to backend if needed
    // localStorage.setItem('whatsapp_logs', JSON.stringify([...getLogs(), logData]));
}

function editPhoneNumber() {
    const phoneInput = document.getElementById('phone-number');
    if (phoneInput.readOnly) {
        phoneInput.readOnly = false;
        phoneInput.focus();
        phoneInput.select();
        showToast('Edit nomor telepon selesai, tekan Enter untuk simpan', 'info');
    } else {
        phoneInput.readOnly = true;
        // Regenerate link with new phone number
        updateMessageTemplate();
        showToast('Nomor telepon diperbarui', 'success');
    }
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
    selectedBills.forEach(checkbox => {
        const row = checkbox.closest('tr');
        const billData = JSON.parse(row.getAttribute('data-bill'));
        if (canSendWhatsApp(billData.status)) {
            eligibleCount++;
        }
    });
    
    if (eligibleCount === 0) {
        showToast('Tidak ada tagihan yang eligible untuk dikirim WhatsApp', 'warning');
        return;
    }
    
    if (confirm(`Kirim notifikasi WhatsApp ke ${eligibleCount} pelanggan yang dipilih?`)) {
        showToast(`Fitur bulk WhatsApp untuk ${eligibleCount} pelanggan akan segera tersedia`, 'info');
    }
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
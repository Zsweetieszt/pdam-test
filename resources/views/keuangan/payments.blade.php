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
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h4 id="pending-payments-count">12</h4>
                                <small>Pembayaran Pending</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4 id="verified-payments-count">45</h4>
                                <small>Terverifikasi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h4 id="rejected-payments-count">3</h4>
                                <small>Ditolak</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4>Rp 15.2M</h4>
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
    loadDummyUnpaidBills();
    loadDummyPaymentHistory();
    
    // Set default payment date to today
    document.getElementById('payment_date').value = new Date().toISOString().split('T')[0];
    
    // Setup event listeners
    document.getElementById('bill_id').addEventListener('change', onBillSelectionChange);
    document.getElementById('per-page-select').addEventListener('change', function() {
        perPage = parseInt(this.value);
        currentPage = 1;
        loadDummyPaymentHistory();
    });
});

// Initialize payment page
function initializePaymentPage() {
    // Initialize without axios for dummy data
    console.log('Payment page initialized with dummy data');
}

// Load unpaid bills for payment form (DUMMY DATA)
function loadDummyUnpaidBills() {
    const billSelect = document.getElementById('bill_id');
    billSelect.innerHTML = '<option value="">Pilih tagihan yang akan dibayar...</option>';
    
    // Dummy unpaid bills data
    unpaidBills = [
        {
            id: 1,
            bill_number: 'BILL-202508-001',
            total_amount: 225000,
            status: 'pending',
            meter: {
                customer: {
                    user: {
                        name: 'Ahmad Yani',
                        phone: '085723302116'
                    }
                }
            },
            billing_period: {
                period_month: 8,
                period_year: 2025
            }
        },
        {
            id: 2,
            bill_number: 'BILL-202508-002',
            total_amount: 275000,
            status: 'overdue',
            meter: {
                customer: {
                    user: {
                        name: 'Maria Sari',
                        phone: '081234567891'
                    }
                }
            },
            billing_period: {
                period_month: 8,
                period_year: 2025
            }
        },
        {
            id: 3,
            bill_number: 'BILL-202508-003',
            total_amount: 310000,
            status: 'sent',
            meter: {
                customer: {
                    user: {
                        name: 'Siti Nurhaliza',
                        phone: '081234567892'
                    }
                }
            },
            billing_period: {
                period_month: 8,
                period_year: 2025
            }
        },
        {
            id: 4,
            bill_number: 'BILL-202508-004',
            total_amount: 180000,
            status: 'pending',
            meter: {
                customer: {
                    user: {
                        name: 'Budi Santoso',
                        phone: '081234567893'
                    }
                }
            },
            billing_period: {
                period_month: 8,
                period_year: 2025
            }
        },
        {
            id: 5,
            bill_number: 'BILL-202508-005',
            total_amount: 450000,
            status: 'overdue',
            meter: {
                customer: {
                    user: {
                        name: 'Indira Sari',
                        phone: '081234567894'
                    }
                }
            },
            billing_period: {
                period_month: 8,
                period_year: 2025
            }
        }
    ];
    
    unpaidBills.forEach(bill => {
        const customerName = bill.meter?.customer?.user?.name || 'Unknown';
        const amount = formatRupiah(bill.total_amount || 0);
        
        const option = document.createElement('option');
        option.value = bill.id;
        option.textContent = `${bill.bill_number} - ${customerName} (${amount})`;
        option.dataset.bill = JSON.stringify(bill);
        billSelect.appendChild(option);
    });
}

// Handle bill selection change
function onBillSelectionChange() {
    const select = document.getElementById('bill_id');
    const billDetails = document.getElementById('bill-details');
    const amountInput = document.getElementById('amount');
    
    if (select.value) {
        const billData = JSON.parse(select.selectedOptions[0].dataset.bill);
        
        // Show bill details
        document.getElementById('bill-customer').textContent = billData.meter?.customer?.user?.name || 'Unknown';
        document.getElementById('bill-period').textContent = `${billData.billing_period?.period_month}/${billData.billing_period?.period_year}` || '-';
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

// REQ-F-6.3: Load payment history (DUMMY DATA)
function loadDummyPaymentHistory() {
    // Dummy payment history data
    const dummyPayments = [
        {
            id: 1,
            payment_number: 'PAY2025001',
            amount: 225000,
            payment_method: 'transfer',
            payment_date: '2025-08-20',
            status: 'pending',
            payment_proof_path: '/storage/payment-proofs/proof1.jpg',
            created_at: '2025-08-20 10:30:00',
            bill: {
                bill_number: 'BILL-202508-001',
                total_amount: 225000,
                meter: {
                    customer: {
                        user: {
                            name: 'Ahmad Yani',
                            phone: '081234567890'
                        }
                    }
                }
            },
            created_by: {
                name: 'Staff Keuangan'
            }
        },
        {
            id: 2,
            payment_number: 'PAY2025002',
            amount: 275000,
            payment_method: 'cash',
            payment_date: '2025-08-19',
            status: 'verified',
            payment_proof_path: null,
            created_at: '2025-08-19 14:15:00',
            verified_at: '2025-08-19 15:00:00',
            verification_notes: 'Pembayaran tunai di kantor PDAM',
            bill: {
                bill_number: 'BILL-202508-002',
                total_amount: 275000,
                meter: {
                    customer: {
                        user: {
                            name: 'Maria Sari',
                            phone: '081234567891'
                        }
                    }
                }
            },
            created_by: {
                name: 'Staff Keuangan'
            },
            verified_by: {
                name: 'Manager Keuangan'
            }
        },
        {
            id: 3,
            payment_number: 'PAY2025003',
            amount: 180000,
            payment_method: 'online',
            payment_date: '2025-08-18',
            status: 'verified',
            payment_proof_path: '/storage/payment-proofs/proof3.jpg',
            reference_number: 'TXN123456789',
            created_at: '2025-08-18 09:45:00',
            verified_at: '2025-08-18 16:30:00',
            bill: {
                bill_number: 'BILL-202508-004',
                total_amount: 180000,
                meter: {
                    customer: {
                        user: {
                            name: 'Budi Santoso',
                            phone: '081234567893'
                        }
                    }
                }
            },
            created_by: {
                name: 'System Auto'
            },
            verified_by: {
                name: 'Manager Keuangan'
            }
        },
        {
            id: 4,
            payment_number: 'PAY2025004',
            amount: 310000,
            payment_method: 'mobile_banking',
            payment_date: '2025-08-17',
            status: 'rejected',
            payment_proof_path: '/storage/payment-proofs/proof4.jpg',
            reference_number: 'MB987654321',
            verification_notes: 'Bukti pembayaran tidak jelas, mohon upload ulang',
            created_at: '2025-08-17 11:20:00',
            verified_at: '2025-08-17 17:45:00',
            bill: {
                bill_number: 'BILL-202508-003',
                total_amount: 310000,
                meter: {
                    customer: {
                        user: {
                            name: 'Siti Nurhaliza',
                            phone: '081234567892'
                        }
                    }
                }
            },
            created_by: {
                name: 'Customer Self'
            },
            verified_by: {
                name: 'Staff Keuangan'
            }
        },
        {
            id: 5,
            payment_number: 'PAY2025005',
            amount: 450000,
            payment_method: 'transfer',
            payment_date: '2025-08-16',
            status: 'pending',
            payment_proof_path: '/storage/payment-proofs/proof5.jpg',
            reference_number: 'TRF555444333',
            notes: 'Pembayaran melalui Internet Banking BCA',
            created_at: '2025-08-16 13:10:00',
            bill: {
                bill_number: 'BILL-202508-005',
                total_amount: 450000,
                meter: {
                    customer: {
                        user: {
                            name: 'Indira Sari',
                            phone: '081234567894'
                        }
                    }
                }
            },
            created_by: {
                name: 'Customer Self'
            }
        },
        {
            id: 6,
            payment_number: 'PAY2025006',
            amount: 195000,
            payment_method: 'cash',
            payment_date: '2025-08-15',
            status: 'verified',
            payment_proof_path: null,
            created_at: '2025-08-15 08:30:00',
            verified_at: '2025-08-15 08:35:00',
            verification_notes: 'Pembayaran langsung di loket',
            bill: {
                bill_number: 'BILL-202507-055',
                total_amount: 195000,
                meter: {
                    customer: {
                        user: {
                            name: 'Rahmat Hidayat',
                            phone: '081234567895'
                        }
                    }
                }
            },
            created_by: {
                name: 'Staff Loket'
            },
            verified_by: {
                name: 'Staff Loket'
            }
        }
    ];
    
    // Apply filters if any
    let filteredPayments = dummyPayments;
    
    if (currentFilters.status) {
        filteredPayments = filteredPayments.filter(p => p.status === currentFilters.status);
    }
    
    if (currentFilters.payment_method) {
        filteredPayments = filteredPayments.filter(p => p.payment_method === currentFilters.payment_method);
    }
    
    if (currentFilters.date_from) {
        filteredPayments = filteredPayments.filter(p => p.payment_date >= currentFilters.date_from);
    }
    
    if (currentFilters.date_to) {
        filteredPayments = filteredPayments.filter(p => p.payment_date <= currentFilters.date_to);
    }
    
    // Simulate pagination
    const startIndex = (currentPage - 1) * perPage;
    const endIndex = startIndex + perPage;
    const pagedPayments = filteredPayments.slice(startIndex, endIndex);
    
    // Create pagination data
    const paginationData = {
        current_page: currentPage,
        last_page: Math.ceil(filteredPayments.length / perPage),
        per_page: perPage,
        total: filteredPayments.length,
        prev_page_url: currentPage > 1 ? true : null,
        next_page_url: currentPage < Math.ceil(filteredPayments.length / perPage) ? true : null
    };
    
    displayPayments(pagedPayments);
    updatePagination(paginationData);
    updateDummyStats(dummyPayments);
    
    // Store for global access
    paymentData = filteredPayments;
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
        const customer = payment.bill?.meter?.customer?.user?.name || 'Unknown';
        const billNumber = payment.bill?.bill_number || '-';
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

// REQ-F-6.2: Confirm and submit payment (DUMMY IMPLEMENTATION)
function confirmPaymentSubmission() {
    const confirmModal = bootstrap.Modal.getInstance(document.getElementById('paymentConfirmModal'));
    const addModal = bootstrap.Modal.getInstance(document.getElementById('addPaymentModal'));
    
    // Simulate API call with delay
    showToast('Menyimpan pembayaran...', 'info');
    
    setTimeout(() => {
        // Generate new payment ID
        const newPaymentId = Math.max(...paymentData.map(p => p.id), 0) + 1;
        
        // Get selected bill
        const billSelect = document.getElementById('bill_id');
        const selectedBill = JSON.parse(billSelect.selectedOptions[0].dataset.bill);
        
        // Create new payment object
        const newPayment = {
            id: newPaymentId,
            payment_number: `PAY2025${String(newPaymentId + 1000).padStart(3, '0')}`,
            amount: parseFloat(paymentConfirmData.amount),
            payment_method: paymentConfirmData.payment_method,
            payment_date: paymentConfirmData.payment_date,
            status: 'pending',
            payment_proof_path: paymentConfirmData.payment_proof ? '/storage/payment-proofs/dummy.jpg' : null,
            reference_number: paymentConfirmData.reference_number,
            notes: paymentConfirmData.notes,
            created_at: new Date().toISOString(),
            bill: {
                bill_number: selectedBill.bill_number,
                total_amount: selectedBill.total_amount,
                meter: selectedBill.meter
            },
            created_by: {
                name: 'Staff Keuangan (Current User)'
            }
        };
        
        // Add to dummy data (simulate database insert)
        paymentData.unshift(newPayment);
        
        showToast('Pembayaran berhasil disimpan!', 'success');
        confirmModal.hide();
        addModal.hide();
        loadDummyPaymentHistory();
        loadDummyUnpaidBills(); // Refresh unpaid bills
    }, 1500);
}

// REQ-F-6.3: View payment detail (DUMMY IMPLEMENTATION)
function viewPaymentDetail(paymentId) {
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
    
    // Simulate API delay
    setTimeout(() => {
        // Find payment in dummy data
        const payment = paymentData.find(p => p.id === paymentId);
        
        if (payment) {
            displayPaymentDetail(payment);
        } else {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Gagal memuat detail pembayaran: Data tidak ditemukan
                </div>
            `;
        }
    }, 800);
}

// Display payment detail in modal
function displayPaymentDetail(payment) {
    const customer = payment.bill?.meter?.customer?.user || {};
    const bill = payment.bill || {};
    
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
                    <tr><td><strong>No. Tagihan:</strong></td><td>${bill.bill_number || '-'}</td></tr>
                    <tr><td><strong>Pelanggan:</strong></td><td>${customer.name || 'Unknown'}</td></tr>
                    <tr><td><strong>No. Telepon:</strong></td><td>${customer.phone || '-'}</td></tr>
                    <tr><td><strong>Periode:</strong></td><td>${bill.billing_period?.period_month || '-'}/${bill.billing_period?.period_year || '-'}</td></tr>
                    <tr><td><strong>Total Tagihan:</strong></td><td>${formatRupiah(bill.total_amount || 0)}</td></tr>
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
                    <div>Dibuat: ${formatDateTime(payment.created_at)} oleh ${payment.created_by?.name || 'System'}</div>
                    ${payment.verified_at ? `<div>Diverifikasi: ${formatDateTime(payment.verified_at)} oleh ${payment.verified_by?.name || 'System'}</div>` : ''}
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

// Submit payment verification (DUMMY IMPLEMENTATION)
function submitVerification() {
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
    
    // Simulate API delay
    setTimeout(() => {
        // Find and update payment in dummy data
        const paymentIndex = paymentData.findIndex(p => p.id === paymentId);
        
        if (paymentIndex !== -1) {
            paymentData[paymentIndex].status = status;
            paymentData[paymentIndex].verification_notes = notes;
            paymentData[paymentIndex].verified_at = new Date().toISOString();
            paymentData[paymentIndex].verified_by = {
                name: 'Manager Keuangan (Current User)'
            };
            
            showToast('Verifikasi pembayaran berhasil!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('verifyPaymentModal')).hide();
            bootstrap.Modal.getInstance(document.getElementById('paymentDetailModal'))?.hide();
            loadDummyPaymentHistory();
        } else {
            showToast('Gagal verifikasi: Data tidak ditemukan', 'error');
        }
    }, 1000);
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
    loadDummyPaymentHistory();
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
    
    loadDummyPaymentHistory();
}

// Update stats with dummy data
function updateDummyStats(payments) {
    const pendingCount = payments.filter(p => p.status === 'pending').length;
    const verifiedCount = payments.filter(p => p.status === 'verified').length;
    const rejectedCount = payments.filter(p => p.status === 'rejected').length;
    
    document.getElementById('pending-payments-count').textContent = pendingCount;
    document.getElementById('verified-payments-count').textContent = verifiedCount;
    document.getElementById('rejected-payments-count').textContent = rejectedCount;
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
    loadDummyPaymentHistory();
}

// Update stats (placeholder - would normally calculate from API data)
function updateStats() {
    // This would be calculated from actual API data
    // For now, we'll keep the static values
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

function getStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'sent': 'info',
        'paid': 'success',
        'overdue': 'danger'
    };
    return colors[status] || 'secondary';
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
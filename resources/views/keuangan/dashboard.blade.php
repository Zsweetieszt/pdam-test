@extends('layouts.app')

@section('content')
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-money-bill-wave text-success me-2"></i>
                    Dashboard Keuangan
                </h2>
                <p class="text-muted">Kelola tagihan dan notifikasi WhatsApp - Selamat datang, {{ auth()->user()->name }}</p>
            </div>
            <div>
                <span class="badge bg-success fs-6">Finance Access</span>
            </div>
        </div>
    </div>
</div>

    <!-- Financial Stats -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-invoice-dollar fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Tagihan Pending</h6>
                            <h4 class="mb-0">--</h4>
                            <small class="opacity-75">Bulan ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Tagihan Lunas</h6>
                            <h4 class="mb-0">--</h4>
                            <small class="opacity-75">Bulan ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Tagihan Terlambat</h6>
                            <h4 class="mb-0">--</h4>
                            <small class="opacity-75">Jatuh tempo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fab fa-whatsapp fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Notifikasi Terkirim</h6>
                            <h4 class="mb-0">--</h4>
                            <small class="opacity-75">Hari ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Features -->
    <div class="row g-4 mb-4">
        <!-- Billing Management -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-file-invoice me-2"></i>
                        Manajemen Tagihan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('keuangan.billing') }}" class="btn btn-primary w-100">
                                <i class="fas fa-eye me-2"></i>
                                Lihat Semua Tagihan
                            </a>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-outline-primary w-100" onclick="generateBilling()">
                                <i class="fas fa-plus me-2"></i>
                                Generate Tagihan
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-success w-100" onclick="markAsPaid()">
                                <i class="fas fa-check me-2"></i>
                                Tandai Lunas
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-warning w-100" onclick="sendReminder()">
                                <i class="fas fa-bell me-2"></i>
                                Kirim Reminder
                            </button>
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-3">Quick Actions:</h6>
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-calculator text-primary me-2"></i>
                            Hitung Tarif Progresif
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar-alt text-info me-2"></i>
                            Jadwal Penagihan
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-search text-secondary me-2"></i>
                            Cari Tagihan Customer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- WhatsApp Integration -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fab fa-whatsapp me-2"></i>
                        WhatsApp Notifications
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <span>Status WhatsApp Service:</span>
                            <span class="badge bg-secondary" id="wa-status">Checking...</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <button class="btn btn-success w-100" onclick="sendBillNotification()">
                                <i class="fas fa-paper-plane me-2"></i>
                                Kirim Tagihan
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-outline-success w-100" onclick="sendPaymentReminder()">
                                <i class="fas fa-clock me-2"></i>
                                Reminder Jatuh Tempo
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-info w-100" onclick="sendThankYou()">
                                <i class="fas fa-heart me-2"></i>
                                Terima Kasih
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-warning w-100" onclick="sendOverdueNotice()">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Pemberitahuan Tunggakan
                            </button>
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-3">Template Messages:</h6>
                    <div class="form-group mb-3">
                        <select class="form-select" id="messageTemplate">
                            <option value="">Pilih Template</option>
                            <option value="bill">Tagihan Bulanan</option>
                            <option value="reminder">Reminder Pembayaran</option>
                            <option value="overdue">Pemberitahuan Tunggakan</option>
                            <option value="thanks">Terima Kasih</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100" onclick="previewTemplate()">
                        <i class="fas fa-eye me-2"></i>
                        Preview Template
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
// Check WhatsApp service status
async function checkWhatsAppStatus() {
    try {
        const response = await fetch('/api/whatsapp/status');
        const statusElement = document.getElementById('wa-status');
        
        if (response.ok) {
            statusElement.innerHTML = '<i class="fas fa-check me-1"></i> Connected';
            statusElement.className = 'badge bg-success';
        } else {
            statusElement.innerHTML = '<i class="fas fa-times me-1"></i> Disconnected';
            statusElement.className = 'badge bg-danger';
        }
    } catch (error) {
        const statusElement = document.getElementById('wa-status');
        statusElement.innerHTML = '<i class="fas fa-times me-1"></i> Unavailable';
        statusElement.className = 'badge bg-danger';
    }
}

function generateBilling() {
    if (confirm('Generate tagihan untuk semua pelanggan bulan ini?')) {
        alert('Fitur generate tagihan akan diimplementasi');
    }
}

function markAsPaid() {
    alert('Fitur tandai lunas akan diimplementasi');
}

function sendReminder() {
    if (confirm('Kirim reminder pembayaran ke semua pelanggan yang belum bayar?')) {
        alert('Reminder akan dikirim via WhatsApp');
    }
}

function sendBillNotification() {
    if (confirm('Kirim notifikasi tagihan ke semua pelanggan?')) {
        alert('Notifikasi tagihan akan dikirim via WhatsApp');
    }
}

function sendPaymentReminder() {
    alert('Reminder pembayaran akan dikirim via WhatsApp');
}

function sendThankYou() {
    alert('Pesan terima kasih akan dikirim ke pelanggan yang sudah bayar');
}

function sendOverdueNotice() {
    if (confirm('Kirim pemberitahuan tunggakan ke pelanggan yang terlambat bayar?')) {
        alert('Pemberitahuan tunggakan akan dikirim via WhatsApp');
    }
}

function previewTemplate() {
    const template = document.getElementById('messageTemplate').value;
    if (!template) {
        alert('Pilih template terlebih dahulu');
        return;
    }
    alert('Preview template: ' + template);
}

// Check status on page load
document.addEventListener('DOMContentLoaded', checkWhatsAppStatus);
</script>
@endsection

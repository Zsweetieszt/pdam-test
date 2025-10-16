@extends('layouts.app')

@section('content')
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-shield-alt text-danger me-2"></i>
                    Dashboard Administrator
                </h2>
                <p class="text-muted">Kontrol penuh sistem PDAM - Selamat datang, {{ auth()->user()->name }}</p>
            </div>
            <div>
                <span class="badge bg-danger fs-6">Full Access</span>
            </div>
        </div>
    </div>
</div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Total Users</h6>
                            <h4 class="mb-0">--</h4>
                            <small class="opacity-75">Semua role</small>
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
                            <i class="fas fa-user-friends fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Total Customer</h6>
                            <h4 class="mb-0">--</h4>
                            <small class="opacity-75">Pelanggan aktif</small>
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
                            <i class="fas fa-file-invoice fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Total Tagihan</h6>
                            <h4 class="mb-0">--</h4>
                            <small class="opacity-75">Bulan ini</small>
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
                            <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Total Pendapatan</h6>
                            <h4 class="mb-0">--</h4>
                            <small class="opacity-75">Bulan ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Features Grid -->
    <div class="row g-4 mb-4">
        <!-- User Management -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0">
                        <i class="fas fa-users-cog text-primary me-2"></i>
                        Manajemen User
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Kelola semua user sistem: Admin, Keuangan, Manajemen, Customer</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye me-1"></i> Lihat Semua User
                        </a>
                        <button class="btn btn-outline-primary btn-sm" onclick="alert('Fitur belum diimplementasi')">
                            <i class="fas fa-plus me-1"></i> Tambah User Baru
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Management -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0">
                        <i class="fas fa-address-book text-success me-2"></i>
                        Manajemen Customer
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Kelola data pelanggan, meteran air, dan informasi tarif</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.customers') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-eye me-1"></i> Lihat Customer
                        </a>
                        <button class="btn btn-outline-success btn-sm" onclick="alert('Fitur belum diimplementasi')">
                            <i class="fas fa-plus me-1"></i> Tambah Customer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Management -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0">
                        <i class="fas fa-calculator text-warning me-2"></i>
                        Manajemen Tagihan
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Generate tagihan, atur tarif, kelola periode penagihan</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.billing') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-eye me-1"></i> Kelola Tagihan
                        </a>
                        <button class="btn btn-outline-warning btn-sm" onclick="alert('Fitur belum diimplementasi')">
                            <i class="fas fa-cog me-1"></i> Atur Tarif
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar text-info me-2"></i>
                        Laporan & Analisis
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Laporan keuangan, analisis pemakaian, trend pembayaran</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.reports') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye me-1"></i> Lihat Laporan
                        </a>
                        <button class="btn btn-outline-info btn-sm" onclick="alert('Fitur belum diimplementasi')">
                            <i class="fas fa-download me-1"></i> Export Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs text-secondary me-2"></i>
                        Pengaturan Sistem
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Konfigurasi WhatsApp, template notifikasi, pengaturan umum</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.settings') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-cog me-1"></i> Pengaturan
                        </a>
                        <button class="btn btn-outline-secondary btn-sm" onclick="alert('Fitur belum diimplementasi')">
                            <i class="fab fa-whatsapp me-1"></i> Konfigurasi WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Check WhatsApp service status
async function checkWhatsAppStatus() {
    try {
        const response = await fetch('/api/whatsapp/status');
        const statusElement = document.getElementById('whatsapp-status');
        
        if (response.ok) {
            statusElement.innerHTML = '<i class="fas fa-check me-1"></i> Connected';
            statusElement.className = 'badge bg-success';
        } else {
            statusElement.innerHTML = '<i class="fas fa-times me-1"></i> Disconnected';
            statusElement.className = 'badge bg-danger';
        }
    } catch (error) {
        const statusElement = document.getElementById('whatsapp-status');
        statusElement.innerHTML = '<i class="fas fa-times me-1"></i> Service Unavailable';
        statusElement.className = 'badge bg-danger';
    }
}

async function testWhatsApp() {
    try {
        const response = await fetch('/api/whatsapp/test');
        if (response.ok) {
            alert('WhatsApp service berhasil dites!');
        } else {
            alert('WhatsApp service tidak tersedia. Pastikan wa-service berjalan di port 3000.');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

<script>
// Fetch dashboard stats dari API
async function loadDashboardStats() {
    try {
        const response = await fetch('/api/admin/dashboard-stats', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json',
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            updateDashboardStats(data.data);
        }
    } catch (error) {
        console.error('Error loading dashboard stats:', error);
    }
}

function updateDashboardStats(stats) {
    // Update cards dengan data real
    document.querySelector('.bg-primary h4').textContent = stats.users.total;
    document.querySelector('.bg-success h4').textContent = stats.customers.total;
    document.querySelector('.bg-warning h4').textContent = stats.bills.total_this_month;
    document.querySelector('.bg-info h4').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(stats.payments.total_this_month);
}

// Load stats on page load
document.addEventListener('DOMContentLoaded', loadDashboardStats);
</script>

// Check status on page load
document.addEventListener('DOMContentLoaded', checkWhatsAppStatus);
</script>
@endsection

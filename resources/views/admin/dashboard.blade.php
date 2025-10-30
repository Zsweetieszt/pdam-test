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
                            <h6 class="mb-1">Total Staff</h6>
                            <h4 class="mb-0">{{ $stats['users']['total'] ?? 0 }}</h4>
                            <small class="opacity-75">Admin, Keuangan, Manajemen</small>
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
                            <h4 class="mb-0">{{ $stats['customers']['total'] ?? 0 }}</h4>
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
                            <h4 class="mb-0">{{ $stats['bills']['total_this_month'] ?? 0 }}</h4>
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
                            <h4 class="mb-0">Rp {{ number_format($stats['payments']['total_this_month'] ?? 0, 0, ',', '.') }}</h4>
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
                    <p class="text-muted mb-3">Generate tagihan, kelola periode penagihan</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.billing') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-eye me-1"></i> Kelola Tagihan
                        </a>
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
                    <p class="text-muted mb-3">Template notifikasi, pengaturan umum</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.settings') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-cog me-1"></i> Pengaturan
                        </a>
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
// Fetch dashboard stats dari API menggunakan Sanctum authentication
async function loadDashboardStats() {
    try {
        const response = await fetch('/api/admin/dashboard-stats', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                updateDashboardStats(data.data);
            } else {
                console.error('API returned error:', data.message);
            }
        } else {
            console.error('Failed to load dashboard stats. Status:', response.status);
            if (response.status === 401) {
                console.error('Authentication failed. Please check if you are logged in as admin.');
            }
        }
    } catch (error) {
        console.error('Error loading dashboard stats:', error);
    }
}

function updateDashboardStats(stats) {
    try {
        // Update Total Users card (hanya staff admin/keuangan/manajemen)
        const usersCard = document.querySelector('.bg-primary h4');
        if (usersCard) {
            usersCard.textContent = stats.users.total || 0;
        }

        // Update Total Customer card
        const customersCard = document.querySelector('.bg-success h4');
        if (customersCard) {
            customersCard.textContent = stats.customers.total || 0;
        }

        // Update Total Tagihan card
        const billsCard = document.querySelector('.bg-warning h4');
        if (billsCard) {
            billsCard.textContent = stats.bills.total_this_month || 0;
        }

        // Update Total Pendapatan card
        const paymentsCard = document.querySelector('.bg-info h4');
        if (paymentsCard) {
            paymentsCard.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(stats.payments.total_this_month || 0);
        }

        console.log('Dashboard stats updated successfully:', stats);
    } catch (error) {
        console.error('Error updating dashboard stats:', error);
    }
}

// Load stats on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardStats();
    checkWhatsAppStatus();
});
</script>
@endsection

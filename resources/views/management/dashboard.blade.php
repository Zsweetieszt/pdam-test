@extends('layouts.app')

@section('content')
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    Dashboard Manajemen
                </h2>
                <p class="text-muted">Overview keseluruhan sistem PDAM</p>
            </div>
            <div>
                <span class="badge bg-primary fs-6">Manajemen</span>
            </div>
        </div>
    </div>
</div>

<!-- Overview Stats -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Total Customer</h6>
                        <h4 class="mb-0">{{ $stats['overview']['total_customers'] ?? 0 }}</h4>
                        <small class="opacity-75">Pelanggan terdaftar</small>
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
                        <i class="fas fa-user-check fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Customer Aktif</h6>
                        <h4 class="mb-0">{{ $stats['overview']['active_customers'] ?? 0 }}</h4>
                        <small class="opacity-75">Customer aktif</small>
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
                        <h4 class="mb-0">{{ $stats['overview']['total_bills'] ?? 0 }}</h4>
                        <small class="opacity-75">Semua tagihan</small>
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
                        <h4 class="mb-0">Rp {{ number_format($stats['overview']['total_revenue'] ?? 0, 0, ',', '.') }}</h4>
                        <small class="opacity-75">Total keseluruhan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="row g-4 mb-4">
    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0">
                    <i class="fas fa-calendar-plus text-primary me-2"></i>
                    Tagihan Bulan Ini
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-invoice fa-3x text-primary opacity-50"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-1">{{ $stats['performance']['bills_this_month'] ?? 0 }}</h4>
                        <p class="text-muted mb-0">Tagihan yang dibuat bulan ini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0">
                    <i class="fas fa-credit-card text-success me-2"></i>
                    Pembayaran Bulan Ini
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-money-bill-wave fa-3x text-success opacity-50"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-1">{{ $stats['performance']['payments_this_month'] ?? 0 }}</h4>
                        <p class="text-muted mb-0">Pembayaran yang diterima</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0">
                    <i class="fas fa-percentage text-info me-2"></i>
                    Tingkat Koleksi
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-pie fa-3x text-info opacity-50"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-1">{{ $stats['performance']['collection_rate'] ?? 0 }}%</h4>
                        <p class="text-muted mb-0">Rasio pembayaran bulan ini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alerts & Recent Activity -->
<div class="row g-4 mb-4">
    <div class="col-lg-6 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Alert & Peringatan
                </h6>
            </div>
            <div class="card-body">
                @if(($stats['alerts']['overdue_bills'] ?? 0) > 0)
                <div class="alert alert-danger">
                    <i class="fas fa-calendar-times me-2"></i>
                    <strong>{{ $stats['alerts']['overdue_bills'] }}</strong> tagihan overdue
                </div>
                @endif

                @if(($stats['alerts']['pending_payments'] ?? 0) > 0)
                <div class="alert alert-warning">
                    <i class="fas fa-clock me-2"></i>
                    <strong>{{ $stats['alerts']['pending_payments'] }}</strong> pembayaran menunggu verifikasi
                </div>
                @endif

                @if(($stats['alerts']['inactive_customers'] ?? 0) > 0)
                <div class="alert alert-info">
                    <i class="fas fa-user-slash me-2"></i>
                    <strong>{{ $stats['alerts']['inactive_customers'] }}</strong> customer tidak aktif
                </div>
                @endif

                @if(($stats['alerts']['overdue_bills'] ?? 0) == 0 && ($stats['alerts']['pending_payments'] ?? 0) == 0 && ($stats['alerts']['inactive_customers'] ?? 0) == 0)
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Sistem dalam kondisi baik - tidak ada alert aktif
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0">
                    <i class="fas fa-history text-info me-2"></i>
                    Aktivitas Terbaru
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @forelse($stats['recent_activity'] ?? [] as $activity)
                    <div class="timeline-item mb-3">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <small class="text-muted">{{ $activity['created_at'] }}</small>
                            <p class="mb-0">
                                <strong>{{ $activity['user'] }}</strong> melakukan
                                <span class="badge bg-secondary">{{ $activity['action'] }}</span>
                                pada <code>{{ $activity['table'] }}</code>
                            </p>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted mb-0">Belum ada aktivitas terbaru</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Management Actions -->
<div class="row g-4">
    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0">
                    <i class="fas fa-users-cog text-primary me-2"></i>
                        Manajemen User & Customer
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users') }}" class="btn btn-primary">
                        <i class="fas fa-users me-1"></i> Kelola User
                    </a>
                    <a href="{{ route('admin.customers') }}" class="btn btn-success">
                        <i class="fas fa-address-book me-1"></i> Kelola Customer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0">
                    <i class="fas fa-file-invoice text-warning me-2"></i>
                    Manajemen Tagihan
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.billing') }}" class="btn btn-warning">
                        <i class="fas fa-calculator me-1"></i> Generate Tagihan
                    </a>
                    <a href="{{ route('manajemen.reports') }}" class="btn btn-info">
                        <i class="fas fa-list me-1"></i> Laporan & Analisis
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar text-success me-2"></i>
                    Laporan & Analisis
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('manajemen.reports') }}" class="btn btn-success">
                        <i class="fas fa-chart-line me-1"></i> Laporan Keuangan
                    </a>
                    <a href="{{ route('manajemen.analytics') }}" class="btn btn-secondary">
                        <i class="fas fa-chart-bar me-1"></i> Analisis Sistem
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-left: 15px;
    padding-bottom: 10px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -16px;
    top: 17px;
    width: 2px;
    height: calc(100% - 5px);
    background: #dee2e6;
}
</style>
@endsection
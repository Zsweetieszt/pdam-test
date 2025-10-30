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
                <p class="text-muted">Monitoring pembayaran dan pendapatan PDAM</p>
            </div>
            <div>
                <span class="badge bg-success fs-6">Keuangan</span>
            </div>
        </div>
    </div>
</div>

<!-- Financial Overview -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Pembayaran Hari Ini</h6>
                        <h4 class="mb-0">Rp {{ number_format($stats['payments']['total_today'] ?? 0, 0, ',', '.') }}</h4>
                        <small class="opacity-75">Total hari ini</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Pendapatan Bulan Ini</h6>
                        <h4 class="mb-0">Rp {{ number_format($stats['payments']['total_this_month'] ?? 0, 0, ',', '.') }}</h4>
                        <small class="opacity-75">Total bulan ini</small>
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
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Menunggu Verifikasi</h6>
                        <h4 class="mb-0">{{ $stats['payments']['pending_verification'] ?? 0 }}</h4>
                        <small class="opacity-75">Pembayaran pending</small>
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
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Diverifikasi Hari Ini</h6>
                        <h4 class="mb-0">{{ $stats['payments']['verified_today'] ?? 0 }}</h4>
                        <small class="opacity-75">Pembayaran verified</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Billing & Outstanding -->
<div class="row g-4 mb-4">
    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0">
                    <i class="fas fa-file-invoice-dollar text-danger me-2"></i>
                    Tagihan Outstanding
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger opacity-50"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-1">Rp {{ number_format($stats['bills']['total_outstanding'] ?? 0, 0, ',', '.') }}</h4>
                        <p class="text-muted mb-0">Total tagihan belum dibayar</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0">
                    <i class="fas fa-calendar-times text-warning me-2"></i>
                    Tagihan Overdue
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock fa-3x text-warning opacity-50"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-1">{{ $stats['bills']['overdue_count'] ?? 0 }}</h4>
                        <p class="text-muted mb-0">Tagihan melewati jatuh tempo</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Tagihan Lunas Bulan Ini
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-thumbs-up fa-3x text-success opacity-50"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-1">{{ $stats['bills']['paid_this_month'] ?? 0 }}</h4>
                        <p class="text-muted mb-0">Tagihan yang sudah dibayar</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Paying Customers -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0">
                    <i class="fas fa-trophy text-warning me-2"></i>
                    Top 5 Pelanggan Pembayaran Tertinggi (Bulan Ini)
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Pelanggan</th>
                                <th class="text-end">Total Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['revenue']['top_paying_customers'] ?? [] as $customer)
                            <tr>
                                <td>{{ $customer['customer_name'] }}</td>
                                <td class="text-end">Rp {{ number_format($customer['total_paid'], 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">Belum ada data pembayaran bulan ini</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Finance Actions -->
<div class="row g-4">
    <div class="col-lg-6 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0">
                    <i class="fas fa-tasks text-primary me-2"></i>
                    Tugas Keuangan
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('keuangan.payments') }}" class="btn btn-primary">
                        <i class="fas fa-credit-card me-1"></i> Kelola Pembayaran
                    </a>
                    <a href="{{ route('keuangan.billing') }}" class="btn btn-warning">
                        <i class="fas fa-file-invoice me-1"></i> Kelola Tagihan
                    </a>
                    <a href="{{ route('keuangan.whatsapp') }}" class="btn btn-success">
                        <i class="fas fa-comments me-1"></i> WhatsApp Integration
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0">
                    <i class="fas fa-bell text-warning me-2"></i>
                    Notifikasi & Alert
                </h6>
            </div>
            <div class="card-body">
                @if(($stats['payments']['pending_verification'] ?? 0) > 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ $stats['payments']['pending_verification'] }} pembayaran menunggu verifikasi
                </div>
                @endif

                @if(($stats['bills']['overdue_count'] ?? 0) > 0)
                <div class="alert alert-danger">
                    <i class="fas fa-calendar-times me-2"></i>
                    {{ $stats['bills']['overdue_count'] }} tagihan overdue
                </div>
                @endif

                @if(($stats['payments']['pending_verification'] ?? 0) == 0 && ($stats['bills']['overdue_count'] ?? 0) == 0)
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Semua pembayaran sudah diverifikasi dan tagihan dalam kondisi baik
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
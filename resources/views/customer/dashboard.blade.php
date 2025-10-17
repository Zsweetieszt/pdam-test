@extends('layouts.app')

@section('content')
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-user-circle text-primary me-2"></i>
                    Dashboard Pelanggan
                </h2>
                <p class="text-muted">Kelola tagihan dan pembayaran air Anda - Selamat datang, {{ auth()->user()->name }}</p>
            </div>
            <div>
                <span class="badge bg-primary fs-6">Customer Access</span>
            </div>
        </div>
    </div>
</div>

    <!-- Customer Info & Quick Status -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-id-card me-2"></i>
                        Informasi Pelanggan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td width="40%" class="text-muted">No. Pelanggan:</td>
                                    <td><strong>--</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nama:</td>
                                    <td><strong>{{ auth()->user()->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No. HP:</td>
                                    <td><strong>{{ auth()->user()->phone }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Alamat:</td>
                                    <td><strong>--</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td width="40%" class="text-muted">Tarif:</td>
                                    <td><strong>-- (Rumah Tangga)</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No. Meter:</td>
                                    <td><strong>--</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status:</td>
                                    <td><span class="badge bg-success">Aktif</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Zona:</td>
                                    <td><strong>--</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-water me-2"></i>
                        Pemakaian Bulan Ini
                    </h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-success mb-2">-- m³</h2>
                    <p class="text-muted mb-2">Konsumsi Periode {{ date('M Y') }}</p>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: 65%"></div>
                    </div>
                    <small class="text-muted">65% dari rata-rata bulanan</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Billing Status -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Tagihan Pending</h6>
                            <h4 class="mb-0">--</h4>
                            <small class="opacity-75">Belum dibayar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Jatuh Tempo</h6>
                            <h4 class="mb-0">-- Hari</h4>
                            <small class="opacity-75">Tersisa waktu</small>
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
                            <h6 class="mb-1">Lunas Bulan Ini</h6>
                            <h4 class="mb-0">--</h4>
                            <small class="opacity-75">Pembayaran</small>
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
                            <h6 class="mb-1">Total Bayar</h6>
                            <h4 class="mb-0">-- IDR</h4>
                            <small class="opacity-75">Tahun ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Features -->
    <div class="row g-4 mb-4">
        <!-- Current Bills -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-file-invoice text-primary me-2"></i>
                            Tagihan Saat Ini
                        </h6>
                        <a href="{{ route('customer.bills') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Sample Bill Card -->
                    <div class="card border-start border-warning border-4 mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <h6 class="text-warning mb-1">TAGIHAN {{ strtoupper(date('M Y')) }}</h6>
                                    <small class="text-muted">Periode: {{ date('d M') }} - {{ date('d M', strtotime('+1 month')) }}</small>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1 text-muted">Pemakaian:</p>
                                    <h6 class="mb-0">-- m³</h6>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1 text-muted">Total Tagihan:</p>
                                    <h5 class="mb-0 text-warning">IDR --</h5>
                                </div>
                                <div class="col-md-3 text-end">
                                    <button class="btn btn-warning btn-sm me-2" onclick="viewBillDetail()">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </button>
                                    <button class="btn btn-success btn-sm" onclick="payBill()">
                                        <i class="fas fa-credit-card me-1"></i> Bayar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Bills Message -->
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Belum ada tagihan yang perlu dibayar</h6>
                        <p class="text-muted mb-0">Tagihan baru akan muncul setiap awal bulan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Aksi Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <button class="btn btn-primary" onclick="viewAllBills()">
                            <i class="fas fa-file-invoice me-2"></i>
                            Lihat Semua Tagihan
                        </button>
                        
                        <button class="btn btn-success" onclick="paymentHistory()">
                            <i class="fas fa-history me-2"></i>
                            Riwayat Pembayaran
                        </button>
                        
                        <button class="btn btn-info" onclick="downloadBill()">
                            <i class="fas fa-download me-2"></i>
                            Download Tagihan
                        </button>
                        
                        <button class="btn btn-warning" onclick="reportIssue()">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Laporkan Masalah
                        </button>
                        
                        <button class="btn btn-secondary" onclick="updateProfile()">
                            <i class="fas fa-user-edit me-2"></i>
                            Update Profil
                        </button>
                    </div>

                    <hr>

                    <h6 class="mb-3">Info Kontak:</h6>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-phone text-primary me-2"></i> Customer Service</span>
                            <small class="text-primary">021-XXXXXX</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fab fa-whatsapp text-success me-2"></i> WhatsApp</span>
                            <small class="text-success">0812-XXXXXXX</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-clock text-info me-2"></i> Jam Layanan</span>
                            <small class="text-muted">08:00 - 17:00</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Tips -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0">
                        <i class="fas fa-history text-primary me-2"></i>
                        Aktivitas Terbaru
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Sample Timeline Items -->
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fas fa-check text-white small"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Pembayaran Berhasil</h6>
                                <p class="text-muted mb-0">Tagihan bulan lalu telah dibayar - IDR --</p>
                                <small class="text-muted">--</small>
                            </div>
                        </div>

                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-info d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fas fa-file text-white small"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Tagihan Baru</h6>
                                <p class="text-muted mb-0">Tagihan bulan ini telah dibuat</p>
                                <small class="text-muted">--</small>
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fab fa-whatsapp text-white small"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Notifikasi WhatsApp</h6>
                                <p class="text-muted mb-0">Reminder pembayaran dikirim ke {{ auth()->user()->phone }}</p>
                                <small class="text-muted">--</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <small class="text-muted">Tidak ada aktivitas terbaru</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        Tips Hemat Air
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0">
                            <h6 class="mb-1">💧 Gunakan Air Secukupnya</h6>
                            <p class="text-muted mb-0 small">Matikan keran saat tidak digunakan untuk menghemat air</p>
                        </div>
                        
                        <div class="list-group-item px-0">
                            <h6 class="mb-1">🚿 Mandi Lebih Singkat</h6>
                            <p class="text-muted mb-0 small">Kurangi waktu mandi untuk menghemat konsumsi air</p>
                        </div>
                        
                        <div class="list-group-item px-0">
                            <h6 class="mb-1">🔧 Periksa Kebocoran</h6>
                            <p class="text-muted mb-0 small">Segera perbaiki keran atau pipa yang bocor</p>
                        </div>
                        
                        <div class="list-group-item px-0">
                            <h6 class="mb-1">📊 Monitor Pemakaian</h6>
                            <p class="text-muted mb-0 small">Pantau konsumsi bulanan melalui dashboard ini</p>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Tips:</strong> Rata-rata konsumsi keluarga 4 orang adalah 15-20 m³/bulan
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewBillDetail() {
    alert('Menampilkan detail tagihan...');
}

function payBill() {
    if (confirm('Lanjutkan ke halaman pembayaran?')) {
        alert('Redirect ke payment gateway...');
    }
}

function viewAllBills() {
    window.location.href = '{{ route("customer.bills") }}';
}

function paymentHistory() {
    window.location.href = '{{ route("customer.payments") }}';
}

function downloadBill() {
    alert('Download tagihan PDF...');
}

function reportIssue() {
    if (confirm('Laporkan masalah ke customer service?')) {
        alert('Formulir laporan akan dibuka...');
    }
}

function updateProfile() {
    window.location.href = '{{ route("profile.index") }}';
}
</script>
@endsection

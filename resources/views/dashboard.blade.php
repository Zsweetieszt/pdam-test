@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Dashboard</h2>
                    <p class="text-muted">Sistem Penagihan PDAM dengan Integrasi WhatsApp</p>
                </div>
                <div>
                    <span class="badge bg-success">Base Project Ready</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded p-3">
                                <i class="fas fa-users text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Total Pelanggan</h6>
                            <h4 class="mb-0">--</h4>
                            <small class="text-muted">Belum ada data</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded p-3">
                                <i class="fas fa-file-invoice text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Tagihan Bulan Ini</h6>
                            <h4 class="mb-0">--</h4>
                            <small class="text-muted">Belum ada data</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded p-3">
                                <i class="fas fa-check-circle text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Sudah Dibayar</h6>
                            <h4 class="mb-0">--</h4>
                            <small class="text-muted">Belum ada data</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 rounded p-3">
                                <i class="fas fa-exclamation-triangle text-danger fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Belum Dibayar</h6>
                            <h4 class="mb-0">--</h4>
                            <small class="text-muted">Belum ada data</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0">
                        <i class="fas fa-tint text-primary me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button class="btn btn-outline-primary w-100" onclick="alert('Fitur belum diimplementasi')">
                                <i class="fas fa-plus me-2"></i>
                                Tambah Pelanggan
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-outline-warning w-100" onclick="alert('Fitur belum diimplementasi')">
                                <i class="fas fa-file-invoice me-2"></i>
                                Buat Tagihan
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-outline-success w-100" onclick="alert('Fitur belum diimplementasi')">
                                <i class="fas fa-money-bill me-2"></i>
                                Input Pembayaran
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-outline-info w-100" onclick="testWhatsApp()">
                                <i class="fab fa-whatsapp me-2"></i>
                                Test WhatsApp
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0">
                        <i class="fas fa-cog text-primary me-2"></i>
                        System Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Laravel Application</span>
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>
                            Active
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Database Connection</span>
                        <span class="badge bg-warning">
                            <i class="fas fa-clock me-1"></i>
                            Not Configured
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>WhatsApp Service</span>
                        <span class="badge bg-secondary" id="whatsapp-status">
                            <i class="fas fa-question me-1"></i>
                            Checking...
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Configuration</span>
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>
                            Ready
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Development Notes -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Status Pengembangan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <h6 class="alert-heading">
                            <i class="fas fa-rocket me-2"></i>
                            Base Project Laravel Telah Siap!
                        </h6>
                        <p class="mb-0">
                            Struktur dasar Laravel untuk Sistem Penagihan PDAM telah dibuat dengan konfigurasi WhatsApp Service.
                        </p>
                    </div>

                    <h6 class="mb-3">Yang Sudah Selesai:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Laravel 11 Project Structure
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            WhatsApp Service Integration (config/whatsapp.php)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            WhatsAppServiceProvider & WhatsAppService
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Basic Views & HomeController
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Database Schema Design (schema.sql)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            WA-Service Repository Integration
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            4 Role System: Admin, Keuangan, Customer, Manajemen
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Phone Verification (bukan email)
                        </li>
                    </ul>

                    <h6 class="mb-3 mt-4">Yang Akan Dikerjakan Selanjutnya:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-clock text-warning me-2"></i>
                            Database Migrations dari schema.sql
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock text-warning me-2"></i>
                            Eloquent Models & Relationships
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock text-warning me-2"></i>
                            Authentication & Authorization
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock text-warning me-2"></i>
                            CRUD Controllers & Views
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock text-warning me-2"></i>
                            WhatsApp Integration Testing
                        </li>
                    </ul>
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

// Check status on page load
document.addEventListener('DOMContentLoaded', checkWhatsAppStatus);
</script>
@endsection

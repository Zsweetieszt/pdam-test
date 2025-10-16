<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PDAM Billing System') }} - Register Customer</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<style>
    :root {
        --primary-blue: #2563eb;
        --secondary-blue: #3b82f6;
        --light-blue: #dbeafe;
        --dark-blue: #1e40af;
        --accent-blue: #60a5fa;
        --gradient-primary: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        --gradient-light: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --bg-light: #f8fafc;
        --white: #ffffff;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--gradient-primary);
        color: var(--text-primary);
        line-height: 1.6;
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* Navigation */
    .navbar-custom {
        background: rgba(255,255,255,0.98);
        backdrop-filter: blur(15px);
        box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        padding: 0.75rem 0;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        border-bottom: 1px solid rgba(37, 99, 235, 0.1);
    }

    .navbar-brand {
        font-weight: 700;
        color: var(--primary-blue) !important;
        font-size: 1.4rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }

    .navbar-brand:hover {
        color: var(--dark-blue) !important;
        transform: scale(1.02);
    }

    .navbar-brand i {
        margin-right: 0.75rem;
        font-size: 1.5rem;
    }

    .navbar-nav {
        align-items: center;
        gap: 0.5rem;
    }

    .nav-item {
        margin: 0 0.25rem;
    }

    .nav-link {
        color: var(--text-primary) !important;
        font-weight: 500;
        padding: 0.5rem 1rem !important;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav-link:hover {
        color: var(--primary-blue) !important;
        background: rgba(37, 99, 235, 0.1);
        transform: translateY(-1px);
    }

    .nav-link.active {
        color: var(--primary-blue) !important;
        background: rgba(37, 99, 235, 0.15);
        font-weight: 600;
    }

    .btn-custom {
        background: var(--gradient-primary);
        border: none;
        color: white !important;
        padding: 0.6rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        font-size: 0.95rem;
    }

    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
        color: white !important;
    }

    .btn-custom.active {
        background: var(--dark-blue);
        box-shadow: 0 2px 8px rgba(30, 64, 175, 0.4);
    }

    .navbar-toggler {
        border: none;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2837, 99, 235, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    .container-fluid-custom {
        padding-left: 2rem;
        padding-right: 2rem;
    }

    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 100px 0 2rem 0;
        background: var(--gradient-primary);
    }

    .auth-card {
        background: var(--white);
        border-radius: 20px;
        padding: 3rem;
        box-shadow: var(--shadow-lg);
        border: none;
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        position: relative;
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-icon {
        width: 80px;
        height: 80px;
        background: var(--gradient-primary);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 2rem;
    }

    .section-divider {
        border-bottom: 2px solid #e2e8f0;
        margin: 2rem 0 1.5rem 0;
        padding-bottom: 0.5rem;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--primary-blue);
        margin: 0;
    }

    .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }

    .form-control.is-valid {
        border-color: var(--success-color);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2310b981' d='m2.3 6.73.98-.98c.04-.04.04-.1 0-.14L2.18 4.5l.98-.98c.04-.04.04-.1 0-.14L2.18 2.5l-.98.98c-.04.04-.04.1 0 .14l1.1 1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .form-control.is-invalid {
        border-color: var(--danger-color);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23ef4444'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4M7.2 4.6l-1.4 1.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .input-group-text {
        background: var(--light-blue);
        border: 2px solid #e2e8f0;
        border-right: none;
        color: var(--primary-blue);
        border-radius: 12px 0 0 12px;
    }

    .input-group .form-control {
        border-left: none;
        border-radius: 0 12px 12px 0;
    }

    .input-group .form-control:focus {
        border-left: none;
    }

    .btn-primary {
        background: var(--gradient-primary);
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }

    .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-primary:disabled {
        background: #6b7280;
        cursor: not-allowed;
        transform: none;
    }

    .btn-outline-primary {
        border: 2px solid var(--primary-blue);
        color: var(--primary-blue);
        border-radius: 12px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background: var(--primary-blue);
        border-color: var(--primary-blue);
    }

    .alert-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
        border-radius: 12px;
    }

    .file-upload-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }

    .file-upload-input {
        position: absolute;
        left: -9999px;
    }

    .file-upload-label {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
        transition: all 0.3s ease;
        text-align: center;
        min-height: 60px;
    }

    .file-upload-label:hover {
        border-color: var(--primary-blue);
        background: var(--light-blue);
    }

    .file-upload-label.has-file {
        border-color: var(--success-color);
        background: #f0fdf4;
        color: var(--success-color);
    }

    .file-upload-label.has-error {
        border-color: var(--danger-color);
        background: #fef2f2;
        color: var(--danger-color);
    }

    .password-strength {
        margin-top: 0.5rem;
        font-size: 0.875rem;
    }

    .strength-indicator {
        height: 4px;
        border-radius: 2px;
        margin-top: 0.25rem;
        transition: all 0.3s ease;
        background: #e2e8f0;
    }

    .strength-weak { background: var(--danger-color); width: 33%; }
    .strength-medium { background: var(--warning-color); width: 66%; }
    .strength-strong { background: var(--success-color); width: 100%; }

    .help-text {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }

    .valid-feedback, .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
    }

    .valid-feedback {
        color: var(--success-color);
    }

    .invalid-feedback {
        color: var(--danger-color);
    }

    /* Dynamic Address/Meter Styles */
    .address-meter-group {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .address-meter-group:hover {
        border-color: var(--primary-blue);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
    }

    .address-meter-group.removable {
        border-color: #fca5a5;
        background: #f7f7f7;
    }

    .address-meter-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .address-meter-title {
        font-weight: 600;
        color: var(--primary-blue);
        font-size: 1rem;
        margin: 0;
        flex-grow: 1;
    }

    .btn-remove-address {
        background: #ef4444;
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 0.875rem;
    }

    .btn-remove-address:hover {
        background: #dc2626;
        transform: scale(1.1);
    }

    .btn-add-address {
        background: var(--success-color);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .btn-add-address:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    @media (max-width: 768px) {
        .container-fluid-custom {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .auth-card {
            padding: 2rem;
            margin: 1rem;
        }

        .navbar-custom {
            padding: 0.5rem 0;
        }

        .navbar-brand {
            font-size: 1.2rem;
        }

        .navbar-brand i {
            margin-right: 0.5rem;
            font-size: 1.3rem;
        }

        .navbar-nav {
            margin-top: 1rem;
            gap: 0.5rem;
        }

        .nav-link {
            padding: 0.75rem 1rem !important;
            border-radius: 8px;
            margin: 0.25rem 0;
        }

        .btn-custom {
            width: 100%;
            justify-content: center;
            margin-top: 0.5rem;
        }

        .address-meter-group {
            padding: 1rem;
        }
    }

    @media (max-width: 576px) {
        .navbar-brand {
            font-size: 1.1rem;
        }

        .container-fluid-custom {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
    }
</style>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid container-fluid-custom">
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="fas fa-tint me-2"></i>
            PDAM Billing System
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">
                        <i class="fas fa-home me-1"></i>
                        Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt me-1"></i>
                        Login
                    </a>
                </li>
                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="btn btn-custom ms-2 active" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i>
                            Register
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<!-- Auth Container -->
<div class="auth-container">
    <div class="container-fluid container-fluid-custom">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="auth-card">
                    <div class="auth-header">
                        <div class="auth-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h4 class="mb-2">Registrasi Customer Baru</h4>
                        <p class="text-muted">Lengkapi data diri dan informasi meter air Anda</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Terdapat kesalahan pada form:
                            </h6>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm">
                        @csrf
                        
                        <!-- Data Personal -->
                        <div class="section-divider">
                            <h5 class="section-title">
                                <i class="fas fa-user me-2"></i>
                                Data Personal
                            </h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Masukkan nama lengkap"
                                           required>
                                </div>
                                <div class="valid-feedback" id="name-valid"></div>
                                <div class="invalid-feedback" id="name-invalid"></div>
                                @error('name')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label fw-semibold">Nomor Telepon <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           placeholder="08xxxxxxxxxx"
                                           pattern="^08[0-9]{8,13}$"
                                           maxlength="15"
                                           required>
                                </div>
                                <div class="help-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Format: 08xxxxxxxx (10-15 digit total)
                                </div>
                                <div class="valid-feedback" id="phone-valid"></div>
                                <div class="invalid-feedback" id="phone-invalid"></div>
                                @error('phone')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ktp_number" class="form-label fw-semibold">Nomor KTP <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('ktp_number') is-invalid @enderror" 
                                       id="ktp_number" 
                                       name="ktp_number" 
                                       value="{{ old('ktp_number') }}" 
                                       placeholder="16 digit nomor KTP"
                                       pattern="[0-9]{16}"
                                       maxlength="16"
                                       required>
                            </div>
                            <div class="help-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Masukkan 16 digit nomor KTP
                            </div>
                            <div class="valid-feedback" id="ktp_number-valid"></div>
                            <div class="invalid-feedback" id="ktp_number-invalid"></div>
                            @error('ktp_number')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Upload KTP -->
                        <div class="mb-4">
                            <label for="ktp_file" class="form-label fw-semibold">Upload Foto/Scan KTP <span class="text-danger">*</span></label>
                            <div class="file-upload-wrapper">
                                <input type="file" 
                                       class="file-upload-input @error('ktp_file') is-invalid @enderror" 
                                       id="ktp_file" 
                                       name="ktp_file" 
                                       accept="image/*,.pdf"
                                       required>
                                <label for="ktp_file" class="file-upload-label" id="ktpLabel">
                                    <div>
                                        <i class="fas fa-cloud-upload-alt fa-2x mb-2 d-block"></i>
                                        <span>Klik untuk upload KTP</span>
                                        <br>
                                        <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                                    </div>
                                </label>
                            </div>
                            <div class="valid-feedback" id="ktp_file-valid"></div>
                            <div class="invalid-feedback" id="ktp_file-invalid"></div>
                            @error('ktp_file')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Data Alamat dan Meter -->
                        <div class="section-divider">
                            <h5 class="section-title">
                                <i class="fas fa-home me-2"></i>
                                Data Alamat & Meter Air
                            </h5>
                        </div>

                        <div id="addressMeterContainer">
                            <!-- Default first address/meter group -->
                            <div class="address-meter-group" data-index="0">
                                <div class="address-meter-header">
                                    <h6 class="address-meter-title">
                                        <i class="fas fa-home me-2"></i>
                                        Alamat & Meter #1
                                    </h6>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Alamat Rumah <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control address-input @error('addresses.0') is-invalid @enderror" 
                                                   name="addresses[]" 
                                                   value="{{ old('addresses.0') }}" 
                                                   placeholder="Alamat lengkap rumah"
                                                   required>
                                        </div>
                                        <div class="valid-feedback address-valid"></div>
                                        <div class="invalid-feedback address-invalid"></div>
                                        @error('addresses.0')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Nomor Meter <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-barcode"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control meter-input @error('meter_numbers.0') is-invalid @enderror" 
                                                   name="meter_numbers[]" 
                                                   value="{{ old('meter_numbers.0') }}" 
                                                   placeholder="Nomor meter air"
                                                   required>
                                        </div>
                                        <div class="help-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Lihat nomor pada meter air di alamat ini
                                        </div>
                                        <div class="valid-feedback meter-valid"></div>
                                        <div class="invalid-feedback meter-invalid"></div>
                                        @error('meter_numbers.0')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add Address Button -->
                        <button type="button" class="btn btn-add-address" id="addAddressBtn">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Alamat & Meter Lainnya
                        </button>

                        <!-- Password -->
                        <div class="section-divider">
                            <h5 class="section-title">
                                <i class="fas fa-lock me-2"></i>
                                Keamanan Akun
                            </h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Minimal 8 karakter"
                                           minlength="8"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                                <div class="password-strength" id="passwordStrength">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Kekuatan password:</small>
                                        <small id="strengthText" class="text-muted">-</small>
                                    </div>
                                    <div class="strength-indicator" id="strengthBar"></div>
                                </div>
                                <div class="help-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Minimal 8 karakter, kombinasi huruf besar, kecil, dan angka
                                </div>
                                <div class="valid-feedback" id="password-valid"></div>
                                <div class="invalid-feedback" id="password-invalid"></div>
                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Ulangi password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="fas fa-eye" id="toggleConfirmIcon"></i>
                                    </button>
                                </div>
                                <div class="valid-feedback" id="password_confirmation-valid"></div>
                                <div class="invalid-feedback" id="password_confirmation-invalid"></div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                <i class="fas fa-user-plus me-2"></i>
                                Daftar Sebagai Customer
                            </button>
                            <small class="text-muted text-center mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Tombol akan aktif setelah semua data valid terisi
                            </small>
                        </div>
                    </form>

                    <div class="text-center">
                        <p class="text-muted mb-3">Sudah punya akun?</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation state
    const validationState = {
        name: false,
        phone: false,
        ktp_number: false,
        ktp_file: false,
        password: false,
        password_confirmation: false,
        addresses: [false], // Array untuk multiple addresses
        meter_numbers: [false] // Array untuk multiple meter numbers
    };

    // Get form elements
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    const addAddressBtn = document.getElementById('addAddressBtn');
    const container = document.getElementById('addressMeterContainer');
    
    let addressIndex = 1; // Start from 1 since we have one default

    // Password visibility toggle
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const passwordConfirmation = document.getElementById('password_confirmation');
    const toggleConfirmIcon = document.getElementById('toggleConfirmIcon');

    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        toggleIcon.classList.toggle('fa-eye');
        toggleIcon.classList.toggle('fa-eye-slash');
    });

    toggleConfirmPassword.addEventListener('click', function() {
        const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmation.setAttribute('type', type);
        toggleConfirmIcon.classList.toggle('fa-eye');
        toggleConfirmIcon.classList.toggle('fa-eye-slash');
    });

    // Add Address/Meter functionality
    addAddressBtn.addEventListener('click', function() {
        const newGroup = document.createElement('div');
        newGroup.className = 'address-meter-group removable';
        newGroup.setAttribute('data-index', addressIndex);
        
        newGroup.innerHTML = `
            <div class="address-meter-header">
                <h6 class="address-meter-title">
                    <i class="fas fa-home me-2"></i>
                    Alamat & Meter #${addressIndex + 1}
                </h6>
                <button type="button" class="btn-remove-address" onclick="removeAddressGroup(${addressIndex})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Alamat Rumah <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                        <input type="text" 
                               class="form-control address-input" 
                               name="addresses[]" 
                               placeholder="Alamat lengkap rumah"
                               data-index="${addressIndex}"
                               required>
                    </div>
                    <div class="valid-feedback address-valid"></div>
                    <div class="invalid-feedback address-invalid"></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Nomor Meter <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-barcode"></i>
                        </span>
                        <input type="text" 
                               class="form-control meter-input" 
                               name="meter_numbers[]" 
                               placeholder="Nomor meter air"
                               data-index="${addressIndex}"
                               required>
                    </div>
                    <div class="help-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Lihat nomor pada meter air di alamat ini
                    </div>
                    <div class="valid-feedback meter-valid"></div>
                    <div class="invalid-feedback meter-invalid"></div>
                </div>
            </div>
        `;

        container.appendChild(newGroup);
        
        // Add validation states for new fields
        validationState.addresses.push(false);
        validationState.meter_numbers.push(false);
        
        // Add event listeners for new inputs
        const addressInput = newGroup.querySelector('.address-input');
        const meterInput = newGroup.querySelector('.meter-input');
        
        addressInput.addEventListener('input', function() {
            validateAddressField(this, addressIndex);
        });
        
        meterInput.addEventListener('input', function() {
            validateMeterField(this, addressIndex);
        });
        
        addressIndex++;
        updateSubmitButton();
    });

    // Remove address group function (global scope)
    window.removeAddressGroup = function(index) {
        const group = document.querySelector(`[data-index="${index}"]`);
        if (group && group.classList.contains('removable')) {
            group.remove();
            
            // Update validation arrays
            const groups = document.querySelectorAll('.address-meter-group');
            validationState.addresses = new Array(groups.length).fill(false);
            validationState.meter_numbers = new Array(groups.length).fill(false);
            
            // Revalidate all remaining fields
            groups.forEach((group, idx) => {
                const addressInput = group.querySelector('.address-input');
                const meterInput = group.querySelector('.meter-input');
                
                if (addressInput && addressInput.value) {
                    validateAddressField(addressInput, idx);
                }
                if (meterInput && meterInput.value) {
                    validateMeterField(meterInput, idx);
                }
                
                // Update title numbers
                const title = group.querySelector('.address-meter-title');
                title.innerHTML = `<i class="fas fa-home me-2"></i>Alamat & Meter #${idx + 1}`;
            });
            
            updateSubmitButton();
        }
    };

    // Update submit button state
    function updateSubmitButton() {
        const basicFields = [
            validationState.name,
            validationState.phone, 
            validationState.ktp_number,
            validationState.ktp_file,
            validationState.password,
            validationState.password_confirmation
        ];
        
        const basicValid = basicFields.every(state => state === true);
        const addressesValid = validationState.addresses.every(state => state === true);
        const metersValid = validationState.meter_numbers.every(state => state === true);
        
        const allValid = basicValid && addressesValid && metersValid;
        
        submitBtn.disabled = !allValid;
        
        if (allValid) {
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-primary');
        } else {
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-secondary');
        }
    }

    // Validation functions
    function validateField(fieldName, value, validationFn, errorMessage) {
        const field = document.getElementById(fieldName);
        const validFeedback = document.getElementById(`${fieldName}-valid`);
        const invalidFeedback = document.getElementById(`${fieldName}-invalid`);

        const isValid = validationFn(value);
        validationState[fieldName] = isValid;

        if (value.trim() === '') {
            field.classList.remove('is-valid', 'is-invalid');
            validFeedback.textContent = '';
            invalidFeedback.textContent = '';
            validationState[fieldName] = false;
        } else if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            validFeedback.textContent = 'Valid';
            invalidFeedback.textContent = '';
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            validFeedback.textContent = '';
            invalidFeedback.textContent = errorMessage;
        }

        updateSubmitButton();
    }

    function validateAddressField(field, index) {
        const value = field.value;
        const group = field.closest('.address-meter-group');
        const validFeedback = group.querySelector('.address-valid');
        const invalidFeedback = group.querySelector('.address-invalid');
        
        const isValid = value.trim().length >= 10;
        validationState.addresses[index] = isValid;

        if (value.trim() === '') {
            field.classList.remove('is-valid', 'is-invalid');
            validFeedback.textContent = '';
            invalidFeedback.textContent = '';
            validationState.addresses[index] = false;
        } else if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            validFeedback.textContent = 'Valid';
            invalidFeedback.textContent = '';
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            validFeedback.textContent = '';
            invalidFeedback.textContent = 'Alamat minimal 10 karakter';
        }

        updateSubmitButton();
    }

    function validateMeterField(field, index) {
        const value = field.value;
        const group = field.closest('.address-meter-group');
        const validFeedback = group.querySelector('.meter-valid');
        const invalidFeedback = group.querySelector('.meter-invalid');
        
        const isValid = value.trim().length >= 3;
        validationState.meter_numbers[index] = isValid;

        if (value.trim() === '') {
            field.classList.remove('is-valid', 'is-invalid');
            validFeedback.textContent = '';
            invalidFeedback.textContent = '';
            validationState.meter_numbers[index] = false;
        } else if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            validFeedback.textContent = 'Valid';
            invalidFeedback.textContent = '';
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            validFeedback.textContent = '';
            invalidFeedback.textContent = 'Nomor meter minimal 3 karakter';
        }

        updateSubmitButton();
    }

    // Name validation
    document.getElementById('name').addEventListener('input', function() {
        validateField('name', this.value, 
            value => value.trim().length >= 2 && /^[a-zA-Z\s.,'-]+$/.test(value.trim()),
            'Nama minimal 2 karakter dan hanya boleh mengandung huruf, spasi, dan tanda baca yang valid'
        );
    });

    // Phone validation
    document.getElementById('phone').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        
        if (value.length > 0 && !value.startsWith('08')) {
            if (value.startsWith('8')) {
                value = '0' + value;
            } else if (value.startsWith('0') && value.charAt(1) !== '8') {
                value = '08' + value.substring(1);
            }
        }
        
        if (value.length > 15) {
            value = value.substring(0, 15);
        }
        
        this.value = value;

        validateField('phone', value,
            val => /^08[0-9]{8,13}$/.test(val),
            'Format nomor telepon tidak valid. Contoh: 08123456789 (10-15 digit total)'
        );
    });

    // KTP validation
    document.getElementById('ktp_number').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 16) {
            value = value.substring(0, 16);
        }
        this.value = value;

        validateField('ktp_number', value,
            val => /^[0-9]{16}$/.test(val),
            'Nomor KTP harus terdiri dari 16 digit angka'
        );
    });

    // Add event listeners for default address/meter fields
    document.querySelector('.address-input').addEventListener('input', function() {
        validateAddressField(this, 0);
    });
    
    document.querySelector('.meter-input').addEventListener('input', function() {
        validateMeterField(this, 0);
    });

    // Password strength checker
    function checkPasswordStrength(password) {
        let score = 0;
        let feedback = [];

        if (password.length >= 8) score += 25;
        else feedback.push('Minimal 8 karakter');

        if (/[A-Z]/.test(password)) score += 25;
        else feedback.push('Huruf besar');

        if (/[a-z]/.test(password)) score += 25;
        else feedback.push('Huruf kecil');

        if (/[0-9]/.test(password)) score += 25;
        else feedback.push('Angka');

        return { score, feedback };
    }

    // Password validation
    document.getElementById('password').addEventListener('input', function() {
        const { score, feedback } = checkPasswordStrength(this.value);
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        const validFeedback = document.getElementById('password-valid');
        const invalidFeedback = document.getElementById('password-invalid');

        strengthBar.className = 'strength-indicator';

        if (this.value === '') {
            strengthBar.className = 'strength-indicator';
            strengthText.textContent = '-';
            strengthText.className = 'text-muted';
            this.classList.remove('is-valid', 'is-invalid');
            validFeedback.textContent = '';
            invalidFeedback.textContent = '';
            validationState.password = false;
        } else if (score < 50) {
            strengthBar.classList.add('strength-weak');
            strengthText.textContent = 'Lemah';
            strengthText.className = 'text-danger';
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
            validFeedback.textContent = '';
            invalidFeedback.textContent = `Password terlalu lemah. Kurang: ${feedback.join(', ')}`;
            validationState.password = false;
        } else if (score < 100) {
            strengthBar.classList.add('strength-medium');
            strengthText.textContent = 'Sedang';
            strengthText.className = 'text-warning';
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
            validFeedback.textContent = '';
            invalidFeedback.textContent = `Password cukup kuat, tapi masih kurang: ${feedback.join(', ')}`;
            validationState.password = false;
        } else {
            strengthBar.classList.add('strength-strong');
            strengthText.textContent = 'Kuat';
            strengthText.className = 'text-success';
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
            validFeedback.textContent = 'Password kuat';
            invalidFeedback.textContent = '';
            validationState.password = true;
        }

        if (passwordConfirmation.value) {
            validatePasswordConfirmation();
        }

        updateSubmitButton();
    });

    // Password confirmation validation
    function validatePasswordConfirmation() {
        const passwordValue = password.value;
        const confirmValue = passwordConfirmation.value;
        const validFeedback = document.getElementById('password_confirmation-valid');
        const invalidFeedback = document.getElementById('password_confirmation-invalid');

        if (confirmValue === '') {
            passwordConfirmation.classList.remove('is-valid', 'is-invalid');
            validFeedback.textContent = '';
            invalidFeedback.textContent = '';
            validationState.password_confirmation = false;
        } else if (passwordValue === confirmValue && validationState.password) {
            passwordConfirmation.classList.remove('is-invalid');
            passwordConfirmation.classList.add('is-valid');
            validFeedback.textContent = 'Password cocok';
            invalidFeedback.textContent = '';
            validationState.password_confirmation = true;
        } else {
            passwordConfirmation.classList.remove('is-valid');
            passwordConfirmation.classList.add('is-invalid');
            validFeedback.textContent = '';
            if (!validationState.password) {
                invalidFeedback.textContent = 'Password utama harus valid terlebih dahulu';
            } else {
                invalidFeedback.textContent = 'Password tidak cocok';
            }
            validationState.password_confirmation = false;
        }

        updateSubmitButton();
    }

    passwordConfirmation.addEventListener('input', validatePasswordConfirmation);

    // File upload validation
    function validateFile(inputId, maxSize, allowedTypes, errorMessages) {
        const input = document.getElementById(inputId);
        const label = document.getElementById(inputId === 'ktp_file' ? 'ktpLabel' : 'meterLabel');
        const validFeedback = document.getElementById(`${inputId}-valid`);
        const invalidFeedback = document.getElementById(`${inputId}-invalid`);

        input.addEventListener('change', function() {
            const file = this.files[0];
            let isValid = true;
            let errorMessage = '';

            if (!file) {
                validationState[inputId] = false;
                label.classList.remove('has-file', 'has-error');
                validFeedback.textContent = '';
                invalidFeedback.textContent = '';
                
                label.innerHTML = `
                    <div>
                        <i class="fas fa-cloud-upload-alt fa-2x mb-2 d-block"></i>
                        <span>Klik untuk upload KTP</span>
                        <br>
                        <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                    </div>
                `;
            } else {
                if (file.size > maxSize) {
                    isValid = false;
                    errorMessage = errorMessages.size;
                }

                const fileType = file.type;
                const fileName = file.name.toLowerCase();
                const isValidType = allowedTypes.some(type => {
                    if (type.startsWith('image/')) {
                        return fileType.startsWith('image/');
                    } else if (type === 'application/pdf') {
                        return fileType === 'application/pdf' || fileName.endsWith('.pdf');
                    }
                    return fileType === type;
                });

                if (!isValidType) {
                    isValid = false;
                    errorMessage = errorMessages.type;
                }

                validationState[inputId] = isValid;

                if (isValid) {
                    label.classList.remove('has-error');
                    label.classList.add('has-file');
                    label.innerHTML = `
                        <div>
                            <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                            <span class="text-success">${file.name}</span>
                            <br>
                            <small class="text-muted">File berhasil dipilih (${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
                        </div>
                    `;
                    validFeedback.textContent = 'File valid';
                    invalidFeedback.textContent = '';
                } else {
                    label.classList.remove('has-file');
                    label.classList.add('has-error');
                    label.innerHTML = `
                        <div>
                            <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block text-danger"></i>
                            <span class="text-danger">File tidak valid</span>
                            <br>
                            <small class="text-danger">${errorMessage}</small>
                        </div>
                    `;
                    validFeedback.textContent = '';
                    invalidFeedback.textContent = errorMessage;
                }
            }

            updateSubmitButton();
        });
    }

    // Initialize file validations
    validateFile('ktp_file', 2 * 1024 * 1024, ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'], {
        size: 'Ukuran file maksimal 2MB',
        type: 'Format file harus JPG, PNG, atau PDF'
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        const basicFields = [
            validationState.name,
            validationState.phone,
            validationState.ktp_number,
            validationState.ktp_file,
            validationState.password,
            validationState.password_confirmation
        ];
        
        const basicValid = basicFields.every(state => state === true);
        const addressesValid = validationState.addresses.every(state => state === true);
        const metersValid = validationState.meter_numbers.every(state => state === true);
        
        const allValid = basicValid && addressesValid && metersValid;
        
        if (!allValid) {
            e.preventDefault();
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger';
            errorDiv.innerHTML = `
                <h6 class="alert-heading">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Form belum lengkap atau masih ada kesalahan
                </h6>
                <p class="mb-0 mt-2">Silakan periksa dan lengkapi semua field yang ditandai dengan warna merah.</p>
            `;
            
            const existingError = form.querySelector('.alert-danger');
            if (existingError) {
                existingError.remove();
            }
            
            form.insertBefore(errorDiv, form.firstChild);
            document.querySelector('.auth-card').scrollIntoView({ behavior: 'smooth' });
            
            return false;
        }

        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mendaftar...';
        submitBtn.disabled = true;
    });

    // Initialize submit button state
    updateSubmitButton();
});
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PDAM Billing System') }} - Sistem Penagihan PDAM</title>
    
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
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--bg-light);
        color: var(--text-primary);
        line-height: 1.6;
        overflow-x: hidden;
    }

    /* Navigation */
    .navbar-custom {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
        box-shadow: var(--shadow);
        padding: 1rem 0;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }

    .navbar-brand {
        font-weight: 700;
        color: var(--primary-blue) !important;
        font-size: 1.5rem;
    }

    .nav-link {
        color: var(--text-primary) !important;
        font-weight: 500;
        margin: 0 0.5rem;
        transition: color 0.3s ease;
    }

    .nav-link:hover {
        color: var(--primary-blue) !important;
    }

    /* Hero Section */
    .hero-section {
        background: var(--gradient-primary);
        min-height: 100vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
        color: white;
        padding-top: 80px;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23a)"/><circle cx="800" cy="300" r="150" fill="url(%23a)"/><circle cx="400" cy="700" r="120" fill="url(%23a)"/></svg>') no-repeat center center;
        background-size: cover;
        opacity: 0.5;
    }

    .hero-content {
        position: relative;
        z-index: 10;
    }

    .hero-title {
        font-size: 4rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin: 4rem 0;
    }

    .stat-card {
        background: var(--white);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        text-align: center;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
    }

    .stat-icon {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
    }

    .stat-icon.primary {
        background: var(--light-blue);
        color: var(--primary-blue);
    }

    .stat-icon.success {
        background: #dcfce7;
        color: #16a34a;
    }

    .stat-icon.warning {
        background: #fef3c7;
        color: #d97706;
    }

    .stat-icon.info {
        background: #e0f2fe;
        color: #0891b2;
    }

    .stat-icon.whatsapp {
        background: #dcfce7;
        color: #25d366;
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 1.2rem;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .stat-description {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    /* Features Section */
    .features-section {
        padding: 8rem 0;
        background: var(--white);
    }

    .feature-card {
        background: var(--white);
        border-radius: 20px;
        padding: 3rem;
        box-shadow: var(--shadow);
        border: 1px solid #e2e8f0;
        height: 100%;
        transition: all 0.3s ease;
        text-align: center;
    }

    .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-blue);
    }

    .feature-icon {
        width: 100px;
        height: 100px;
        border-radius: 20px;
        background: var(--gradient-primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 2rem;
    }

    .btn-custom {
        background: var(--gradient-primary);
        border: none;
        color: white;
        padding: 1.2rem 2.5rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
        font-size: 1.1rem;
    }

    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    .btn-outline-custom {
        background: transparent;
        border: 2px solid white;
        color: white;
        padding: 1.2rem 2.5rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        font-size: 1.1rem;
    }

    .btn-outline-custom:hover {
        background: white;
        color: var(--primary-blue);
    }

    .section-title {
        font-size: 3rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
        text-align: center;
    }

    .section-subtitle {
        font-size: 1.25rem;
        color: var(--text-secondary);
        text-align: center;
        margin-bottom: 4rem;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }

    .info-section {
        background: var(--bg-light);
        padding: 6rem 0;
    }

    .system-status {
        background: var(--white);
        padding: 4rem;
        border-radius: 24px;
        margin: 2rem 0;
        box-shadow: var(--shadow-lg);
    }

    .status-item {
        background: var(--white);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--shadow);
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }

    .status-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .status-icon {
        width: 70px;
        height: 70px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.8rem;
        color: white;
    }

    .status-connected { background: #16a34a; }
    .status-warning { background: #d97706; }
    .status-active { background: var(--primary-blue); }

    /* Full window utilities */
    .full-container {
        width: 100vw;
        max-width: 100vw;
        padding: 0;
        margin: 0;
    }

    .container-fluid-custom {
        padding-left: 2rem;
        padding-right: 2rem;
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .stat-card, .feature-card {
            padding: 1.5rem;
        }

        .container-fluid-custom {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .section-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 576px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .btn-custom, .btn-outline-custom {
            padding: 1rem 1.5rem;
            font-size: 1rem;
        }
    }

    .hero-section {
        background: var(--gradient-primary);
        min-height: 100vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
        color: white;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23a)"/><circle cx="800" cy="300" r="150" fill="url(%23a)"/><circle cx="400" cy="700" r="120" fill="url(%23a)"/></svg>') no-repeat center center;
        background-size: cover;
        opacity: 0.5;
    }

    .hero-content {
        position: relative;
        z-index: 10;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin: 4rem 0;
    }

    .stat-card {
        background: var(--white);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        text-align: center;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .stat-icon {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
    }

    .stat-icon.primary {
        background: var(--light-blue);
        color: var(--primary-blue);
    }

    .stat-icon.success {
        background: #dcfce7;
        color: #16a34a;
    }

    .stat-icon.warning {
        background: #fef3c7;
        color: #d97706;
    }

    .stat-icon.info {
        background: #e0f2fe;
        color: #0891b2;
    }

    .stat-icon.whatsapp {
        background: #dcfce7;
        color: #25d366;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 1.1rem;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .stat-description {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .features-section {
        padding: 6rem 0;
        background: var(--bg-light);
    }

    .feature-card {
        background: var(--white);
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: var(--shadow);
        border: 1px solid #e2e8f0;
        height: 100%;
        transition: all 0.3s ease;
        text-align: center;
    }

    .feature-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-blue);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        background: var(--gradient-primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 1.5rem;
    }

    .btn-custom {
        background: var(--gradient-primary);
        border: none;
        color: white;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
    }

    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    .btn-outline-custom {
        background: transparent;
        border: 2px solid white;
        color: white;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-outline-custom:hover {
        background: white;
        color: var(--primary-blue);
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
        text-align: center;
    }

    .section-subtitle {
        font-size: 1.125rem;
        color: var(--text-secondary);
        text-align: center;
        margin-bottom: 4rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .info-section {
        background: var(--white);
        padding: 4rem 0;
        border-top: 1px solid #e2e8f0;
    }

    .system-status {
        background: var(--gradient-light);
        padding: 3rem 0;
        border-radius: 20px;
        margin: 2rem 0;
    }

    .status-item {
        background: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        text-align: center;
        transition: all 0.3s ease;
    }

    .status-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .status-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
        color: white;
    }

    .status-connected { background: #16a34a; }
    .status-warning { background: #d97706; }
    .status-active { background: var(--primary-blue); }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .stat-card, .feature-card {
            padding: 1.5rem;
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
                @if (Route::has('login'))
                    @auth
                        <li class="nav-item">
                            @if(auth()->user()->hasRole('admin'))
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i>
                                    Dashboard Admin
                                </a>
                            @elseif(auth()->user()->hasRole('keuangan'))
                                <a class="nav-link" href="{{ route('keuangan.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i>
                                    Dashboard Keuangan
                                </a>
                            @elseif(auth()->user()->hasRole('manajemen'))
                                <a class="nav-link" href="{{ route('manajemen.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i>
                                    Dashboard Manajemen
                                </a>
                            @elseif(auth()->user()->hasRole('customer'))
                                <a class="nav-link" href="{{ route('customer.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i>
                                    Dashboard Saya
                                </a>
                            @else
                                <a class="nav-link" href="{{ url('/dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i>
                                    Dashboard
                                </a>
                            @endif
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-1"></i>
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Login
                            </a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="btn btn-custom ms-2" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-1"></i>
                                    Register
                                </a>
                            </li>
                        @endif
                    @endauth
                @endif
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container-fluid container-fluid-custom">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <h1 class="hero-title">
                    {{ $title ?? 'Sistem Penagihan PDAM' }}
                </h1>
                <h2 class="h3 mb-4 text-light">
                    {{ $subtitle ?? 'Dengan Integrasi WhatsApp Modern' }}
                </h2>
                <p class="lead mb-4">
                    Kelola penagihan air bersih dengan mudah dan efisien. Kirim notifikasi langsung melalui WhatsApp 
                    untuk meningkatkan tingkat pembayaran hingga 85% dan mempercepat proses penagihan.
                </p>
                
                <!-- Quick Stats -->
                <div class="row g-3 mb-5">
                    <div class="col-4">
                        <div class="text-center">
                            <h3 class="display-6 mb-1">98%</h3>
                            <small class="text-light">Akurasi Sistem</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <h3 class="display-6 mb-1">24/7</h3>
                            <small class="text-light">Operasional</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <h3 class="display-6 mb-1">5 Detik</h3>
                            <small class="text-light">Kirim Notifikasi</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-3">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-custom btn-lg">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard Admin
                            </a>
                        @elseif(auth()->user()->isKeuangan())
                            <a href="{{ route('keuangan.dashboard') }}" class="btn btn-custom btn-lg">
                                <i class="fas fa-calculator"></i>
                                Dashboard Keuangan
                            </a>
                        @elseif(auth()->user()->isManajemen())
                            <a href="{{ route('manajemen.dashboard') }}" class="btn btn-custom btn-lg">
                                <i class="fas fa-chart-line"></i>
                                Dashboard Manajemen
                            </a>
                        @elseif(auth()->user()->isCustomer())
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-custom btn-lg">
                                <i class="fas fa-user"></i>
                                Dashboard Saya
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-custom btn-lg">
                            <i class="fas fa-sign-in-alt"></i>
                            Masuk Sistem
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-custom btn-lg">
                            <i class="fas fa-user-plus"></i>
                            Daftar Customer
                        </a>
                    @endauth
                </div>
            </div>
            <div class="col-lg-6">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number">1,250+</div>
                        <div class="stat-label">Total Pelanggan</div>
                        <div class="stat-description">Terdaftar aktif dalam sistem</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-number">85%</div>
                        <div class="stat-label">Tingkat Pembayaran</div>
                        <div class="stat-description">Peningkatan dengan WhatsApp</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="stat-number">15,000+</div>
                        <div class="stat-label">Pesan Terkirim</div>
                        <div class="stat-description">Notifikasi otomatis per bulan</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number">2 Menit</div>
                        <div class="stat-label">Proses Tagihan</div>
                        <div class="stat-description">Dari input hingga notifikasi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container-fluid container-fluid-custom">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 mb-4">Fitur Unggulan Sistem</h2>
                <p class="lead text-muted">Dilengkapi dengan teknologi terkini untuk memudahkan pengelolaan penagihan air</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h3>Integrasi WhatsApp</h3>
                    <p>Kirim notifikasi tagihan dan pengingat pembayaran langsung ke WhatsApp pelanggan secara otomatis.</p>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon primary">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Dashboard Analitik</h3>
                    <p>Pantau performa penagihan, tingkat pembayaran, dan tren konsumsi air dengan grafik interaktif.</p>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon success">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Keamanan Data</h3>
                    <p>Sistem keamanan berlapis dengan enkripsi data dan kontrol akses berdasarkan peran pengguna.</p>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon warning">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Responsive</h3>
                    <p>Akses sistem dari perangkat apapun dengan tampilan yang optimal di smartphone dan tablet.</p>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon danger">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Real-time Updates</h3>
                    <p>Sinkronisasi data real-time untuk memastikan informasi tagihan dan pembayaran selalu terkini.</p>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon info">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>Laporan Otomatis</h3>
                    <p>Generate laporan keuangan dan operasional secara otomatis dengan format yang dapat disesuaikan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Information Section -->
<section class="info-section">
    <div class="container-fluid container-fluid-custom">
        <div class="system-status">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h3 class="display-6 mb-4">Status Sistem Real-Time</h3>
                    <p class="lead text-muted">Monitoring kesehatan sistem dan konektivitas layanan</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="status-item">
                        <div class="status-icon status-connected">
                            <i class="fas fa-database"></i>
                        </div>
                        <h6 class="mb-2">Database Server</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-success">Online</span>
                            <small class="text-muted">99.9% Uptime</small>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: 99%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="status-item">
                        <div class="status-icon status-warning">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <h6 class="mb-2">WhatsApp Gateway</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-warning">Setup Required</span>
                            <small class="text-muted">Ready to Configure</small>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-warning" style="width: 75%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="status-item">
                        <div class="status-icon status-active">
                            <i class="fas fa-server"></i>
                        </div>
                        <h6 class="mb-2">Laravel Application</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">Active</span>
                            <small class="text-muted">v11.0 Latest</small>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Benefits -->
        <div class="row g-4 mt-5">
            <div class="col-lg-10 mx-auto">
                <div class="text-center mb-5">
                    <h3 class="display-6">Mengapa Memilih Sistem Kami?</h3>
                    <p class="lead text-muted">Keunggulan yang membuat sistem penagihan PDAM lebih efektif</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-rocket text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">Implementasi Cepat</h6>
                                <small class="text-muted">Setup sistem dalam 1-2 hari kerja dengan migrasi data lengkap</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-money-bill-wave text-success"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">ROI Tinggi</h6>
                                <small class="text-muted">Peningkatan koleksi pembayaran hingga 85% dalam 3 bulan</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-headset text-info"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">Support 24/7</h6>
                                <small class="text-muted">Tim teknis siap membantu kapan saja via WhatsApp & Email</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-sync text-warning"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">Update Berkala</h6>
                                <small class="text-muted">Fitur baru dan perbaikan bug secara otomatis tanpa biaya tambahan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Call to Action -->
        <div class="text-center mt-5">
            <div class="bg-light rounded-4 p-5">
                <h4 class="display-6 mb-4">Siap Meningkatkan Efisiensi Penagihan?</h4>
                <p class="lead text-muted mb-5">Hubungi tim kami untuk konsultasi gratis dan demo sistem</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-custom btn-lg">
                            <i class="fas fa-user-plus"></i>
                            Daftar Sekarang
                        </a>
                        <a href="https://wa.me/6281234567890?text=Halo, saya tertarik dengan sistem penagihan PDAM" target="_blank" class="btn btn-outline-primary btn-lg">
                            <i class="fab fa-whatsapp"></i>
                            Konsultasi via WhatsApp
                        </a>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-custom btn-lg">
                            <i class="fas fa-tachometer-alt"></i>
                            Ke Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

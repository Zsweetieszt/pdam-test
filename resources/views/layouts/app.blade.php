<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>PDAM Billing System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <meta name="api-token" content="{{ auth()->user()->createToken('AdminToken')->plainTextToken ?? '' }}">
    
    <!-- Custom CSS -->
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
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }

        .sidebar {
            height: 100vh;
            background: var(--gradient-primary);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-x: hidden;
            box-shadow: var(--shadow-lg);
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar .sidebar-header {
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            white-space: nowrap;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }
        
        .sidebar .sidebar-header .d-flex {
            gap: 2.5rem;
        }
        
        .sidebar.collapsed .sidebar-header .sidebar-brand {
            opacity: 0;
            transform: scale(0.8);
        }
        
        .sidebar .sidebar-brand {
            transition: all 0.3s ease;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .sidebar .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar .sidebar-menu li {
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        
        .sidebar .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 1rem;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
            font-weight: 500;
        }
        
        .sidebar .sidebar-menu a:hover {
            background: rgba(255,255,255,0.12);
            color: white;
            transform: translateX(3px);
        }
        
        .sidebar .sidebar-menu a.active {
            background: var(--gradient-light);
            color: var(--primary-blue);
            border-right: 4px solid var(--light-blue);
            font-weight: 600;
        }
        
        .sidebar .sidebar-menu i {
            width: 20px;
            min-width: 20px;
            margin-right: 12px;
            text-align: center;
        }
        
        .sidebar.collapsed .sidebar-menu .menu-text {
            opacity: 0;
            transform: translateX(-10px);
        }
        
        .sidebar .sidebar-menu .menu-text {
            transition: all 0.3s ease;
        }
        
        /* Tooltip untuk collapsed sidebar */
        .sidebar.collapsed .sidebar-menu a {
            position: relative;
        }
        
        .sidebar.collapsed .sidebar-menu a:hover::after {
            content: attr(data-title);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
            white-space: nowrap;
            z-index: 1001;
            margin-left: 10px;
            font-size: 0.875rem;
        }
        
        .sidebar.collapsed .sidebar-menu a:hover::before {
            content: '';
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: rgba(0,0,0,0.8);
            margin-left: 5px;
            z-index: 1001;
        }
        
        .main-content {
            transition: all 0.3s ease;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        
        .main-content.with-sidebar {
            margin-left: 280px;
            
        }
        
        .main-content.with-sidebar.collapsed {
            margin-left: 70px;
            
        }
        
        .topbar {
            background: var(--gradient-primary);
            color: white;
            padding: 1rem 1.5rem;
            box-shadow: var(--shadow-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .topbar .navbar-brand {
            color: white !important;
            font-weight: 600;
        }

        .topbar .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            transition: all 0.3s ease;
        }

        .topbar .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.375rem;
        }
        
        .content-wrapper {
            padding: 2rem 1.5rem;
            background: var(--bg-light);
            min-height: calc(100vh - 76px);
        }

        .content-wrapper .card {
            box-shadow: var(--shadow);
            border: none;
            border-radius: 0.75rem;
        }

        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 250px;
                margin-left: -250px;
            }
            
            .sidebar.show {
                margin-left: 0;
            }
            
            .main-content.with-sidebar {
                margin-left: 0;
            }
            
            .sidebar.collapsed {
                margin-left: -250px;
            }
        }
        
        .guest-navbar {
            background: var(--gradient-primary);
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .guest-navbar .navbar-brand {
            font-weight: 600;
        }

        .guest-navbar .nav-link {
            transition: all 0.3s ease;
            border-radius: 0.375rem;
            margin: 0 0.25rem;
        }

        .guest-navbar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .guest-content {
            min-height: calc(100vh - 76px);
            background: var(--gradient-light);
        }
        
        .guest-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: var(--shadow-lg);
        }
        
        .role-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .role-admin { 
            background: var(--gradient-primary); 
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }
        .role-keuangan { 
            background: linear-gradient(135deg, var(--secondary-blue) 0%, var(--primary-blue) 100%); 
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }
        .role-manajemen { 
            background: linear-gradient(135deg, var(--accent-blue) 0%, var(--secondary-blue) 100%); 
            box-shadow: 0 2px 8px rgba(96, 165, 250, 0.3);
        }
        .role-customer { 
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--primary-blue) 100%); 
            box-shadow: 0 2px 8px rgba(30, 64, 175, 0.3);
        }
        
        /* Responsive: hide role-badge when sidebar collapsed */
        .sidebar.collapsed .role-badge-wrapper {
            display: none !important;
        }
        
        .role-badge-wrapper {
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>
    @auth
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="d-flex align-items-center">
                <i class="fas fa-tint fa-2x me-2"></i>
                <div class="sidebar-brand">
                    <h5 class="mb-0">PDAM</h5>
                    <small class="opacity-75">Billing System</small>
                </div>
            </div>
            <div class="mt-2 role-badge-wrapper">
                <span class="role-badge role-{{ auth()->user()->role->name }}">
                    {{ ucfirst(auth()->user()->role->name) }}
                </span>
            </div>
        </div>
        
        <ul class="sidebar-menu">
            <!-- Dashboard -->
            <li>
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-title="Dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                @elseif(auth()->user()->hasRole('keuangan'))
                    <a href="{{ route('keuangan.dashboard') }}" class="{{ request()->routeIs('keuangan.dashboard') ? 'active' : '' }}" data-title="Dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                @elseif(auth()->user()->hasRole('manajemen'))
                    <a href="{{ route('manajemen.dashboard') }}" class="{{ request()->routeIs('manajemen.dashboard') ? 'active' : '' }}" data-title="Dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                @elseif(auth()->user()->hasRole('customer'))
                    <a href="{{ route('customer.dashboard') }}" class="{{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" data-title="Dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                @endif
            </li>
            
            <!-- Admin Menu -->
            @if(auth()->user()->hasRole('admin'))
                <li>
                    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}" data-title="Manajemen User">
                        <i class="fas fa-users-cog"></i>
                        <span class="menu-text">Manajemen User</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.customers') }}" class="{{ request()->routeIs('admin.customers*') ? 'active' : '' }}" data-title="Manajemen Customer">
                        <i class="fas fa-address-book"></i>
                        <span class="menu-text">Manajemen Customer</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.billing') }}" class="{{ request()->routeIs('admin.billing*') ? 'active' : '' }}" data-title="Manajemen Tagihan">
                        <i class="fas fa-calculator"></i>
                        <span class="menu-text">Manajemen Tagihan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports*') ? 'active' : '' }}" data-title="Laporan">
                        <i class="fas fa-chart-bar"></i>
                        <span class="menu-text">Laporan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.tariff-calculator') }}" class="{{ request()->routeIs('admin.tariff-calculator*') ? 'active' : '' }}" data-title="Kalkulator Tarif">
                        <i class="fas fa-calculator"></i>
                        <span class="menu-text">Kalkulator Tarif</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}" data-title="Pengaturan">
                        <i class="fas fa-cogs"></i>
                        <span class="menu-text">Pengaturan</span>
                    </a>
                </li>
            @endif
            
            <!-- Keuangan Menu -->
            @if(auth()->user()->hasRole('keuangan'))
                <li>
                    <a href="{{ route('keuangan.billing') }}" class="{{ request()->routeIs('keuangan.billing*') ? 'active' : '' }}" data-title="Kelola Tagihan">
                        <i class="fas fa-file-invoice"></i>
                        <span class="menu-text">Kelola Tagihan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('keuangan.payments') }}" class="{{ request()->routeIs('keuangan.payments*') ? 'active' : '' }}" data-title="Pembayaran">
                        <i class="fas fa-credit-card"></i>
                        <span class="menu-text">Pembayaran</span>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="alert('WhatsApp Integration')" data-title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                        <span class="menu-text">WhatsApp</span>
                    </a>
                </li>
            @endif
            
            <!-- Manajemen Menu -->
            @if(auth()->user()->hasRole('manajemen'))
                <li>
                    <a href="{{ route('manajemen.reports') }}" class="{{ request()->routeIs('manajemen.reports*') ? 'active' : '' }}" data-title="Laporan">
                        <i class="fas fa-chart-bar"></i>
                        <span class="menu-text">Laporan & Analisis</span>
                    </a>
                </li>
                {{-- <li>
                    <a href="#" onclick="alert('Analytics Feature')" data-title="Analisis">
                        <i class="fas fa-chart-pie"></i>
                        <span class="menu-text">Analisis</span>
                    </a>
                </li> --}}
            @endif
            
            <!-- Customer Menu -->
            @if(auth()->user()->hasRole('customer'))
                <li>
                    <a href="{{ route('customer.bills') }}" class="{{ request()->routeIs('customer.bills*') ? 'active' : '' }}" data-title="Tagihan Saya">
                        <i class="fas fa-file-invoice"></i>
                        <span class="menu-text">Tagihan Saya</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer.payments') }}" class="{{ request()->routeIs('customer.payments*') ? 'active' : '' }}" data-title="Riwayat Bayar">
                        <i class="fas fa-history"></i>
                        <span class="menu-text">Riwayat Bayar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('profile.index') }}" class="{{ request()->routeIs('profile*') ? 'active' : '' }}" data-title="Profil">
                        <i class="fas fa-user-edit"></i>
                        <span class="menu-text">Profil</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
    @endauth
    
    <!-- Main Content -->
    <div class="main-content @auth with-sidebar @endauth" id="mainContent">
        @auth
        <!-- Top Bar -->
        <div class="topbar">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link text-light p-0 me-3" id="sidebarToggle">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <h6 class="mb-0 text-light">{{ ucfirst(auth()->user()->role->name) }} Panel</h6>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-link text-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>
                            {{ auth()->user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <h6 class="dropdown-header">
                                    {{ auth()->user()->name }}
                                    <br>
                                    <small class="text-muted">{{ ucfirst(auth()->user()->role->name) }}</small>
                                </h6>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="fas fa-user me-2"></i> Profil</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-edit me-2"></i> Edit Profil</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.password') }}"><i class="fas fa-key me-2"></i> Ubah Password</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.activity') }}"><i class="fas fa-history me-2"></i> Riwayat Aktivitas</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endauth
        
        <!-- Content Area -->
        <div class="content-wrapper">
            @guest
            <!-- Guest Navigation -->
            <nav class="guest-navbar navbar navbar-expand-lg navbar-ligth fixed-top">
                <div class="container">
                    <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                        <i class="fas fa-water me-2"></i>
                        PDAM Billing System
                    </a>
                    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#guestNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse" id="guestNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i> Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-1"></i> Register
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <!-- Guest Content with proper spacing -->
            <div class="guest-content" style="padding-top: 76px;">
                <div class="container py-4">
                    @yield('content')
                </div>
            </div>
            @else
                @yield('content')
            @endguest
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle functionality - hanya jika user sudah login
        @auth
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        });
        
        // Mobile sidebar toggle
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }
        
        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                const sidebarToggle = document.getElementById('sidebarToggle');
                
                if (sidebar && !sidebar.contains(event.target) && 
                    sidebarToggle && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
        @endauth
    </script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>

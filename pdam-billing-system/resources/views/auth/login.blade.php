<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PDAM Billing System') }} - Login</title>
    
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
        padding: 80px 0 2rem 0;
        background: var(--gradient-primary);
    }

    .auth-card {
        background: var(--white);
        border-radius: 20px;
        padding: 3rem;
        box-shadow: var(--shadow-lg);
        border: none;
        width: 100%;
        max-width: 450px;
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

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .form-control.is-valid {
        border-color: #28a745;
    }

    .input-group-text {
        background: var(--light-blue);
        border: 2px solid #e2e8f0;
        border-right: none;
        color: var(--primary-blue);
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

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        transform: none;
        cursor: not-allowed;
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

    .invalid-feedback {
        font-size: 0.875rem;
        color: #dc3545;
    }

    .valid-feedback {
        font-size: 0.875rem;
        color: #28a745;
    }

    .validation-message {
        margin-top: 0.25rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .validation-message.error {
        color: #dc3545;
    }

    .validation-message.success {
        color: #28a745;
    }

    .password-strength {
        margin-top: 0.5rem;
    }

    .password-strength-bar {
        height: 4px;
        border-radius: 2px;
        background: #e2e8f0;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .password-strength-fill {
        height: 100%;
        transition: all 0.3s ease;
        border-radius: 2px;
    }

    .strength-weak { background: #ef4444; }
    .strength-fair { background: #f59e0b; }
    .strength-good { background: #10b981; }
    .strength-strong { background: #059669; }

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

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }

    .form-floating {
        position: relative;
    }

    .form-floating > label {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        padding: 1rem 0.75rem;
        pointer-events: none;
        border: 1px solid transparent;
        transform-origin: 0 0;
        transition: opacity .1s ease-in-out, transform .1s ease-in-out;
        color: #6c757d;
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
                    <a class="nav-link active" href="{{ route('login') }}">
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
            </ul>
        </div>
    </div>
</nav>

<!-- Auth Container -->
<div class="auth-container">
    <div class="container-fluid container-fluid-custom">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-4">
                <div class="auth-card">
                    <div class="auth-header">
                        <div class="auth-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <h4 class="mb-2">Masuk ke Sistem</h4>
                        <p class="text-muted">Silakan masukkan kredensial Anda</p>
                    </div>

                    <!-- Server-side Error Messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger" id="server-errors">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Client-side Error Container -->
                    <div id="client-errors" class="alert alert-danger" style="display: none;">
                        <ul class="mb-0" id="client-error-list"></ul>
                    </div>

                    <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                        @csrf
                        
                        <!-- Phone Number Field -->
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-semibold">Nomor Telepon</label>
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
                                       maxlength="15"
                                       required>
                            </div>
                            <div id="phone-validation" class="validation-message"></div>
                            @error('phone')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       minlength="8"
                                       required>
                                <span class="input-group-text" id="toggle-password" style="cursor: pointer;">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <div id="password-validation" class="validation-message"></div>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="loginBtn">
                                <span id="login-spinner" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="display: none;"></span>
                                <i class="fas fa-sign-in-alt me-2" id="login-icon"></i>
                                <span id="login-text">Masuk</span>
                            </button>
                        </div>
                    </form>

                    <div class="text-center">
                        <p class="text-muted mb-3">
                            <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                                Lupa Password?
                            </a>
                        </p>
                        <p class="text-muted mb-3">Belum punya akun?</p>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-2"></i>
                            Daftar Sebagai Customer
                        </a>
                    </div>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <strong>Demo Account:</strong><br>
                            Admin: 08111111111 / password123<br>
                            Keuangan: 08222222222 / password123<br>
                            Manajemen: 08333333333 / password123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: var(--shadow-lg);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="forgotPasswordModalLabel">
                    <i class="fas fa-key me-2 text-primary"></i>
                    Reset Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <div class="text-center mb-3">
                    <div class="auth-icon mx-auto" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        <i class="fas fa-question-circle"></i>
                    </div>
                </div>
                <p class="text-muted text-center mb-4">
                    Untuk reset password, silakan hubungi administrator sistem atau staff IT PDAM.
                </p>
                <div class="alert alert-info border-0" style="background: rgba(59, 130, 246, 0.1); border-radius: 10px;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <small>
                            <strong>Kontak Admin:</strong><br>
                            Email: admin@pdam.go.id<br>
                            Telepon: (022) 1234-5678<br>
                            WhatsApp: 08111111111
                        </small>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                    Tutup
                </button>
                <a href="https://wa.me/6281111111111?text=Halo%20Admin%2C%20saya%20ingin%20reset%20password%20akun%20PDAM%20saya" 
                   target="_blank" 
                   class="btn btn-success" 
                   style="border-radius: 10px;">
                    <i class="fab fa-whatsapp me-1"></i>
                    Hubungi via WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');
    const loginBtn = document.getElementById('loginBtn');
    const clientErrors = document.getElementById('client-errors');
    const clientErrorList = document.getElementById('client-error-list');
    const togglePassword = document.getElementById('toggle-password');
    const loginSpinner = document.getElementById('login-spinner');
    const loginIcon = document.getElementById('login-icon');
    const loginText = document.getElementById('login-text');

    // Client-side validation functions
    function validatePhone(phone) {
        const errors = [];
        
        if (!phone || phone.trim() === '') {
            errors.push('Nomor telepon wajib diisi');
        } else {
            // Indonesian phone number validation
            const phoneRegex = /^(08|628|\+628)[0-9]{8,12}$/;
            if (!phoneRegex.test(phone.replace(/\s+/g, ''))) {
                errors.push('Format nomor telepon tidak valid (contoh: 08xxxxxxxxxx)');
            }
        }
        
        return errors;
    }

    function validatePassword(password) {
        const errors = [];
        
        if (!password || password.trim() === '') {
            errors.push('Password wajib diisi');
        } else {
            if (password.length < 8) {
                errors.push('Password minimal 8 karakter');
            }
            
            // Check for at least one uppercase letter
            if (!/[A-Z]/.test(password)) {
                errors.push('Password harus mengandung huruf besar');
            }
            
            // Check for at least one lowercase letter  
            if (!/[a-z]/.test(password)) {
                errors.push('Password harus mengandung huruf kecil');
            }
            
            // Check for at least one number
            if (!/[0-9]/.test(password)) {
                errors.push('Password harus mengandung angka');
            }
        }
        
        return errors;
    }

    function showValidationMessage(input, messages, isError = true) {
        const validationDiv = document.getElementById(input.id + '-validation');
        
        if (messages.length > 0) {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            validationDiv.className = 'validation-message error';
            validationDiv.innerHTML = messages.join('<br>');
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            validationDiv.className = 'validation-message success';
            validationDiv.innerHTML = '✓ Valid';
        }
    }

    function showClientErrors(errors) {
        if (errors.length > 0) {
            clientErrorList.innerHTML = '';
            errors.forEach(error => {
                const li = document.createElement('li');
                li.textContent = error;
                clientErrorList.appendChild(li);
            });
            clientErrors.style.display = 'block';
            
            // Hide server errors if client errors exist
            const serverErrors = document.getElementById('server-errors');
            if (serverErrors) {
                serverErrors.style.display = 'none';
            }
        } else {
            clientErrors.style.display = 'none';
        }
    }

    // Real-time validation
    phoneInput.addEventListener('input', function() {
        const errors = validatePhone(this.value);
        showValidationMessage(this, errors);
    });

    phoneInput.addEventListener('blur', function() {
        const errors = validatePhone(this.value);
        showValidationMessage(this, errors);
    });

    passwordInput.addEventListener('input', function() {
        const errors = validatePassword(this.value);
        showValidationMessage(this, errors);
    });

    passwordInput.addEventListener('blur', function() {
        const errors = validatePassword(this.value);
        showValidationMessage(this, errors);
    });

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        if (type === 'password') {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    });

    // Function to show loading state
    function showLoadingState() {
        loginBtn.disabled = true;
        loginSpinner.style.display = 'inline-block';
        loginIcon.style.display = 'none';
        loginText.textContent = 'Memproses...';
    }

    // Function to reset loading state
    function resetLoadingState() {
        loginBtn.disabled = false;
        loginSpinner.style.display = 'none';
        loginIcon.style.display = 'inline';
        loginText.textContent = 'Masuk';
    }

    // Form submission with validation
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const phoneErrors = validatePhone(phoneInput.value);
        const passwordErrors = validatePassword(passwordInput.value);
        const allErrors = [...phoneErrors, ...passwordErrors];
        
        showValidationMessage(phoneInput, phoneErrors);
        showValidationMessage(passwordInput, passwordErrors);
        showClientErrors(allErrors);
        
        if (allErrors.length === 0) {
            showLoadingState();
            
            // Set timeout to reset if form submission takes too long or fails
            const timeoutId = setTimeout(resetLoadingState, 10000); // Reset after 10 seconds
            
            // Store timeout ID to clear it if page unloads
            window.loginTimeout = timeoutId;
            
            // Submit form
            this.submit();
        }
    });

    // Reset loading state on page show (handles back button)
    window.addEventListener('pageshow', function(event) {
        resetLoadingState();
        
        // Clear any existing timeout
        if (window.loginTimeout) {
            clearTimeout(window.loginTimeout);
        }
    });

    // Reset loading state on page visibility change
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            resetLoadingState();
        }
    });

    // Reset loading state before page unloads
    window.addEventListener('beforeunload', function() {
        resetLoadingState();
        
        if (window.loginTimeout) {
            clearTimeout(window.loginTimeout);
        }
    });

    // Phone number input - only allow numbers
    phoneInput.addEventListener('input', function() {
        // Remove all non-digit characters
        let value = this.value.replace(/\D/g, '');
        this.value = value;
    });

    // Auto-focus on page load
    phoneInput.focus();
});
</script>

</body>
</html>
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-key text-primary me-2"></i>
                        Ubah Password
                    </h4>
                    <p class="text-muted mb-0">Perbarui password untuk keamanan akun Anda</p>
                </div>
                <div>
                    <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali ke Profil
                    </a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Terdapat kesalahan:
                    </h6>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Password Change Form -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        Keamanan Password
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Security Info -->
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>
                            Persyaratan Password
                        </h6>
                        <ul class="mb-0">
                            <li>Minimal 8 karakter</li>
                            <li>Mengandung huruf besar (A-Z)</li>
                            <li>Mengandung huruf kecil (a-z)</li>
                            <li>Mengandung angka (0-9)</li>
                        </ul>
                    </div>

                    <form method="POST" action="{{ route('profile.update.password') }}" id="passwordForm">
                        @csrf
                        @method('PUT')

                        <!-- Current Password -->
                        <div class="mb-4">
                            <label for="current_password" class="form-label fw-semibold">
                                Password Saat Ini <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock text-primary"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       placeholder="Masukkan password saat ini"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                    <i class="fas fa-eye" id="toggleCurrentIcon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="current_password-invalid">
                                @error('current_password') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">
                                Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-key text-primary"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Masukkan password baru"
                                       minlength="8"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            <div class="password-strength mt-2" id="passwordStrength">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Kekuatan password:</small>
                                    <small id="strengthText" class="text-muted">-</small>
                                </div>
                                <div class="strength-indicator" id="strengthBar"></div>
                            </div>
                            <div class="valid-feedback" id="password-valid"></div>
                            <div class="invalid-feedback" id="password-invalid">
                                @error('password') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                Konfirmasi Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-key text-primary"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Ulangi password baru"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye" id="toggleConfirmIcon"></i>
                                </button>
                            </div>
                            <div class="valid-feedback" id="password_confirmation-valid"></div>
                            <div class="invalid-feedback" id="password_confirmation-invalid"></div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-warning" id="submitBtn" disabled>
                                <i class="fas fa-key me-2"></i>
                                Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        Tips Keamanan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <strong>Gunakan Password Unik</strong>
                                    <p class="small text-muted mb-0">Jangan gunakan password yang sama dengan akun lain</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <strong>Update Berkala</strong>
                                    <p class="small text-muted mb-0">Ganti password setiap 3-6 bulan sekali</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <strong>Jangan Bagikan</strong>
                                    <p class="small text-muted mb-0">Password hanya untuk Anda pribadi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <strong>Logout Setelah Selesai</strong>
                                    <p class="small text-muted mb-0">Selalu logout dari perangkat bersama</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

.strength-weak { 
    background: var(--danger-color, #ef4444); 
    width: 33%; 
}

.strength-medium { 
    background: var(--warning-color, #f59e0b); 
    width: 66%; 
}

.strength-strong { 
    background: var(--success-color, #10b981); 
    width: 100%; 
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const validationState = {
        current_password: false,
        password: false,
        password_confirmation: false
    };

    // Password visibility toggles
    const toggles = [
        { button: 'toggleCurrentPassword', field: 'current_password', icon: 'toggleCurrentIcon' },
        { button: 'togglePassword', field: 'password', icon: 'toggleIcon' },
        { button: 'toggleConfirmPassword', field: 'password_confirmation', icon: 'toggleConfirmIcon' }
    ];

    toggles.forEach(toggle => {
        const button = document.getElementById(toggle.button);
        const field = document.getElementById(toggle.field);
        const icon = document.getElementById(toggle.icon);

        button.addEventListener('click', function() {
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    });

    // Update submit button state
    function updateSubmitButton() {
        const allValid = Object.values(validationState).every(state => state === true);
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = !allValid;
        
        if (allValid) {
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-warning');
        } else {
            submitBtn.classList.remove('btn-warning');
            submitBtn.classList.add('btn-secondary');
        }
    }

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

    // Current password validation
    document.getElementById('current_password').addEventListener('input', function() {
        validationState.current_password = this.value.length >= 1;
        
        if (this.value.length >= 1) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
        
        updateSubmitButton();
    });

    // New password validation
    document.getElementById('password').addEventListener('input', function() {
        const { score, feedback } = checkPasswordStrength(this.value);
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        const validFeedback = document.getElementById('password-valid');
        const invalidFeedback = document.getElementById('password-invalid');

        // Update strength indicator
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

        // Revalidate confirmation when password changes
        if (document.getElementById('password_confirmation').value) {
            validatePasswordConfirmation();
        }

        updateSubmitButton();
    });

    // Password confirmation validation
    function validatePasswordConfirmation() {
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;
        const field = document.getElementById('password_confirmation');
        const validFeedback = document.getElementById('password_confirmation-valid');
        const invalidFeedback = document.getElementById('password_confirmation-invalid');

        if (confirmation === '') {
            field.classList.remove('is-valid', 'is-invalid');
            validFeedback.textContent = '';
            invalidFeedback.textContent = '';
            validationState.password_confirmation = false;
        } else if (password === confirmation && validationState.password) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            validFeedback.textContent = 'Password cocok';
            invalidFeedback.textContent = '';
            validationState.password_confirmation = true;
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
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

    document.getElementById('password_confirmation').addEventListener('input', validatePasswordConfirmation);

    // Form submission
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        const allValid = Object.values(validationState).every(state => state === true);
        
        if (!allValid) {
            e.preventDefault();
            alert('Mohon periksa kembali form Anda. Ada field yang belum valid.');
            return false;
        }

        if (!confirm('Apakah Anda yakin ingin mengubah password? Anda akan tetap login setelah password berhasil diubah.')) {
            e.preventDefault();
            return false;
        }

        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengubah Password...';
        submitBtn.disabled = true;
    });

    // Initialize
    updateSubmitButton();
});
</script>
@endsection
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Edit Profil
                    </h4>
                    <p class="text-muted mb-0">Perbarui informasi personal dan data kontak Anda</p>
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
                        Terdapat kesalahan pada form:
                    </h6>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Profile Preview -->
                <div class="col-lg-4 col-md-5 mb-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-eye text-primary me-2"></i>
                                Preview Profil
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <!-- Avatar Preview -->
                            <div class="avatar-container mb-3">
                                <div class="position-relative d-inline-block">
                                    <img src="{{ $gravatarUrl }}" 
                                         alt="Profile Avatar" 
                                         class="rounded-circle avatar-img" 
                                         style="width: 100px; height: 100px; object-fit: cover; border: 3px solid var(--light-blue);"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="avatar-fallback rounded-circle d-none align-items-center justify-content-center"
                                         style="width: 100px; height: 100px; background: var(--gradient-primary); color: white; font-size: 2rem; font-weight: bold; border: 3px solid var(--light-blue);">
                                        {{ strtoupper(substr($user->name, 0, 1)) . strtoupper(substr(str_replace(' ', '', $user->name), 1, 1)) }}
                                    </div>
                                </div>
                            </div>

                            <h6 class="card-title mb-2" id="preview-name">{{ $user->name }}</h6>
                            <span class="role-badge role-{{ $user->role->name }}">
                                {{ ucfirst($user->role->name) }}
                            </span>

                            @if($user->isCustomer() && $user->customer)
                                <div class="mt-2">
                                    <small class="text-muted">Customer ID:</small><br>
                                    <strong class="text-primary">{{ $user->customer->customer_number }}</strong>
                                </div>
                            @endif

                            <div class="mt-3 small">
                                <div class="text-muted mb-1">Kontak:</div>
                                <div id="preview-phone">{{ $user->phone }}</div>
                                <div id="preview-email">{{ $user->email ?: 'Belum diisi' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="col-lg-8 col-md-7 mb-4">
                    <form method="POST" action="{{ route('profile.update') }}" id="editProfileForm">
                        @csrf
                        @method('PUT')

                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    Informasi Personal
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Nama Lengkap -->
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label fw-semibold">
                                            Nama Lengkap <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-user text-primary"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name', $user->name) }}" 
                                                   placeholder="Masukkan nama lengkap"
                                                   required>
                                        </div>
                                        <div class="valid-feedback" id="name-valid"></div>
                                        <div class="invalid-feedback" id="name-invalid">
                                            @error('name') {{ $message }} @enderror
                                        </div>
                                    </div>

                                    <!-- Nomor Telepon -->
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label fw-semibold">
                                            Nomor Telepon <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-phone text-primary"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" 
                                                   name="phone" 
                                                   value="{{ old('phone', $user->phone) }}" 
                                                   placeholder="08xxxxxxxxxx"
                                                   required>
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Format: 08xxxxxxxx (10-15 digit total)
                                        </small>
                                        <div class="valid-feedback" id="phone-valid"></div>
                                        <div class="invalid-feedback" id="phone-invalid">
                                            @error('phone') {{ $message }} @enderror
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label fw-semibold">
                                            Email <span class="text-muted">(Opsional)</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope text-primary"></i>
                                            </span>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email', $user->email) }}" 
                                                   placeholder="email@domain.com">
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Email akan digunakan untuk Gravatar avatar
                                        </small>
                                        <div class="valid-feedback" id="email-valid"></div>
                                        <div class="invalid-feedback" id="email-invalid">
                                            @error('email') {{ $message }} @enderror
                                        </div>
                                    </div>

                                    <!-- Role (Read-only) -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold text-muted">Role Akses</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-shield-alt text-muted"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control" 
                                                   value="{{ ucfirst($user->role->name) }}" 
                                                   disabled>
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Role tidak dapat diubah oleh pengguna
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($user->isCustomer() && $user->customer)
                            <!-- Customer Information -->
                            <div class="card mt-4">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-home text-primary me-2"></i>
                                        Informasi Customer
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Alamat -->
                                        <div class="col-12 mb-3">
                                            <label for="address" class="form-label fw-semibold">
                                                Alamat <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                                </span>
                                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                                          id="address" 
                                                          name="address" 
                                                          rows="3" 
                                                          placeholder="Alamat lengkap sesuai KTP"
                                                          required>{{ old('address', $user->customer->address) }}</textarea>
                                            </div>
                                            <div class="valid-feedback" id="address-valid"></div>
                                            <div class="invalid-feedback" id="address-invalid">
                                                @error('address') {{ $message }} @enderror
                                            </div>
                                        </div>

                                        <!-- Read-only Customer Info -->
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-semibold text-muted">Nomor Customer</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-id-badge text-muted"></i>
                                                </span>
                                                <input type="text" 
                                                       class="form-control" 
                                                       value="{{ $user->customer->customer_number }}" 
                                                       disabled>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-semibold text-muted">Nomor KTP</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-id-card text-muted"></i>
                                                </span>
                                                <input type="text" 
                                                       class="form-control" 
                                                       value="{{ $user->customer->ktp_number }}" 
                                                       disabled>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-semibold text-muted">Golongan Tarif</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-tags text-muted"></i>
                                                </span>
                                                <input type="text" 
                                                       class="form-control" 
                                                       value="{{ strtoupper($user->customer->tariff_group) }}" 
                                                       disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Submit Buttons -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>
                                        Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save me-2"></i>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const validationState = {
        name: true,
        phone: true,
        email: true,
        address: {{ $user->isCustomer() && $user->customer ? 'true' : 'true' }}
    };

    // Update submit button state
    function updateSubmitButton() {
        const allValid = Object.values(validationState).every(state => state === true);
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = !allValid;
    }

    // Live preview update
    function updatePreview() {
        const name = document.getElementById('name').value || '{{ $user->name }}';
        const phone = document.getElementById('phone').value || '{{ $user->phone }}';
        const email = document.getElementById('email').value || 'Belum diisi';

        document.getElementById('preview-name').textContent = name;
        document.getElementById('preview-phone').textContent = phone;
        document.getElementById('preview-email').textContent = email;

        // Update avatar fallback initials
        const initials = name.split(' ').map(word => word.charAt(0).toUpperCase()).join('').substring(0, 2);
        document.querySelector('.avatar-fallback').textContent = initials;
    }

    // Validation function
    function validateField(fieldName, value, validationFn, errorMessage) {
        const field = document.getElementById(fieldName);
        const validFeedback = document.getElementById(`${fieldName}-valid`);
        const invalidFeedback = document.getElementById(`${fieldName}-invalid`);

        const isValid = validationFn(value);
        validationState[fieldName] = isValid;

        if (value.trim() === '' && fieldName !== 'email') {
            field.classList.remove('is-valid', 'is-invalid');
            validationState[fieldName] = false;
        } else if (isValid || (fieldName === 'email' && value.trim() === '')) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            if (validFeedback) validFeedback.textContent = 'Valid';
            if (invalidFeedback) invalidFeedback.textContent = '';
            if (fieldName === 'email' && value.trim() === '') validationState[fieldName] = true;
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            if (validFeedback) validFeedback.textContent = '';
            if (invalidFeedback && !invalidFeedback.textContent) {
                invalidFeedback.textContent = errorMessage;
            }
        }

        updateSubmitButton();
    }

    // Name validation
    document.getElementById('name').addEventListener('input', function() {
        validateField('name', this.value, 
            value => value.trim().length >= 2 && /^[a-zA-Z\s.,'-]+$/.test(value.trim()),
            'Nama minimal 2 karakter dan hanya boleh mengandung huruf, spasi, dan tanda baca yang valid'
        );
        updatePreview();
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
        updatePreview();
    });

    // Email validation
    document.getElementById('email').addEventListener('input', function() {
        validateField('email', this.value,
            value => value === '' || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
            'Format email tidak valid'
        );
        updatePreview();
    });

    // Address validation (if exists)
    const addressField = document.getElementById('address');
    if (addressField) {
        addressField.addEventListener('input', function() {
            validateField('address', this.value,
                value => value.trim().length >= 10,
                'Alamat minimal 10 karakter'
            );
        });
    }

    // Form submission confirmation
    document.getElementById('editProfileForm').addEventListener('submit', function(e) {
        const allValid = Object.values(validationState).every(state => state === true);
        
        if (!allValid) {
            e.preventDefault();
            alert('Mohon periksa kembali form Anda. Ada field yang belum valid.');
            return false;
        }

        if (!confirm('Apakah Anda yakin ingin menyimpan perubahan profil?')) {
            e.preventDefault();
            return false;
        }

        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        submitBtn.disabled = true;
    });

    // Initialize
    updateSubmitButton();
});
</script>
@endsection
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-user-edit text-primary me-2"></i>
                        Profil Saya
                    </h2>
                    <p class="text-muted">Kelola informasi profil dan pengaturan akun Anda</p>
                </div>
                <div>
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="row g-4">
        <!-- Profile Photo & Basic Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>
                        Foto Profil
                    </h6>
                </div>
                <div class="card-body text-center">
                    <!-- Current Avatar Display -->
                    <div class="position-relative d-inline-block mb-3">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 120px; height: 120px; font-size: 3rem;" id="avatarDisplay">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                                     class="rounded-circle" 
                                     style="width: 120px; height: 120px; object-fit: cover;"
                                     alt="Avatar">
                            @else
                                <span class="text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-warning rounded-circle position-absolute" 
                                style="bottom: 5px; right: 5px;" onclick="$('#avatarInput').click()">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>

                    <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-3">{{ auth()->user()->phone }}</p>
                    
                    <!-- Avatar Upload (Disabled for now) -->
                    <div class="text-center mb-3">
                        <small class="text-muted">Upload avatar akan segera tersedia</small>
                    </div>

                    <div class="row text-center">
                        <div class="col-6">
                            <h6 class="text-success mb-0">{{ date('Y') - date('Y', strtotime(auth()->user()->created_at ?? '2023-01-01')) }}</h6>
                            <small class="text-muted">Tahun Bergabung</small>
                        </div>
                        <div class="col-6">
                            <h6 class="text-info mb-0">Aktif</h6>
                            <small class="text-muted">Status</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Security -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Keamanan Akun
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-danger" onclick="showChangePasswordModal()">
                            <i class="fas fa-key me-2"></i>
                            Ubah Password
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="showDeleteAccountModal()">
                            <i class="fas fa-user-times me-2"></i>
                            Hapus Akun
                        </button>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Password terakhir diubah: {{ auth()->user()->updated_at ? auth()->user()->updated_at->format('d M Y') : 'Belum pernah' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Informasi Profil
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Profile Update Form -->
                    <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-user me-2"></i>
                                    Informasi Personal
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', auth()->user()->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
                                <div class="form-text">Format: 08xxxxxxxxxx</div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                       id="birth_date" name="birth_date" value="{{ old('birth_date', auth()->user()->birth_date) }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    Informasi Alamat
                                </h6>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3" 
                                          placeholder="Masukkan alamat lengkap...">{{ old('address', auth()->user()->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">Kota</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" value="{{ old('city', auth()->user()->city) }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="province" class="form-label">Provinsi</label>
                                <input type="text" class="form-control @error('province') is-invalid @enderror" 
                                       id="province" name="province" value="{{ old('province', auth()->user()->province) }}">
                                @error('province')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="postal_code" class="form-label">Kode Pos</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                       id="postal_code" name="postal_code" value="{{ old('postal_code', auth()->user()->postal_code) }}">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Notification Preferences -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-bell me-2"></i>
                                    Preferensi Notifikasi
                                </h6>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="whatsapp_notifications" 
                                           name="whatsapp_notifications" value="1" 
                                           {{ old('whatsapp_notifications', auth()->user()->whatsapp_notifications ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="whatsapp_notifications">
                                        <i class="fab fa-whatsapp text-success me-1"></i>
                                        Notifikasi WhatsApp untuk tagihan
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" 
                                           name="email_notifications" value="1"
                                           {{ old('email_notifications', auth()->user()->email_notifications ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_notifications">
                                        <i class="fas fa-envelope text-info me-1"></i>
                                        Notifikasi Email
                                    </label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sms_notifications" 
                                           name="sms_notifications" value="1"
                                           {{ old('sms_notifications', auth()->user()->sms_notifications ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_notifications">
                                        <i class="fas fa-sms text-warning me-1"></i>
                                        Notifikasi SMS
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                        <i class="fas fa-undo me-1"></i>
                                        Reset
                                    </button>
                                    <div>
                                        <button type="button" class="btn btn-warning me-2" onclick="previewChanges()">
                                            <i class="fas fa-eye me-1"></i>
                                            Preview
                                        </button>
                                        <button type="submit" class="btn btn-success" onclick="return confirmSave()">
                                            <i class="fas fa-save me-1"></i>
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activity History Card -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Riwayat Aktivitas Profil
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Sample Activity Items -->
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" 
                                     style="width: 32px; height: 32px;">
                                    <i class="fas fa-user-edit text-white small"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Profil Diperbarui</h6>
                                <p class="text-muted mb-0">Mengubah informasi kontak</p>
                                <small class="text-muted">{{ auth()->user()->updated_at ? auth()->user()->updated_at->format('d M Y, H:i') : 'Belum ada aktivitas' }}</small>
                            </div>
                        </div>

                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                                     style="width: 32px; height: 32px;">
                                    <i class="fas fa-user-plus text-white small"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Akun Dibuat</h6>
                                <p class="text-muted mb-0">Registrasi akun customer</p>
                                <small class="text-muted">{{ auth()->user()->created_at ? auth()->user()->created_at->format('d M Y, H:i') : 'Tanggal tidak tersedia' }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Semua aktivitas tercatat untuk keamanan akun Anda
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-key me-2"></i>
                    Ubah Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('profile.update.password') }}" method="POST" id="passwordForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <div class="form-text">Minimal 8 karakter, kombinasi huruf besar, kecil, dan angka</div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-key me-1"></i>
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Hapus Akun
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan. Semua data Anda akan dihapus secara permanen.
                </div>
                <p>Untuk melanjutkan penghapusan akun, silakan hubungi customer service di:</p>
                <ul>
                    <li>Telepon: 021-XXXXXX</li>
                    <li>WhatsApp: 0812-XXXXXXX</li>
                    <li>Email: cs@pdam.go.id</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
// Form handling functions
function resetForm() {
    if (confirm('Yakin ingin mereset semua perubahan?')) {
        document.getElementById('profileForm').reset();
    }
}

function previewChanges() {
    const form = document.getElementById('profileForm');
    const formData = new FormData(form);
    let preview = 'Preview Perubahan:\n\n';
    
    for (let [key, value] of formData.entries()) {
        if (value && key !== '_token' && key !== '_method') {
            preview += `${key}: ${value}\n`;
        }
    }
    
    alert(preview);
}

function confirmSave() {
    return confirm('Simpan perubahan profil?');
}

// Avatar handling
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const avatarDisplay = document.getElementById('avatarDisplay');
            avatarDisplay.innerHTML = `<img src="${e.target.result}" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;" alt="New Avatar">`;
        }
        reader.readAsDataURL(input.files[0]);
        
        // Auto-submit avatar form
        if (confirm('Upload foto profil baru?')) {
            document.getElementById('avatarForm').submit();
        }
    }
}

// Modal functions
function showChangePasswordModal() {
    const modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
    modal.show();
}

function showDeleteAccountModal() {
    const modal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
    modal.show();
}

// Form validation
document.getElementById('profileForm').addEventListener('submit', function(e) {
    const phone = document.getElementById('phone').value;
    const phonePattern = /^08[0-9]{8,13}$/;
    
    if (!phonePattern.test(phone)) {
        e.preventDefault();
        alert('Format nomor telepon tidak valid. Gunakan format 08xxxxxxxxxx');
        document.getElementById('phone').focus();
    }
});

// Password form validation
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('new_password_confirmation').value;
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('Konfirmasi password tidak cocok');
        document.getElementById('new_password_confirmation').focus();
    }
    
    if (newPassword.length < 8) {
        e.preventDefault();
        alert('Password minimal 8 karakter');
        document.getElementById('new_password').focus();
    }
});
</script>
@endsection
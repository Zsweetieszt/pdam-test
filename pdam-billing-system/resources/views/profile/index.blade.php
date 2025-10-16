@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-user-circle text-primary me-2"></i>
                        Profil Pengguna
                    </h4>
                    <p class="text-muted mb-0">Informasi akun dan data personal Anda</p>
                </div>
                <div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>
                        Edit Profil
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Profile Card -->
                <div class="col-lg-4 col-md-5 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <!-- REQ-F-2.1: Avatar dengan Gravatar atau inisial -->
                            <div class="avatar-container mb-4">
                                <div class="position-relative d-inline-block">
                                    <img src="{{ $gravatarUrl }}" 
                                         alt="Profile Avatar" 
                                         class="rounded-circle avatar-img" 
                                         style="width: 120px; height: 120px; object-fit: cover; border: 4px solid var(--light-blue);"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="avatar-fallback rounded-circle d-none align-items-center justify-content-center"
                                         style="width: 120px; height: 120px; background: var(--gradient-primary); color: white; font-size: 2.5rem; font-weight: bold; border: 4px solid var(--light-blue);">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) . strtoupper(substr(str_replace(' ', '', auth()->user()->name), 1, 1)) }}
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-title mb-2">{{ $user->name }}</h5>
                            <span class="role-badge role-{{ $user->role->name }}">
                                {{ ucfirst($user->role->name) }}
                            </span>
                            
                            @if($user->isCustomer() && $user->customer)
                                <div class="mt-3">
                                    <small class="text-muted">Customer ID:</small><br>
                                    <strong class="text-primary">{{ $user->customer->customer_number }}</strong>
                                </div>
                            @endif

                            <div class="mt-4">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h6 class="text-muted mb-1">Status</h6>
                                            @if($user->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Non-Aktif</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-muted mb-1">Terdaftar</h6>
                                        <small>{{ $user->created_at->format('d M Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="col-lg-8 col-md-7 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Informasi Personal
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Nama Lengkap</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">
                                        <i class="fas fa-user text-primary me-2"></i>
                                        {{ $user->name }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Nomor Telepon</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        {{ $user->phone }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Email</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        {{ $user->email ?: 'Belum diisi' }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Role Akses</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">
                                        <i class="fas fa-shield-alt text-primary me-2"></i>
                                        {{ ucfirst($user->role->name) }}
                                    </div>
                                </div>
                            </div>

                            @if($user->isCustomer() && $user->customer)
                                <hr class="my-4">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-home text-primary me-2"></i>
                                    Informasi Customer
                                </h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small">Nomor Customer</label>
                                        <div class="form-control-plaintext bg-light p-2 rounded">
                                            <i class="fas fa-id-badge text-primary me-2"></i>
                                            {{ $user->customer->customer_number }}
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small">Nomor KTP</label>
                                        <div class="form-control-plaintext bg-light p-2 rounded">
                                            <i class="fas fa-id-card text-primary me-2"></i>
                                            {{ $user->customer->ktp_number }}
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small">Golongan Tarif</label>
                                        <div class="form-control-plaintext bg-light p-2 rounded">
                                            <i class="fas fa-tags text-primary me-2"></i>
                                            {{ strtoupper($user->customer->tariff_group) }}
                                        </div>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label text-muted small">Alamat</label>
                                        <div class="form-control-plaintext bg-light p-2 rounded">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                            {{ $user->customer->address }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Cards -->
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card h-100 border-primary">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-edit fa-2x text-primary"></i>
                            </div>
                            <h6 class="card-title">Edit Profil</h6>
                            <p class="card-text text-muted small">Ubah informasi personal dan data kontak Anda</p>
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                Edit Sekarang
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card h-100 border-warning">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-key fa-2x text-warning"></i>
                            </div>
                            <h6 class="card-title">Ubah Password</h6>
                            <p class="card-text text-muted small">Ganti password untuk keamanan akun Anda</p>
                            <a href="{{ route('profile.password') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-key me-1"></i>
                                Ganti Password
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card h-100 border-info">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-history fa-2x text-info"></i>
                            </div>
                            <h6 class="card-title">Riwayat Aktivitas</h6>
                            <p class="card-text text-muted small">Lihat log aktivitas dan perubahan akun Anda</p>
                            <a href="{{ route('profile.activity') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-history me-1"></i>
                                Lihat Riwayat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-fallback {
    position: absolute;
    top: 0;
    left: 0;
}
</style>
@endsection
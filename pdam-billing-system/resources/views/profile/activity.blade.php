@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-history text-primary me-2"></i>
                        Riwayat Aktivitas
                    </h4>
                    <p class="text-muted mb-0">Log aktivitas dan perubahan akun Anda dalam sistem</p>
                </div>
                <div>
                    <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali ke Profil
                    </a>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-filter text-primary me-2"></i>
                        Filter Aktivitas
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('profile.activity') }}" id="filterForm">
                        <div class="row align-items-end">
                            <div class="col-md-3 mb-3">
                                <label for="action" class="form-label fw-semibold">Jenis Aktivitas</label>
                                <select class="form-select" id="action" name="action">
                                    <option value="">Semua Aktivitas</option>
                                    @foreach($availableActions as $actionType)
                                        <option value="{{ $actionType }}" 
                                                {{ request('action') == $actionType ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $actionType)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="date_from" class="form-label fw-semibold">Dari Tanggal</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="date_from" 
                                       name="date_from" 
                                       value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="date_to" class="form-label fw-semibold">Sampai Tanggal</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="date_to" 
                                       name="date_to" 
                                       value="{{ request('date_to') }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>
                                    Filter
                                </button>
                                <a href="{{ route('profile.activity') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-refresh me-1"></i>
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activities Timeline -->
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-clock text-primary me-2"></i>
                        Timeline Aktivitas
                    </h6>
                    <small class="text-muted">
                        Total: {{ $activities->total() }} aktivitas
                    </small>
                </div>
                <div class="card-body">
                    @if($activities->count() > 0)
                        <div class="timeline">
                            @foreach($activities as $activity)
                                <div class="timeline-item mb-4">
                                    <div class="row">
                                        <div class="col-auto">
                                            <div class="timeline-marker">
                                                @switch($activity->action)
                                                    @case('login')
                                                        <i class="fas fa-sign-in-alt text-success"></i>
                                                        @break
                                                    @case('logout')
                                                        <i class="fas fa-sign-out-alt text-warning"></i>
                                                        @break
                                                    @case('update_profile')
                                                        <i class="fas fa-user-edit text-primary"></i>
                                                        @break
                                                    @case('change_password')
                                                        <i class="fas fa-key text-danger"></i>
                                                        @break
                                                    @case('create')
                                                        <i class="fas fa-plus-circle text-success"></i>
                                                        @break
                                                    @case('update')
                                                        <i class="fas fa-edit text-info"></i>
                                                        @break
                                                    @case('delete')
                                                        <i class="fas fa-trash text-danger"></i>
                                                        @break
                                                    @default
                                                        <i class="fas fa-circle text-secondary"></i>
                                                @endswitch
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                                            @if($activity->table_name)
                                                                <small class="text-muted">
                                                                    - {{ ucfirst($activity->table_name) }}
                                                                </small>
                                                            @endif
                                                        </h6>
                                                        <p class="text-muted mb-2">
                                                            <i class="fas fa-clock me-1"></i>
                                                            {{ $activity->created_at->format('d M Y, H:i:s') }}
                                                            <small class="ms-2">
                                                                ({{ $activity->created_at->diffForHumans() }})
                                                            </small>
                                                        </p>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-info" 
                                                            type="button" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#details-{{ $activity->id }}"
                                                            aria-expanded="false">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Detail
                                                    </button>
                                                </div>

                                                <!-- Activity Details (Collapsible) -->
                                                <div class="collapse mt-3" id="details-{{ $activity->id }}">
                                                    <div class="card card-body bg-light">
                                                        @if($activity->old_values || $activity->new_values)
                                                            <div class="row">
                                                                @if($activity->old_values && count($activity->old_values) > 0)
                                                                    <div class="col-md-6">
                                                                        <h6 class="text-danger">
                                                                            <i class="fas fa-minus-circle me-1"></i>
                                                                            Data Lama:
                                                                        </h6>
                                                                        <ul class="list-unstyled small">
                                                                            @foreach($activity->old_values as $key => $value)
                                                                                <li class="mb-1">
                                                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                                    <span class="text-muted">
                                                                                        {{ is_array($value) ? json_encode($value) : ($value ?: 'Kosong') }}
                                                                                    </span>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @endif

                                                                @if($activity->new_values && count($activity->new_values) > 0)
                                                                    <div class="col-md-6">
                                                                        <h6 class="text-success">
                                                                            <i class="fas fa-plus-circle me-1"></i>
                                                                            Data Baru:
                                                                        </h6>
                                                                        <ul class="list-unstyled small">
                                                                            @foreach($activity->new_values as $key => $value)
                                                                                <li class="mb-1">
                                                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                                    <span class="text-success">
                                                                                        {{ is_array($value) ? json_encode($value) : ($value ?: 'Kosong') }}
                                                                                    </span>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        <!-- Technical Details -->
                                                        <hr class="my-3">
                                                        <div class="row small text-muted">
                                                            <div class="col-md-6">
                                                                <strong>IP Address:</strong> {{ $activity->ip_address ?: 'N/A' }}
                                                            </div>
                                                            <div class="col-md-6">
                                                                <strong>Record ID:</strong> {{ $activity->record_id ?: 'N/A' }}
                                                            </div>
                                                        </div>
                                                        @if($activity->user_agent)
                                                            <div class="mt-2 small text-muted">
                                                                <strong>User Agent:</strong><br>
                                                                <code>{{ Str::limit($activity->user_agent, 100) }}</code>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $activities->appends(request()->query())->links() }}
                        </div>

                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-history fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-3">Tidak Ada Aktivitas</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['action', 'date_from', 'date_to']))
                                    Tidak ditemukan aktivitas dengan filter yang dipilih.<br>
                                    <a href="{{ route('profile.activity') }}" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="fas fa-refresh me-1"></i>
                                        Reset Filter
                                    </a>
                                @else
                                    Belum ada aktivitas yang tercatat untuk akun Anda.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--light-blue);
    z-index: 1;
}

.timeline-marker {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    border: 3px solid var(--light-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 2;
}

.timeline-content {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1rem;
    margin-left: 1rem;
    position: relative;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-content::before {
    content: '';
    position: absolute;
    left: -8px;
    top: 15px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 8px solid #e2e8f0;
}

.timeline-content::after {
    content: '';
    position: absolute;
    left: -7px;
    top: 15px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 8px solid white;
}

@media (max-width: 768px) {
    .timeline::before {
        left: 15px;
    }
    
    .timeline-marker {
        width: 30px;
        height: 30px;
    }
    
    .timeline-content {
        margin-left: 0.5rem;
    }
    
    .timeline-content::before,
    .timeline-content::after {
        display: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filter changes
    const filterForm = document.getElementById('filterForm');
    const actionSelect = document.getElementById('action');
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');

    // Set max date to today for date inputs
    const today = new Date().toISOString().split('T')[0];
    dateFrom.setAttribute('max', today);
    dateTo.setAttribute('max', today);

    // Validate date range
    dateFrom.addEventListener('change', function() {
        if (dateTo.value && this.value > dateTo.value) {
            alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
            this.value = '';
        }
    });

    dateTo.addEventListener('change', function() {
        if (dateFrom.value && this.value < dateFrom.value) {
            alert('Tanggal akhir tidak boleh lebih kecil dari tanggal mulai');
            this.value = '';
        }
    });

    // Smooth scrolling to timeline when form is submitted
    filterForm.addEventListener('submit', function(e) {
        setTimeout(function() {
            document.querySelector('.timeline')?.scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }, 100);
    });
});
</script>
@endsection
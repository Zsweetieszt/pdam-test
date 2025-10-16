@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    Tagihan Saya
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h4>1</h4>
                                <small>Tagihan Pending</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4>7</h4>
                                <small>Sudah Lunas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4>Rp 1.2M</h4>
                                <small>Total Tahun Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h4>15 m³</h4>
                                <small>Rata-rata/Bulan</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="text-muted">Riwayat tagihan air Anda</h6>
                    <div>
                        <button class="btn btn-primary me-2">
                            <i class="fas fa-download me-2"></i>
                            Download Semua
                        </button>
                        <button class="btn btn-success">
                            <i class="fas fa-credit-card me-2"></i>
                            Bayar Sekarang
                        </button>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" placeholder="Cari berdasarkan periode...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option>Semua Status</option>
                            <option>Pending</option>
                            <option>Lunas</option>
                            <option>Terlambat</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>
                            Filter
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Periode</th>
                                <th>Pemakaian</th>
                                <th>Meter Awal</th>
                                <th>Meter Akhir</th>
                                <th>Total Tagihan</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-warning">
                                <td><strong>Agu 2025</strong></td>
                                <td><strong>16 m³</strong></td>
                                <td>1,245</td>
                                <td>1,261</td>
                                <td><strong class="text-warning">Rp 200.000</strong></td>
                                <td>25 Agu 2025</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-success" title="Bayar">
                                            <i class="fas fa-credit-card"></i>
                                        </button>
                                        <button class="btn btn-secondary" title="Download">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Jul 2025</td>
                                <td>14 m³</td>
                                <td>1,231</td>
                                <td>1,245</td>
                                <td>Rp 175.000</td>
                                <td>25 Jul 2025</td>
                                <td><span class="badge bg-success">Lunas</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-secondary" title="Download">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-primary" title="Bukti Bayar">
                                            <i class="fas fa-receipt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Jun 2025</td>
                                <td>18 m³</td>
                                <td>1,213</td>
                                <td>1,231</td>
                                <td>Rp 225.000</td>
                                <td>25 Jun 2025</td>
                                <td><span class="badge bg-success">Lunas</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-secondary" title="Download">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-primary" title="Bukti Bayar">
                                            <i class="fas fa-receipt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="alert alert-info mt-4">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Penting
                    </h6>
                    <ul class="mb-0">
                        <li>Tagihan jatuh tempo setiap tanggal 25</li>
                        <li>Denda keterlambatan 10% setelah jatuh tempo</li>
                        <li>Pembayaran dapat dilakukan melalui transfer bank atau cash</li>
                        <li>Notifikasi WhatsApp akan dikirim 3 hari sebelum jatuh tempo</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

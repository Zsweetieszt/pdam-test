<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Keuangan\KeuanganController;
use App\Http\Controllers\Manajemen\ManajemenController;
use App\Http\Controllers\Customer\CustomerController;

// Auth routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes (require authentication)
Route::middleware('auth')->group(function () {

    // Profile routes untuk semua role yang sudah login
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
        Route::get('/password', [App\Http\Controllers\ProfileController::class, 'password'])->name('password');
        Route::put('/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('update.password');
        Route::get('/activity', [App\Http\Controllers\ProfileController::class, 'activity'])->name('activity');
    });
    
    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/customers', [AdminController::class, 'customers'])->name('customers');
        Route::get('/billing', [AdminController::class, 'billing'])->name('billing');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit.logs');
    });

    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::get('/tariff-calculator', function () {
            return view('admin.tariff-calculator');
        })->name('tariff-calculator');
    });

    // Keuangan routes
    Route::middleware('role:keuangan')->prefix('keuangan')->name('keuangan.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\FinanceController::class, 'dashboard'])->name('dashboard');
        Route::get('/billing', [KeuanganController::class, 'billing'])->name('billing');
        Route::get('/payments', [KeuanganController::class, 'payments'])->name('payments');
        Route::get('/whatsapp', [KeuanganController::class, 'whatsapp'])->name('whatsapp');
    });

    // Manajemen routes
    Route::middleware('role:manajemen')->prefix('manajemen')->name('manajemen.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\ManagementController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports', [ManajemenController::class, 'reports'])->name('reports');
        Route::get('/analytics', [ManajemenController::class, 'analytics'])->name('analytics');
    });

    // Customer routes
    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\CustomerDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/bills', [CustomerController::class, 'bills'])->name('bills');
        Route::get('/payments', [CustomerController::class, 'payments'])->name('payments');
    });
});

// API routes for WhatsApp testing
Route::prefix('api')->group(function () {
    Route::get('/whatsapp/status', function () {
        try {
            $whatsappService = app('whatsapp');
            $status = $whatsappService->checkHealth();
            return response()->json($status);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    });
    
    Route::get('/whatsapp/test', function () {
        try {
            $whatsappService = app('whatsapp');
            $qr = $whatsappService->getQRCode();
            return response()->json(['status' => 'success', 'qr' => $qr]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    });
});

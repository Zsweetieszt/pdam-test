<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\WhatsAppController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\SystemConfigController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\DataTableController;
use App\Http\Controllers\Api\TariffController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// [REQ-B-1] API Pengguna dan Autentikasi
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // [REQ-B-2] API Manajemen Data Pelanggan
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::post('/', [CustomerController::class, 'store'])->middleware('role:admin');
        Route::get('/search', [CustomerController::class, 'search']);
        Route::get('/stats', [CustomerController::class, 'getStats']);
        Route::get('/{customer}', [CustomerController::class, 'show']);
        Route::put('/{customer}', [CustomerController::class, 'update'])->middleware('role:admin');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->middleware('role:admin');
        Route::post('/validate-meter', [CustomerController::class, 'validateMeter']);
        
        // NEW: Multiple meter management
        Route::get('/{customer}/meters', [CustomerController::class, 'getCustomerMeters']);
        Route::post('/{customer}/meters', [CustomerController::class, 'addMeter'])->middleware('role:admin');
    });
    
    // NEW: Meter-specific endpoints for billing per meter
    Route::prefix('meters')->group(function () {
        Route::get('/{meter}/details', [CustomerController::class, 'getMeterDetails']);
        Route::get('/{meter}/bills', [BillController::class, 'getBillsByMeter']);
        Route::get('/{meter}/outstanding', [BillController::class, 'getOutstandingBills']);
        Route::post('/{meter}/calculate-bill', [BillController::class, 'calculateBill']);
        Route::post('/{meter}/generate-bill', [BillController::class, 'generateMeterBill'])->middleware('role:admin,keuangan');
    });

    // [REQ-B-3] API Manajemen Tagihan
    Route::prefix('bills')->group(function () {
        Route::get('/', [BillController::class, 'index']);
        Route::post('/generate', [BillController::class, 'generateBill'])->middleware('role:admin,keuangan');
        Route::get('/{bill}', [BillController::class, 'show']);
        Route::put('/{bill}/status', [BillController::class, 'updateStatus'])->middleware('role:admin,keuangan');
        Route::get('/meter/{meter}', [BillController::class, 'getBillsByMeter']);
        Route::get('/billing-periods', [BillController::class, 'getBillingPeriods']);
    });

    // [REQ-B-4] API Generate Link WhatsApp
    Route::prefix('whatsapp')->middleware('role:keuangan')->group(function () {
        Route::post('/generate-link', [WhatsAppController::class, 'generateLink']);
        Route::post('/format-message', [WhatsAppController::class, 'formatMessage']);
        Route::get('/logs', [WhatsAppController::class, 'getLogs']);
    });

    // [REQ-B-5] API Manajemen Pembayaran
    Route::prefix('payments')->group(function () {
        Route::post('/', [PaymentController::class, 'store']);
        Route::get('/history', [PaymentController::class, 'history']);
        Route::get('/{payment}', [PaymentController::class, 'show']);
        Route::put('/{payment}/verify', [PaymentController::class, 'verify'])->middleware('role:keuangan');
        Route::get('/{payment}/download-proof', [PaymentController::class, 'downloadProof']);
    });

    // [REQ-B-6] API Template dan Konfigurasi
    Route::prefix('templates')->group(function () {
        Route::get('/', [TemplateController::class, 'index']);
        Route::post('/', [TemplateController::class, 'store'])->middleware('role:admin');
        Route::get('/variables', [TemplateController::class, 'getVariables']);
        Route::post('/process', [TemplateController::class, 'processTemplate']);
        Route::get('/{template}', [TemplateController::class, 'show']);
        Route::put('/{template}', [TemplateController::class, 'update'])->middleware('role:admin');
        Route::delete('/{template}', [TemplateController::class, 'destroy'])->middleware('role:admin');
    });

    Route::prefix('system')->middleware('role:admin')->group(function () {
        Route::get('/config', [SystemConfigController::class, 'index']);
        Route::put('/config', [SystemConfigController::class, 'update']);
        Route::get('/config/tariff-rates', [SystemConfigController::class, 'getTariffRates']);
        Route::put('/config/tariff-rates', [SystemConfigController::class, 'updateTariffRates']);
        Route::post('/config/reset', [SystemConfigController::class, 'resetToDefault']);
    });

    // [REQ-B-7] API Laporan dan Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    Route::prefix('reports')->group(function () {
        Route::post('/generate', [ReportController::class, 'generate'])->middleware('role:admin,manajemen,keuangan');
        Route::get('/revenue', [ReportController::class, 'revenueReport'])->middleware('role:admin,manajemen,keuangan');
        Route::get('/customer-analysis', [ReportController::class, 'customerAnalysis'])->middleware('role:admin,manajemen');
        Route::get('/usage-analysis', [ReportController::class, 'usageAnalysis'])->middleware('role:admin,manajemen');
    });

    // [REQ-B-8] API Admin Management - Enhanced
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Dashboard Stats
        Route::get('/dashboard-stats', [AdminController::class, 'dashboardStats']);
        
        // [REQ-B-8.1] User Management APIs
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::post('/users', [AdminController::class, 'createUser']);
        Route::get('/users/{user}', [AdminController::class, 'getUser']);
        Route::put('/users/{user}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser']);
        Route::get('/roles', [AdminController::class, 'getRoles']);
        
        // [REQ-B-8.2] Audit Logs APIs
        Route::get('/audit-logs', [AdminController::class, 'getAuditLogs']);
        Route::get('/audit-logs/{auditLog}', [AdminController::class, 'getAuditLog']);
        
        // [REQ-B-8.3] System Management
        Route::post('/backup', [AdminController::class, 'createBackup']);
        Route::get('/system-info', [AdminController::class, 'getSystemInfo']);
    });

    // [REQ-B-9] API File Management
    Route::middleware('auth:sanctum')->prefix('files')->name('files.')->group(function () {
        // [REQ-B-9.1] File Upload with Hashing
        Route::post('/upload', [FileController::class, 'upload']);
        
        // [REQ-B-9.2] File Validation and Compression
        Route::post('/validate', [FileController::class, 'validateFile']);
        
        // File Management
        Route::get('/download/{filename}', [FileController::class, 'download']);
        Route::delete('/{filename}', [FileController::class, 'delete']);
    });

    // [REQ-B-10] API Data Tables (Dynamic Data Display)
    Route::middleware('auth:sanctum')->prefix('datatables')->name('datatables.')->group(function () {
        // [REQ-B-10.1 & REQ-B-10.2] Dynamic Data with Search, Sort, Pagination
        Route::get('/{table}', [DataTableController::class, 'getData']);
        Route::get('/{table}/schema', [DataTableController::class, 'getTableSchema']);
        Route::get('/{table}/export', [DataTableController::class, 'exportData']);
    });

    // [REQ-NEW] API Tariff Management (Based on Kepbup)
    Route::middleware('auth:sanctum')->prefix('tariff')->name('tariff.')->group(function () {
        // Public tariff information
        Route::get('/customer-groups', [TariffController::class, 'getCustomerGroups']);
        Route::get('/customer-groups/{code}', [TariffController::class, 'getCustomerGroupDetail']);
        Route::get('/meter-sizes', [TariffController::class, 'getMeterSizes']);
        Route::post('/simulate', [TariffController::class, 'simulateTariff']);
        
        // Admin-only tariff management
        Route::middleware('role:admin')->group(function () {
            Route::put('/customer-groups/{code}', [TariffController::class, 'updateCustomerGroup']);
            Route::put('/meter-admin-fees/{meterSize}', [TariffController::class, 'updateMeterAdminFee']);
        });
    });
});
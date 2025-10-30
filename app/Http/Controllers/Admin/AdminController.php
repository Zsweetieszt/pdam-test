<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Customer;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\AuditLog;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Ambil data statistik untuk dashboard
        $stats = [
            'users' => [
                'total' => User::whereHas('role', function($q) {
                    $q->whereIn('name', ['admin', 'keuangan', 'manajemen']);
                })->count(),
            ],
            'customers' => [
                'total' => Customer::count(),
            ],
            'bills' => [
                'total_this_month' => Bill::whereMonth('created_at', now()->month)->count(),
            ],
            'payments' => [
                'total_this_month' => Payment::whereMonth('created_at', now()->month)->sum('amount'),
            ],
        ];

        return view('admin.dashboard', [
            'title' => 'Dashboard Admin - PDAM Billing System',
            'stats' => $stats
        ]);
    }

    public function users()
    {
        return view('admin.users', [
            'title' => 'Manajemen User - Admin PDAM'
        ]);
    }

    // REQ-F-3: Tambah method customers untuk manajemen pelanggan
    public function customers()
    {
        return view('admin.customers', [
            'title' => 'Manajemen Customer - Admin PDAM'
        ]);
    }

    public function billing()
    {
        return view('admin.billing', [
            'title' => 'Manajemen Tagihan - Admin PDAM'
        ]);
    }

    public function reports()
    {
        return view('admin.reports', [
            'title' => 'Laporan - Admin PDAM'
        ]);
    }

    public function settings()
    {
        return view('admin.settings', [
            'title' => 'Pengaturan - Admin PDAM'
        ]);
    }

    public function auditLogs()
    {
        return view('admin.audit-logs', [
            'title' => 'Audit Logs - Admin PDAM'
        ]);
    }
}
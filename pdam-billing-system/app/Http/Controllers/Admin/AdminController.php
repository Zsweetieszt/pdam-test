<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'title' => 'Dashboard Admin - PDAM Billing System'
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
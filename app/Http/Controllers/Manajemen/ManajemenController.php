<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManajemenController extends Controller
{
    public function dashboard()
    {
        return view('manajemen.dashboard', [
            'title' => 'Dashboard Manajemen - PDAM Billing System'
        ]);
    }

    public function reports()
    {
        return view('manajemen.reports', [
            'title' => 'Laporan - Manajemen PDAM'
        ]);
    }

    public function analytics()
    {
        return view('manajemen.analytics', [
            'title' => 'Analisis - Manajemen PDAM'
        ]);
    }
}

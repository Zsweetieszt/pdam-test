<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page of PDAM Billing System
     */
    public function index()
    {
        return view('home', [
            'title' => 'Sistem Penagihan PDAM',
            'subtitle' => 'Dengan Integrasi WhatsApp'
        ]);
    }

    /**
     * Display dashboard for authenticated users
     */
    public function dashboard()
    {
        return view('dashboard', [
            'title' => 'Dashboard - Sistem Penagihan PDAM'
        ]);
    }
}

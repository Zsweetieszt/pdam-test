<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function dashboard()
    {
        return view('customer.dashboard', [
            'title' => 'Dashboard Customer - PDAM Billing System'
        ]);
    }

    public function bills()
    {
        return view('customer.bills', [
            'title' => 'Tagihan Saya - Customer PDAM'
        ]);
    }

    public function payments()
    {
        return view('customer.payments', [
            'title' => 'Riwayat Pembayaran - Customer PDAM'
        ]);
    }

    public function profile()
    {
        return view('profile.index', [
            'title' => 'Profil Saya - Customer PDAM'
        ]);
    }
}
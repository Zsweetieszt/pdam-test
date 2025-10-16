<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function dashboard()
    {
        return view('keuangan.dashboard', [
            'title' => 'Dashboard Keuangan - PDAM Billing System'
        ]);
    }

    public function billing()
    {
        return view('keuangan.billing', [
            'title' => 'Penagihan - Keuangan PDAM'
        ]);
    }

    public function payments()
    {
        return view('keuangan.payments', [
            'title' => 'Pembayaran - Keuangan PDAM'
        ]);
    }

    public function whatsapp()
    {
        return view('keuangan.whatsapp', [
            'title' => 'WhatsApp Notification - Keuangan PDAM'
        ]);
    }
}

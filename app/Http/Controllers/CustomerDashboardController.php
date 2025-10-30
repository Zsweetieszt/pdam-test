<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Meter;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return redirect()->route('login')->with('error', 'Data customer tidak ditemukan.');
        }

        // Data khusus untuk dashboard customer
        $stats = [
            'customer_info' => [
                'name' => $user->name,
                'customer_number' => $customer->customer_number,
                'phone' => $user->phone,
                'address' => $customer->address,
            ],
            'meters' => Meter::where('customer_id', $customer->id)->get(),
            'billing' => [
                'current_bill' => Bill::whereHas('meter', function($q) use ($customer) {
                    $q->where('customer_id', $customer->id);
                })->where('status', 'pending')->first(),
                'last_payment' => Payment::whereHas('bill.meter', function($q) use ($customer) {
                    $q->where('customer_id', $customer->id);
                })->latest()->first(),
                'total_outstanding' => Bill::whereHas('meter', function($q) use ($customer) {
                    $q->where('customer_id', $customer->id);
                })->whereIn('status', ['pending', 'overdue'])->sum('total_amount'),
            ],
            'history' => [
                'recent_bills' => Bill::whereHas('meter', function($q) use ($customer) {
                    $q->where('customer_id', $customer->id);
                })->latest()->take(5)->get(),
                'payment_history' => Payment::whereHas('bill.meter', function($q) use ($customer) {
                    $q->where('customer_id', $customer->id);
                })->with('bill')->latest()->take(5)->get(),
            ]
        ];

        return view('customer.dashboard', [
            'title' => 'Dashboard Customer - PDAM Billing System',
            'stats' => $stats
        ]);
    }
}
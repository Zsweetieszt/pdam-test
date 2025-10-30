<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function dashboard()
    {
        // Data khusus untuk dashboard keuangan
        $stats = [
            'payments' => [
                'total_today' => Payment::whereDate('created_at', today())->sum('amount'),
                'total_this_month' => Payment::whereMonth('created_at', now()->month)->sum('amount'),
                'total_this_year' => Payment::whereYear('created_at', now()->year)->sum('amount'),
                'pending_verification' => Payment::whereNull('verified_at')->count(),
                'verified_today' => Payment::whereNotNull('verified_at')->whereDate('verified_at', today())->count(),
            ],
            'bills' => [
                'total_outstanding' => Bill::where('status', 'pending')->sum('total_amount'),
                'overdue_count' => Bill::where('status', 'overdue')->count(),
                'paid_this_month' => Bill::where('status', 'paid')->whereMonth('updated_at', now()->month)->count(),
            ],
            'revenue' => [
                'monthly_trend' => $this->getMonthlyRevenueTrend(),
                'top_paying_customers' => $this->getTopPayingCustomers(),
            ]
        ];

        return view('finance.dashboard', [
            'title' => 'Dashboard Keuangan - PDAM Billing System',
            'stats' => $stats
        ]);
    }

    private function getMonthlyRevenueTrend()
    {
        return Payment::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                return [
                    'month' => date('F', mktime(0, 0, 0, $item->month, 1)),
                    'total' => $item->total
                ];
            });
    }

    private function getTopPayingCustomers()
    {
        return Payment::with('bill.meter.customer.user')
            ->select(
                'bill_id',
                DB::raw('SUM(amount) as total_paid')
            )
            ->join('bills', 'payments.bill_id', '=', 'bills.id')
            ->join('meters', 'bills.meter_id', '=', 'meters.id')
            ->join('customers', 'meters.customer_id', '=', 'customers.id')
            ->join('users', 'customers.user_id', '=', 'users.id')
            ->whereMonth('payments.created_at', now()->month)
            ->groupBy('bill_id')
            ->orderBy('total_paid', 'desc')
            ->limit(5)
            ->get()
            ->map(function($payment) {
                return [
                    'customer_name' => $payment->bill->meter->customer->user->name,
                    'total_paid' => $payment->total_paid
                ];
            });
    }
}
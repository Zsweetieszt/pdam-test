<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class ManagementController extends Controller
{
    public function dashboard()
    {
        // Data khusus untuk dashboard manajemen
        $stats = [
            'overview' => [
                'total_customers' => Customer::count(),
                'active_customers' => Customer::whereHas('user', function($q) {
                    $q->where('is_active', true);
                })->count(),
                'total_bills' => Bill::count(),
                'total_revenue' => Payment::sum('amount'),
            ],
            'performance' => [
                'bills_this_month' => Bill::whereMonth('created_at', now()->month)->count(),
                'payments_this_month' => Payment::whereMonth('created_at', now()->month)->count(),
                'collection_rate' => $this->calculateCollectionRate(),
                'customer_growth' => $this->getCustomerGrowth(),
            ],
            'alerts' => [
                'overdue_bills' => Bill::where('status', 'overdue')->count(),
                'pending_payments' => Payment::whereNull('verified_at')->count(),
                'inactive_customers' => Customer::whereHas('user', function($q) {
                    $q->where('is_active', false);
                })->count(),
            ],
            'recent_activity' => AuditLog::with('user')
                ->latest()
                ->take(10)
                ->get()
                ->map(function($log) {
                    return [
                        'action' => $log->action,
                        'table' => $log->table_name,
                        'user' => $log->user->name ?? 'System',
                        'created_at' => $log->created_at->diffForHumans()
                    ];
                })
        ];

        return view('management.dashboard', [
            'title' => 'Dashboard Manajemen - PDAM Billing System',
            'stats' => $stats
        ]);
    }

    private function calculateCollectionRate()
    {
        $totalBills = Bill::whereMonth('created_at', now()->month)->count();
        $paidBills = Bill::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->count();

        return $totalBills > 0 ? round(($paidBills / $totalBills) * 100, 2) : 0;
    }

    private function getCustomerGrowth()
    {
        $thisMonth = Customer::whereMonth('created_at', now()->month)->count();
        $lastMonth = Customer::whereMonth('created_at', now()->subMonth()->month)->count();

        if ($lastMonth == 0) return $thisMonth > 0 ? 100 : 0;

        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2);
    }
}
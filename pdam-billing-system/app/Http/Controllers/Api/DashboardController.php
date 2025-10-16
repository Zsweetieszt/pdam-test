<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Meter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * [REQ-B-7.1] Data dashboard dengan agregasi statistik
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $userRole = $user->role->name;

            switch ($userRole) {
                case 'admin':
                    return $this->getAdminDashboard($request);
                case 'keuangan':
                    return $this->getKeuanganDashboard($request);
                case 'manajemen':
                    return $this->getManajemenDashboard($request);
                case 'customer':
                    return $this->getCustomerDashboard($request);
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid user role'
                    ], 403);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin Dashboard - Complete overview
     */
    private function getAdminDashboard(Request $request)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();

        // Customer statistics
        $totalCustomers = Customer::count();
        $newCustomersThisMonth = Customer::where('created_at', '>=', $currentMonth)->count();
        $activeCustomers = Customer::whereHas('meters.bills', function($q) use ($currentMonth) {
            $q->where('created_at', '>=', $currentMonth);
        })->count();

        // Bill statistics
        $totalBills = Bill::count();
        $billsThisMonth = Bill::where('created_at', '>=', $currentMonth)->count();
        $pendingBills = Bill::where('status', 'pending')->count();
        $overdueBills = Bill::where('status', 'overdue')->count();
        $paidBills = Bill::where('status', 'paid')->count();

        // Revenue statistics
        $totalRevenue = Payment::where('status', 'verified')->sum('amount');
        $revenueThisMonth = Payment::where('status', 'verified')
            ->where('payment_date', '>=', $currentMonth)
            ->sum('amount');
        $revenuePreviousMonth = Payment::where('status', 'verified')
            ->whereBetween('payment_date', [$previousMonth, $currentMonth])
            ->sum('amount');

        // Outstanding amount
        $outstandingAmount = Bill::whereIn('status', ['pending', 'overdue'])
            ->sum('total_amount');

        // Monthly trends (last 12 months)
        $monthlyTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthlyTrends[] = [
                'month' => $monthStart->format('Y-m'),
                'month_name' => $monthStart->format('M Y'),
                'bills_generated' => Bill::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'revenue' => Payment::where('status', 'verified')
                    ->whereBetween('payment_date', [$monthStart, $monthEnd])
                    ->sum('amount'),
                'new_customers' => Customer::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            ];
        }

        // Recent activities
        $recentBills = Bill::with(['meter.customer.user'])
            ->latest()
            ->limit(5)
            ->get();

        $recentPayments = Payment::with(['bill.meter.customer.user'])
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_customers' => $totalCustomers,
                    'new_customers_this_month' => $newCustomersThisMonth,
                    'active_customers' => $activeCustomers,
                    'total_bills' => $totalBills,
                    'bills_this_month' => $billsThisMonth,
                    'pending_bills' => $pendingBills,
                    'overdue_bills' => $overdueBills,
                    'paid_bills' => $paidBills,
                    'total_revenue' => $totalRevenue,
                    'revenue_this_month' => $revenueThisMonth,
                    'revenue_previous_month' => $revenuePreviousMonth,
                    'outstanding_amount' => $outstandingAmount,
                ],
                'trends' => $monthlyTrends,
                'recent_activities' => [
                    'bills' => $recentBills,
                    'payments' => $recentPayments,
                ],
                'quick_stats' => [
                    'collection_rate' => $totalRevenue > 0 ? round(($totalRevenue / ($totalRevenue + $outstandingAmount)) * 100, 2) : 0,
                    'avg_bill_amount' => $totalBills > 0 ? round(Bill::avg('total_amount'), 2) : 0,
                    'avg_payment_time' => $this->getAveragePaymentTime(),
                ]
            ]
        ]);
    }

    /**
     * Keuangan Dashboard - Payment focused
     */
    private function getKeuanganDashboard(Request $request)
    {
        $currentMonth = Carbon::now()->startOfMonth();

        // Payment statistics
        $pendingPayments = Payment::where('status', 'pending')->count();
        $verifiedPayments = Payment::where('status', 'verified')->count();
        $rejectedPayments = Payment::where('status', 'rejected')->count();

        $paymentsToday = Payment::whereDate('created_at', today())->count();
        $paymentsThisMonth = Payment::where('created_at', '>=', $currentMonth)->count();

        // Revenue this month
        $revenueThisMonth = Payment::where('status', 'verified')
            ->where('payment_date', '>=', $currentMonth)
            ->sum('amount');

        // Outstanding bills
        $overdueAmount = Bill::where('status', 'overdue')->sum('total_amount');
        $pendingAmount = Bill::where('status', 'pending')->sum('total_amount');

        // Recent payments requiring verification
        $paymentsForVerification = Payment::with(['bill.meter.customer.user'])
            ->where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        // Payment method statistics
        $paymentMethods = Payment::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->where('status', 'verified')
            ->groupBy('payment_method')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'payment_overview' => [
                    'pending_payments' => $pendingPayments,
                    'verified_payments' => $verifiedPayments,
                    'rejected_payments' => $rejectedPayments,
                    'payments_today' => $paymentsToday,
                    'payments_this_month' => $paymentsThisMonth,
                    'revenue_this_month' => $revenueThisMonth,
                    'overdue_amount' => $overdueAmount,
                    'pending_amount' => $pendingAmount,
                ],
                'pending_verifications' => $paymentsForVerification,
                'payment_methods' => $paymentMethods,
                'quick_actions' => [
                    'bills_to_notify' => Bill::whereIn('status', ['pending', 'overdue'])->count(),
                    'overdue_bills' => Bill::where('status', 'overdue')->count(),
                ]
            ]
        ]);
    }

    /**
     * Manajemen Dashboard - Analytics focused [C-20]
     */
    private function getManajemenDashboard(Request $request)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $currentYear = Carbon::now()->startOfYear();

        // Performance metrics
        $collectionRate = $this->getCollectionRate();
        $customerGrowth = $this->getCustomerGrowthRate();
        $avgBillAmount = Bill::where('created_at', '>=', $currentYear)->avg('total_amount');
        $avgUsage = Bill::where('created_at', '>=', $currentYear)->avg('usage_m3');

        // Regional analysis (by tariff group)
        $tariffAnalysis = Bill::select('tariff_group', 
                DB::raw('count(*) as bill_count'),
                DB::raw('sum(total_amount) as total_revenue'),
                DB::raw('avg(usage_m3) as avg_usage'))
            ->where('created_at', '>=', $currentYear)
            ->groupBy('tariff_group')
            ->get();

        // Monthly performance
        $monthlyPerformance = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthlyPerformance[] = [
                'month' => $monthStart->format('Y-m'),
                'month_name' => $monthStart->format('M Y'),
                'bills_count' => Bill::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'revenue' => Payment::where('status', 'verified')
                    ->whereBetween('payment_date', [$monthStart, $monthEnd])
                    ->sum('amount'),
                'collection_rate' => $this->getMonthlyCollectionRate($monthStart, $monthEnd),
                'avg_usage' => Bill::whereBetween('created_at', [$monthStart, $monthEnd])->avg('usage_m3') ?? 0,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'performance_metrics' => [
                    'collection_rate' => $collectionRate,
                    'customer_growth_rate' => $customerGrowth,
                    'avg_bill_amount' => round($avgBillAmount ?? 0, 2),
                    'avg_usage_m3' => round($avgUsage ?? 0, 2),
                ],
                'tariff_analysis' => $tariffAnalysis,
                'monthly_performance' => $monthlyPerformance,
                'insights' => [
                    'top_performing_tariff' => $this->getTopPerformingTariff(),
                    'payment_trend' => $this->getPaymentTrend(),
                    'usage_trend' => $this->getUsageTrend(),
                ]
            ]
        ]);
    }

    /**
     * Customer Dashboard - Personal overview
     */
    private function getCustomerDashboard(Request $request)
    {
        $user = $request->user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found'
            ], 404);
        }

        // Customer bills
        $currentBills = Bill::whereHas('meter', function($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            })
            ->whereIn('status', ['pending', 'overdue'])
            ->with(['billingPeriod', 'meter'])
            ->get();

        $recentBills = Bill::whereHas('meter', function($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            })
            ->with(['billingPeriod', 'meter', 'payments'])
            ->latest()
            ->limit(6)
            ->get();

        // Payment history
        $payments = Payment::whereHas('bill.meter', function($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            })
            ->with(['bill.billingPeriod'])
            ->latest()
            ->limit(5)
            ->get();

        // Usage statistics
        $yearlyUsage = Bill::whereHas('meter', function($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            })
            ->where('created_at', '>=', Carbon::now()->startOfYear())
            ->sum('usage_m3');

        $avgMonthlyUsage = Bill::whereHas('meter', function($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            })
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->avg('usage_m3');

        return response()->json([
            'success' => true,
            'data' => [
                'customer_info' => $customer->load(['user', 'meters']),
                'current_bills' => $currentBills,
                'recent_bills' => $recentBills,
                'recent_payments' => $payments,
                'usage_summary' => [
                    'yearly_usage' => round($yearlyUsage ?? 0, 2),
                    'avg_monthly_usage' => round($avgMonthlyUsage ?? 0, 2),
                    'current_tariff' => $customer->tariff_group,
                ],
                'alerts' => [
                    'overdue_count' => $currentBills->where('status', 'overdue')->count(),
                    'pending_count' => $currentBills->where('status', 'pending')->count(),
                    'total_outstanding' => $currentBills->sum('total_amount'),
                ]
            ]
        ]);
    }

    /**
     * Helper methods
     */
    private function getAveragePaymentTime()
    {
        $avgDays = Bill::join('payments', 'bills.id', '=', 'payments.bill_id')
            ->where('payments.status', 'verified')
            ->select(DB::raw('AVG(DATEDIFF(payments.payment_date, bills.created_at)) as avg_days'))
            ->value('avg_days');

        return round($avgDays ?? 0, 1);
    }

    private function getCollectionRate()
    {
        $totalBilled = Bill::sum('total_amount');
        $totalCollected = Payment::where('status', 'verified')->sum('amount');
        
        return $totalBilled > 0 ? round(($totalCollected / $totalBilled) * 100, 2) : 0;
    }

    private function getCustomerGrowthRate()
    {
        $currentMonth = Customer::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $previousMonth = Customer::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->count();

        return $previousMonth > 0 ? round((($currentMonth - $previousMonth) / $previousMonth) * 100, 2) : 0;
    }

    private function getMonthlyCollectionRate($start, $end)
    {
        $billed = Bill::whereBetween('created_at', [$start, $end])->sum('total_amount');
        $collected = Payment::where('status', 'verified')
            ->whereBetween('payment_date', [$start, $end])
            ->sum('amount');

        return $billed > 0 ? round(($collected / $billed) * 100, 2) : 0;
    }

    private function getTopPerformingTariff()
    {
        return Bill::select('tariff_group', DB::raw('sum(total_amount) as revenue'))
            ->where('created_at', '>=', Carbon::now()->startOfYear())
            ->groupBy('tariff_group')
            ->orderBy('revenue', 'desc')
            ->first()?->tariff_group ?? 'N/A';
    }

    private function getPaymentTrend()
    {
        $thisMonth = Payment::where('status', 'verified')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->sum('amount');
        
        $lastMonth = Payment::where('status', 'verified')
            ->whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->sum('amount');

        if ($lastMonth == 0) return 'No data';
        
        $change = (($thisMonth - $lastMonth) / $lastMonth) * 100;
        return $change > 0 ? 'Increasing' : ($change < 0 ? 'Decreasing' : 'Stable');
    }

    private function getUsageTrend()
    {
        $thisMonth = Bill::where('created_at', '>=', Carbon::now()->startOfMonth())->avg('usage_m3');
        $lastMonth = Bill::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->avg('usage_m3');

        if ($lastMonth == 0) return 'No data';
        
        $change = (($thisMonth - $lastMonth) / $lastMonth) * 100;
        return $change > 0 ? 'Increasing' : ($change < 0 ? 'Decreasing' : 'Stable');
    }
}

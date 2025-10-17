<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * [REQ-B-7.2] Generate laporan dalam berbagai format
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_type' => 'required|string|in:revenue,billing,customer,payment,usage',
            'format' => 'required|string|in:json,pdf,excel',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'filters' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $reportType = $request->report_type;
            $format = $request->format;
            $dateFrom = Carbon::parse($request->date_from);
            $dateTo = Carbon::parse($request->date_to);
            $filters = $request->filters ?? [];

            // Generate report data based on type
            $reportData = $this->generateReportData($reportType, $dateFrom, $dateTo, $filters);

            // Log report generation
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'GENERATE_REPORT',
                'table_name' => 'reports',
                'record_id' => null,
                'new_values' => [
                    'report_type' => $reportType,
                    'format' => $format,
                    'date_range' => "{$dateFrom->format('Y-m-d')} to {$dateTo->format('Y-m-d')}"
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            switch ($format) {
                case 'json':
                    return response()->json([
                        'success' => true,
                        'data' => $reportData
                    ]);

                case 'pdf':
                    return $this->generatePdfReport($reportData, $reportType, $dateFrom, $dateTo);

                case 'excel':
                    return $this->generateExcelReport($reportData, $reportType, $dateFrom, $dateTo);

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid format specified'
                    ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revenue Report
     */
    public function revenueReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'group_by' => 'nullable|string|in:day,week,month,tariff_group',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dateFrom = Carbon::parse($request->date_from);
            $dateTo = Carbon::parse($request->date_to);
            $groupBy = $request->input('group_by', 'month');

            $query = Payment::where('status', 'verified')
                ->whereBetween('payment_date', [$dateFrom, $dateTo])
                ->with(['bill.meter.customer']);

            switch ($groupBy) {
                case 'day':
                    $results = $query->select(
                        DB::raw('DATE(payment_date) as period'),
                        DB::raw('SUM(amount) as total_revenue'),
                        DB::raw('COUNT(*) as payment_count')
                    )->groupBy('period')->orderBy('period')->get();
                    break;

                case 'month':
                    $results = $query->select(
                        DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as period'),
                        DB::raw('SUM(amount) as total_revenue'),
                        DB::raw('COUNT(*) as payment_count')
                    )->groupBy('period')->orderBy('period')->get();
                    break;

                case 'tariff_group':
                    $results = $query->join('bills', 'payments.bill_id', '=', 'bills.id')
                        ->select(
                            'bills.tariff_group as period',
                            DB::raw('SUM(payments.amount) as total_revenue'),
                            DB::raw('COUNT(payments.id) as payment_count')
                        )->groupBy('bills.tariff_group')->get();
                    break;

                default:
                    $results = collect();
            }

            $summary = [
                'total_revenue' => $results->sum('total_revenue'),
                'total_payments' => $results->sum('payment_count'),
                'average_payment' => $results->avg('total_revenue'),
                'date_range' => "{$dateFrom->format('Y-m-d')} to {$dateTo->format('Y-m-d')}",
                'group_by' => $groupBy,
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                    'details' => $results,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate revenue report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Customer Analysis Report
     */
    public function customerAnalysis(Request $request)
    {
        try {
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->startOfYear();
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

            // Customer growth
            $customerGrowth = Customer::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as new_customers')
            )->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

            // Customer by tariff group
            $customersByTariff = Customer::select('tariff_group', DB::raw('COUNT(*) as count'))
                ->groupBy('tariff_group')
                ->get();

            // Active vs inactive customers
            $activeCustomers = Customer::whereHas('meters.bills', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('created_at', [$dateFrom, $dateTo]);
            })->count();

            $totalCustomers = Customer::count();
            $inactiveCustomers = $totalCustomers - $activeCustomers;

            // Top customers by revenue
            $topCustomers = Customer::select('customers.*', DB::raw('SUM(payments.amount) as total_paid'))
                ->join('meters', 'customers.id', '=', 'meters.customer_id')
                ->join('bills', 'meters.id', '=', 'bills.meter_id')
                ->join('payments', 'bills.id', '=', 'payments.bill_id')
                ->where('payments.status', 'verified')
                ->whereBetween('payments.payment_date', [$dateFrom, $dateTo])
                ->with(['user'])
                ->groupBy('customers.id')
                ->orderBy('total_paid', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'customer_growth' => $customerGrowth,
                    'customers_by_tariff' => $customersByTariff,
                    'activity_summary' => [
                        'total_customers' => $totalCustomers,
                        'active_customers' => $activeCustomers,
                        'inactive_customers' => $inactiveCustomers,
                        'activity_rate' => $totalCustomers > 0 ? round(($activeCustomers / $totalCustomers) * 100, 2) : 0,
                    ],
                    'top_customers' => $topCustomers,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate customer analysis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Usage Analysis Report
     */
    public function usageAnalysis(Request $request)
    {
        try {
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->startOfYear();
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

            // Usage by month
            $monthlyUsage = Bill::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(usage_m3) as total_usage'),
                DB::raw('AVG(usage_m3) as avg_usage'),
                DB::raw('COUNT(*) as bill_count')
            )->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

            // Usage by tariff group
            $usageByTariff = Bill::select(
                'tariff_group',
                DB::raw('SUM(usage_m3) as total_usage'),
                DB::raw('AVG(usage_m3) as avg_usage'),
                DB::raw('COUNT(*) as customer_count')
            )->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('tariff_group')
            ->get();

            // High usage customers
            $highUsageCustomers = Bill::select('meter_id', DB::raw('SUM(usage_m3) as total_usage'))
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->with(['meter.customer.user'])
                ->groupBy('meter_id')
                ->orderBy('total_usage', 'desc')
                ->limit(10)
                ->get();

            // Usage distribution
            $usageDistribution = Bill::select(
                DB::raw('CASE 
                    WHEN usage_m3 <= 10 THEN "0-10 m³"
                    WHEN usage_m3 <= 20 THEN "11-20 m³"
                    WHEN usage_m3 <= 30 THEN "21-30 m³"
                    WHEN usage_m3 <= 50 THEN "31-50 m³"
                    ELSE "50+ m³"
                END as usage_range'),
                DB::raw('COUNT(*) as count')
            )->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('usage_range')
            ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'monthly_usage' => $monthlyUsage,
                    'usage_by_tariff' => $usageByTariff,
                    'high_usage_customers' => $highUsageCustomers,
                    'usage_distribution' => $usageDistribution,
                    'summary' => [
                        'total_usage' => $monthlyUsage->sum('total_usage'),
                        'avg_monthly_usage' => $monthlyUsage->avg('avg_usage'),
                        'total_bills' => $monthlyUsage->sum('bill_count'),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate usage analysis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * [REQ-B-7.3] Export data ke PDF dan Excel
     */
    private function generatePdfReport($data, $reportType, $dateFrom, $dateTo)
    {
        $pdf = Pdf::loadView('reports.pdf_template', [
            'data' => $data,
            'reportType' => $reportType,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedAt' => now(),
            'title' => ucfirst($reportType) . ' Report'
        ]);

        $filename = "{$reportType}_report_{$dateFrom->format('Y-m-d')}_to_{$dateTo->format('Y-m-d')}.pdf";
        
        return $pdf->download($filename);
    }

    private function generateExcelReport($data, $reportType, $dateFrom, $dateTo)
    {
        $filename = "{$reportType}_report_{$dateFrom->format('Y-m-d')}_to_{$dateTo->format('Y-m-d')}.xlsx";
        
        return Excel::download(new ReportExport($data, $reportType, $dateFrom, $dateTo), $filename);
    }

    /**
     * Generate report data based on type
     */
    private function generateReportData($reportType, $dateFrom, $dateTo, $filters)
    {
        switch ($reportType) {
            case 'revenue':
                return $this->generateRevenueData($dateFrom, $dateTo, $filters);
            case 'billing':
                return $this->generateBillingData($dateFrom, $dateTo, $filters);
            case 'customer':
                return $this->generateCustomerData($dateFrom, $dateTo, $filters);
            case 'payment':
                return $this->generatePaymentData($dateFrom, $dateTo, $filters);
            case 'usage':
                return $this->generateUsageData($dateFrom, $dateTo, $filters);
            default:
                throw new \Exception('Invalid report type');
        }
    }

    private function generateRevenueData($dateFrom, $dateTo, $filters)
    {
        $query = Payment::where('status', 'verified')
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->with(['bill.meter.customer.user']);

        if (isset($filters['tariff_group'])) {
            $query->whereHas('bill', function($q) use ($filters) {
                $q->where('tariff_group', $filters['tariff_group']);
            });
        }

        return [
            'summary' => [
                'total_revenue' => $query->sum('amount'),
                'payment_count' => $query->count(),
                'avg_payment' => $query->avg('amount'),
            ],
            'details' => $query->get(),
        ];
    }

    private function generateBillingData($dateFrom, $dateTo, $filters)
    {
        $query = Bill::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['meter.customer.user', 'billingPeriod', 'payments']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['tariff_group'])) {
            $query->where('tariff_group', $filters['tariff_group']);
        }

        return [
            'summary' => [
                'total_bills' => $query->count(),
                'total_amount' => $query->sum('total_amount'),
                'avg_amount' => $query->avg('total_amount'),
            ],
            'details' => $query->get(),
        ];
    }

    private function generateCustomerData($dateFrom, $dateTo, $filters)
    {
        $query = Customer::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['user', 'meters']);

        if (isset($filters['tariff_group'])) {
            $query->where('tariff_group', $filters['tariff_group']);
        }

        return [
            'summary' => [
                'new_customers' => $query->count(),
                'by_tariff' => Customer::select('tariff_group', DB::raw('count(*) as count'))
                    ->groupBy('tariff_group')->get(),
            ],
            'details' => $query->get(),
        ];
    }

    private function generatePaymentData($dateFrom, $dateTo, $filters)
    {
        $query = Payment::whereBetween('payment_date', [$dateFrom, $dateTo])
            ->with(['bill.meter.customer.user']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        return [
            'summary' => [
                'total_payments' => $query->count(),
                'total_amount' => $query->sum('amount'),
                'by_method' => Payment::select('payment_method', DB::raw('count(*) as count'))
                    ->whereBetween('payment_date', [$dateFrom, $dateTo])
                    ->groupBy('payment_method')->get(),
            ],
            'details' => $query->get(),
        ];
    }

    private function generateUsageData($dateFrom, $dateTo, $filters)
    {
        $query = Bill::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['meter.customer.user']);

        if (isset($filters['tariff_group'])) {
            $query->where('tariff_group', $filters['tariff_group']);
        }

        return [
            'summary' => [
                'total_usage' => $query->sum('usage_m3'),
                'avg_usage' => $query->avg('usage_m3'),
                'bill_count' => $query->count(),
            ],
            'details' => $query->get(),
        ];
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Meter;
use App\Models\BillingPeriod;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function getBillingPeriods()
    {
        try {
            $periods = BillingPeriod::where('is_active', true)
                                   ->orderBy('period_year', 'desc')
                                   ->orderBy('period_month', 'desc')
                                   ->get();
            
            return response()->json([
                'success' => true,
                'data' => $periods
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve billing periods',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * [REQ-B-3.3] Get bills with filter and pagination
     */
    public function index(Request $request)
    {
        try {
            $query = Bill::with(['meter.customer.user', 'billingPeriod', 'payments']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('billing_period_id')) {
                $query->where('billing_period_id', $request->billing_period_id);
            }

            if ($request->filled('due_date_from')) {
                $query->where('due_date', '>=', $request->due_date_from);
            }

            if ($request->filled('due_date_to')) {
                $query->where('due_date', '<=', $request->due_date_to);
            }

            if ($request->filled('customer_search')) {
                $search = $request->customer_search;
                $query->whereHas('meter.customer', function($q) use ($search) {
                    $q->where('customer_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('phone', 'like', "%{$search}%");
                      });
                });
            }

            // Sort by due date descending by default
            $query->orderBy('due_date', 'desc');

            $perPage = $request->input('per_page', 15);
            $bills = $query->paginate($perPage);

            // Add computed attributes to each bill
            $bills->getCollection()->transform(function ($bill) {
                $bill->usage_m3 = $bill->current_reading - $bill->previous_reading;
                $bill->total_amount = $bill->base_amount + $bill->additional_charges + $bill->tax_amount;
                return $bill;
            });

            return response()->json([
                'success' => true,
                'data' => $bills
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bills',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * [REQ-B-3.1] Generate bill based on meter reading and tariff
     */
    public function generateBill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meter_id' => 'required|exists:meters,id',
            'billing_period_id' => 'required|exists:billing_periods,id',
            'current_reading' => 'required|integer|min:0',
            'base_amount' => 'required|numeric|min:0',
            'additional_charges' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'due_date' => 'required|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $meter = Meter::findOrFail($request->meter_id);
            $billingPeriod = BillingPeriod::findOrFail($request->billing_period_id);

            // Check if bill already exists for this meter and period
            $existingBill = Bill::where('meter_id', $request->meter_id)
                               ->where('billing_period_id', $request->billing_period_id)
                               ->first();

            if ($existingBill) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bill already exists for this meter and billing period'
                ], 422);
            }

            // Get previous reading from last bill
            $lastBill = Bill::where('meter_id', $request->meter_id)
                           ->orderBy('created_at', 'desc')
                           ->first();

            $previousReading = $lastBill ? $lastBill->current_reading : 0;

            // Validate current reading is not less than previous
            if ($request->current_reading < $previousReading) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current reading cannot be less than previous reading (' . $previousReading . ')'
                ], 422);
            }

            // Generate bill number
            $billNumber = $this->generateBillNumber($billingPeriod);

            $bill = Bill::create([
                'meter_id' => $request->meter_id,
                'billing_period_id' => $request->billing_period_id,
                'bill_number' => $billNumber,
                'previous_reading' => $previousReading,
                'current_reading' => $request->current_reading,
                'base_amount' => $request->base_amount,
                'additional_charges' => $request->additional_charges ?? 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'status' => 'pending',
                'issued_date' => now()->toDateString(),
                'due_date' => $request->due_date,
            ]);

            // Log bill generation
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'GENERATE_BILL',
                'table_name' => 'bills',
                'record_id' => $bill->id,
                'new_values' => $bill->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            // Add computed attributes
            $bill->usage_m3 = $bill->current_reading - $bill->previous_reading;
            $bill->total_amount = $bill->base_amount + $bill->additional_charges + $bill->tax_amount;

            return response()->json([
                'success' => true,
                'message' => 'Bill generated successfully',
                'data' => $bill->load(['meter.customer.user', 'billingPeriod'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate bill',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * [REQ-B-3.2] Update bill status with business rules validation
     */
    public function updateStatus(Request $request, Bill $bill)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,sent,paid,overdue,cancelled',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // [C-10] Tagihan yang sudah dibayar tidak dapat diubah status pembayarannya
            if ($bill->status === 'paid' && $request->status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Paid bills cannot be changed to another status'
                ], 422);
            }

            $oldStatus = $bill->status;
            $oldValues = $bill->toArray();

            $bill->update([
                'status' => $request->status
            ]);

            // Log status change
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'UPDATE_BILL_STATUS',
                'table_name' => 'bills',
                'record_id' => $bill->id,
                'old_values' => $oldValues,
                'new_values' => $bill->fresh()->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Add computed attributes
            $bill->usage_m3 = $bill->current_reading - $bill->previous_reading;
            $bill->total_amount = $bill->base_amount + $bill->additional_charges + $bill->tax_amount;

            return response()->json([
                'success' => true,
                'message' => 'Bill status updated successfully',
                'data' => [
                    'bill' => $bill->load(['meter.customer.user', 'billingPeriod']),
                    'old_status' => $oldStatus,
                    'new_status' => $bill->status
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bill status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single bill
     */
    public function show(Bill $bill)
    {
        try {
            $bill->load([
                'meter.customer.user',
                'billingPeriod',
                'payments.verifiedBy'
            ]);

            // Add computed attributes
            $bill->usage_m3 = $bill->current_reading - $bill->previous_reading;
            $bill->total_amount = $bill->base_amount + $bill->additional_charges + $bill->tax_amount;

            return response()->json([
                'success' => true,
                'data' => $bill
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bill',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bills by meter
     */
    public function getBillsByMeter(Meter $meter, Request $request)
    {
        try {
            $query = $meter->bills()->with(['billingPeriod', 'payments']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $bills = $query->orderBy('created_at', 'desc')->get();

            // Add computed attributes
            $bills->transform(function ($bill) {
                $bill->usage_m3 = $bill->current_reading - $bill->previous_reading;
                $bill->total_amount = $bill->base_amount + $bill->additional_charges + $bill->tax_amount;
                return $bill;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'meter' => $meter->load('customer.user'),
                    'bills' => $bills
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bills',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique bill number
     */
    private function generateBillNumber(BillingPeriod $period): string
    {
        $prefix = 'BILL';
        $yearMonth = $period->period_year . str_pad($period->period_month, 2, '0', STR_PAD_LEFT);
        
        // Get last bill number for this period
        $lastBill = Bill::where('billing_period_id', $period->id)
                       ->orderBy('id', 'desc')
                       ->first();

        $sequence = $lastBill ? 
            (int) substr($lastBill->bill_number, -4) + 1 : 1;

        $sequenceStr = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$yearMonth}-{$sequenceStr}";
    }

    // NEW METHODS untuk Multiple Meter Billing Support
    
    /**
     * Get outstanding bills for specific meter
     * REQ-NEW: Billing per meter, bukan per customer
     */
    public function getOutstandingBills(Meter $meter)
    {
        try {
            $outstandingBills = $meter->bills()
                ->where('status', 'unpaid')
                ->with(['billingPeriod'])
                ->orderBy('due_date', 'asc')
                ->get()
                ->map(function ($bill) {
                    return [
                        'id' => $bill->id,
                        'bill_number' => $bill->bill_number,
                        'period' => $bill->billingPeriod->period_name,
                        'usage_m3' => $bill->usage_m3,
                        'water_charge' => $bill->base_amount,
                        'admin_fee' => $bill->additional_charges,
                        'total_amount' => $bill->total_amount,
                        'due_date' => $bill->due_date,
                        'days_overdue' => $bill->due_date->isPast() ? $bill->due_date->diffInDays(now()) : 0,
                    ];
                });
            
            $summary = [
                'total_bills' => $outstandingBills->count(),
                'total_amount' => $outstandingBills->sum('total_amount'),
                'overdue_bills' => $outstandingBills->where('days_overdue', '>', 0)->count(),
                'overdue_amount' => $outstandingBills->where('days_overdue', '>', 0)->sum('total_amount'),
            ];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'meter_info' => [
                        'id' => $meter->id,
                        'meter_number' => $meter->meter_number,
                        'customer_name' => $meter->customer->user->name,
                    ],
                    'summary' => $summary,
                    'bills' => $outstandingBills,
                ],
                'message' => 'Outstanding bills retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve outstanding bills: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Calculate bill for specific meter reading
     * REQ-NEW: Perhitungan tarif per meter dengan golongan
     */
    public function calculateBill(Request $request, Meter $meter)
    {
        $validator = Validator::make($request->all(), [
            'current_reading' => 'required|integer|min:0',
            'previous_reading' => 'nullable|integer|min:0',
            'billing_period_id' => 'required|exists:billing_periods,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $currentReading = $request->current_reading;
            $previousReading = $request->previous_reading ?? $meter->last_reading;
            
            if ($currentReading < $previousReading) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current reading cannot be less than previous reading'
                ], 400);
            }
            
            // Use TariffCalculationService
            $tariffService = app(\App\Services\TariffCalculationService::class);
            $calculation = $tariffService->calculateBill($meter, $currentReading, $previousReading);
            
            return response()->json([
                'success' => true,
                'data' => $calculation,
                'message' => 'Bill calculation completed successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate bill: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate bill for specific meter
     * REQ-NEW: Generate bill dengan perhitungan tarif baru
     */
    public function generateMeterBill(Request $request, Meter $meter)
    {
        $validator = Validator::make($request->all(), [
            'current_reading' => 'required|integer|min:0',
            'billing_period_id' => 'required|exists:billing_periods,id',
            'notes' => 'nullable|string|max:500',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $billingPeriod = BillingPeriod::findOrFail($request->billing_period_id);
            
            // Check if bill already exists for this meter and period
            $existingBill = Bill::where('meter_id', $meter->id)
                ->where('billing_period_id', $billingPeriod->id)
                ->first();
                
            if ($existingBill) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bill already exists for this meter and billing period'
                ], 400);
            }
            
            // Generate bill using TariffCalculationService
            $tariffService = app(\App\Services\TariffCalculationService::class);
            $bill = $tariffService->generateBill($meter, $request->current_reading, $billingPeriod);
            
            // Add notes if provided
            if ($request->notes) {
                $bill->update(['notes' => $request->notes]);
            }
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'generate_bill',
                'model' => 'Bill',
                'model_id' => $bill->id,
                'changes' => [
                    'meter_id' => $meter->id,
                    'billing_period_id' => $billingPeriod->id,
                    'amount' => $bill->total_amount,
                ],
            ]);
            
            $bill->load(['meter.customer.user', 'billingPeriod']);
            
            return response()->json([
                'success' => true,
                'data' => $bill,
                'message' => 'Bill generated successfully'
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate bill: ' . $e->getMessage()
            ], 500);
        }
    }
}
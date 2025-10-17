<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Bill;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * [REQ-B-5.1] Input dan validasi pembayaran
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bill_id' => 'required|exists:bills,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:transfer,cash,online,mobile_banking',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // 2MB max
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

            $bill = Bill::findOrFail($request->bill_id);

            // [C-10] Tagihan yang sudah dibayar tidak dapat diubah
            if ($bill->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bill is already paid and cannot be modified'
                ], 422);
            }

            // Handle file upload dengan hash filename [C-17]
            $paymentProofPath = null;
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $hashedName = hash('sha256', $file->getClientOriginalName() . time()) . '.' . $file->getClientOriginalExtension();
                $paymentProofPath = $file->storeAs('payment-proofs', $hashedName, 'private');
            }

            // Generate payment number
            $latestPayment = Payment::latest('id')->first();
            $nextNumber = $latestPayment ? (intval(substr($latestPayment->payment_number, -4)) + 1) : 1;
            $paymentNumber = 'PAY' . date('Y') . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $payment = Payment::create([
                'bill_id' => $request->bill_id,
                'payment_number' => $paymentNumber,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'payment_proof_path' => $paymentProofPath,
                'status' => 'pending', // Default status
                'created_by' => $request->user()->id,
            ]);

            // Log payment creation
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'CREATE',
                'table_name' => 'payments',
                'record_id' => $payment->id,
                'new_values' => $payment->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'data' => $payment->load(['bill.meter.customer.user'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * [REQ-B-5.2] Verifikasi pembayaran oleh staff keuangan
     */
    public function verify(Request $request, Payment $payment)
    {
        // [C-19] Hanya role Keuangan yang dapat memproses pembayaran
        if ($request->user()->role->name !== 'keuangan') {
            return response()->json([
                'success' => false,
                'message' => 'Only Keuangan staff can verify payments'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:verified,rejected',
            'verification_notes' => 'nullable|string|max:500',
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

            $oldValues = $payment->toArray();

            $payment->update([
                'status' => $request->status,
                'verification_notes' => $request->verification_notes,
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
            ]);

            // If payment is verified, update bill status to paid
            if ($request->status === 'verified') {
                $bill = $payment->bill;
                $bill->update([
                    'status' => 'paid',
                    'paid_amount' => $payment->amount,
                    'paid_at' => $payment->payment_date,
                ]);

                // Log bill status update
                AuditLog::create([
                    'user_id' => $request->user()->id,
                    'action' => 'UPDATE',
                    'table_name' => 'bills',
                    'record_id' => $bill->id,
                    'old_values' => ['status' => 'pending'],
                    'new_values' => $bill->toArray(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            // Log payment verification
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'VERIFY_PAYMENT',
                'table_name' => 'payments',
                'record_id' => $payment->id,
                'old_values' => $oldValues,
                'new_values' => $payment->fresh()->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment verification completed',
                'data' => $payment->load(['bill.meter.customer.user'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * [REQ-B-5.3] Riwayat pembayaran pelanggan
     */
    public function history(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:customers,id',
            'bill_id' => 'nullable|exists:bills,id',
            'status' => 'nullable|string|in:pending,verified,rejected',
            'payment_method' => 'nullable|string|in:transfer,cash,online,mobile_banking',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = Payment::with(['bill.meter.customer.user', 'createdBy', 'verifiedBy']);

            // Filter by customer (for customer role or specific customer)
            if ($request->customer_id) {
                $query->whereHas('bill.meter.customer', function($q) use ($request) {
                    $q->where('id', $request->customer_id);
                });
            } elseif ($request->user()->role->name === 'customer') {
                // Customer can only see their own payments
                $query->whereHas('bill.meter.customer', function($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                });
            }

            // Apply filters
            if ($request->bill_id) {
                $query->where('bill_id', $request->bill_id);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->payment_method) {
                $query->where('payment_method', $request->payment_method);
            }

            if ($request->date_from) {
                $query->whereDate('payment_date', '>=', $request->date_from);
            }

            if ($request->date_to) {
                $query->whereDate('payment_date', '<=', $request->date_to);
            }

            $perPage = $request->input('per_page', 15);
            $payments = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment details
     */
    public function show(Payment $payment)
    {
        try {
            // Check if user can access this payment
            $user = request()->user();
            
            if ($user->role->name === 'customer') {
                // Customer can only see their own payments
                $customerUserId = $payment->bill->meter->customer->user_id;
                if ($user->id !== $customerUserId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied'
                    ], 403);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $payment->load(['bill.meter.customer.user', 'createdBy', 'verifiedBy'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download payment proof
     */
    public function downloadProof(Payment $payment)
    {
        try {
            if (!$payment->payment_proof_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'No payment proof available'
                ], 404);
            }

            if (!Storage::disk('private')->exists($payment->payment_proof_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment proof file not found'
                ], 404);
            }

            return Storage::disk('private')->download($payment->payment_proof_path);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download payment proof',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

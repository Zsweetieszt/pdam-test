<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\NotificationTemplate;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WhatsAppController extends Controller
{
    /**
     * [REQ-B-4.1] Generate WhatsApp link with Keuangan role validation
     */
    public function generateLink(Request $request)
    {
        // [C-19] Notifikasi tagihan hanya dapat digenerate oleh role Keuangan
        if (!$request->user()->isKeuangan()) {
            return response()->json([
                'success' => false,
                'message' => 'Only Keuangan role can generate WhatsApp links'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'bill_id' => 'required|exists:bills,id',
            'template_type' => 'required|in:bill_reminder,overdue_notice',
            'phone' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bill = Bill::with(['meter.customer.user', 'billingPeriod'])->findOrFail($request->bill_id);

            // [C-9] Notifikasi tagihan hanya dapat digenerate pada tagihan dengan status 'pending' atau 'overdue'
            if (!in_array($bill->status, ['pending', 'overdue'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp link can only be generated for pending or overdue bills'
                ], 422);
            }

            // Get notification template
            $template = NotificationTemplate::where('template_type', $request->template_type)
                                           ->where('is_active', true)
                                           ->first();

            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification template not found'
                ], 404);
            }

            // Prepare template data
            $templateData = [
                'customer_name' => $bill->meter->customer->user->name,
                'customer_number' => $bill->meter->customer->customer_number,
                'bill_number' => $bill->bill_number,
                'amount' => 'Rp ' . number_format($bill->total_amount, 0, ',', '.'),
                'due_date' => $bill->due_date->format('d/m/Y'),
                'period' => $bill->billingPeriod->period_month . '/' . $bill->billingPeriod->period_year,
                'usage_m3' => $bill->usage_m3,
            ];

            // Generate message
            $message = $template->generateMessage($templateData);

            // Generate WhatsApp link
            $whatsappLink = $this->generateWhatsAppUrl($request->phone, $message);

            // Update bill status to 'sent' if it was 'pending'
            if ($bill->status === 'pending') {
                $bill->update(['status' => 'sent']);
            }

            // Log WhatsApp link generation
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'GENERATE_WHATSAPP_LINK',
                'table_name' => 'bills',
                'record_id' => $bill->id,
                'new_values' => [
                    'phone' => $request->phone,
                    'template_type' => $request->template_type,
                    'message_preview' => substr($message, 0, 100) . '...'
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp link generated successfully',
                'data' => [
                    'whatsapp_link' => $whatsappLink,
                    'message' => $message,
                    'phone' => $request->phone,
                    'bill' => [
                        'id' => $bill->id,
                        'bill_number' => $bill->bill_number,
                        'status' => $bill->fresh()->status
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate WhatsApp link',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * [REQ-B-4.2] Format message with template
     */
    public function formatMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_type' => 'required|in:bill_reminder,overdue_notice,payment_confirmation',
            'data' => 'required|array',
            'preview_only' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $template = NotificationTemplate::where('template_type', $request->template_type)
                                           ->where('is_active', true)
                                           ->first();

            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found'
                ], 404);
            }

            $message = $template->generateMessage($request->data);

            $response = [
                'success' => true,
                'data' => [
                    'template' => $template,
                    'formatted_message' => $message,
                    'available_variables' => $template->variables
                ]
            ];

            // If not preview only, generate WhatsApp link
            if (!$request->input('preview_only', false) && isset($request->data['phone'])) {
                $response['data']['whatsapp_link'] = $this->generateWhatsAppUrl(
                    $request->data['phone'], 
                    $message
                );
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to format message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * [REQ-B-4.3] Get WhatsApp generation logs
     */
    public function getLogs(Request $request)
    {
        try {
            $query = AuditLog::where('action', 'GENERATE_WHATSAPP_LINK')
                            ->with('user')
                            ->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $perPage = $request->input('per_page', 15);
            $logs = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $logs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate WhatsApp URL with message
     */
    private function generateWhatsAppUrl(string $phone, string $message): string
    {
        // Clean phone number (remove non-digits)
        $cleanPhone = preg_replace('/\D/', '', $phone);
        
        // Add Indonesian country code if not present
        if (!str_starts_with($cleanPhone, '62')) {
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            } else {
                $cleanPhone = '62' . $cleanPhone;
            }
        }

        // Encode message for URL
        $encodedMessage = urlencode($message);

        // Generate WhatsApp wa.me link
        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }
}

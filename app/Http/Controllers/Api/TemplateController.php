<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TemplateController extends Controller
{
    /**
     * [REQ-B-6.1] CRUD template notifikasi WhatsApp
     * [C-4] Hanya Admin yang dapat mengelola template
     */
    public function index(Request $request)
    {
        try {
            $query = NotificationTemplate::query();

            // Filter by type if provided
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // Search by name or content
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }

            $perPage = $request->input('per_page', 15);
            $templates = $query->orderBy('name')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $templates
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve templates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new template - Admin only [C-4]
     */
    public function store(Request $request)
    {
        if ($request->user()->role->name !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin can manage templates'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:notification_templates',
            'type' => 'required|string|in:bill_reminder,overdue_notice,payment_confirmation',
            'content' => 'required|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
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

            $template = NotificationTemplate::create([
                'name' => $request->name,
                'type' => $request->type,
                'content' => $request->content,
                'variables' => $request->variables ?? [],
                'is_active' => $request->input('is_active', true),
            ]);

            // Log template creation
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'CREATE',
                'table_name' => 'notification_templates',
                'record_id' => $template->id,
                'new_values' => $template->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Template created successfully',
                'data' => $template
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show specific template
     */
    public function show(NotificationTemplate $template)
    {
        return response()->json([
            'success' => true,
            'data' => $template
        ]);
    }

    /**
     * Update template - Admin only [C-4]
     */
    public function update(Request $request, NotificationTemplate $template)
    {
        if ($request->user()->role->name !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin can manage templates'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100|unique:notification_templates,name,' . $template->id,
            'type' => 'sometimes|required|string|in:bill_reminder,overdue_notice,payment_confirmation',
            'content' => 'sometimes|required|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $oldValues = $template->toArray();
            $template->update($request->only(['name', 'type', 'content', 'variables', 'is_active']));

            // Log template update
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'UPDATE',
                'table_name' => 'notification_templates',
                'record_id' => $template->id,
                'old_values' => $oldValues,
                'new_values' => $template->fresh()->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template updated successfully',
                'data' => $template
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete template - Admin only [C-4]
     */
    public function destroy(Request $request, NotificationTemplate $template)
    {
        if ($request->user()->role->name !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin can manage templates'
            ], 403);
        }

        try {
            $oldValues = $template->toArray();

            // Log template deletion
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'DELETE',
                'table_name' => 'notification_templates',
                'record_id' => $template->id,
                'old_values' => $oldValues,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $template->delete();

            return response()->json([
                'success' => true,
                'message' => 'Template deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * [REQ-B-6.2] Replace variabel dalam template dengan data real
     */
    public function processTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:notification_templates,id',
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $template = NotificationTemplate::findOrFail($request->template_id);
            
            if (!$template->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template is not active'
                ], 422);
            }

            $content = $template->content;
            $data = $request->data;

            // Replace variables in template
            foreach ($data as $key => $value) {
                $placeholder = '{' . $key . '}';
                $content = str_replace($placeholder, $value, $content);
            }

            // Check for unreplaced variables
            preg_match_all('/\{([^}]+)\}/', $content, $matches);
            $unreplacedVars = $matches[1] ?? [];

            return response()->json([
                'success' => true,
                'data' => [
                    'template' => $template,
                    'processed_content' => $content,
                    'unreplaced_variables' => $unreplacedVars,
                    'replacement_data' => $data
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available variables for a template type
     */
    public function getVariables(Request $request)
    {
        $type = $request->query('type');

        $variables = [
            'bill_reminder' => [
                'customer_name' => 'Nama pelanggan',
                'bill_number' => 'Nomor tagihan',
                'amount' => 'Jumlah tagihan',
                'due_date' => 'Tanggal jatuh tempo',
                'meter_number' => 'Nomor meter',
                'period' => 'Periode tagihan',
                'current_reading' => 'Angka meter saat ini',
                'previous_reading' => 'Angka meter sebelumnya',
                'usage' => 'Pemakaian (m³)',
            ],
            'overdue_notice' => [
                'customer_name' => 'Nama pelanggan',
                'bill_number' => 'Nomor tagihan',
                'amount' => 'Jumlah tagihan',
                'overdue_days' => 'Hari terlambat',
                'meter_number' => 'Nomor meter',
                'original_due_date' => 'Tanggal jatuh tempo asli',
            ],
            'payment_confirmation' => [
                'customer_name' => 'Nama pelanggan',
                'bill_number' => 'Nomor tagihan',
                'payment_amount' => 'Jumlah pembayaran',
                'payment_date' => 'Tanggal pembayaran',
                'payment_method' => 'Metode pembayaran',
                'payment_number' => 'Nomor pembayaran',
            ],
        ];

        if ($type && isset($variables[$type])) {
            return response()->json([
                'success' => true,
                'data' => [
                    'type' => $type,
                    'variables' => $variables[$type]
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'all_types' => $variables
            ]
        ]);
    }
}

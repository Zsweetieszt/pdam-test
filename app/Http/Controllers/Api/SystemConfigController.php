<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SystemConfigController extends Controller
{
    /**
     * [REQ-B-6.3] Manajemen pengaturan sistem
     * [C-4] Hanya Admin yang dapat mengelola pengaturan
     */
    
    protected $defaultConfigs = [
        'company_name' => 'PDAM Kota',
        'company_address' => 'Jl. Contoh No. 123, Kota',
        'company_phone' => '021-1234567',
        'company_email' => 'info@pdam.kota.go.id',
        'billing_due_days' => 30,
        'late_fee_percentage' => 2.5,
        'session_timeout_hours' => 2, // [C-18]
        'max_file_upload_mb' => 2,
        'whatsapp_base_url' => 'https://wa.me/',
        'notification_enabled' => true,
        'maintenance_mode' => false,
        'backup_retention_days' => 30,
        'audit_log_retention_days' => 365,
        'tariff_rates' => [
            'R1' => ['base' => 15000, 'per_m3' => 2500],
            'R2' => ['base' => 20000, 'per_m3' => 3000],
            'R3' => ['base' => 25000, 'per_m3' => 3500],
            'B1' => ['base' => 30000, 'per_m3' => 4000],
            'B2' => ['base' => 40000, 'per_m3' => 5000],
        ],
    ];

    public function index(Request $request)
    {
        try {
            $configs = Cache::remember('system_configs', 3600, function() {
                return $this->getAllConfigs();
            });

            // Filter specific config if requested
            if ($request->has('key')) {
                $key = $request->key;
                if (isset($configs[$key])) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'key' => $key,
                            'value' => $configs[$key]
                        ]
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Configuration key not found'
                    ], 404);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $configs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve system configurations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        if ($request->user()->role->name !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin can manage system configurations'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'configs' => 'required|array',
            'configs.*.key' => 'required|string',
            'configs.*.value' => 'required',
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

            $oldConfigs = $this->getAllConfigs();
            $updatedConfigs = [];

            foreach ($request->configs as $config) {
                $key = $config['key'];
                $value = $config['value'];

                // Validate specific config rules
                if (!$this->validateConfigValue($key, $value)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Invalid value for configuration key: {$key}"
                    ], 422);
                }

                // Store in database or file (simplified to cache for demo)
                $this->setConfig($key, $value);
                $updatedConfigs[$key] = $value;
            }

            // Clear cache
            Cache::forget('system_configs');

            // Log configuration changes
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'UPDATE_SYSTEM_CONFIG',
                'table_name' => 'system_configs',
                'record_id' => null,
                'old_values' => $oldConfigs,
                'new_values' => $updatedConfigs,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'System configurations updated successfully',
                'data' => $updatedConfigs
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update system configurations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tariff rates for calculation
     */
    public function getTariffRates()
    {
        try {
            $configs = $this->getAllConfigs();
            $tariffRates = $configs['tariff_rates'] ?? $this->defaultConfigs['tariff_rates'];

            return response()->json([
                'success' => true,
                'data' => [
                    'tariff_rates' => $tariffRates,
                    'last_updated' => Cache::get('tariff_rates_updated_at')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tariff rates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update tariff rates - Admin only
     */
    public function updateTariffRates(Request $request)
    {
        if ($request->user()->role->name !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin can update tariff rates'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'tariff_rates' => 'required|array',
            'tariff_rates.*.base' => 'required|numeric|min:0',
            'tariff_rates.*.per_m3' => 'required|numeric|min:0',
            'effective_date' => 'nullable|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $oldRates = $this->getConfig('tariff_rates');
            $newRates = $request->tariff_rates;

            $this->setConfig('tariff_rates', $newRates);
            Cache::put('tariff_rates_updated_at', now());
            Cache::forget('system_configs');

            // Log tariff rate changes
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'UPDATE_TARIFF_RATES',
                'table_name' => 'system_configs',
                'record_id' => null,
                'old_values' => ['tariff_rates' => $oldRates],
                'new_values' => ['tariff_rates' => $newRates],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tariff rates updated successfully',
                'data' => [
                    'tariff_rates' => $newRates,
                    'effective_date' => $request->effective_date ?? now()->format('Y-m-d'),
                    'updated_at' => now()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tariff rates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset configurations to default
     */
    public function resetToDefault(Request $request)
    {
        if ($request->user()->role->name !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin can reset configurations'
            ], 403);
        }

        try {
            $oldConfigs = $this->getAllConfigs();

            // Reset to default values
            foreach ($this->defaultConfigs as $key => $value) {
                $this->setConfig($key, $value);
            }

            Cache::forget('system_configs');

            // Log configuration reset
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'RESET_SYSTEM_CONFIG',
                'table_name' => 'system_configs',
                'record_id' => null,
                'old_values' => $oldConfigs,
                'new_values' => $this->defaultConfigs,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'System configurations reset to default successfully',
                'data' => $this->defaultConfigs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset configurations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper methods
     */
    private function getAllConfigs()
    {
        // In real implementation, this would read from database
        // For now, we'll use cache with default fallback
        $configs = [];
        foreach ($this->defaultConfigs as $key => $defaultValue) {
            $configs[$key] = $this->getConfig($key, $defaultValue);
        }
        return $configs;
    }

    private function getConfig($key, $default = null)
    {
        return Cache::get("config.{$key}", $default);
    }

    private function setConfig($key, $value)
    {
        Cache::forever("config.{$key}", $value);
    }

    private function validateConfigValue($key, $value)
    {
        switch ($key) {
            case 'billing_due_days':
                return is_numeric($value) && $value > 0 && $value <= 365;
            case 'late_fee_percentage':
                return is_numeric($value) && $value >= 0 && $value <= 100;
            case 'session_timeout_hours':
                return is_numeric($value) && $value > 0 && $value <= 24;
            case 'max_file_upload_mb':
                return is_numeric($value) && $value > 0 && $value <= 100;
            case 'notification_enabled':
            case 'maintenance_mode':
                return is_bool($value);
            case 'backup_retention_days':
            case 'audit_log_retention_days':
                return is_numeric($value) && $value > 0 && $value <= 3650;
            default:
                return true; // Allow other configs
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataTableController extends Controller
{
    /**
     * Get paginated data with search, sort, and filters - REQ-B-10.1 & REQ-B-10.2
     */
    public function getData(Request $request, $table)
    {
        try {
            $allowedTables = ['users', 'customers', 'bills', 'payments', 'audit_logs'];
            
            if (!in_array($table, $allowedTables)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid table name',
                    'allowed_tables' => $allowedTables
                ], 400);
            }

            $query = $this->getQueryForTable($table);
            
            // Apply search - REQ-B-10.1
            if ($request->filled('search')) {
                $query = $this->applySearch($query, $table, $request->search);
            }

            // Apply filters - REQ-B-10.1
            if ($request->filled('filters')) {
                $filters = is_array($request->filters) ? $request->filters : json_decode($request->filters, true);
                $query = $this->applyFilters($query, $table, $filters);
            }

            // Apply sorting - REQ-B-10.1
            $sortField = $request->get('sort_field', $this->getDefaultSortField($table));
            $sortDirection = $request->get('sort_direction', 'desc');
            $query = $this->applySorting($query, $table, $sortField, $sortDirection);

            // Pagination - REQ-B-10.1
            $perPage = min($request->get('per_page', 10), 100); // Max 100 items per page
            $page = $request->get('page', 1);
            
            $results = $query->paginate($perPage, ['*'], 'page', $page);

            // Format response with metadata - REQ-B-10.2
            $response = [
                'success' => true,
                'data' => $this->formatTableData(collect($results->items()), $table),
                'meta' => [
                    'pagination' => [
                        'current_page' => $results->currentPage(),
                        'last_page' => $results->lastPage(),
                        'per_page' => $results->perPage(),
                        'total' => $results->total(),
                        'from' => $results->firstItem(),
                        'to' => $results->lastItem(),
                        'has_previous' => $results->previousPageUrl() !== null,
                        'has_next' => $results->nextPageUrl() !== null,
                        'links' => [
                            'first' => $results->url(1),
                            'last' => $results->url($results->lastPage()),
                            'prev' => $results->previousPageUrl(),
                            'next' => $results->nextPageUrl(),
                        ]
                    ],
                    'query' => [
                        'search' => $request->search,
                        'sort_field' => $sortField,
                        'sort_direction' => $sortDirection,
                        'filters' => $request->filters,
                        'per_page' => $perPage
                    ],
                    'table_info' => [
                        'name' => $table,
                        'searchable_fields' => $this->getSearchableFields($table),
                        'sortable_fields' => $this->getSortableFields($table),
                        'filterable_fields' => $this->getFilterableFields($table)
                    ]
                ]
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get table schema information
     */
    public function getTableSchema(Request $request, $table)
    {
        try {
            $allowedTables = ['users', 'customers', 'bills', 'payments', 'audit_logs'];
            
            if (!in_array($table, $allowedTables)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid table name'
                ], 400);
            }

            $schema = [
                'table_name' => $table,
                'fields' => $this->getTableFields($table),
                'searchable_fields' => $this->getSearchableFields($table),
                'sortable_fields' => $this->getSortableFields($table),
                'filterable_fields' => $this->getFilterableFields($table),
                'default_sort' => [
                    'field' => $this->getDefaultSortField($table),
                    'direction' => 'desc'
                ],
                'relationships' => $this->getTableRelationships($table)
            ];

            return response()->json([
                'success' => true,
                'data' => $schema
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving table schema',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export table data
     */
    public function exportData(Request $request, $table)
    {
        try {
            $allowedTables = ['users', 'customers', 'bills', 'payments', 'audit_logs'];
            
            if (!in_array($table, $allowedTables)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid table name'
                ], 400);
            }

            $format = $request->get('format', 'json');
            $allowedFormats = ['json', 'csv'];
            
            if (!in_array($format, $allowedFormats)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid export format',
                    'allowed_formats' => $allowedFormats
                ], 400);
            }

            $query = $this->getQueryForTable($table);
            
            // Apply same filters as regular data endpoint
            if ($request->filled('search')) {
                $query = $this->applySearch($query, $table, $request->search);
            }

            if ($request->filled('filters')) {
                $filters = is_array($request->filters) ? $request->filters : json_decode($request->filters, true);
                $query = $this->applyFilters($query, $table, $filters);
            }

            // Limit export to prevent memory issues
            $maxExport = 1000;
            $data = $query->limit($maxExport)->get();

            if ($format === 'csv') {
                return $this->exportToCsv($data, $table);
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatTableData($data, $table),
                'meta' => [
                    'exported_count' => $data->count(),
                    'format' => $format,
                    'table' => $table,
                    'exported_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper methods
     */
    private function getQueryForTable($table)
    {
        switch ($table) {
            case 'users':
                return User::with('role');
            case 'customers':
                return Customer::with('user');
            case 'bills':
                return Bill::with(['meter.customer.user', 'billingPeriod']);
            case 'payments':
                return Payment::with(['bill.meter.customer.user', 'verifiedBy']);
            case 'audit_logs':
                return AuditLog::with('user');
            default:
                throw new \Exception('Invalid table name');
        }
    }

    private function applySearch($query, $table, $search)
    {
        $searchFields = $this->getSearchableFields($table);
        
        $query->where(function($q) use ($searchFields, $search, $table) {
            foreach ($searchFields as $field) {
                if (strpos($field, '.') !== false) {
                    // Handle relationship fields
                    $parts = explode('.', $field);
                    $relation = $parts[0];
                    $column = $parts[1];
                    
                    $q->orWhereHas($relation, function($relationQuery) use ($column, $search) {
                        $relationQuery->where($column, 'like', "%{$search}%");
                    });
                } else {
                    // Handle direct fields
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            }
        });

        return $query;
    }

    private function applyFilters($query, $table, $filters)
    {
        $filterableFields = $this->getFilterableFields($table);
        
        foreach ($filters as $field => $value) {
            if (!in_array($field, $filterableFields) || $value === null || $value === '') {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($field, $value);
            } elseif (strpos($field, '_date') !== false || strpos($field, '_at') !== false) {
                // Handle date filters
                if (isset($value['from'])) {
                    $query->whereDate($field, '>=', $value['from']);
                }
                if (isset($value['to'])) {
                    $query->whereDate($field, '<=', $value['to']);
                }
            } else {
                $query->where($field, $value);
            }
        }

        return $query;
    }

    private function applySorting($query, $table, $sortField, $sortDirection)
    {
        $sortableFields = $this->getSortableFields($table);
        
        if (!in_array($sortField, $sortableFields)) {
            $sortField = $this->getDefaultSortField($table);
        }

        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? $sortDirection : 'desc';
        
        return $query->orderBy($sortField, $sortDirection);
    }

    private function formatTableData($data, $table)
    {
        return $data->map(function($item) use ($table) {
            $formatted = $item->toArray();
            
            // Add computed fields based on table
            switch ($table) {
                case 'bills':
                    $formatted['usage_m3'] = $item->current_reading - $item->previous_reading;
                    $formatted['total_amount'] = $item->base_amount + $item->additional_charges + $item->tax_amount;
                    break;
                case 'users':
                    $formatted['role_name'] = $item->role ? $item->role->name : null;
                    break;
            }
            
            return $formatted;
        });
    }

    private function getTableFields($table)
    {
        $fields = [
            'users' => ['id', 'name', 'phone', 'email', 'role_id', 'is_active', 'created_at', 'updated_at'],
            'customers' => ['id', 'user_id', 'customer_number', 'ktp_number', 'address', 'tariff_group', 'created_at', 'updated_at'],
            'bills' => ['id', 'meter_id', 'billing_period_id', 'previous_reading', 'current_reading', 'base_amount', 'additional_charges', 'tax_amount', 'due_date', 'status', 'created_at'],
            'payments' => ['id', 'bill_id', 'amount', 'payment_method', 'payment_date', 'status', 'verified_at', 'created_at'],
            'audit_logs' => ['id', 'user_id', 'action', 'table_name', 'record_id', 'ip_address', 'created_at']
        ];

        return $fields[$table] ?? [];
    }

    private function getSearchableFields($table)
    {
        $searchableFields = [
            'users' => ['name', 'phone', 'email'],
            'customers' => ['customer_number', 'ktp_number', 'address', 'user.name', 'user.phone'],
            'bills' => ['meter.customer.user.name', 'meter.meter_number', 'status'],
            'payments' => ['payment_method', 'reference_number', 'bill.meter.customer.user.name'],
            'audit_logs' => ['action', 'table_name', 'user.name']
        ];

        return $searchableFields[$table] ?? [];
    }

    private function getSortableFields($table)
    {
        $sortableFields = [
            'users' => ['id', 'name', 'created_at', 'updated_at'],
            'customers' => ['id', 'customer_number', 'created_at', 'updated_at'],
            'bills' => ['id', 'due_date', 'status', 'created_at'],
            'payments' => ['id', 'amount', 'payment_date', 'status', 'created_at'],
            'audit_logs' => ['id', 'action', 'created_at']
        ];

        return $sortableFields[$table] ?? [];
    }

    private function getFilterableFields($table)
    {
        $filterableFields = [
            'users' => ['role_id', 'is_active'],
            'customers' => ['tariff_group'],
            'bills' => ['status', 'meter_id', 'billing_period_id'],
            'payments' => ['status', 'payment_method'],
            'audit_logs' => ['action', 'table_name', 'user_id']
        ];

        return $filterableFields[$table] ?? [];
    }

    private function getDefaultSortField($table)
    {
        $defaultSorts = [
            'users' => 'created_at',
            'customers' => 'created_at',
            'bills' => 'created_at',
            'payments' => 'created_at',
            'audit_logs' => 'created_at'
        ];

        return $defaultSorts[$table] ?? 'id';
    }

    private function getTableRelationships($table)
    {
        $relationships = [
            'users' => ['role'],
            'customers' => ['user', 'meters'],
            'bills' => ['meter', 'billingPeriod', 'payments'],
            'payments' => ['bill', 'verifiedBy'],
            'audit_logs' => ['user']
        ];

        return $relationships[$table] ?? [];
    }

    private function exportToCsv($data, $table)
    {
        $filename = $table . '_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $table) {
            $file = fopen('php://output', 'w');
            
            // Write header
            if ($data->isNotEmpty()) {
                $firstItem = $data->first()->toArray();
                fputcsv($file, array_keys($firstItem));
                
                // Write data
                foreach ($data as $item) {
                    fputcsv($file, $item->toArray());
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

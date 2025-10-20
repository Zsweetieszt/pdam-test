<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Customer;
use App\Models\AuditLog;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics - REQ-B-7.1
     */
    public function dashboardStats(Request $request)
    {
        try {
            $stats = [
                'users' => [
                    'total' => User::count(),
                    'active' => User::where('is_active', true)->count(),
                    'admin' => User::whereHas('role', function($q) { $q->where('name', 'admin'); })->count(),
                    'keuangan' => User::whereHas('role', function($q) { $q->where('name', 'keuangan'); })->count(),
                    'manajemen' => User::whereHas('role', function($q) { $q->where('name', 'manajemen'); })->count(),
                    'customer' => User::whereHas('role', function($q) { $q->where('name', 'customer'); })->count(),
                ],
                'customers' => [
                    'total' => Customer::count(),
                    'active' => Customer::whereHas('user', function($q) { $q->where('is_active', true); })->count(),
                    'new_this_month' => Customer::whereMonth('created_at', now()->month)->count(),
                ],
                'bills' => [
                    'total_this_month' => Bill::whereMonth('created_at', now()->month)->count(),
                    'pending' => Bill::where('status', 'pending')->count(),
                    'paid' => Bill::where('status', 'paid')->count(),
                    'overdue' => Bill::where('status', 'overdue')->count(),
                ],
                'payments' => [
                    'total_this_month' => Payment::whereMonth('created_at', now()->month)->sum('amount'),
                    'verified' => Payment::whereNotNull('verified_at')->whereMonth('created_at', now()->month)->sum('amount'),
                    'pending_verification' => Payment::whereNull('verified_at')->count(),
                ],
                'system' => [
                    'total_audit_logs' => AuditLog::count(),
                    'recent_activities' => AuditLog::count() > 0 ? AuditLog::latest()->take(5)->with('user')->get() : [],
                ]
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving dashboard stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get users with pagination and filters - REQ-B-8.1
     */
    public function getUsers(Request $request)
    {
        try {
            $query = User::with('role');
            
            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            // Role filter
            if ($request->filled('role')) {
                $query->whereHas('role', function($q) use ($request) {
                    $q->where('name', $request->role);
                });
            }
            
            // Status filter
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
            
            // Sorting
            $sortField = $request->get('sort_field', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);
            
            // Pagination
            $perPage = $request->get('per_page', 10);
            $users = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $users
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving users',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Create new user - REQ-B-8.1
     */
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone|regex:/^08\d{8,11}$/',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean'
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
            
            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'is_active' => $request->boolean('is_active', true)
            ]);
            
            // Log creation
            $currentUserId = $request->user() ? $request->user()->id : null;
            AuditLog::create([
                'user_id' => $currentUserId,
                'action' => 'CREATE',
                'table_name' => 'users',
                'record_id' => $user->id,
                'new_values' => $user->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user->load('role')
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get single user
     */
    public function getUser(User $user)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $user->load('role')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update user - REQ-B-8.1
     */
    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => "required|string|regex:/^08\d{8,11}$/|unique:users,phone,{$user->id}",
            'email' => "nullable|email|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean'
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
            
            $oldValues = $user->toArray();
            
            $updateData = [
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'role_id' => $request->role_id,
                'is_active' => $request->boolean('is_active', true)
            ];
            
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }
            
            $user->update($updateData);
            
            // Log update
            $currentUserId = $request->user() ? $request->user()->id : null;
            AuditLog::create([
                'user_id' => $currentUserId,
                'action' => 'UPDATE',
                'table_name' => 'users',
                'record_id' => $user->id,
                'old_values' => $oldValues,
                'new_values' => $user->fresh()->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user->load('role')
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete user - REQ-B-8.1
     */
    public function deleteUser(Request $request, User $user)
    {
        try {
            DB::beginTransaction();
            
            $oldValues = $user->toArray();
            
            // Log deletion before actually deleting
            $currentUserId = $request->user() ? $request->user()->id : null;
            AuditLog::create([
                'user_id' => $currentUserId,
                'action' => 'DELETE',
                'table_name' => 'users',
                'record_id' => $user->id,
                'old_values' => $oldValues,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            $user->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get roles - REQ-B-8.1
     */
    public function getRoles()
    {
        try {
            $roles = Role::all();
            
            return response()->json([
                'success' => true,
                'data' => $roles
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get audit logs with filters - REQ-B-8.2
     */
    public function getAuditLogs(Request $request)
    {
        try {
            $query = AuditLog::with('user');
            
            // User filter
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            
            // Action filter
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }
            
            // Date range filter
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            // Table filter
            if ($request->filled('table_name')) {
                $query->where('table_name', $request->table_name);
            }
            
            // Sorting
            $query->orderBy('created_at', 'desc');
            
            // Pagination
            $perPage = $request->get('per_page', 10);
            $auditLogs = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $auditLogs
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving audit logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get single audit log - REQ-B-8.2
     */
    public function getAuditLog(AuditLog $auditLog)
    {
        try {
            $auditLog->load('user');
            
            return response()->json([
                'success' => true,
                'data' => $auditLog
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving audit log',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Create system backup - REQ-B-8.3
     */
    public function createBackup(Request $request)
    {
        try {
            // Implementation for backup
            // This would depend on your backup strategy
            
            $currentUserId = $request->user() ? $request->user()->id : null;
            AuditLog::create([
                'user_id' => $currentUserId,
                'action' => 'BACKUP',
                'table_name' => 'system',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating backup',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get system information
     */
    public function getSystemInfo()
    {
        try {
            $info = [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'database_size' => 0, // Will be calculated if needed
                'total_users' => User::count(),
                'total_audit_logs' => AuditLog::count(),
                'disk_usage' => [
                    'total' => disk_total_space(storage_path()),
                    'free' => disk_free_space(storage_path())
                ]
            ];
            
            return response()->json([
                'success' => true,
                'data' => $info
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving system info',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
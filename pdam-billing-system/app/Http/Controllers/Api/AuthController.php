<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Customer;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * [REQ-B-1.1] Register new user with phone validation
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users',
            'email' => 'nullable|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'nullable|exists:roles,id'
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

            // Get customer role ID (default for new registrations)
            $customerRole = Role::where('name', 'customer')->first();
            if (!$customerRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer role not found in system'
                ], 500);
            }

            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id ?? $customerRole->id, // Default to customer role
                'is_active' => true,
            ]);

            // If user is registering as customer, automatically create customer record
            if (($request->role_id ?? $customerRole->id) == $customerRole->id) {
                // Generate customer number
                $latestCustomer = Customer::latest('id')->first();
                $nextNumber = $latestCustomer ? (intval(substr($latestCustomer->customer_number, -4)) + 1) : 1;
                $customerNumber = 'CUS' . date('Y') . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                // Create customer record
                Customer::create([
                    'user_id' => $user->id,
                    'customer_number' => $customerNumber,
                    'ktp_number' => $request->ktp_number ?? '', // Optional for now
                    'address' => $request->address ?? '', // Optional for now
                    'tariff_group' => $request->tariff_group ?? 'R1', // Default tariff group
                ]);
            }

            // Log registration
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'REGISTER',
                'table_name' => 'users',
                'record_id' => $user->id,
                'new_values' => $user->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user->load('role'),
                    'token' => $token
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * [REQ-B-1.2] Login with phone and password validation
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // Log failed login attempt
            AuditLog::create([
                'user_id' => null,
                'action' => 'LOGIN_FAILED',
                'table_name' => 'users',
                'record_id' => null,
                'new_values' => ['phone' => $request->phone],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            throw ValidationException::withMessages([
                'phone' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is deactivated'
            ], 403);
        }

        // Log successful login
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'LOGIN',
            'table_name' => 'users',
            'record_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user->load('role'),
                'token' => $token
            ]
        ]);
    }

    /**
     * [REQ-B-1.3] Reset password via WhatsApp verification
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
            'new_password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('phone', $request->phone)->first();
            
            $oldValues = ['password_changed' => false];
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Log password reset
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'PASSWORD_RESET',
                'table_name' => 'users',
                'record_id' => $user->id,
                'old_values' => $oldValues,
                'new_values' => ['password_changed' => true],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password reset failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * [REQ-B-1.4] Logout and revoke tokens
     */
    public function logout(Request $request)
    {
        try {
            // Log logout
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'LOGOUT',
                'table_name' => 'users',
                'record_id' => $request->user()->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current authenticated user
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('role')
        ]);
    }
}

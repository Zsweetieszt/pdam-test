<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Models\Role;
use App\Models\Meter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    // REQ-B-2.3: Get customers with search and pagination - REQ-F-3.1
    public function index(Request $request)
    {
        $query = Customer::with(['user', 'meters'])
            ->join('users', 'customers.user_id', '=', 'users.id');
        
        // Search functionality - REQ-F-3.1
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customers.customer_number', 'like', "%{$search}%")
                  ->orWhere('users.name', 'like', "%{$search}%")
                  ->orWhere('users.phone', 'like', "%{$search}%")
                  ->orWhereHas('meters', function ($meterQuery) use ($search) {
                      $meterQuery->where('meter_number', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by tariff group
        if ($request->filled('tariff_group')) {
            $query->where('customers.tariff_group', $request->tariff_group);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('users.is_active', $isActive);
        }
        
        // Sorting - REQ-F-10
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        // Map frontend sort fields to database fields
        $sortFieldMap = [
            'customer_number' => 'customers.customer_number',
            'name' => 'users.name',
            'created_at' => 'customers.created_at'
        ];
        
        $dbSortField = $sortFieldMap[$sortField] ?? 'customers.created_at';
        $query->orderBy($dbSortField, $sortDirection);
        
        // Pagination - REQ-F-10
        $perPage = $request->get('per_page', 10);
        $customers = $query->select('customers.*')->paginate($perPage);
        
        return response()->json($customers);
    }
    
    // REQ-B-2.1: Create new customer - REQ-F-3.2
    // Updated untuk requirement baru: pendaftaran oleh admin sama dengan mandiri
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // User validation - SAMA dengan pendaftaran mandiri
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone|regex:/^08\d{8,11}$/',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            
            // Customer validation - SAMA dengan pendaftaran mandiri
            'customer_number' => 'nullable|string|unique:customers,customer_number',
            'ktp_number' => 'required|string|size:16|unique:customers,ktp_number',
            'address' => 'required|string|min:10',
            
            // Meter validation - UPDATED untuk requirement baru
            'meters' => 'required|array|min:1|max:5', // Multiple meters support
            'meters.*.meter_number' => 'required|string|unique:meters,meter_number',
            'meters.*.meter_type' => 'required|string|in:analog,digital',
            'meters.*.customer_group_code' => 'required|string|exists:customer_groups,code',
            'meters.*.meter_size' => [
                'required',
                'string',
                Rule::in(['1/2"', '3/4"', '1"', '1 1/2"', '2"', '3"', '4"'])
            ],
            'meters.*.installation_date' => 'required|date|before_or_equal:today'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            // Get customer role
            $customerRole = Role::where('name', 'customer')->first();
            if (!$customerRole) {
                throw new \Exception('Customer role not found');
            }
            
            // Create user - SAMA dengan pendaftaran mandiri
            $user = User::create([
                'role_id' => $customerRole->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);
            
            // Generate customer number if not provided
            $customerNumber = $request->customer_number;
            if (!$customerNumber) {
                $customerNumber = 'CUST-' . str_pad(Customer::count() + 1, 6, '0', STR_PAD_LEFT);
            }
            
            // Create customer - UPDATED tanpa tariff_group
            $customer = Customer::create([
                'user_id' => $user->id,
                'customer_number' => $customerNumber,
                'ktp_number' => $request->ktp_number,
                'address' => $request->address,
                // tariff_group removed - now per meter
            ]);
            
            // Create meters - UPDATED untuk multiple meters dengan golongan
            foreach ($request->meters as $meterData) {
                Meter::create([
                    'customer_id' => $customer->id,
                    'meter_number' => $meterData['meter_number'],
                    'meter_type' => $meterData['meter_type'],
                    'customer_group_code' => $meterData['customer_group_code'],
                    'meter_size' => $meterData['meter_size'],
                    'installation_date' => $meterData['installation_date'],
                    'is_active' => true,
                    // Tariff rates will be auto-populated by Model boot method
                ]);
            }
            
            DB::commit();
            
            // Load with relationships for response
            $customer->load(['user', 'meters.customerGroup']);
            
            return response()->json([
                'message' => 'Customer created successfully',
                'data' => $customer
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to create customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // REQ-B-2.1: Show customer detail - REQ-F-3.3
    public function show(Customer $customer)
    {
        $customer->load(['user', 'meters']);
        
        return response()->json([
            'data' => $customer
        ]);
    }
    
    // REQ-B-2.1: Update customer - REQ-F-3.2
    public function update(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            // User validation
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($customer->user_id)],
            'phone' => ['required', 'string', 'regex:/^08\d{8,11}$/', Rule::unique('users')->ignore($customer->user_id)],
            'password' => 'nullable|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            
            // Customer validation
            'ktp_number' => ['required', 'string', 'size:16', Rule::unique('customers')->ignore($customer->id)],
            'address' => 'required|string',
            'tariff_group' => 'required|string|in:R1,R2,N1,N2',
            
            // Meter validation (for primary meter)
            'meter_number' => [
                'required', 
                'string', 
                Rule::unique('meters')->ignore($customer->meters->first()?->id ?? null)
            ],
            'meter_type' => 'required|string|in:analog,digital',
            'installation_date' => 'required|date'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            // Update user
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ];
            
            // Add password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            
            $customer->user->update($userData);
            
            // Update customer
            $customer->update([
                'ktp_number' => $request->ktp_number,
                'address' => $request->address,
                'tariff_group' => $request->tariff_group,
            ]);
            
            // Update primary meter (first meter)
            $primaryMeter = $customer->meters->first();
            if ($primaryMeter) {
                $primaryMeter->update([
                    'meter_number' => $request->meter_number,
                    'meter_type' => $request->meter_type,
                    'installation_date' => $request->installation_date,
                ]);
            }
            
            DB::commit();
            
            // Load with relationships for response
            $customer->load(['user', 'meters']);
            
            return response()->json([
                'message' => 'Customer updated successfully',
                'data' => $customer
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to update customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // REQ-B-2.1: Delete customer
    public function destroy(Customer $customer)
    {
        DB::beginTransaction();
        
        try {
            // Delete associated user (will cascade delete customer and meters)
            $customer->user->delete();
            
            DB::commit();
            
            return response()->json([
                'message' => 'Customer deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to delete customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // REQ-B-2.2: Search customers
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'data' => []
            ]);
        }
        
        $customers = Customer::with(['user', 'meters'])
            ->join('users', 'customers.user_id', '=', 'users.id')
            ->where(function ($q) use ($query) {
                $q->where('customers.customer_number', 'like', "%{$query}%")
                  ->orWhere('users.name', 'like', "%{$query}%")
                  ->orWhere('users.phone', 'like', "%{$query}%")
                  ->orWhereHas('meters', function ($meterQuery) use ($query) {
                      $meterQuery->where('meter_number', 'like', "%{$query}%");
                  });
            })
            ->select('customers.*')
            ->limit(10)
            ->get();
        
        return response()->json([
            'data' => $customers
        ]);
    }
    
    // REQ-B-2.3: Validate meter number
    public function validateMeter(Request $request)
    {
        $meterNumber = $request->get('meter_number');
        $customerId = $request->get('customer_id'); // For edit mode
        
        $query = Meter::where('meter_number', $meterNumber);
        
        if ($customerId) {
            $query->where('customer_id', '!=', $customerId);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Nomor meter sudah digunakan' : 'Nomor meter tersedia'
        ]);
    }
    
    // Get customer statistics for dashboard
    public function getStats()
    {
        $stats = [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::whereHas('user', function ($query) {
                $query->where('is_active', true);
            })->count(),
            'total_meters' => Meter::count(),
            'inactive_meters' => Meter::where('is_active', false)->count(),
        ];
        
        return response()->json([
            'data' => $stats
        ]);
    }

    // NEW METHODS untuk Multiple Meter Management
    
    /**
     * Get all meters for a specific customer
     * REQ-NEW: Support multiple meters per customer
     */
    public function getCustomerMeters(Customer $customer)
    {
        $meters = $customer->meters()
            ->with(['customerGroup', 'bills' => function ($query) {
                $query->where('status', 'unpaid')->orderBy('due_date', 'asc');
            }])
            ->get()
            ->map(function ($meter) {
                return [
                    'id' => $meter->id,
                    'meter_number' => $meter->meter_number,
                    'meter_type' => $meter->meter_type,
                    'meter_size' => $meter->meter_size,
                    'customer_group' => [
                        'code' => $meter->customer_group_code,
                        'name' => $meter->customer_group_name,
                    ],
                    'installation_date' => $meter->installation_date,
                    'is_active' => $meter->is_active,
                    'last_reading' => $meter->last_reading,
                    'outstanding_bills' => $meter->bills->count(),
                    'outstanding_amount' => $meter->total_outstanding,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $meters,
            'message' => 'Customer meters retrieved successfully'
        ]);
    }
    
    /**
     * Add new meter to existing customer
     * REQ-NEW: Customer dapat memiliki multiple meters
     */
    public function addMeter(Request $request, Customer $customer)
    {
        // Check if customer can register new meter
        if (!$customer->canRegisterNewMeter()) {
            return response()->json([
                'success' => false,
                'message' => 'Customer tidak dapat mendaftarkan meter baru. Pastikan tidak ada tagihan tertunggak dan belum mencapai batas maksimal meter.'
            ], 400);
        }
        
        $validator = Validator::make($request->all(), [
            'meter_number' => 'required|string|unique:meters,meter_number',
            'meter_type' => 'required|string|in:analog,digital',
            'customer_group_code' => 'required|string|exists:customer_groups,code',
            'meter_size' => [
                'required',
                'string',
                Rule::in(['1/2"', '3/4"', '1"', '1 1/2"', '2"', '3"', '4"'])
            ],
            'installation_date' => 'required|date|before_or_equal:today'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $meter = Meter::create([
                'customer_id' => $customer->id,
                'meter_number' => $request->meter_number,
                'meter_type' => $request->meter_type,
                'customer_group_code' => $request->customer_group_code,
                'meter_size' => $request->meter_size,
                'installation_date' => $request->installation_date,
                'is_active' => true,
                // Tariff rates auto-populated by Model boot method
            ]);
            
            $meter->load('customerGroup');
            
            return response()->json([
                'success' => true,
                'data' => $meter,
                'message' => 'Meter added successfully'
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add meter: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get meter details with outstanding bills
     * REQ-NEW: Data tagihan per meter, bukan per customer
     */
    public function getMeterDetails(Meter $meter)
    {
        $meter->load([
            'customer.user',
            'customerGroup',
            'bills' => function ($query) {
                $query->with('billingPeriod')->orderBy('created_at', 'desc');
            }
        ]);
        
        $details = [
            'meter_info' => [
                'id' => $meter->id,
                'meter_number' => $meter->meter_number,
                'meter_type' => $meter->meter_type,
                'meter_size' => $meter->meter_size,
                'installation_date' => $meter->installation_date,
                'is_active' => $meter->is_active,
            ],
            'customer_info' => [
                'id' => $meter->customer->id,
                'customer_number' => $meter->customer->customer_number,
                'name' => $meter->customer->user->name,
                'phone' => $meter->customer->user->phone,
            ],
            'tariff_info' => [
                'customer_group' => [
                    'code' => $meter->customer_group_code,
                    'name' => $meter->customer_group_name,
                ],
                'admin_fee' => $meter->admin_fee,
                'rates' => [
                    'block1' => $meter->block1_rate,
                    'block2' => $meter->block2_rate,
                    'block3' => $meter->block3_rate,
                    'block4' => $meter->block4_rate,
                ],
            ],
            'billing_info' => [
                'last_reading' => $meter->last_reading,
                'outstanding_bills' => $meter->bills->where('status', 'unpaid')->count(),
                'total_outstanding' => $meter->total_outstanding,
                'recent_bills' => $meter->bills->take(5)->map(function ($bill) {
                    return [
                        'id' => $bill->id,
                        'bill_number' => $bill->bill_number,
                        'period' => $bill->billingPeriod->period_name,
                        'usage_m3' => $bill->usage_m3,
                        'total_amount' => $bill->total_amount,
                        'status' => $bill->status,
                        'due_date' => $bill->due_date,
                    ];
                }),
            ],
        ];
        
        return response()->json([
            'success' => true,
            'data' => $details,
            'message' => 'Meter details retrieved successfully'
        ]);
    }
}
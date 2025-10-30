<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private $adminUser;
    private $adminRole;
    private $customerRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create admin user for authentication
        $this->adminUser = User::create([
            'name' => 'Test Admin',
            'phone' => '081234567889',
            'email' => 'testadmin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $this->adminRole->id,
            'is_active' => true
        ]);
    }

    /**
     * Test that getUsers endpoint excludes customer role users
     */
    public function test_get_users_excludes_customer_role()
    {
        // Create additional users for testing
        $adminUser = User::create([
            'name' => 'Admin User',
            'phone' => '081234567890',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $this->adminRole->id,
            'is_active' => true
        ]);

        $customerUser = User::create([
            'name' => 'Customer User',
            'phone' => '081234567891',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role_id' => $this->customerRole->id,
            'is_active' => true
        ]);

        // Test getUsers endpoint with authentication
        $response = $this->actingAs($this->adminUser, 'sanctum')->getJson('/api/admin/users');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'name',
                                'phone',
                                'email',
                                'role'
                            ]
                        ]
                    ]
                ]);

        // Verify that customer user is excluded, but admin users are included
        $users = $response->json('data.data');
        $userNames = collect($users)->pluck('name')->toArray();
        
        // Should include the test admin user and the additional admin user, but not customer user
        $this->assertContains('Test Admin', $userNames);
        $this->assertContains('Admin User', $userNames);
        $this->assertNotContains('Customer User', $userNames);
        
        // All returned users should have admin/keuangan/manajemen roles
        foreach ($users as $user) {
            $this->assertContains($user['role']['name'], ['admin', 'keuangan', 'manajemen']);
        }
    }

    /**
     * Test that createUser endpoint rejects customer role
     */
    public function test_create_user_rejects_customer_role()
    {
        // Try to create user with customer role
        $response = $this->actingAs($this->adminUser, 'sanctum')->postJson('/api/admin/users', [
            'name' => 'Test Customer User',
            'phone' => '081234567892',
            'email' => 'testcustomer@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role_id' => $this->customerRole->id,
            'is_active' => true
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid role. Customer role is not allowed for user management.',
                    'errors' => [
                        'role_id' => ['Customer role cannot be assigned to users.']
                    ]
                ]);
    }

    /**
     * Test that createUser endpoint accepts admin role
     */
    public function test_create_user_accepts_admin_role()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')->postJson('/api/admin/users', [
            'name' => 'Test Admin User',
            'phone' => '081234567893',
            'email' => 'testadmin2@example.com', // Use different email
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role_id' => $this->adminRole->id,
            'is_active' => true
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'User created successfully',
                    'data' => [
                        'name' => 'Test Admin User',
                        'phone' => '081234567893',
                        'email' => 'testadmin2@example.com',
                        'role' => [
                            'name' => 'admin'
                        ]
                    ]
                ]);
    }

    /**
     * Test that getRoles endpoint excludes customer role
     */
    public function test_get_roles_excludes_customer_role()
    {
        Role::firstOrCreate(['name' => 'keuangan']);
        Role::firstOrCreate(['name' => 'manajemen']);

        $response = $this->actingAs($this->adminUser, 'sanctum')->getJson('/api/admin/roles');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'name'
                        ]
                    ]
                ]);

        $roles = $response->json('data');
        $this->assertCount(3, $roles);

        $roleNames = collect($roles)->pluck('name')->toArray();
        $this->assertContains('admin', $roleNames);
        $this->assertContains('keuangan', $roleNames);
        $this->assertContains('manajemen', $roleNames);
        $this->assertNotContains('customer', $roleNames);
    }
}
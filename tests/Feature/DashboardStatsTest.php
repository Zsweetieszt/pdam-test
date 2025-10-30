<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    private $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create admin user for authentication
        $this->adminUser = User::create([
            'name' => 'Test Admin',
            'phone' => '081234567889',
            'email' => 'testadmin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_active' => true
        ]);
    }

    /**
     * Test that dashboard stats endpoint returns correct data structure
     */
    public function test_dashboard_stats_returns_correct_structure()
    {
        // Test dashboard stats endpoint
        $response = $this->actingAs($this->adminUser, 'sanctum')->getJson('/api/admin/dashboard-stats');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'users' => [
                            'total',
                            'active',
                            'admin',
                            'keuangan',
                            'manajemen'
                        ],
                        'customers' => [
                            'total',
                            'active',
                            'new_this_month'
                        ],
                        'bills' => [
                            'total_this_month',
                            'pending',
                            'paid',
                            'overdue'
                        ],
                        'payments' => [
                            'total_this_month',
                            'verified',
                            'pending_verification'
                        ],
                        'system' => [
                            'total_audit_logs',
                            'recent_activities'
                        ]
                    ]
                ]);

        $data = $response->json('data');

        // Verify that users total only counts admin/keuangan/manajemen roles
        $this->assertEquals(1, $data['users']['total']); // Only the test admin user
        $this->assertEquals(0, $data['customers']['total']); // No customers created yet
        $this->assertEquals(0, $data['bills']['total_this_month']); // No bills created yet
        $this->assertEquals(0, $data['payments']['total_this_month']); // No payments created yet
    }

    /**
     * Test that dashboard stats excludes customer role users from user count
     */
    public function test_dashboard_stats_excludes_customer_role_users()
    {
        // Create customer role and user
        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $customerUser = User::create([
            'name' => 'Customer User',
            'phone' => '081234567890',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'role_id' => $customerRole->id,
            'is_active' => true
        ]);

        // Test dashboard stats endpoint
        $response = $this->actingAs($this->adminUser, 'sanctum')->getJson('/api/admin/dashboard-stats');

        $response->assertStatus(200);

        $data = $response->json('data');

        // Verify that customer role user is not counted in users.total
        $this->assertEquals(1, $data['users']['total']); // Still only 1 (the admin user)
        $this->assertNotEquals(2, $data['users']['total']); // Should not include customer user
    }
}
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardViewTest extends TestCase
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
     * Test that dashboard view receives stats data
     */
    public function test_dashboard_view_receives_stats_data()
    {
        // Act as admin user
        $response = $this->actingAs($this->adminUser)->get('/admin/dashboard');

        // Assert response is successful
        $response->assertStatus(200);

        // Assert that the view contains stats data
        $response->assertViewHas('stats', function ($stats) {
            // Check that stats array has the expected structure
            return isset($stats['users']['total']) &&
                   isset($stats['customers']['total']) &&
                   isset($stats['bills']['total_this_month']) &&
                   isset($stats['payments']['total_this_month']);
        });

        // Assert that the view has the correct title
        $response->assertViewHas('title', 'Dashboard Admin - PDAM Billing System');
    }

    /**
     * Test that dashboard view shows correct initial values
     */
    public function test_dashboard_view_shows_correct_initial_values()
    {
        // Act as admin user
        $response = $this->actingAs($this->adminUser)->get('/admin/dashboard');

        // Get the view data
        $viewData = $response->getOriginalContent()->getData();

        // Assert that stats are present and are numbers
        $this->assertIsInt($viewData['stats']['users']['total']);
        $this->assertIsInt($viewData['stats']['customers']['total']);
        $this->assertIsInt($viewData['stats']['bills']['total_this_month']);
        $this->assertIsInt($viewData['stats']['payments']['total_this_month']);

        // Assert that all values are >= 0
        $this->assertGreaterThanOrEqual(0, $viewData['stats']['users']['total']);
        $this->assertGreaterThanOrEqual(0, $viewData['stats']['customers']['total']);
        $this->assertGreaterThanOrEqual(0, $viewData['stats']['bills']['total_this_month']);
        $this->assertGreaterThanOrEqual(0, $viewData['stats']['payments']['total_this_month']);
    }
}
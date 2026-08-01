<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('administrator');

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $response->assertSee('Administrator');
    }

    public function test_client_is_redirected_to_client_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'Client User',
            'email' => 'client@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('client');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/client');
    }
}

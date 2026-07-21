<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Log in');
        $response->assertSee('Sign in');
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
        $response->assertSee('Create an account');
    }

    public function test_dashboard_can_be_rendered_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Dashboard');
        $response->assertSee($user->name);
    }

    public function test_registration_can_create_user_and_redirect_to_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}

<?php

namespace Tests\Feature;

use App\Models\BusinessPlan;
use Tests\TestCase;

class BusinessRegistrationTest extends TestCase
{
    public function test_business_registration_creates_an_owner_and_free_business(): void
    {
        $response = $this->post(route('business.register.store'), [
            'name' => 'Jane Doe',
            'business_name' => 'Jane Rentals',
            'city' => 'Manila',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('business.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('businesses', ['name' => 'Jane Rentals', 'slug' => 'jane-rentals', 'city' => 'Manila', 'business_plan_id' => BusinessPlan::query()->where('slug', 'free')->sole()->id]);
        $this->assertDatabaseHas('business_users', ['business_role' => 'owner']);
        $this->assertTrue(auth()->user()->hasRole('business_owner'));
    }

    public function test_business_registration_page_is_available(): void
    {
        $this->get(route('business.register'))->assertOk()->assertSee('Register your rental business')->assertSee('Create a renter account');
    }
}

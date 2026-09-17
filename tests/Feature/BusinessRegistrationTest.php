<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessPlan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
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

        $response->assertRedirect(route('business.profile.create'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('businesses', ['name' => 'Jane Rentals', 'slug' => 'jane-rentals', 'city' => 'Manila', 'business_plan_id' => BusinessPlan::query()->where('slug', 'free')->sole()->id]);
        $this->assertDatabaseHas('business_users', ['business_role' => 'owner']);
        $this->assertTrue(auth()->user()->hasRole('business_owner'));
    }

    public function test_business_dashboard_requires_a_completed_business_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('business_owner');

        $business = Business::factory()->create([
            'name' => 'Sample Fleet',
            'slug' => 'sample-fleet',
            'city' => 'Naga',
        ]);

        $user->businesses()->attach($business, ['business_role' => 'owner']);

        $this->actingAs($user)
            ->get(route('business.dashboard'))
            ->assertRedirect(route('business.profile.create'));
    }

    public function test_business_profile_can_be_completed_with_permit_images(): void
    {
        $user = User::factory()->create();
        $user->assignRole('business_owner');

        $business = Business::factory()->create([
            'name' => 'Profile Test Rentals',
            'slug' => 'profile-test-rentals',
            'city' => 'Legazpi',
            'profile_completed_at' => null,
        ]);

        $user->businesses()->attach($business, ['business_role' => 'owner']);

        $response = $this->actingAs($user)->post(route('business.profile.store'), [
            'business_type' => 'single_proprietorship',
            'business_category' => 'car_rental',
            'business_address' => 'Rizal Street, Legazpi City',
            'contact_number' => '+639171234567',
            'registration_number' => 'REG-12345',
            'tin' => '123-456-789-000',
            'permit_issuer' => 'City Mayor\'s Office',
            'permit_images' => [
                UploadedFile::fake()->image('permit-1.png', 1200, 900),
                UploadedFile::fake()->image('permit-2.png', 1200, 900),
            ],
        ]);

        $response->assertRedirect(route('business.dashboard'));
        $this->assertNotNull($business->fresh()->profile_completed_at);
        $this->assertCount(2, $business->fresh()->permit_images ?? []);
    }

    public function test_completed_business_profile_can_be_managed(): void
    {
        $user = User::factory()->create();
        $user->assignRole('business_owner');

        $business = Business::factory()->create([
            'name' => 'Original Rentals',
            'slug' => 'original-rentals',
            'city' => 'Naga',
            'business_type' => 'single_proprietorship',
            'business_category' => 'car_rental',
            'business_address' => 'Old Address',
            'contact_number' => '09171234567',
            'permit_images' => [],
            'profile_completed_at' => now(),
        ]);

        $user->businesses()->attach($business, ['business_role' => 'owner']);

        $this->actingAs($user)
            ->get(route('business.profile.edit'))
            ->assertOk()
            ->assertSee('Original Rentals');

        $this->actingAs($user)
            ->put(route('business.profile.update'), [
                'name' => 'Updated Rentals',
                'city' => 'Legazpi',
                'business_type' => 'corporation',
                'business_category' => 'van_rental',
                'business_address' => 'New Address',
                'contact_number' => '09179876543',
                'registration_number' => 'REG-123',
                'tin' => '123-456-789',
                'permit_issuer' => 'City Hall',
            ])
            ->assertRedirect(route('business.profile.edit'));

        $this->assertDatabaseHas('businesses', [
            'id' => $business->id,
            'name' => 'Updated Rentals',
            'city' => 'Legazpi',
            'business_type' => 'corporation',
        ]);
    }

    public function test_business_registration_page_is_available(): void
    {
        $this->get(route('business.register'))->assertOk()->assertSee('Register your rental business')->assertSee('Create a renter account');
    }
}

<?php

namespace Tests\Feature;

use App\Models\PlatformLandingPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PlatformLandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_landing_page_uses_saved_content(): void
    {
        PlatformLandingPage::create([
            'content' => $this->content(),
            'featured_business_slugs' => [],
            'featured_car_ids' => [],
        ]);

        $this->get(route('home'))->assertOk()->assertSee('A dynamic hero title');
    }

    public function test_administrator_can_update_the_landing_page(): void
    {
        Role::create(['name' => 'administrator']);
        $administrator = User::factory()->create();
        $administrator->assignRole('administrator');
        $landingPage = PlatformLandingPage::create(['content' => $this->content()]);

        $this->actingAs($administrator)->put(route('admin.landing.update'), [
            'hero_eyebrow' => 'Plan your trip', 'hero_title' => 'Updated title', 'hero_highlight' => 'Updated highlight',
            'hero_subtitle' => 'Updated subtitle', 'company_heading' => 'Companies', 'destination_heading' => 'Destinations',
            'vehicle_heading' => 'Vehicles', 'why_heading' => 'Why us', 'testimonial_quote' => 'Excellent service',
            'testimonial_name' => 'A renter', 'testimonial_role' => 'Verified renter', 'pricing_heading' => 'Pricing',
            'faq_heading' => 'Questions', 'footer_text' => 'Footer text',
        ])->assertRedirect();

        $this->assertSame('Updated title', $landingPage->fresh()->content['hero_title']);
    }

    /** @return array<string, mixed> */
    private function content(): array
    {
        return [
            'hero_eyebrow' => 'Travel', 'hero_title' => 'A dynamic hero title', 'hero_highlight' => 'Explore', 'hero_subtitle' => 'Find a ride.',
            'company_heading' => 'Companies', 'destination_heading' => 'Destinations', 'vehicle_heading' => 'Vehicles', 'why_heading' => 'Why Nogaru',
            'testimonial' => ['quote' => 'Great service', 'name' => 'Renter', 'role' => 'Verified'], 'pricing_heading' => 'Pricing',
            'faq_heading' => 'FAQ', 'footer_text' => 'Footer',
        ];
    }
}

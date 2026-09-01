<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Car;
use App\Models\PartnerFleetRequest;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PartnerFleetRequestTest extends TestCase
{
    public function test_business_can_request_and_partner_can_approve_a_fleet_unit(): void
    {
        [$requester, $requestingBusiness] = $this->businessUser();
        [$partner, $partnerBusiness] = $this->businessUser();
        $car = Car::factory()->for($partnerBusiness)->create(['status' => 'available']);

        $this->actingAs($requester)->post(route('business.partner-fleets.store'), [
            'partner_business_id' => $partnerBusiness->id,
            'car_id' => $car->id,
        ])->assertRedirect(route('business.partner-fleets.index'));

        $partnerFleetRequest = PartnerFleetRequest::query()->sole();

        $this->actingAs($partner)->put(route('business.partner-fleets.approve', $partnerFleetRequest))
            ->assertRedirect(route('business.partner-fleets.index'));

        $this->assertDatabaseHas('partner_fleet_requests', ['id' => $partnerFleetRequest->id, 'status' => 'approved']);
        $this->actingAs($requester)->get(route('business.partner-fleets.index'))
            ->assertOk()
            ->assertSee($partnerBusiness->name)
            ->assertSee($car->car_model);
    }

    /**
     * @return array{User, Business}
     */
    private function businessUser(): array
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();

        Role::findOrCreate('business_owner');
        $user->assignRole('business_owner');
        $user->businesses()->attach($business, ['business_role' => 'owner']);

        return [$user, $business];
    }
}

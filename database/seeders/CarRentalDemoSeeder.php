<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Business;
use App\Models\BusinessPlan;
use App\Models\BusinessUser;
use App\Models\Car;
use App\Models\ClientIdentityDocument;
use App\Models\ClientProfile;
use App\Models\Driver;
use App\Models\PlatformLandingPage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CarRentalDemoSeeder extends Seeder
{
    private function seedAdministrator(): void
    {
        $administrator = User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'System Administrator',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $administrator->syncRoles(['administrator']);
    }

    public function run(): void
    {
        Role::findOrCreate('business_owner');
        Role::findOrCreate('client');
        Role::findOrCreate('administrator');
        $this->seedPlatformLandingPage();
        $plans = $this->seedPlans();

        foreach ($this->businesses() as $businessData) {
            $business = Business::updateOrCreate(
                ['slug' => $businessData['slug']],
                Arr::except($businessData, ['plan_slug', 'owner_name', 'owner_email']) + [
                    'business_plan_id' => $plans[$businessData['plan_slug']]->id,
                ],
            );

            $owner = User::firstOrCreate(
                ['email' => $businessData['owner_email']],
                [
                    'name' => $businessData['owner_name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ],
            );
            $owner->syncRoles(['business_owner']);

            BusinessUser::firstOrCreate([
                'business_id' => $business->id,
                'user_id' => $owner->id,
                'business_role' => 'owner',
            ]);

            $this->seedDriversForBusiness($business);

            foreach ($this->fleet() as $index => $carData) {
                $car = Car::updateOrCreate(
                    ['business_id' => $business->id, 'car_model' => $carData['car_model']],
                    Arr::except($carData, ['image_path', 'rates']) + [
                        'owner_id' => $owner->id,
                        'plate_number' => sprintf('%s-%03d', strtoupper(substr($business->slug, 0, 3)), $index + 1),
                    ],
                );

                $car->images()->updateOrCreate(['image_path' => $carData['image_path']], []);

                foreach ($carData['rates'] as $name => $value) {
                    $car->rates()->updateOrCreate(['name' => $name], ['value' => $value]);
                }
            }
        }

        $this->seedClient();
        $this->seedDriverBookings();
        $this->seedAdministrator();
    }

    private function seedPlatformLandingPage(): void
    {
        PlatformLandingPage::query()->updateOrCreate(['id' => 1], [
            'content' => [
                'hero_eyebrow' => 'Your trip starts here',
                'hero_title' => 'The road is yours.',
                'hero_highlight' => 'Make it memorable.',
                'hero_subtitle' => 'Find a dependable ride from local rental companies, wherever your next adventure takes you.',
                'company_heading' => 'Great rentals, from people nearby.',
                'destination_heading' => 'Where will the road take you?',
                'vehicle_heading' => 'Popular vehicles this week.',
                'why_heading' => 'Car rental without the runaround.',
                'testimonial' => ['quote' => 'Booking a car for our family trip took minutes. The choices were clear and the rental company was fantastic.', 'name' => 'Maria Santos', 'role' => 'Verified renter · Cebu'],
                'pricing_heading' => 'A better way to grow your fleet.',
                'faq_heading' => 'Frequently asked questions.',
                'footer_text' => 'Making local car rentals easier, one trip at a time.',
            ],
            'featured_business_slugs' => ['freeway-rentals', 'coastline-car-hire', 'summit-mobility', 'premier-fleet-services'],
            'featured_car_ids' => [],
        ]);
    }

    /** @return array<string, BusinessPlan> */
    private function seedPlans(): array
    {
        $plans = [
            ['name' => 'Free', 'slug' => 'free', 'price' => 0, 'vehicle_limit' => 3, 'permissions' => []],
            ['name' => 'Basic', 'slug' => 'basic', 'price' => 499, 'vehicle_limit' => 10, 'permissions' => ['payment_tracking', 'landing_page']],
            ['name' => 'Pro', 'slug' => 'pro', 'price' => 999, 'vehicle_limit' => 25, 'permissions' => ['payment_tracking', 'landing_page', 'reports', 'vehicle_inspection', 'rental_agreement']],
            ['name' => 'Business', 'slug' => 'business', 'price' => 1999, 'vehicle_limit' => null, 'permissions' => ['payment_tracking', 'landing_page', 'reports', 'vehicle_inspection', 'rental_agreement', 'staff_accounts', 'partner_management']],
        ];

        $seededPlans = [];
        foreach ($plans as $planData) {
            $plan = BusinessPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                Arr::except($planData, ['permissions']),
            );
            $permissionIds = collect($planData['permissions'])->map(function (string $permissionName): int {
                return Permission::query()->firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ])->id;
            });
            $plan->permissions()->sync($permissionIds);
            $seededPlans[$plan->slug] = $plan;
        }

        return $seededPlans;
    }

    /** @return array<int, array<string, mixed>> */
    private function businesses(): array
    {
        $businesses = [
            ['free', 'Freeway Rentals', 'freeway-rentals', 'Ava Cruz', 'ava@freeway-rentals.test'],
            ['free', 'Budget Drive', 'budget-drive', 'Noah Reyes', 'noah@budget-drive.test'],
            ['free', 'Coastline Car Hire', 'coastline-car-hire', 'Mia Santos', 'mia@coastline-car-hire.test'],
            ['free', 'City Wheels', 'city-wheels', 'Ethan Garcia', 'ethan@city-wheels.test'],
            ['free', 'Summit Mobility', 'summit-mobility', 'Liam Torres', 'liam@summit-mobility.test'],
            ['free', 'Island Auto Rental', 'island-auto-rental', 'Sofia Ramos', 'sofia@island-auto-rental.test'],
            ['free', 'Premier Fleet Services', 'premier-fleet-services', 'Lucas Mendoza', 'lucas@premier-fleet.test'],
            ['free', 'Voyager Executive Rentals', 'voyager-executive-rentals', 'Isla Navarro', 'isla@voyager-executive.test'],
        ];

        return array_map(function (array $business): array {
            [$planSlug, $name, $slug, $ownerName, $ownerEmail] = $business;

            return [
                'name' => $name,
                'slug' => $slug,
                'city' => 'Legazpi City',
                'description' => "{$name} provides dependable, well-maintained vehicles for Bicol trips.",
                'plan_slug' => $planSlug,
                'hero_title' => "Drive farther with {$name}",
                'hero_subtitle' => 'Comfortable vehicles, clear rates, and friendly local service.',
                'about_title' => 'Your local mobility partner',
                'about_content' => 'Our fleet is prepared for city errands, family holidays, and business travel.',
                'about_features' => [
                    [
                        'icon' => 'shield-check',
                        'title' => 'Fully Insured Vehicles',
                        'description' => 'Travel with confidence knowing every vehicle is fully insured.',
                    ],
                    [
                        'icon' => 'map-pin',
                        'title' => 'Flexible Pickup Options',
                        'description' => 'Choose a pickup location and time that suits your schedule.',
                    ],
                    [
                        'icon' => 'headset',
                        'title' => '24/7 Roadside Assistance',
                        'description' => 'Our support team is ready to help whenever you need us.',
                    ],
                ],
                'contact_email' => $ownerEmail,
                'contact_phone' => '+639171234567',
                'contact_address' => 'Rizal Street, Legazpi City, Albay',
                'garage_address' => 'Maharlika Highway, Santa Cruz, San Isidro, Baao, Camarines Sur',
                'garage_latitude' => 13.4543,
                'garage_longitude' => 123.3654,
                'diesel_premium_price_per_liter' => 90,
                'diesel_regular_price_per_liter' => 80,
                'gasoline_premium_price_per_liter' => 95,
                'gasoline_regular_price_per_liter' => 85,
                'discount_7_to_14_days_percent' => 10,
                'discount_15_to_24_days_percent' => 20,
                'discount_25_to_31_days_percent' => 30,
                'owner_name' => $ownerName,
                'owner_email' => $ownerEmail,
            ];
        }, $businesses);
    }

    /** @return array<int, array<string, mixed>> */
    private function fleet(): array
    {
        $fleet = [
            ['vehicle_type' => 'Hatchback', 'car_model' => 'Toyota Wigo', 'variant' => 'G CVT', 'transmission' => 'Automatic', 'seats' => 5, 'rental_type' => 'Daily', 'status' => 'available', 'year_model' => 2024, 'ownership' => 'owned', 'registration_expires_at' => now()->addYear()->toDateString(), 'insurance_expires_at' => now()->addMonths(10)->toDateString(), 'image_path' => 'https://images.unsplash.com/photo-1542282088-72c9c27ed0cd?auto=format&fit=crop&w=1200&q=80', 'rates' => ['12hrs' => 1800, '24hrs' => 2500, 'Extension per hour' => 250, 'Pick-up & Drop-off' => 350, 'Car Wash Fee' => 200]],
            ['vehicle_type' => 'Sedan', 'car_model' => 'Toyota Vios', 'variant' => 'XLE CVT', 'transmission' => 'Automatic', 'seats' => 5, 'rental_type' => 'Daily', 'status' => 'available', 'year_model' => 2023, 'ownership' => 'owned', 'registration_expires_at' => now()->addYear()->toDateString(), 'insurance_expires_at' => now()->addMonths(11)->toDateString(), 'image_path' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=80', 'rates' => ['12hrs' => 2200, '24hrs' => 3200, 'Extension per hour' => 300, 'Pick-up & Drop-off' => 400, 'Car Wash Fee' => 250]],
            ['vehicle_type' => 'SUV', 'car_model' => 'Toyota Fortuner', 'variant' => '2.4 G 4x2', 'transmission' => 'Automatic', 'seats' => 7, 'rental_type' => 'Daily', 'status' => 'available', 'year_model' => 2024, 'ownership' => 'owned', 'registration_expires_at' => now()->addYear()->toDateString(), 'insurance_expires_at' => now()->addMonths(9)->toDateString(), 'image_path' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1200&q=80', 'rates' => ['12hrs' => 3500, '24hrs' => 4800, 'Extension per hour' => 450, 'Pick-up & Drop-off' => 500, 'Car Wash Fee' => 350]],
            ['vehicle_type' => 'MPV', 'car_model' => 'Mitsubishi Xpander', 'variant' => 'Cross', 'transmission' => 'Automatic', 'seats' => 7, 'rental_type' => 'Daily', 'status' => 'available', 'year_model' => 2023, 'ownership' => 'consigned', 'registration_expires_at' => now()->addMonths(8)->toDateString(), 'insurance_expires_at' => now()->addMonths(7)->toDateString(), 'image_path' => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=1200&q=80', 'rates' => ['12hrs' => 3000, '24hrs' => 4000, 'Extension per hour' => 400, 'Car Wash Fee' => 300, 'Daily Discount' => 125, 'Out-of-town Fee per km' => 24]],
            ['vehicle_type' => 'Van', 'car_model' => 'Toyota Hiace', 'variant' => 'Commuter Deluxe', 'transmission' => 'Manual', 'seats' => 15, 'rental_type' => 'Daily', 'status' => 'available', 'year_model' => 2022, 'ownership' => 'owned', 'registration_expires_at' => now()->addMonths(6)->toDateString(), 'insurance_expires_at' => now()->addMonths(8)->toDateString(), 'image_path' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1200&q=80', 'rates' => ['12hrs' => 4500, '24hrs' => 6000, 'Extension per hour' => 600, 'Car Wash Fee' => 500, 'Daily Discount' => 200, 'Out-of-town Fee per km' => 35]],
        ];

        $fuelDetails = [
            'Toyota Wigo' => ['Gasoline Regular', 42],
            'Toyota Vios' => ['Gasoline Premium', 42],
            'Toyota Fortuner' => ['Diesel Premium', 80],
            'Mitsubishi Xpander' => ['Gasoline Regular', 45],
            'Toyota Hiace' => ['Diesel Regular', 70],
        ];

        foreach ($fleet as &$car) {
            [$car['fuel_type'], $car['fuel_tank_capacity_liters']] = $fuelDetails[$car['car_model']];
            $car['fuel_display_bar'] = 8;
            $car['fuel_consumption_km_per_liter'] = 10;
            $car['rates'] += [
                'Pick-up & Drop-off' => 500,
            ];
            unset($car['rates']['Daily Discount'], $car['rates']['Out-of-town Fee per km']);
        }
        unset($car);

        return $fleet;
    }

    private function seedDriversForBusiness(Business $business): void
    {
        $drivers = [
            ['license_number' => 'N01-23-456789', 'full_name' => 'Marco Villanueva', 'phone' => '+639171112233', 'email' => 'marco.villanueva@example.test', 'daily_rate' => 1500],
            ['license_number' => 'N02-34-567890', 'full_name' => 'Paolo Ramirez', 'phone' => '+639179876543', 'email' => 'paolo.ramirez@example.test', 'daily_rate' => 1650],
        ];

        foreach ($drivers as $index => $driverData) {
            $driver = Driver::updateOrCreate(
                ['license_number' => $driverData['license_number']],
                Arr::except($driverData, ['daily_rate']),
            );
            $business->drivers()->syncWithoutDetaching([
                $driver->license_number => ['daily_rate' => $driverData['daily_rate'], 'is_available' => true, 'is_default' => $index === 0],
            ]);
        }
    }

    private function seedClient(): void
    {
        $client = User::firstOrCreate(['email' => 'client@example.com'], [
            'name' => 'Jordan Dela Cruz', 'email_verified_at' => now(), 'password' => Hash::make('password'),
        ]);
        $client->syncRoles(['client']);

        $profile = ClientProfile::updateOrCreate(['user_id' => $client->id], [
            'full_name' => 'Jordan Dela Cruz', 'permanent_address' => '123 Rizal Street, Legazpi City, Albay',
            'mobile_numbers' => [['telecom' => 'Globe', 'number' => '+639171234567']], 'email_address' => $client->email,
            'facebook_url' => 'https://www.facebook.com/jordan.delacruz', 'whatsapp_number' => '+639171234567', 'viber_number' => '+639171234567',
        ]);

        ClientIdentityDocument::updateOrCreate(['client_profile_id' => $profile->id], [
            'valid_id_1_path' => 'demo/client/valid-id-1.jpg', 'valid_id_2_path' => 'demo/client/valid-id-2.jpg',
            'selfie_with_ids_path' => 'demo/client/selfie-with-ids.jpg', 'ltms_welcome_path' => 'demo/client/ltms-welcome.jpg',
            'ltms_client_id_path' => 'demo/client/ltms-client-id.jpg', 'ltms_license_front_path' => 'demo/client/license-front.jpg',
            'ltms_license_back_path' => 'demo/client/license-back.jpg', 'proof_of_billing_path' => 'demo/client/proof-of-billing.jpg', 'is_complete' => true,
        ]);
    }

    private function seedDriverBookings(): void
    {
        $client = User::query()->where('email', 'client@example.com')->sole();

        Business::query()->with(['cars', 'drivers'])->each(function (Business $business) use ($client): void {
            $car = $business->cars->first();
            $driver = $business->drivers->first(fn (Driver $driver): bool => (bool) $driver->pivot->is_default);

            if (! $car instanceof Car || ! $driver instanceof Driver) {
                return;
            }

            foreach ([
                ['pickup_date' => now()->addDays(7)->toDateString(), 'status' => 'confirmed', 'destination_itinerary' => 'Upcoming client travel'],
                ['pickup_date' => now()->subDays(14)->toDateString(), 'status' => 'completed', 'destination_itinerary' => 'Completed client travel'],
            ] as $bookingData) {
                Booking::query()->updateOrCreate(
                    [
                        'business_id' => $business->id,
                        'driver_license_number' => $driver->license_number,
                        'destination_itinerary' => $bookingData['destination_itinerary'],
                    ],
                    [
                        'user_id' => $client->id,
                        'car_id' => $car->id,
                        'rental_type' => 'with_driver',
                        'pickup_date' => $bookingData['pickup_date'],
                        'pickup_time' => '09:00',
                        'pickup_location' => 'Legazpi City, Albay',
                        'preferred_vehicle' => $car->car_model ?: $car->vehicle_type,
                        'passengers_count' => 2,
                        'return_date' => $bookingData['pickup_date'],
                        'return_time' => '17:00',
                        'return_location' => 'Daraga, Albay',
                        'handover_option' => 'with_driver',
                        'initial_rate' => 2500,
                        'final_rate' => 2500,
                        'status' => $bookingData['status'],
                    ],
                );
            }
        });
    }
}

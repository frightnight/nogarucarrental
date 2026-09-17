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
use App\Models\DriverRate;
use App\Models\PlatformLandingPage;
use App\Models\Quotation;
use App\Models\QuotationFootnote;
use App\Models\SavedLocation;
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
        Role::findOrCreate('driver');
        Role::findOrCreate('sales_agent');
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

            $this->seedSavedLocations($business);
        }

        $this->seedClient();
        $this->seedDriverBookings();
        $this->seedQuotations();
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
            ['free', 'Nogaru Car Rental', 'nogaru-car-rental', 'Jasper Garcera', 'nogaru.car.rental@gmail.com'],
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
                'contact_phone' => '09765297961',
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
            ['license_number' => 'N01-23-456789', 'full_name' => 'Marco Villanueva', 'phone' => '+639171112233', 'email' => 'marco.villanueva@example.test', 'daily_rate' => 1000],
            ['license_number' => 'N02-34-567890', 'full_name' => 'Paolo Ramirez', 'phone' => '+639179876543', 'email' => 'paolo.ramirez@example.test', 'daily_rate' => 1000],
        ];

        foreach ($drivers as $index => $driverData) {
            $driver = Driver::updateOrCreate(
                ['license_number' => $driverData['license_number']],
                Arr::except($driverData, ['daily_rate']) + [
                    'driver_code' => 'DRV-'.str_replace('-', '', $driverData['license_number']),
                    'approval_status' => 'approved',
                    'employment_status' => 'active',
                ],
            );
            $business->drivers()->syncWithoutDetaching([
                $driver->license_number => ['daily_rate' => $driverData['daily_rate'], 'is_available' => true, 'is_default' => $index === 0],
            ]);

            foreach ([
                ['trip_type' => 'airport_transfer', 'rate_type' => 'per_trip', 'amount' => 600, 'overtime_rate' => 200],
                ['trip_type' => 'city_tour', 'rate_type' => 'per_day', 'amount' => 1000, 'overtime_rate' => 150],
                ['trip_type' => 'full_day', 'rate_type' => 'per_day', 'amount' => 1500, 'overtime_rate' => 200],
                ['trip_type' => 'out_of_town', 'rate_type' => 'per_day', 'amount' => 1800, 'overtime_rate' => 200],
                ['trip_type' => 'multi_day', 'rate_type' => 'per_day', 'amount' => 1500, 'overtime_rate' => 200],
                ['trip_type' => 'wedding_event', 'rate_type' => 'per_event', 'amount' => 2000, 'overtime_rate' => 250],
                ['trip_type' => 'corporate', 'rate_type' => 'per_day', 'amount' => 2500, 'overtime_rate' => 300],
            ] as $rate) {
                DriverRate::updateOrCreate(
                    ['driver_license_number' => $driver->license_number, 'trip_type' => $rate['trip_type']],
                    Arr::except($rate, ['trip_type']),
                );
            }
        }
    }

    private function seedSavedLocations(Business $business): void
    {
        if ($business->slug !== 'nogaru-car-rental') {
            return;
        }

        $locations = [
            ['title' => 'Cagsawa Ruins', 'address' => 'Cagsawa Church, Maharlika Highway, Cullat, Busay, Daraga, Albay, Bicol Region, 4501, Philippines', 'latitude' => 13.1659400, 'longitude' => 123.7009832],
            ['title' => 'Giant Statue of Nuestra Sra de Salvacion', 'address' => 'Tamaoyan, Legazpi, Albay, Bicol Region, 4500, Philippines', 'latitude' => 13.1702189, 'longitude' => 123.7392095],
            ['title' => 'Legazpi Sign', 'address' => 'Legazpi Sign, Legazpi Boulevard, Puro, Lamba, Legazpi, Albay, Bicol Region, 4500, Philippines', 'latitude' => 13.1343216, 'longitude' => 123.7670857],
            ['title' => 'Daraga Church', 'address' => 'Santa Maria Street, Purok 7, Market Area Poblacion, Kimantong, Daraga, Albay, Bicol Region, 4501, Philippines', 'latitude' => 13.1499150, 'longitude' => 123.7125564],
            ['title' => 'Farm Plate', 'address' => 'Kiwalo Street, Kiwalo, Daraga, Albay, Bicol Region, 4501, Philippines', 'latitude' => 13.1296016, 'longitude' => 123.7159762],
            ['title' => 'Highlands Park', 'address' => 'Estanza-Tabon-Tabon Road, Estanza, Legazpi, Albay, Bicol Region, 4500, Philippines', 'latitude' => 13.1245916, 'longitude' => 123.7261498],
            ['title' => 'ATV Adventure', 'address' => 'Cullat, Busay, Daraga, Albay, Bicol Region, 4501, Philippines', 'latitude' => 13.1648231, 'longitude' => 123.6987752],
            ['title' => 'National Museum - Bicol', 'address' => 'National Museum - Bicol Regional Museum, Maharlika Highway, Cullat, Busay, Daraga, Albay, Bicol Region, 4501, Philippines', 'latitude' => 13.1647291, 'longitude' => 123.7015808],
            ['title' => 'Quituinan Ranch', 'address' => 'Quituinan Ranch, D. Nieves Street, Barangay 3, Lacag, Mina, Albay, Bicol Region, 4502, Philippines', 'latitude' => 13.1702105, 'longitude' => 123.6654491],
            ['title' => '7 Eleven - Camalig Bypass Road', 'address' => '7-Eleven, Camalig Diversion Road, Barangay 4, Salvacion, Salugan, Albay, Bicol Region, 4502, Philippines', 'latitude' => 13.1883640, 'longitude' => 123.6619055],
            ['title' => 'Quitinday Hills and Nature Park', 'address' => 'Quitinday Hills, General Simeon A. Ola Road, Pariaan, Albay, Bicol Region, Philippines', 'latitude' => 13.0998037, 'longitude' => 123.6156803],
            ['title' => 'Hoyop-hoyopan Cave', 'address' => 'Hoyop-Hoyopan Cave, Comun-Inarado-Peñafrancia Road, Binitayan, Daraga, Albay, Bicol Region, Philippines', 'latitude' => 13.1207143, 'longitude' => 123.6563101],
            ['title' => 'Hobbit Hills', 'address' => 'Amtic, Albay, Bicol Region, Philippines', 'latitude' => 13.2975250, 'longitude' => 123.6135721],
            ['title' => 'Jovellar Underground River', 'address' => 'Poblacion, Quitinday, Albay, Bicol Region, Philippines', 'latitude' => 13.0766514, 'longitude' => 123.6042273],
            ['title' => 'The Oriental Hotel', 'address' => 'The Oriental Legazpi, Legazpi City - Punta De Jesus Road, Tula-tula, Estanza, Legazpi, Albay, Bicol Region, 4500, Philippines', 'latitude' => 13.1345659, 'longitude' => 123.7387703],
            ['title' => 'Pepita Park - Rest area', 'address' => 'Maharlika Highway, Rizal, Sorsogon, Bicol Region, Philippines', 'latitude' => 12.9791124, 'longitude' => 123.9182527],
            ['title' => 'Sorsogon Prov Capitol', 'address' => 'Sorsogon Provincial Capitol Complex, Bitan-o, Sorsogon City, Sorsogon, Bicol Region, 4700, Philippines', 'latitude' => 12.9718657, 'longitude' => 124.0017296],
            ['title' => 'Sorsogon Museum', 'address' => 'Sorsogon Museum, Flores Street, Bitan-o, Sorsogon City, Sorsogon, Bicol Region, 4700, Philippines', 'latitude' => 12.9722956, 'longitude' => 124.0018702],
            ['title' => 'Rompeolas', 'address' => 'Rompeolas, Bitan-o, Sorsogon City, Sorsogon, Bicol Region, 4700, Philippines', 'latitude' => 12.9637511, 'longitude' => 124.0046409],
            ['title' => 'Sports Complex', 'address' => 'Street Road, Balogo Sports Complex, Bibincahan, Sorsogon City, Sorsogon, Bicol Region, 4700, Philippines', 'latitude' => 12.9774813, 'longitude' => 124.0139508],
            ['title' => 'Barcelona Ruins', 'address' => 'Poblacion Norte, Benquet, Barcelona, Sorsogon, Bicol Region, Philippines', 'latitude' => 12.8678490, 'longitude' => 124.1435605],
            ['title' => 'Barcelona Church', 'address' => 'Poblacion Norte, Benquet, Barcelona, Sorsogon, Bicol Region, Philippines', 'latitude' => 12.8675274, 'longitude' => 124.1437671],
            ['title' => 'Bulusan Volcano Nature Park', 'address' => 'San Rafael, Sorsogon, Bicol Region, 4704, Philippines', 'latitude' => 12.7398091, 'longitude' => 124.0987086],
            ['title' => 'Bidi-Bidi (handicrafts)', 'address' => 'Rizal Street, Del Rosario, La Medalla, Baao, Camarines Sur, Bicol Region, 4432, Philippines', 'latitude' => 13.4554824, 'longitude' => 123.3655509],
            ['title' => 'The House of Pili', 'address' => 'J Emmanuel Pastries, Magsaysay Avenue, Jacob, Peñafrancia, San Felipe, Naga, Bicol Region, 4400, Philippines', 'latitude' => 13.6310642, 'longitude' => 123.1973737],
            ['title' => 'Basilica Church', 'address' => 'Basilica Loop, Lomeda, Balatas, San Felipe, Naga, Bicol Region, 4400, Philippines', 'latitude' => 13.6318696, 'longitude' => 123.1995463],
            ['title' => 'Our Lady of Peñafrancia Shrine', 'address' => 'P. Miguel Robles de Covarrubias Monument, Peñafrancia Avenue, Jacob, Peñafrancia, San Felipe, Naga, Bicol Region, 4400, Philippines', 'latitude' => 13.6342182, 'longitude' => 123.1952816],
            ['title' => 'Cathedral Naga Church', 'address' => 'Naga Cathedral Historical Marker, Elias Angeles Street, Zone 1, San Francisco, San Felipe, Naga, Bicol Region, 4400, Philippines', 'latitude' => 13.6281786, 'longitude' => 123.1872591],
            ['title' => 'NHCP Museo ni Jesse Robredo', 'address' => 'Dayangdang, San Felipe, Naga, Bicol Region, 4400, Philippines', 'latitude' => 13.6284262, 'longitude' => 123.1973577],
            ['title' => 'The Original Buko Pie', 'address' => 'Maharlika Highway, Palestina, Pili, Camarines Sur, Bicol Region, 4418, Philippines', 'latitude' => 13.6154888, 'longitude' => 123.2468739],
            ['title' => 'CWC, Cam Sur WaterSports Complex', 'address' => 'CWC Road, Cadlan, Pili, Camarines Sur, Bicol Region, 4418, Philippines', 'latitude' => 13.5900969, 'longitude' => 123.2528794],
            ['title' => 'Sumlang Lake', 'address' => 'Maharlika Highway, Salvacion, Daraga, Albay, Bicol Region, 4502, Philippines', 'latitude' => 13.1786858, 'longitude' => 123.6727524],
        ];

        foreach ($locations as $location) {
            SavedLocation::updateOrCreate(
                ['business_id' => $business->id, 'title' => $location['title']],
                Arr::except($location, ['title']),
            );
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

    private function seedQuotations(): void
    {
        Business::query()->with(['cars.rates', 'drivers'])->each(function (Business $business): void {
            $car = $business->cars->first();
            $driver = $business->drivers->first(fn (Driver $driver): bool => (bool) $driver->pivot->is_default);

            if (! $car instanceof Car || ! $driver instanceof Driver) {
                return;
            }

            $vehicleRate = $car->rates->firstWhere('name', '24hrs') ?? $car->rates->first();
            if ($vehicleRate === null) {
                return;
            }

            $otherPayments = [
                ['name' => 'Airport pickup', 'amount' => 500],
                ['name' => 'Additional insurance', 'amount' => 350],
            ];
            $hiddenCharges = 250;
            $vehicleRateAmount = (float) $vehicleRate->value;
            $driverRate = (float) $driver->pivot->daily_rate;
            $distanceRate = 900;
            $totalAmount = $vehicleRateAmount + $driverRate + $distanceRate + collect($otherPayments)->sum('amount') + $hiddenCharges;

            $footnote = QuotationFootnote::updateOrCreate(
                ['business_id' => $business->id, 'title' => 'Standard quotation terms'],
                ['content' => 'Rates are subject to vehicle availability. A valid license and identification are required before release.'],
            );

            Quotation::updateOrCreate(
                ['quotation_number' => 'QT-DEMO-'.strtoupper($business->slug)],
                [
                    'business_id' => $business->id,
                    'car_id' => $car->id,
                    'driver_license_number' => $driver->license_number,
                    'quotation_footnote_id' => $footnote->id,
                    'quotation_date' => now()->toDateString(),
                    'title' => 'Legazpi city tour package',
                    'client_name' => 'Jordan Dela Cruz',
                    'package_type' => 'all_in',
                    'itinerary' => [
                        ['title' => 'Pickup', 'address' => 'Legazpi Airport, Albay', 'latitude' => 13.1575, 'longitude' => 123.7351],
                        ['title' => 'Destination', 'address' => 'Cagsawa Ruins, Daraga, Albay', 'latitude' => 13.1412, 'longitude' => 123.7061],
                    ],
                    'other_payments' => $otherPayments,
                    'hidden_charges' => $hiddenCharges,
                    'itinerary_start_address' => 'Legazpi Airport, Albay',
                    'itinerary_start_latitude' => 13.1575,
                    'itinerary_start_longitude' => 123.7351,
                    'itinerary_end_address' => 'Cagsawa Ruins, Daraga, Albay',
                    'itinerary_end_latitude' => 13.1412,
                    'itinerary_end_longitude' => 123.7061,
                    'footnote_content' => $footnote->content,
                    'total_distance_km' => 38.5,
                    'vehicle_rate_name' => $vehicleRate->name,
                    'vehicle_rate' => $vehicleRateAmount,
                    'driver_rate' => $driverRate,
                    'distance_rate' => $distanceRate,
                    'total_amount' => $totalAmount,
                ],
            );
        });
    }
}

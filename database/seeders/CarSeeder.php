<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Car;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Realistic car data per business.
     */
    private array $fleets = [
        'metro-rentals' => [
            ['vehicle_type' => 'Sedan', 'car_model' => 'Toyota Altis', 'variant' => '1.8 E', 'transmission' => 'Automatic', 'rental_type' => 'Daily', 'year_model' => 2023, 'ownership' => 'owned'],
            ['vehicle_type' => 'Sedan', 'car_model' => 'Honda Civic', 'variant' => 'RS Turbo', 'transmission' => 'Automatic', 'rental_type' => 'Daily', 'year_model' => 2024, 'ownership' => 'owned'],
            ['vehicle_type' => 'SUV', 'car_model' => 'Toyota RAV4', 'variant' => '2.0 G', 'transmission' => 'Automatic', 'rental_type' => 'Daily', 'year_model' => 2023, 'ownership' => 'owned'],
            ['vehicle_type' => 'SUV', 'car_model' => 'Mitsubishi Xpander', 'variant' => 'GLS', 'transmission' => 'Automatic', 'rental_type' => 'Daily', 'year_model' => 2022, 'ownership' => 'consigned'],
            ['vehicle_type' => 'Van', 'car_model' => 'Toyota Innova', 'variant' => 'E', 'transmission' => 'Manual', 'rental_type' => 'Daily', 'year_model' => 2021, 'ownership' => 'owned'],
        ],
        'sunrise-car-rentals' => [
            ['vehicle_type' => 'Hatchback', 'car_model' => 'Suzuki Swift', 'variant' => 'GL', 'transmission' => 'Manual', 'rental_type' => 'Daily', 'year_model' => 2022, 'ownership' => 'owned'],
            ['vehicle_type' => 'Sedan', 'car_model' => 'Nissan Almera', 'variant' => 'VL', 'transmission' => 'Automatic', 'rental_type' => 'Daily', 'year_model' => 2023, 'ownership' => 'owned'],
            ['vehicle_type' => 'SUV', 'car_model' => 'Ford Territory', 'variant' => 'Titanium', 'transmission' => 'Automatic', 'rental_type' => 'Daily', 'year_model' => 2024, 'ownership' => 'consigned'],
            ['vehicle_type' => 'Van', 'car_model' => 'Nissan Urvan', 'variant' => 'Premium', 'transmission' => 'Manual', 'rental_type' => 'Weekly', 'year_model' => 2021, 'ownership' => 'owned'],
        ],
        'oceanview-rentals' => [
            ['vehicle_type' => 'SUV', 'car_model' => 'Mitsubishi Montero Sport', 'variant' => 'GT 4x4', 'transmission' => 'Automatic', 'rental_type' => 'Daily', 'year_model' => 2024, 'ownership' => 'owned'],
            ['vehicle_type' => 'SUV', 'car_model' => 'Toyota Fortuner', 'variant' => '2.4 G', 'transmission' => 'Automatic', 'rental_type' => 'Daily', 'year_model' => 2023, 'ownership' => 'owned'],
            ['vehicle_type' => 'Pickup', 'car_model' => 'Ford Ranger', 'variant' => 'Wildtrak', 'transmission' => 'Automatic', 'rental_type' => 'Daily', 'year_model' => 2024, 'ownership' => 'owned'],
            ['vehicle_type' => 'Sedan', 'car_model' => 'Mazda 3', 'variant' => 'Sportback', 'transmission' => 'Automatic', 'rental_type' => 'Daily', 'year_model' => 2022, 'ownership' => 'consigned'],
        ],
        'mountain-mobility' => [
            ['vehicle_type' => 'Pickup', 'car_model' => 'Toyota Hilux', 'variant' => '2.4 J', 'transmission' => 'Manual', 'rental_type' => 'Daily', 'year_model' => 2023, 'ownership' => 'owned'],
            ['vehicle_type' => 'Pickup', 'car_model' => 'Isuzu D-Max', 'variant' => 'LS 4x4', 'transmission' => 'Manual', 'rental_type' => 'Daily', 'year_model' => 2022, 'ownership' => 'owned'],
            ['vehicle_type' => 'SUV', 'car_model' => 'Toyota Land Cruiser Prado', 'variant' => 'TX', 'transmission' => 'Automatic', 'rental_type' => 'Weekly', 'year_model' => 2024, 'ownership' => 'owned'],
            ['vehicle_type' => 'Van', 'car_model' => 'Hyundai Starex', 'variant' => 'GLS', 'transmission' => 'Manual', 'rental_type' => 'Weekly', 'year_model' => 2021, 'ownership' => 'consigned'],
        ],
        'cityline-rides' => [
            ['vehicle_type' => 'Hatchback', 'car_model' => 'Toyota Wigo', 'variant' => 'G', 'transmission' => 'Manual', 'rental_type' => 'Daily', 'year_model' => 2023, 'ownership' => 'owned'],
            ['vehicle_type' => 'Hatchback', 'car_model' => 'Honda Brio', 'variant' => 'RS', 'transmission' => 'Automatic', 'rental_type' => 'Daily', 'year_model' => 2024, 'ownership' => 'owned'],
            ['vehicle_type' => 'Sedan', 'car_model' => 'Mitsubishi Mirage G4', 'variant' => 'GLS', 'transmission' => 'Manual', 'rental_type' => 'Daily', 'year_model' => 2022, 'ownership' => 'owned'],
            ['vehicle_type' => 'SUV', 'car_model' => 'Geely Coolray', 'variant' => 'Sport', 'transmission' => 'Automatic', 'rental_type' => 'Daily', 'year_model' => 2024, 'ownership' => 'consigned'],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->fleets as $slug => $cars) {
            $business = Business::where('slug', $slug)->first();

            if (! $business) {
                continue;
            }

            foreach ($cars as $carData) {
                Car::firstOrCreate(
                    [
                        'business_id' => $business->id,
                        'car_model' => $carData['car_model'],
                        'variant' => $carData['variant'],
                    ],
                    $carData + [
                        'owner_id' => null,
                        'rental_type' => $carData['rental_type'] ?? 'Daily',
                        'plate_number' => 'DEMO'.strtoupper(fake()->bothify('####')),
                    ]
                );
            }
        }
    }
}

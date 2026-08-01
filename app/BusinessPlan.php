<?php

namespace App;

enum BusinessPlan: string
{
    case Free = 'free';
    case Basic = 'basic';
    case Pro = 'pro';
    case Business = 'business';

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Free',
            self::Basic => 'Basic',
            self::Pro => 'Pro',
            self::Business => 'Business',
        };
    }

    public function price(): string
    {
        return match ($this) {
            self::Free => '₱0',
            self::Basic => '₱499/month',
            self::Pro => '₱999/month',
            self::Business => '₱1,999/month',
        };
    }

    public function vehicleLimit(): ?int
    {
        return match ($this) {
            self::Free => 3,
            self::Basic => 10,
            self::Pro => 25,
            self::Business => null,
        };
    }

    /**
     * @return array<int, string>
     */
    public function includedFeatures(): array
    {
        return array_map(
            fn (BusinessFeature $feature): string => match ($feature) {
                BusinessFeature::PaymentTracking => 'Payment tracking',
                BusinessFeature::VehicleInspection => 'Vehicle inspections',
                BusinessFeature::RentalAgreement => 'Rental agreements',
                BusinessFeature::LandingPage => 'Landing page editor',
                BusinessFeature::Reports => 'Business analytics',
                BusinessFeature::StaffAccounts => 'Staff accounts',
                BusinessFeature::PartnerManagement => 'Partner management',
            },
            array_filter(BusinessFeature::cases(), fn (BusinessFeature $feature): bool => $this->allows($feature)),
        );
    }

    public function allows(BusinessFeature $feature): bool
    {
        return match ($feature) {
            BusinessFeature::PaymentTracking, BusinessFeature::LandingPage => $this !== self::Free,
            BusinessFeature::Reports, BusinessFeature::VehicleInspection, BusinessFeature::RentalAgreement => in_array($this, [self::Pro, self::Business], true),
            BusinessFeature::StaffAccounts, BusinessFeature::PartnerManagement => $this === self::Business,
        };
    }
}

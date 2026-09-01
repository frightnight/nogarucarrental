<?php

namespace App;

enum BusinessFeature: string
{
    case Reports = 'reports';
    case PaymentTracking = 'payment_tracking';
    case VehicleInspection = 'vehicle_inspection';
    case RentalAgreement = 'rental_agreement';
    case LandingPage = 'landing_page';
    case StaffAccounts = 'staff_accounts';
    case PartnerManagement = 'partner_management';

    public function label(): string
    {
        return match ($this) {
            self::Reports => 'Business analytics',
            self::PaymentTracking => 'Payment tracking',
            self::VehicleInspection => 'Vehicle inspections',
            self::RentalAgreement => 'Rental agreements',
            self::LandingPage => 'Landing page editor',
            self::StaffAccounts => 'Staff accounts',
            self::PartnerManagement => 'Partner management',
        };
    }
}

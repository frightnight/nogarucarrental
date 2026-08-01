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
}

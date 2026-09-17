<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDriverController;
use App\Http\Controllers\AdminPlatformLandingController;
use App\Http\Controllers\AdminSalesAgentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthenticationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BusinessBookingController;
use App\Http\Controllers\BusinessDashboardController;
use App\Http\Controllers\BusinessLandingContentController;
use App\Http\Controllers\BusinessLandingController;
use App\Http\Controllers\BusinessPaymentMethodController;
use App\Http\Controllers\BusinessPlanController;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\BusinessQuotationController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\ClientProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverPortalController;
use App\Http\Controllers\DriverRegistrationController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\FleetRateController;
use App\Http\Controllers\GuestCheckoutController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\PartnerFleetController;
use App\Http\Controllers\PublicLandingController;
use App\Http\Controllers\RentalManagementController;
use App\Http\Controllers\SalesAgentController;
use App\Http\Controllers\SalesAgentRegistrationController;
use App\Http\Controllers\VehicleDetailsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicLandingController::class, 'index'])->name('home');

Route::get('/businesses', [BusinessLandingController::class, 'index'])->name('businesses.index');
Route::get('/businesses/{slug}', [BusinessLandingController::class, 'show'])->name('businesses.show');
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/vehicles/{car}', [VehicleDetailsController::class, 'show'])->name('vehicles.show');
Route::post('/vehicles/{car}/guest-checkout', [GuestCheckoutController::class, 'store'])->name('guest-checkout.store');
Route::get('/guest-checkout/{booking}/{token}', [GuestCheckoutController::class, 'show'])->name('guest-checkout.show');
Route::post('/guest-checkout/{booking}/{token}/payment', [GuestCheckoutController::class, 'payment'])->name('guest-checkout.payment');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('register/business', [RegisteredUserController::class, 'createBusiness'])->name('business.register');
    Route::post('register/business', [RegisteredUserController::class, 'storeBusiness'])->name('business.register.store');
    Route::get('register/driver', [DriverRegistrationController::class, 'create'])->name('driver.register');
    Route::post('register/driver', [DriverRegistrationController::class, 'store'])->name('driver.register.store');
    Route::get('register/driver/success/{driver}', [DriverRegistrationController::class, 'success'])->name('driver.registration.success');
    Route::get('register/sales-agent', [SalesAgentRegistrationController::class, 'create'])->name('sales-agent.register');
    Route::post('register/sales-agent', [SalesAgentRegistrationController::class, 'store'])->name('sales-agent.register.store');
    Route::get('register/sales-agent/success', [SalesAgentRegistrationController::class, 'success'])->name('sales-agent.registration.success');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('login/{provider}', [SocialAuthenticationController::class, 'redirect'])->name('social.redirect');
    Route::get('login/{provider}/callback', [SocialAuthenticationController::class, 'callback'])->name('social.callback');
    Route::get('login/social/email', [SocialAuthenticationController::class, 'emailForm'])->name('social.email');
    Route::post('login/social/email', [SocialAuthenticationController::class, 'storeEmail'])->name('social.email.store');
});

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('admin', [AdminDashboardController::class, 'index'])->middleware('role:administrator|moderator')->name('admin.dashboard');
    Route::get('admin/drivers', [AdminDriverController::class, 'index'])->middleware('role:administrator')->name('admin.drivers.index');
    Route::put('admin/drivers/{driver}/approve', [AdminDriverController::class, 'approve'])->middleware('role:administrator')->name('admin.drivers.approve');
    Route::put('admin/drivers/{driver}/reject', [AdminDriverController::class, 'reject'])->middleware('role:administrator')->name('admin.drivers.reject');
    Route::get('admin/sales-agents', [AdminSalesAgentController::class, 'index'])->middleware('role:administrator')->name('admin.sales-agents.index');
    Route::post('admin/sales-agents', [AdminSalesAgentController::class, 'store'])->middleware('role:administrator')->name('admin.sales-agents.store');
    Route::put('admin/sales-agents/{user}/toggle', [AdminSalesAgentController::class, 'toggle'])->middleware('role:administrator')->name('admin.sales-agents.toggle');
    Route::put('admin/sales-agents/{user}/approve', [AdminSalesAgentController::class, 'approve'])->middleware('role:administrator')->name('admin.sales-agents.approve');
    Route::put('admin/sales-agents/{user}/reject', [AdminSalesAgentController::class, 'reject'])->middleware('role:administrator')->name('admin.sales-agents.reject');
    Route::put('admin/sales-commissions/{commission}/approve', [AdminSalesAgentController::class, 'approveCommission'])->middleware('role:administrator')->name('admin.sales-commissions.approve');
    Route::get('admin/landing-page', [AdminPlatformLandingController::class, 'edit'])->middleware('role:administrator')->name('admin.landing.edit');
    Route::put('admin/landing-page', [AdminPlatformLandingController::class, 'update'])->middleware('role:administrator')->name('admin.landing.update');
    Route::get('admin/business-plans', [BusinessPlanController::class, 'adminIndex'])->middleware('role:administrator')->name('admin.business-plans.index');
    Route::put('admin/business-plans/{business}', [BusinessPlanController::class, 'update'])->middleware('role:administrator')->name('admin.business-plans.update');
    Route::get('business', [BusinessDashboardController::class, 'index'])->middleware('role:business_owner|booker|agent')->name('business.dashboard');
    Route::get('business/profile', [BusinessProfileController::class, 'create'])->middleware('role:business_owner|booker|agent')->name('business.profile.create');
    Route::post('business/profile', [BusinessProfileController::class, 'store'])->middleware('role:business_owner|booker|agent')->name('business.profile.store');
    Route::get('business/profile/manage', [BusinessProfileController::class, 'edit'])->middleware('role:business_owner|booker|agent')->name('business.profile.edit');
    Route::put('business/profile/manage', [BusinessProfileController::class, 'update'])->middleware('role:business_owner|booker|agent')->name('business.profile.update');
    Route::delete('business/profile/permits', [BusinessProfileController::class, 'destroyPermit'])->middleware('role:business_owner|booker|agent')->name('business.profile.permits.destroy');
    Route::get('business/plan', [BusinessPlanController::class, 'index'])->middleware('role:business_owner|booker|agent')->name('business.plan');
    Route::get('business/quotations', [BusinessQuotationController::class, 'index'])->middleware('role:business_owner|booker|agent')->name('business.quotations.index');
    Route::post('business/quotations', [BusinessQuotationController::class, 'store'])->middleware('role:business_owner|booker|agent')->name('business.quotations.store');
    Route::post('business/quotations/saved-locations', [BusinessQuotationController::class, 'storeSavedLocation'])->middleware('role:business_owner|booker|agent')->name('business.quotations.saved-locations.store');
    Route::put('business/quotations/saved-locations/{savedLocation}', [BusinessQuotationController::class, 'updateSavedLocation'])->middleware('role:business_owner|booker|agent')->name('business.quotations.saved-locations.update');
    Route::delete('business/quotations/saved-locations/{savedLocation}', [BusinessQuotationController::class, 'destroySavedLocation'])->middleware('role:business_owner|booker|agent')->name('business.quotations.saved-locations.destroy');
    Route::post('business/quotations/footnotes', [BusinessQuotationController::class, 'storeFootnote'])->middleware('role:business_owner|booker|agent')->name('business.quotations.footnotes.store');
    Route::put('business/quotations/footnotes/{footnote}', [BusinessQuotationController::class, 'updateFootnote'])->middleware('role:business_owner|booker|agent')->name('business.quotations.footnotes.update');
    Route::delete('business/quotations/footnotes/{footnote}', [BusinessQuotationController::class, 'destroyFootnote'])->middleware('role:business_owner|booker|agent')->name('business.quotations.footnotes.destroy');
    Route::put('business/quotations/{quotation}', [BusinessQuotationController::class, 'update'])->middleware('role:business_owner|booker|agent')->name('business.quotations.update');
    Route::delete('business/quotations/{quotation}', [BusinessQuotationController::class, 'destroy'])->middleware('role:business_owner|booker|agent')->name('business.quotations.destroy');
    Route::get('business/quotations/{quotation}', [BusinessQuotationController::class, 'show'])->middleware('role:business_owner|booker|agent')->name('business.quotations.show');
    Route::get('business/payment-methods', [BusinessPaymentMethodController::class, 'index'])->middleware('role:business_owner|booker|agent')->name('business.payment-methods.index');
    Route::post('business/payment-methods', [BusinessPaymentMethodController::class, 'store'])->middleware('role:business_owner|booker|agent')->name('business.payment-methods.store');
    Route::get('business/payment-methods/{paymentMethod}/edit', [BusinessPaymentMethodController::class, 'edit'])->middleware('role:business_owner|booker|agent')->name('business.payment-methods.edit');
    Route::put('business/payment-methods/{paymentMethod}', [BusinessPaymentMethodController::class, 'update'])->middleware('role:business_owner|booker|agent')->name('business.payment-methods.update');
    Route::delete('business/payment-methods/{paymentMethod}', [BusinessPaymentMethodController::class, 'destroy'])->middleware('role:business_owner|booker|agent')->name('business.payment-methods.destroy');
    Route::get('business/partner-fleets', [PartnerFleetController::class, 'index'])->middleware('role:business_owner|booker|agent')->name('business.partner-fleets.index');
    Route::post('business/partner-fleets', [PartnerFleetController::class, 'store'])->middleware('role:business_owner|booker|agent')->name('business.partner-fleets.store');
    Route::put('business/partner-fleets/{partnerFleetRequest}/approve', [PartnerFleetController::class, 'approve'])->middleware('role:business_owner|booker|agent')->name('business.partner-fleets.approve');
    Route::put('business/partner-fleets/{partnerFleetRequest}/reject', [PartnerFleetController::class, 'reject'])->middleware('role:business_owner|booker|agent')->name('business.partner-fleets.reject');
    Route::delete('business/partner-fleets/{partnerFleetRequest}', [PartnerFleetController::class, 'destroy'])->middleware('role:business_owner|booker|agent')->name('business.partner-fleets.destroy');
    Route::get('business/bookings/create', [RentalManagementController::class, 'createBooking'])->middleware('role:business_owner|booker|agent')->name('business.bookings.create');
    Route::post('business/bookings/manual', [RentalManagementController::class, 'storeBooking'])->middleware('role:business_owner|booker|agent')->name('business.bookings.manual.store');
    Route::get('business/bookings/{booking}/agreement', [RentalManagementController::class, 'agreement'])->middleware('role:business_owner|booker|agent')->name('business.bookings.agreement');
    Route::get('business/bookings/{booking}/inspection', [RentalManagementController::class, 'inspection'])->middleware('role:business_owner|booker|agent')->name('business.bookings.inspection');
    Route::post('business/bookings/{booking}/inspection', [RentalManagementController::class, 'storeInspection'])->middleware('role:business_owner|booker|agent')->name('business.bookings.inspection.store');
    Route::get('business/reports', [RentalManagementController::class, 'reports'])->middleware('role:business_owner|booker|agent')->name('business.reports');
    Route::get('client', [ClientDashboardController::class, 'index'])->middleware('role:client')->name('client.dashboard');

    // Business landing page content management (owners & moderators only)
    Route::get('business/landing-content', [BusinessLandingContentController::class, 'edit'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.landing.content.edit');
    Route::post('business/landing-content', [BusinessLandingContentController::class, 'update'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.landing.content.update');

    // Fleet CRUD
    Route::get('business/fleet', [FleetController::class, 'index'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.fleet.index');
    Route::put('business/fleet/fuel-prices', [FleetController::class, 'updateFuelPrices'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.fleet.fuel-prices.update');
    Route::put('business/fleet/long-term-discounts', [FleetController::class, 'updateLongTermDiscounts'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.fleet.long-term-discounts.update');
    Route::put('business/fleet/garage-address', [FleetController::class, 'updateGarageAddress'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.fleet.garage-address.update');
    Route::get('business/drivers', [DriverController::class, 'index'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.drivers.index');
    Route::get('business/drivers/{driver}', [DriverController::class, 'show'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.drivers.show');
    Route::post('business/bookings/{booking}/driver-applications/{application}/select', [BusinessBookingController::class, 'selectDriverApplication'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.bookings.driver-applications.select');
    Route::post('business/bookings/{booking}/driver-evaluation', [BusinessBookingController::class, 'evaluateDriver'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.bookings.driver-evaluation');
    Route::get('business/fleet/create', [FleetController::class, 'create'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.fleet.create');
    Route::post('business/fleet', [FleetController::class, 'store'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.fleet.store');
    Route::get('business/fleet/{car}/edit', [FleetController::class, 'edit'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.fleet.edit');
    Route::match(['PUT', 'PATCH', 'POST'], 'business/fleet/{car}', [FleetController::class, 'update'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.fleet.update');
    Route::delete('business/fleet/{car}', [FleetController::class, 'destroy'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.fleet.destroy');
    Route::post('business/fleet/{car}/rates', [FleetRateController::class, 'store'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.fleet.rates.store');
    Route::put('business/fleet/{car}/rates', [FleetRateController::class, 'sync'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.fleet.rates.sync');
    Route::put('business/fleet/{car}/rates/{rate}', [FleetRateController::class, 'update'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.fleet.rates.update');
    Route::delete('business/fleet/{car}/rates/{rate}', [FleetRateController::class, 'destroy'])
        ->middleware('role:business_owner|booker|agent')
        ->name('business.fleet.rates.destroy');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Client Profile
    Route::get('client/profile', [ClientProfileController::class, 'edit'])->middleware('role:client')->name('client.profile.edit');
    Route::post('client/profile', [ClientProfileController::class, 'update'])->middleware('role:client')->name('client.profile.update');
    Route::get('client/profile/show', [ClientProfileController::class, 'show'])->middleware('role:client')->name('client.profile.show');
    Route::get('client/profile/documents', [ClientProfileController::class, 'documents'])->middleware('role:client')->name('client.profile.documents');
    Route::post('client/profile/documents', [ClientProfileController::class, 'uploadDocuments'])->middleware('role:client')->name('client.profile.documents.upload');

    // Booking flow
    Route::get('bookings/details', [BookingController::class, 'details'])->middleware('role:client')->name('bookings.details');
    Route::post('bookings', [BookingController::class, 'store'])->middleware('role:client')->name('bookings.store');
    Route::get('bookings/{booking}/review', [BookingController::class, 'review'])->middleware('role:client')->name('bookings.review');
    Route::get('bookings/{booking}/payment', [BookingController::class, 'payment'])->middleware('role:client')->name('bookings.payment');
    Route::post('bookings/{booking}/payment', [BookingController::class, 'storePayment'])->middleware('role:client')->name('bookings.payment.store');
    Route::get('business/bookings', [BusinessBookingController::class, 'index'])->middleware('role:business_owner|booker|agent')->name('business.bookings.index');
    Route::get('business/bookings/calendar', [BusinessBookingController::class, 'calendar'])->middleware('role:business_owner|booker|agent')->name('business.bookings.calendar');
    Route::get('business/crm', [BusinessBookingController::class, 'crm'])->middleware('role:business_owner|booker|agent')->name('business.crm');
    Route::get('business/clients', [BusinessBookingController::class, 'clients'])->middleware('role:business_owner|booker|agent')->name('business.clients.index');
    Route::get('business/clients/{client}', [BusinessBookingController::class, 'showClient'])->middleware('role:business_owner|booker|agent')->name('business.clients.show');
    Route::get('business/bookings/{booking}/edit', [BusinessBookingController::class, 'edit'])->middleware('role:business_owner|booker|agent')->name('business.bookings.edit');
    Route::put('business/bookings/{booking}', [BusinessBookingController::class, 'update'])->middleware('role:business_owner|booker|agent')->name('business.bookings.update');
    Route::put('business/bookings/{booking}/payment/confirm', [BusinessBookingController::class, 'confirmPayment'])->middleware('role:business_owner|booker|agent')->name('business.bookings.payment.confirm');
    Route::put('business/bookings/{booking}/status', [BusinessBookingController::class, 'updateStatus'])->middleware('role:business_owner|booker|agent')->name('business.bookings.status.update');
    Route::get('driver', [DriverPortalController::class, 'index'])->middleware('role:driver')->name('driver.dashboard');
    Route::post('driver/rates', [DriverPortalController::class, 'storeRate'])->middleware('role:driver')->name('driver.rates.store');
    Route::delete('driver/rates/{rate}', [DriverPortalController::class, 'destroyRate'])->middleware('role:driver')->name('driver.rates.destroy');
    Route::post('driver/bookings/{booking}/apply', [DriverPortalController::class, 'apply'])->middleware('role:driver')->name('driver.bookings.apply');
    Route::get('sales-agent', [SalesAgentController::class, 'index'])->middleware('role:sales_agent')->name('sales-agent.dashboard');
    Route::post('sales-agent/leads', [SalesAgentController::class, 'storeLead'])->middleware('role:sales_agent')->name('sales-agent.leads.store');
    Route::put('sales-agent/leads/{lead}', [SalesAgentController::class, 'updateLead'])->middleware('role:sales_agent')->name('sales-agent.leads.update');
    Route::post('sales-agent/leads/{lead}/quotation', [SalesAgentController::class, 'createQuotation'])->middleware('role:sales_agent')->name('sales-agent.leads.quotation');
    Route::post('sales-agent/leads/{lead}/reservation', [SalesAgentController::class, 'createReservation'])->middleware('role:sales_agent')->name('sales-agent.leads.reservation');
    Route::post('sales-agent/leads/{lead}/convert', [SalesAgentController::class, 'convertLead'])->middleware('role:sales_agent')->name('sales-agent.leads.convert');

    Route::get('connect/{provider}', [SocialAuthenticationController::class, 'connectRedirect'])->name('social.connect.redirect');
    Route::get('connect/{provider}/callback', [SocialAuthenticationController::class, 'connectCallback'])->name('social.connect.callback');
});

// Business landing single-page (e.g. /metro-rentals/)
// MUST be defined last so more specific routes (bookings, login, register, dashboard, etc.) take priority.
Route::get('/{businessSlug}/', [BusinessLandingController::class, 'show'])
    ->where('businessSlug', '^(?!login$)(?!register$)(?!dashboard$)(?!admin$)(?!business$)(?!client$)(?!bookings(?:/.*)?$)[a-z0-9]+(?:-[a-z0-9]+)*$')
    ->name('business.landing');

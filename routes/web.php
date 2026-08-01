<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPlatformLandingController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthenticationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BusinessBookingController;
use App\Http\Controllers\BusinessDashboardController;
use App\Http\Controllers\BusinessLandingContentController;
use App\Http\Controllers\BusinessLandingController;
use App\Http\Controllers\BusinessPlanController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\ClientProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\FleetRateController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\PublicLandingController;
use App\Http\Controllers\RentalManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicLandingController::class, 'index'])->name('home');

Route::get('/businesses', [BusinessLandingController::class, 'index'])->name('businesses.index');
Route::get('/businesses/{slug}', [BusinessLandingController::class, 'show'])->name('businesses.show');
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

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
    Route::get('admin/landing-page', [AdminPlatformLandingController::class, 'edit'])->middleware('role:administrator')->name('admin.landing.edit');
    Route::put('admin/landing-page', [AdminPlatformLandingController::class, 'update'])->middleware('role:administrator')->name('admin.landing.update');
    Route::get('admin/business-plans', [BusinessPlanController::class, 'adminIndex'])->middleware('role:administrator')->name('admin.business-plans.index');
    Route::put('admin/business-plans/{business}', [BusinessPlanController::class, 'update'])->middleware('role:administrator')->name('admin.business-plans.update');
    Route::get('business', [BusinessDashboardController::class, 'index'])->middleware('role:business_owner|booker|agent')->name('business.dashboard');
    Route::get('business/plan', [BusinessPlanController::class, 'index'])->middleware('role:business_owner|booker|agent')->name('business.plan');
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
    Route::get('business/clients', [BusinessBookingController::class, 'clients'])->middleware('role:business_owner|booker|agent')->name('business.clients.index');
    Route::get('business/clients/{client}', [BusinessBookingController::class, 'showClient'])->middleware('role:business_owner|booker|agent')->name('business.clients.show');
    Route::get('business/bookings/{booking}/edit', [BusinessBookingController::class, 'edit'])->middleware('role:business_owner|booker|agent')->name('business.bookings.edit');
    Route::put('business/bookings/{booking}', [BusinessBookingController::class, 'update'])->middleware('role:business_owner|booker|agent')->name('business.bookings.update');
    Route::put('business/bookings/{booking}/payment/confirm', [BusinessBookingController::class, 'confirmPayment'])->middleware('role:business_owner|booker|agent')->name('business.bookings.payment.confirm');
    Route::put('business/bookings/{booking}/status', [BusinessBookingController::class, 'updateStatus'])->middleware('role:business_owner|booker|agent')->name('business.bookings.status.update');

    Route::get('connect/{provider}', [SocialAuthenticationController::class, 'connectRedirect'])->name('social.connect.redirect');
    Route::get('connect/{provider}/callback', [SocialAuthenticationController::class, 'connectCallback'])->name('social.connect.callback');
});

// Business landing single-page (e.g. /metro-rentals/)
// MUST be defined last so more specific routes (bookings, login, register, dashboard, etc.) take priority.
Route::get('/{businessSlug}/', [BusinessLandingController::class, 'show'])
    ->where('businessSlug', '^(?!login$)(?!register$)(?!dashboard$)(?!admin$)(?!business$)(?!client$)(?!bookings(?:/.*)?$)[a-z0-9]+(?:-[a-z0-9]+)*$')
    ->name('business.landing');

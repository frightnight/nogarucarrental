<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class BusinessDashboardController extends Controller
{
    public function index(): View
    {
        return app(RentalManagementController::class)->dashboard();
    }
}

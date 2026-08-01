<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('panels.admin', ['role' => 'Administrator']);
    }
}

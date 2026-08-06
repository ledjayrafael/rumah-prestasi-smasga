<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardStats;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(AdminDashboardStats $stats): View
    {
        return view('admin.dashboard', [
            'dashboard' => $stats->build(),
        ]);
    }
}

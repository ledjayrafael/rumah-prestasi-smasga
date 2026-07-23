<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AchievementStatus;
use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'total_achievements' => Achievement::count(),
            'pending' => Achievement::where('status', AchievementStatus::Pending)->count(),
            'total_students' => StudentProfile::count(),
            'total_classes' => SchoolClass::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}

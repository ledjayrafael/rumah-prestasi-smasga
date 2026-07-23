<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\AchievementStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $student = Auth::user();

        $achievements = $student->achievements()->latest('event_date')->take(5)->get();

        $stats = [
            'total' => $student->achievements()->count(),
            'approved' => $student->achievements()->where('status', AchievementStatus::Approved)->count(),
            'pending' => $student->achievements()->where('status', AchievementStatus::Pending)->count(),
        ];

        return view('siswa.dashboard', compact('student', 'achievements', 'stats'));
    }
}

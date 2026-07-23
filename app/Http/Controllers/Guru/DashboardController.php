<?php

namespace App\Http\Controllers\Guru;

use App\Enums\AchievementStatus;
use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $teacher = Auth::user();
        $classIds = $teacher->taughtClasses()->pluck('school_classes.id');

        $baseQuery = Achievement::whereHas(
            'student.studentProfile',
            fn ($q) => $q->whereIn('school_class_id', $classIds)
        );

        $stats = [
            'pending' => (clone $baseQuery)->where('status', AchievementStatus::Pending)->count(),
            'approved_this_month' => (clone $baseQuery)
                ->where('status', AchievementStatus::Approved)
                ->whereMonth('reviewed_at', now()->month)
                ->whereYear('reviewed_at', now()->year)
                ->count(),
            'revision' => (clone $baseQuery)->where('status', AchievementStatus::Revision)->count(),
            'active_students' => StudentProfile::whereIn('school_class_id', $classIds)->count(),
        ];

        return view('guru.dashboard', compact('teacher', 'stats'));
    }
}

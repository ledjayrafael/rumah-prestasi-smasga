<?php

namespace App\Http\Controllers\Guru;

use App\Enums\AchievementCategory;
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

        $activeStudents = StudentProfile::whereIn('school_class_id', $classIds)->count();

        $studentsWithApproved = (clone $baseQuery)
            ->where('status', AchievementStatus::Approved)
            ->distinct('student_id')
            ->count('student_id');

        $stats = [
            'pending' => (clone $baseQuery)->where('status', AchievementStatus::Pending)->count(),
            'approved_this_month' => (clone $baseQuery)
                ->where('status', AchievementStatus::Approved)
                ->whereMonth('reviewed_at', now()->month)
                ->whereYear('reviewed_at', now()->year)
                ->count(),
            'revision' => (clone $baseQuery)->where('status', AchievementStatus::Revision)->count(),
            'active_students' => $activeStudents,
        ];

        $approvedByCategory = (clone $baseQuery)
            ->where('status', AchievementStatus::Approved)
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->get()
            ->mapWithKeys(fn (Achievement $row) => [$row->category->value => $row->total]);

        $chartData = [
            'coverage' => [
                'total_students' => $activeStudents,
                'with_approved' => $studentsWithApproved,
                'without_approved' => max($activeStudents - $studentsWithApproved, 0),
            ],
            'categories' => collect(AchievementCategory::cases())->map(fn (AchievementCategory $category) => [
                'label' => $category->label(),
                'count' => $approvedByCategory[$category->value] ?? 0,
            ])->values()->all(),
        ];

        $notifications = $teacher->unreadNotifications()->latest()->take(5)->get();

        return view('guru.dashboard', compact('teacher', 'stats', 'chartData', 'notifications'));
    }
}

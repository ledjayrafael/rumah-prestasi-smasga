<?php

namespace App\Services;

use App\Enums\AchievementCategory;
use App\Enums\AchievementLevel;
use App\Enums\AchievementStatus;
use App\Models\Achievement;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardStats
{
    public function build(): array
    {
        $now = now();
        $startOfThisMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $endOfLastMonth = $startOfThisMonth->copy()->subSecond();

        $totalStudents = StudentProfile::count();
        $totalClasses = SchoolClass::count();
        $totalAchievements = Achievement::count();
        $pendingCount = Achievement::where('status', AchievementStatus::Pending)->count();

        $approvedThisMonth = Achievement::where('status', AchievementStatus::Approved)
            ->whereBetween('reviewed_at', [$startOfThisMonth, $now])->count();
        $approvedLastMonth = Achievement::where('status', AchievementStatus::Approved)
            ->whereBetween('reviewed_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $achievementsThisMonth = Achievement::whereBetween('created_at', [$startOfThisMonth, $now])->count();
        $achievementsLastMonth = Achievement::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $pendingThisMonth = Achievement::where('status', AchievementStatus::Pending)
            ->whereBetween('created_at', [$startOfThisMonth, $now])->count();
        $pendingLastMonth = Achievement::where('status', AchievementStatus::Pending)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $studentsThisMonth = StudentProfile::whereBetween('created_at', [$startOfThisMonth, $now])->count();
        $studentsLastMonth = StudentProfile::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $classesThisMonth = SchoolClass::whereBetween('created_at', [$startOfThisMonth, $now])->count();
        $classesLastMonth = SchoolClass::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $studentsWithApproved = Achievement::where('status', AchievementStatus::Approved)
            ->distinct('student_id')->count('student_id');
        $studentsApprovedThisMonth = Achievement::where('status', AchievementStatus::Approved)
            ->whereBetween('reviewed_at', [$startOfThisMonth, $now])
            ->distinct('student_id')->count('student_id');
        $studentsApprovedLastMonth = Achievement::where('status', AchievementStatus::Approved)
            ->whereBetween('reviewed_at', [$startOfLastMonth, $endOfLastMonth])
            ->distinct('student_id')->count('student_id');

        $coveragePct = $totalStudents > 0 ? (int) round(($studentsWithApproved / $totalStudents) * 100) : 0;
        $coveragePctThisMonth = $totalStudents > 0 ? round(($studentsApprovedThisMonth / $totalStudents) * 100) : 0;
        $coveragePctLastMonth = $totalStudents > 0 ? round(($studentsApprovedLastMonth / $totalStudents) * 100) : 0;

        $kpis = [
            'total_achievements' => $this->metric($totalAchievements, $achievementsThisMonth - $achievementsLastMonth),
            'pending' => $this->metric($pendingCount, $pendingThisMonth - $pendingLastMonth),
            'approved_this_month' => $this->metric($approvedThisMonth, $approvedThisMonth - $approvedLastMonth),
            'total_students' => $this->metric($totalStudents, $studentsThisMonth - $studentsLastMonth),
            'total_classes' => $this->metric($totalClasses, $classesThisMonth - $classesLastMonth),
            'coverage_pct' => $this->metric($coveragePct, (int) ($coveragePctThisMonth - $coveragePctLastMonth)),
        ];

        $pendingOlderThan3Days = Achievement::where('status', AchievementStatus::Pending)
            ->where('created_at', '<=', $now->copy()->subDays(3))
            ->count();

        $classesWithoutApprovedThisYear = SchoolClass::query()
            ->whereDoesntHave('students.user.achievements', function ($query) use ($now) {
                $query->where('status', AchievementStatus::Approved)
                    ->whereYear('reviewed_at', $now->year);
            })
            ->count();

        $attention = collect([
            [
                'label' => 'Prestasi menunggu verifikasi >3 hari',
                'count' => $pendingOlderThan3Days,
                'hint' => 'Ingatkan guru mapel/wali kelas untuk segera memverifikasi.',
            ],
            [
                'label' => 'Kelas belum punya prestasi disetujui tahun ini',
                'count' => $classesWithoutApprovedThisYear,
                'hint' => 'Dorong wali kelas mengajak siswa mengikuti lomba.',
            ],
        ])->filter(fn (array $item) => $item['count'] > 0)->take(5)->values()->all();

        $activity = Achievement::query()
            ->with('student')
            ->latest('created_at')
            ->take(8)
            ->get()
            ->map(fn (Achievement $achievement) => [
                'student_name' => $achievement->student->name,
                'title' => $achievement->title,
                'status' => $achievement->status->value,
                'status_label' => $achievement->status->label(),
                'created_at_human' => $achievement->created_at->diffForHumans(),
            ])->values()->all();

        $trend = collect(range(5, 0))->map(function (int $monthsAgo) use ($now) {
            $month = $now->copy()->subMonthsNoOverflow($monthsAgo);
            $count = Achievement::where('status', AchievementStatus::Approved)
                ->whereYear('reviewed_at', $month->year)
                ->whereMonth('reviewed_at', $month->month)
                ->count();

            return [
                'label' => $month->locale('id')->translatedFormat('M'),
                'approved_count' => $count,
            ];
        })->values()->all();

        $coverage = [
            'total_students' => $totalStudents,
            'with_approved' => $studentsWithApproved,
            'without_approved' => max($totalStudents - $studentsWithApproved, 0),
        ];

        $approvedByCategory = Achievement::where('status', AchievementStatus::Approved)
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->get()
            ->mapWithKeys(fn (Achievement $row) => [$row->category->value => $row->total]);

        $categories = collect(AchievementCategory::cases())->map(fn (AchievementCategory $category) => [
            'label' => $category->label(),
            'count' => $approvedByCategory[$category->value] ?? 0,
        ])->values()->all();

        $approvedByLevel = Achievement::where('status', AchievementStatus::Approved)
            ->selectRaw('level, count(*) as total')
            ->groupBy('level')
            ->get()
            ->mapWithKeys(fn (Achievement $row) => [$row->level->value => $row->total]);

        $levels = collect(AchievementLevel::cases())->map(fn (AchievementLevel $level) => [
            'label' => $level->label(),
            'count' => $approvedByLevel[$level->value] ?? 0,
        ])->values()->all();

        $highlights = Achievement::query()
            ->with('student.studentProfile.schoolClass')
            ->where('status', AchievementStatus::Approved)
            ->latest('reviewed_at')
            ->take(5)
            ->get()
            ->map(fn (Achievement $achievement) => [
                'title' => $achievement->title,
                'student_name' => $achievement->student->name,
                'rank_label' => $achievement->rank_label,
                'level' => $achievement->level->label(),
                'event_date' => $achievement->event_date->locale('id')->translatedFormat('d M Y'),
            ])->values()->all();

        $leaderboard = Achievement::query()
            ->select('student_id', DB::raw('count(*) as total'))
            ->where('status', AchievementStatus::Approved)
            ->whereBetween('reviewed_at', [$startOfThisMonth, $now])
            ->groupBy('student_id')
            ->orderByDesc('total')
            ->take(5)
            ->with('student.studentProfile.schoolClass')
            ->get()
            ->map(fn ($row) => [
                'name' => $row->student->name,
                'class_name' => optional($row->student->studentProfile?->schoolClass)->name ?? '-',
                'count' => (int) $row->total,
            ])->values()->all();

        $actions = [
            ['label' => 'Kelola Kelas', 'url' => route('admin.classes.index')],
            ['label' => 'Kelola Guru', 'url' => route('admin.teachers.index')],
            ['label' => 'Kelola Info Lomba', 'url' => route('admin.competitions.index')],
        ];

        return [
            'header' => [
                'greeting' => $this->greeting($now),
                'date_label' => $now->locale('id')->translatedFormat('l, d F Y'),
                'pending_today' => $pendingCount,
            ],
            'kpis' => $kpis,
            'attention' => $attention,
            'activity' => $activity,
            'charts' => [
                'trend' => $trend,
                'coverage' => $coverage,
                'categories' => $categories,
                'levels' => $levels,
            ],
            'highlights' => $highlights,
            'leaderboard' => $leaderboard,
            'actions' => $actions,
        ];
    }

    private function metric(int $value, int $delta): array
    {
        return ['value' => $value, 'delta' => $delta];
    }

    private function greeting(Carbon $now): string
    {
        $hour = (int) $now->format('G');

        return match (true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 19 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }
}

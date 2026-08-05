<?php

namespace Tests\Feature;

use App\Enums\AchievementCategory;
use App\Enums\AchievementLevel;
use App\Enums\AchievementStatus;
use App\Enums\ParticipationType;
use App\Enums\UserRole;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruDashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_dashboard_shows_coverage_and_category_stats(): void
    {
        $class = SchoolClass::create([
            'name' => 'XI MIPA 1',
            'grade_level' => 'XI',
            'major' => 'MIPA',
        ]);

        $wali = User::factory()->create([
            'username' => 'wali.dashboard@test.sch.id',
            'role' => UserRole::Guru,
            'must_change_password' => false,
        ]);
        $wali->teacherProfile()->create([
            'subject' => 'Matematika',
        ]);
        $wali->taughtClasses()->attach($class->id);

        $studentWithAchievement = User::factory()->create([
            'username' => '10001',
            'role' => UserRole::Siswa,
        ]);
        $studentWithAchievement->studentProfile()->create([
            'nis' => '10001',
            'school_class_id' => $class->id,
        ]);

        $studentWithoutAchievement = User::factory()->create([
            'username' => '10002',
            'role' => UserRole::Siswa,
        ]);
        $studentWithoutAchievement->studentProfile()->create([
            'nis' => '10002',
            'school_class_id' => $class->id,
        ]);

        $studentWithAchievement->achievements()->create([
            'title' => 'Juara 1 Olimpiade Matematika',
            'category' => AchievementCategory::Akademik,
            'level' => AchievementLevel::Kabupaten,
            'participation_type' => ParticipationType::Perorangan,
            'rank_label' => 'Juara 1',
            'organizer' => 'Dinas Pendidikan',
            'event_date' => now()->subDays(5),
            'status' => AchievementStatus::Approved,
        ]);

        $studentWithAchievement->achievements()->create([
            'title' => 'Ketua OSIS',
            'category' => AchievementCategory::Organisasi,
            'level' => AchievementLevel::Sekolah,
            'participation_type' => ParticipationType::Perorangan,
            'rank_label' => 'Ketua',
            'organizer' => 'OSIS',
            'event_date' => now()->subDays(3),
            'status' => AchievementStatus::Approved,
        ]);

        $studentWithoutAchievement->achievements()->create([
            'title' => 'Lomba Basket Antar Sekolah',
            'category' => AchievementCategory::NonAkademik,
            'level' => AchievementLevel::Kabupaten,
            'participation_type' => ParticipationType::Tim,
            'rank_label' => 'Peserta',
            'organizer' => 'Disdik',
            'event_date' => now()->subDays(2),
            'status' => AchievementStatus::Pending,
        ]);

        $response = $this->actingAs($wali)->get(route('guru.dashboard'));

        $response->assertOk();
        $response->assertViewHas('chartData', function (array $chartData) {
            return $chartData['coverage']['total_students'] === 2
                && $chartData['coverage']['with_approved'] === 1
                && $chartData['coverage']['without_approved'] === 1
                && collect($chartData['categories'])->firstWhere('label', 'Akademik')['count'] === 1
                && collect($chartData['categories'])->firstWhere('label', 'Organisasi')['count'] === 1
                && collect($chartData['categories'])->firstWhere('label', 'Non-Akademik')['count'] === 0;
        });
    }
}

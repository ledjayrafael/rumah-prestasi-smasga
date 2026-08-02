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

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_school_wide_command_center_stats(): void
    {
        $admin = User::factory()->create([
            'username' => 'admin.dashboard@test.sch.id',
            'role' => UserRole::Admin,
            'must_change_password' => false,
        ]);

        $classWithAchievements = SchoolClass::create([
            'name' => 'XI MIPA 1',
            'grade_level' => 'XI',
            'major' => 'MIPA',
        ]);

        $classWithoutAchievements = SchoolClass::create([
            'name' => 'XI MIPA 2',
            'grade_level' => 'XI',
            'major' => 'MIPA',
        ]);

        $studentWithAchievements = User::factory()->create([
            'username' => '20001',
            'role' => UserRole::Siswa,
        ]);
        $studentWithAchievements->studentProfile()->create([
            'nis' => '20001',
            'school_class_id' => $classWithAchievements->id,
        ]);

        $studentWithoutAchievements = User::factory()->create([
            'username' => '20002',
            'role' => UserRole::Siswa,
        ]);
        $studentWithoutAchievements->studentProfile()->create([
            'nis' => '20002',
            'school_class_id' => $classWithoutAchievements->id,
        ]);

        $studentWithAchievements->achievements()->create([
            'title' => 'Juara 1 Olimpiade Fisika',
            'category' => AchievementCategory::Akademik,
            'level' => AchievementLevel::Provinsi,
            'participation_type' => ParticipationType::Perorangan,
            'rank_label' => 'Juara 1',
            'organizer' => 'Dinas Pendidikan',
            'event_date' => now()->subDays(10),
            'status' => AchievementStatus::Approved,
            'reviewed_at' => now(),
        ]);

        $studentWithAchievements->achievements()->create([
            'title' => 'Juara 2 Lomba Debat',
            'category' => AchievementCategory::Akademik,
            'level' => AchievementLevel::Kabupaten,
            'participation_type' => ParticipationType::Tim,
            'rank_label' => 'Juara 2',
            'organizer' => 'Disdik',
            'event_date' => now()->subMonth()->subDays(2),
            'status' => AchievementStatus::Approved,
            'reviewed_at' => now()->subMonth(),
        ]);

        $oldPending = $studentWithAchievements->achievements()->create([
            'title' => 'Lomba Basket Antar Sekolah',
            'category' => AchievementCategory::NonAkademik,
            'level' => AchievementLevel::Kabupaten,
            'participation_type' => ParticipationType::Tim,
            'rank_label' => 'Peserta',
            'organizer' => 'Disdik',
            'event_date' => now()->subDays(6),
            'status' => AchievementStatus::Pending,
        ]);
        $oldPending->forceFill(['created_at' => now()->subDays(5)])->save();

        $studentWithAchievements->achievements()->create([
            'title' => 'Lomba Cerdas Cermat',
            'category' => AchievementCategory::Akademik,
            'level' => AchievementLevel::Sekolah,
            'participation_type' => ParticipationType::Tim,
            'rank_label' => 'Peserta',
            'organizer' => 'Sekolah',
            'event_date' => now()->subDay(),
            'status' => AchievementStatus::Pending,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Juara 1 Olimpiade Fisika');

        $response->assertViewHas('dashboard', function (array $dashboard) {
            $kpis = $dashboard['kpis'];

            return $dashboard['header']['pending_today'] === 2
                && $kpis['total_achievements']['value'] === 4
                && $kpis['pending']['value'] === 2
                && $kpis['approved_this_month']['value'] === 1
                && $kpis['total_students']['value'] === 2
                && $kpis['total_classes']['value'] === 2
                && $dashboard['charts']['coverage']['total_students'] === 2
                && $dashboard['charts']['coverage']['with_approved'] === 1
                && $dashboard['charts']['coverage']['without_approved'] === 1
                && collect($dashboard['attention'])->contains(
                    fn (array $item) => $item['count'] === 1 && str_contains($item['label'], '3 hari')
                )
                && collect($dashboard['attention'])->contains(
                    fn (array $item) => $item['count'] === 1 && str_contains(mb_strtolower($item['label']), 'kelas')
                )
                && collect($dashboard['highlights'])->contains(
                    fn (array $item) => $item['title'] === 'Juara 1 Olimpiade Fisika'
                );
        });
    }
}

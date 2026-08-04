<?php

namespace Database\Seeders;

use App\Enums\AchievementCategory;
use App\Enums\AchievementLevel;
use App\Enums\ParticipationType;
use App\Enums\TeacherPosition;
use App\Enums\UserRole;
use App\Models\Competition;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed demo data so the core flow (siswa upload -> guru verifikasi) can be tried end-to-end.
     * Ganti/hapus akun-akun ini sebelum aplikasi dipakai produksi di sekolah.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        $admin = User::create([
            'name' => 'Bambang Wijaya',
            'username' => 'admin@smasga.sch.id',
            'email' => 'admin@smasga.sch.id',
            'role' => UserRole::Admin,
            'password' => 'password',
            'must_change_password' => false,
        ]);

        $kelasXiMipa2 = SchoolClass::create([
            'name' => 'XI MIPA 2',
            'grade_level' => 'XI',
            'major' => 'MIPA',
        ]);

        $guru = User::create([
            'name' => 'Siti Rahayu, S.Pd.',
            'username' => 'siti.rahayu@smasga.sch.id',
            'email' => 'siti.rahayu@smasga.sch.id',
            'role' => UserRole::Guru,
            'password' => 'password',
            'must_change_password' => false,
        ]);

        $guru->teacherProfile()->create([
            'subject' => 'Matematika',
            'position' => TeacherPosition::WaliKelas,
        ]);

        $kelasXiMipa2->update(['homeroom_teacher_id' => $guru->id]);
        $guru->taughtClasses()->attach($kelasXiMipa2->id);

        $siswa = User::create([
            'name' => 'Dinda Pratiwi',
            'username' => '21034',
            'role' => UserRole::Siswa,
            'password' => 'password',
            'must_change_password' => false,
        ]);

        $siswa->studentProfile()->create([
            'nis' => '21034',
            'school_class_id' => $kelasXiMipa2->id,
        ]);

        $siswa->achievements()->create([
            'title' => 'Juara 2 Olimpiade Matematika',
            'category' => AchievementCategory::Akademik,
            'level' => AchievementLevel::Kabupaten,
            'participation_type' => ParticipationType::Perorangan,
            'rank_label' => 'Juara 2',
            'organizer' => 'Dinas Pendidikan Kab. Bondowoso',
            'event_date' => now()->subDays(9),
            'description' => 'Meraih juara 2 pada babak final yang diikuti 120 peserta se-Kabupaten Bondowoso.',
        ]);

        Competition::create([
            'title' => 'Olimpiade Sains Nasional (OSN) 2026',
            'category' => AchievementCategory::Akademik,
            'level' => AchievementLevel::Nasional,
            'organizer' => 'Kemendikbud',
            'registration_deadline' => now()->addDays(4),
            'created_by' => $admin->id,
        ]);

        Competition::create([
            'title' => 'Lomba Cerdas Cermat Matematika',
            'category' => AchievementCategory::Akademik,
            'level' => AchievementLevel::Provinsi,
            'organizer' => 'Universitas Jember',
            'registration_deadline' => now()->addDays(18),
            'created_by' => $admin->id,
        ]);

        Competition::create([
            'title' => 'Festival Lomba Seni Siswa Nasional',
            'category' => AchievementCategory::NonAkademik,
            'level' => AchievementLevel::Provinsi,
            'organizer' => 'Disdik Jawa Timur',
            'registration_deadline' => now()->addDays(24),
            'created_by' => $admin->id,
        ]);

        Competition::create([
            'title' => 'Lomba Debat Bahasa Indonesia',
            'category' => AchievementCategory::Organisasi,
            'level' => AchievementLevel::Kabupaten,
            'organizer' => 'Kwarcab Bondowoso',
            'registration_deadline' => now()->addDays(33),
            'created_by' => $admin->id,
        ]);
    }
}

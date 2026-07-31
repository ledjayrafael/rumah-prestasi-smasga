<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Achievement;
use App\Models\AchievementFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecureFileAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('local');
    }

    public function test_student_can_download_own_achievement_file(): void
    {
        $siswa = User::where('username', '21034')->firstOrFail();
        $achievement = $siswa->achievements()->firstOrFail();
        $path = 'bukti-prestasi/test.pdf';
        Storage::disk('local')->put($path, 'pdf-content');

        $file = AchievementFile::create([
            'achievement_id' => $achievement->id,
            'path' => $path,
            'original_name' => 'test.pdf',
            'mime_type' => 'application/pdf',
            'size' => 11,
        ]);

        $this->actingAs($siswa)
            ->get(route('achievement-files.show', $file))
            ->assertOk();
    }

    public function test_other_student_cannot_download_achievement_file(): void
    {
        $owner = User::where('username', '21034')->firstOrFail();
        $achievement = $owner->achievements()->firstOrFail();
        $path = 'bukti-prestasi/secret.pdf';
        Storage::disk('local')->put($path, 'secret');

        $file = AchievementFile::create([
            'achievement_id' => $achievement->id,
            'path' => $path,
            'original_name' => 'secret.pdf',
            'mime_type' => 'application/pdf',
            'size' => 6,
        ]);

        $other = User::factory()->create([
            'role' => UserRole::Siswa,
            'username' => '77777',
            'password' => 'password',
        ]);
        $other->studentProfile()->create([
            'nis' => '77777',
            'school_class_id' => $owner->studentProfile->school_class_id,
        ]);

        $this->actingAs($other)
            ->get(route('achievement-files.show', $file))
            ->assertForbidden();
    }

    public function test_guru_can_download_file_for_class_student(): void
    {
        $guru = User::where('role', UserRole::Guru)->firstOrFail();
        $achievement = Achievement::firstOrFail();
        $path = 'bukti-prestasi/guru-view.pdf';
        Storage::disk('local')->put($path, 'content');

        $file = AchievementFile::create([
            'achievement_id' => $achievement->id,
            'path' => $path,
            'original_name' => 'guru-view.pdf',
            'mime_type' => 'application/pdf',
            'size' => 7,
        ]);

        $this->actingAs($guru)
            ->get(route('achievement-files.show', $file))
            ->assertOk();
    }

    public function test_guest_cannot_download_achievement_file(): void
    {
        $achievement = Achievement::firstOrFail();
        $path = 'bukti-prestasi/guest.pdf';
        Storage::disk('local')->put($path, 'x');

        $file = AchievementFile::create([
            'achievement_id' => $achievement->id,
            'path' => $path,
            'original_name' => 'guest.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1,
        ]);

        $this->get(route('achievement-files.show', $file))
            ->assertRedirect(route('login'));
    }
}

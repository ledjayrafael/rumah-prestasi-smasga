<?php

namespace Tests\Feature;

use App\Enums\AchievementCategory;
use App\Enums\AchievementLevel;
use App\Enums\AchievementStatus;
use App\Enums\ParticipationType;
use App\Models\Achievement;
use App\Models\AchievementFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AchievementSubmissionResilienceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('local');
    }

    public function test_store_cleans_up_uploaded_files_when_saving_fails(): void
    {
        $siswa = User::where('username', '21034')->firstOrFail();

        // Simulate "faktor X": the DB explodes right after the achievement
        // is created but while files are still being attached.
        Event::listen('eloquent.creating: '.AchievementFile::class, function () {
            throw new \RuntimeException('Simulated disk/DB failure');
        });

        $response = $this->actingAs($siswa)
            ->postJson(route('siswa.achievements.store'), [
                'title' => 'Juara Lomba Robotik',
                'category' => AchievementCategory::cases()[0]->value,
                'level' => AchievementLevel::cases()[0]->value,
                'participation_type' => ParticipationType::cases()[0]->value,
                'rank_label' => 'Juara 1',
                'organizer' => 'Dinas Pendidikan',
                'event_date' => now()->toDateString(),
                'files' => [UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf')],
            ]);

        $response->assertStatus(500);
        $response->assertJsonFragment(['message' => 'Terjadi kesalahan pada server saat menyimpan prestasi. Data yang sudah Anda isi tidak hilang — silakan coba kirim lagi.']);

        $this->assertDatabaseMissing('achievements', ['title' => 'Juara Lomba Robotik']);

        $orphans = collect(Storage::disk('local')->allFiles('bukti-prestasi'));
        $this->assertTrue($orphans->isEmpty(), 'Uploaded file was not cleaned up after failed submission: '.$orphans->implode(', '));
    }

    public function test_update_preserves_old_evidence_files_when_saving_new_ones_fails(): void
    {
        $siswa = User::where('username', '21034')->firstOrFail();
        $achievement = $siswa->achievements()->create([
            'title' => 'Prestasi Lama',
            'category' => AchievementCategory::cases()[0]->value,
            'level' => AchievementLevel::cases()[0]->value,
            'participation_type' => ParticipationType::cases()[0]->value,
            'rank_label' => 'Juara 3',
            'organizer' => 'Panitia',
            'event_date' => now()->subDay()->toDateString(),
            'status' => AchievementStatus::Revision,
            'reviewer_notes' => 'Perbaiki data',
        ]);

        $oldPath = 'bukti-prestasi/old-evidence.pdf';
        Storage::disk('local')->put($oldPath, 'old-content');
        $oldFile = AchievementFile::create([
            'achievement_id' => $achievement->id,
            'path' => $oldPath,
            'original_name' => 'old-evidence.pdf',
            'mime_type' => 'application/pdf',
            'size' => 11,
        ]);

        Event::listen('eloquent.creating: '.AchievementFile::class, function () {
            throw new \RuntimeException('Simulated disk/DB failure');
        });

        $response = $this->actingAs($siswa)
            ->postJson(route('siswa.achievements.update', $achievement), [
                '_method' => 'PUT',
                'title' => 'Prestasi Diperbaiki',
                'category' => AchievementCategory::cases()[0]->value,
                'level' => AchievementLevel::cases()[0]->value,
                'participation_type' => ParticipationType::cases()[0]->value,
                'rank_label' => 'Juara 2',
                'organizer' => 'Panitia',
                'event_date' => now()->toDateString(),
                'files' => [UploadedFile::fake()->create('new-evidence.pdf', 100, 'application/pdf')],
            ]);

        $response->assertStatus(500);

        // Old DB record and old physical file must both survive the failed attempt.
        $this->assertDatabaseHas('achievement_files', ['id' => $oldFile->id, 'path' => $oldPath]);
        $this->assertTrue(Storage::disk('local')->exists($oldPath), 'Old evidence file was deleted despite the update failing.');

        $achievement->refresh();
        $this->assertSame('Prestasi Lama', $achievement->title);
    }
}

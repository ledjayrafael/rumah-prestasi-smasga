<?php

namespace Tests\Feature;

use App\Enums\TeacherPosition;
use App\Enums\UserRole;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StudentAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_cannot_access_student_management_routes(): void
    {
        $admin = User::where('role', UserRole::Admin)->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/siswa')
            ->assertNotFound();
    }

    public function test_guru_mapel_cannot_access_student_routes(): void
    {
        $guruMapel = User::factory()->create([
            'name' => 'Guru Mapel',
            'username' => 'mapel@test.sch.id',
            'email' => 'mapel@test.sch.id',
            'role' => UserRole::Guru,
            'password' => 'password',
            'must_change_password' => false,
        ]);

        $guruMapel->teacherProfile()->create([
            'subject' => 'Fisika',
            'position' => TeacherPosition::GuruMapel,
        ]);

        $this->actingAs($guruMapel)
            ->get(route('guru.students.index'))
            ->assertForbidden();
    }

    public function test_wali_kelas_sees_only_homeroom_students(): void
    {
        $wali = User::where('role', UserRole::Guru)->firstOrFail();
        $this->assertTrue($wali->isWaliKelas());

        $otherClass = SchoolClass::create([
            'name' => 'XII IPS 1',
            'grade_level' => 'XII',
            'major' => 'IPS',
        ]);

        $otherStudent = User::factory()->create([
            'role' => UserRole::Siswa,
            'username' => '99999',
            'password' => 'password',
        ]);
        $otherStudent->studentProfile()->create([
            'nis' => '99999',
            'school_class_id' => $otherClass->id,
        ]);

        $response = $this->actingAs($wali)
            ->get(route('guru.students.index'));

        $response->assertOk();
        $response->assertSee('Dinda Pratiwi');
        $response->assertDontSee('99999');
    }

    public function test_wali_kelas_cannot_store_student_in_other_class(): void
    {
        $wali = User::where('role', UserRole::Guru)->firstOrFail();

        $otherClass = SchoolClass::create([
            'name' => 'XII IPS 2',
            'grade_level' => 'XII',
            'major' => 'IPS',
        ]);

        $this->actingAs($wali)
            ->post(route('guru.students.store'), [
                'name' => 'Siswa Baru',
                'nis' => '88888',
                'school_class_id' => $otherClass->id,
            ])
            ->assertSessionHasErrors('school_class_id');
    }

    public function test_import_creates_student_for_homeroom_class(): void
    {
        $wali = User::where('role', UserRole::Guru)->firstOrFail();
        $className = $wali->homeroomClasses()->firstOrFail()->name;

        $csv = "nama,nis,kelas\nSiswa Import,55555,{$className}\n";
        $file = UploadedFile::fake()->createWithContent('siswa.csv', $csv);

        $this->actingAs($wali)
            ->post(route('guru.students.import.store'), ['file' => $file])
            ->assertRedirect(route('guru.students.import.create'))
            ->assertSessionHas('import_success', 1);

        $this->assertDatabaseHas('student_profiles', ['nis' => '55555']);
    }

    public function test_import_rejects_class_not_homeroom(): void
    {
        $wali = User::where('role', UserRole::Guru)->firstOrFail();

        $csv = "nama,nis,kelas\nSiswa Import,44444,XII IPS 99\n";
        $file = UploadedFile::fake()->createWithContent('siswa.csv', $csv);

        $this->actingAs($wali)
            ->post(route('guru.students.import.store'), ['file' => $file])
            ->assertRedirect(route('guru.students.import.create'))
            ->assertSessionHas('import_success', 0);

        $this->assertDatabaseMissing('student_profiles', ['nis' => '44444']);
    }
}

<?php

namespace Tests\Feature;

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

    public function test_admin_can_access_student_management_index(): void
    {
        $admin = User::where('role', UserRole::Admin)->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/siswa')
            ->assertOk();
    }

    public function test_guru_without_homeroom_class_cannot_access_student_routes(): void
    {
        $guruTanpaKelasBinaan = User::factory()->create([
            'name' => 'Guru Tanpa Kelas Binaan',
            'username' => 'tanpa-kelas@test.sch.id',
            'email' => 'tanpa-kelas@test.sch.id',
            'role' => UserRole::Guru,
            'password' => 'password',
            'must_change_password' => false,
        ]);

        $guruTanpaKelasBinaan->teacherProfile()->create([
            'subject' => 'Fisika',
        ]);

        $this->actingAs($guruTanpaKelasBinaan)
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

    public function test_wali_kelas_can_bulk_delete_homeroom_students(): void
    {
        $wali = User::where('role', UserRole::Guru)->firstOrFail();
        $homeroomClass = $wali->homeroomClasses()->firstOrFail();

        $students = collect(range(0, 1))->map(fn (int $i) => User::factory()->create([
            'role' => UserRole::Siswa,
            'username' => '7000'.$i,
            'password' => 'password',
        ]));
        $students->each(fn (User $student, int $i) => $student->studentProfile()->create([
            'nis' => '7000'.$i,
            'school_class_id' => $homeroomClass->id,
        ]));

        $this->actingAs($wali)
            ->post(route('guru.students.bulk-destroy'), [
                'student_ids' => $students->pluck('id')->all(),
            ])
            ->assertRedirect();

        foreach ($students as $student) {
            $this->assertDatabaseMissing('users', ['id' => $student->id]);
        }
    }

    public function test_wali_kelas_cannot_bulk_delete_students_from_other_class(): void
    {
        $wali = User::where('role', UserRole::Guru)->firstOrFail();

        $otherClass = SchoolClass::create([
            'name' => 'XII IPS 3',
            'grade_level' => 'XII',
            'major' => 'IPS',
        ]);

        $otherStudent = User::factory()->create([
            'role' => UserRole::Siswa,
            'username' => '77777',
            'password' => 'password',
        ]);
        $otherStudent->studentProfile()->create([
            'nis' => '77777',
            'school_class_id' => $otherClass->id,
        ]);

        $this->actingAs($wali)
            ->post(route('guru.students.bulk-destroy'), [
                'student_ids' => [$otherStudent->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $otherStudent->id]);
    }
}

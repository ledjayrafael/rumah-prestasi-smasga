<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStudentBulkMoveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_sees_all_students_with_class_or_placeholder(): void
    {
        $admin = User::where('role', UserRole::Admin)->firstOrFail();

        $classless = User::factory()->create([
            'name' => 'Siswa Tanpa Kelas',
            'username' => '77777',
            'role' => UserRole::Siswa,
        ]);
        $classless->studentProfile()->create([
            'nis' => '77777',
            'school_class_id' => null,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.students.index'));

        $response->assertOk();
        $response->assertSee('Dinda Pratiwi');
        $response->assertSee('XI MIPA 2');
        $response->assertSee('Siswa Tanpa Kelas');
        $response->assertSee('Belum ada kelas', false);
    }

    public function test_guru_cannot_access_admin_student_index(): void
    {
        $guru = User::where('role', UserRole::Guru)->firstOrFail();

        $this->actingAs($guru)
            ->get(route('admin.students.index'))
            ->assertForbidden();
    }

    public function test_admin_can_bulk_move_students_to_new_class(): void
    {
        $admin = User::where('role', UserRole::Admin)->firstOrFail();
        $sourceClass = SchoolClass::where('name', 'XI MIPA 2')->firstOrFail();

        $targetClass = SchoolClass::create([
            'name' => 'XI MIPA 3',
            'grade_level' => 'XI',
            'major' => 'MIPA',
        ]);

        $studentA = User::factory()->create(['role' => UserRole::Siswa, 'username' => '30001']);
        $studentA->studentProfile()->create(['nis' => '30001', 'school_class_id' => $sourceClass->id]);

        $studentB = User::factory()->create(['role' => UserRole::Siswa, 'username' => '30002']);
        $studentB->studentProfile()->create(['nis' => '30002', 'school_class_id' => null]);

        $response = $this->actingAs($admin)->post(route('admin.students.bulk-move'), [
            'student_ids' => [$studentA->id, $studentB->id],
            'school_class_id' => $targetClass->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('student_profiles', [
            'user_id' => $studentA->id,
            'school_class_id' => $targetClass->id,
        ]);
        $this->assertDatabaseHas('student_profiles', [
            'user_id' => $studentB->id,
            'school_class_id' => $targetClass->id,
        ]);
    }

    public function test_bulk_move_rejects_more_than_fifty_students(): void
    {
        $admin = User::where('role', UserRole::Admin)->firstOrFail();
        $targetClass = SchoolClass::where('name', 'XI MIPA 2')->firstOrFail();

        $ids = range(1, 51);

        $this->actingAs($admin)
            ->post(route('admin.students.bulk-move'), [
                'student_ids' => $ids,
                'school_class_id' => $targetClass->id,
            ])
            ->assertSessionHasErrors('student_ids');
    }

    public function test_bulk_move_rejects_invalid_school_class_id(): void
    {
        $admin = User::where('role', UserRole::Admin)->firstOrFail();

        $student = User::factory()->create(['role' => UserRole::Siswa, 'username' => '30003']);
        $student->studentProfile()->create(['nis' => '30003', 'school_class_id' => null]);

        $this->actingAs($admin)
            ->post(route('admin.students.bulk-move'), [
                'student_ids' => [$student->id],
                'school_class_id' => 999999,
            ])
            ->assertSessionHasErrors('school_class_id');
    }

    public function test_non_admin_cannot_bulk_move_students(): void
    {
        $guru = User::where('role', UserRole::Guru)->firstOrFail();
        $targetClass = SchoolClass::where('name', 'XI MIPA 2')->firstOrFail();

        $student = User::factory()->create(['role' => UserRole::Siswa, 'username' => '30004']);
        $student->studentProfile()->create(['nis' => '30004', 'school_class_id' => null]);

        $this->actingAs($guru)
            ->post(route('admin.students.bulk-move'), [
                'student_ids' => [$student->id],
                'school_class_id' => $targetClass->id,
            ])
            ->assertForbidden();
    }
}

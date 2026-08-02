<?php

namespace Tests\Feature;

use App\Enums\TeacherPosition;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTeacherResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function makeTeacher(): User
    {
        $teacher = User::factory()->create([
            'role' => UserRole::Guru,
            'email' => 'guru.reset@example.test',
            'username' => 'guru.reset@example.test',
            'password' => 'old-password-123',
            'must_change_password' => false,
        ]);
        $teacher->teacherProfile()->create(['position' => TeacherPosition::GuruMapel]);

        return $teacher;
    }

    public function test_admin_can_reset_teacher_password(): void
    {
        $admin = User::where('role', UserRole::Admin)->firstOrFail();
        $teacher = $this->makeTeacher();
        $oldHash = $teacher->password;

        $response = $this->actingAs($admin)->post(route('admin.teachers.reset-password', $teacher));

        $response->assertRedirect(route('admin.teachers.index'));
        $response->assertSessionHas('credential.login', $teacher->email);

        $teacher->refresh();
        $this->assertNotSame($oldHash, $teacher->password);
        $this->assertTrue($teacher->must_change_password);

        $indexResponse = $this->actingAs($admin)->get(route('admin.teachers.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Kredensial Akun Guru');
        $indexResponse->assertSee($teacher->email);
    }

    public function test_teacher_is_forced_to_change_password_after_reset(): void
    {
        $admin = User::where('role', UserRole::Admin)->firstOrFail();
        $teacher = $this->makeTeacher();

        $this->actingAs($admin)->post(route('admin.teachers.reset-password', $teacher));
        $teacher->refresh();

        $response = $this->actingAs($teacher)->get(route('admin.teachers.index'));

        $response->assertRedirect(route('password.force.edit'));
    }

    public function test_non_admin_cannot_reset_teacher_password(): void
    {
        $guru = User::where('role', UserRole::Guru)->firstOrFail();
        $teacher = $this->makeTeacher();

        $this->actingAs($guru)
            ->post(route('admin.teachers.reset-password', $teacher))
            ->assertForbidden();
    }
}

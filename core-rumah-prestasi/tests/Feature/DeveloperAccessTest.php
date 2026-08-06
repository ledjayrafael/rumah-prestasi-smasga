<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Competition;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeveloperAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function developer(): User
    {
        return User::where('role', UserRole::Developer)->firstOrFail();
    }

    private function admin(): User
    {
        return User::where('role', UserRole::Admin)->firstOrFail();
    }

    public function test_developer_login_redirects_to_admin_dashboard(): void
    {
        $response = $this->post('/login', [
            'username' => 'developer@smasga.sch.id',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_developer_can_access_admin_read_pages(): void
    {
        $developer = $this->developer();

        $this->actingAs($developer)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($developer)->get(route('admin.students.index'))->assertOk();
        $this->actingAs($developer)->get(route('admin.teachers.index'))->assertOk();
        $this->actingAs($developer)->get(route('admin.classes.index'))->assertOk();
        $this->actingAs($developer)->get(route('admin.competitions.index'))->assertOk();
        $this->actingAs($developer)->get(route('admin.admins.index'))->assertOk();
    }

    public function test_developer_cannot_write_operational_admin_data(): void
    {
        $developer = $this->developer();
        $teacher = User::where('role', UserRole::Guru)->firstOrFail();
        $class = SchoolClass::firstOrFail();
        $competition = Competition::firstOrFail();
        $student = User::where('role', UserRole::Siswa)->firstOrFail();

        $this->actingAs($developer)
            ->post(route('admin.teachers.store'), [
                'name' => 'Guru Baru',
                'email' => 'guru.baru@example.test',
            ])
            ->assertForbidden();

        $this->actingAs($developer)
            ->post(route('admin.teachers.reset-password', $teacher))
            ->assertForbidden();

        $this->actingAs($developer)
            ->delete(route('admin.classes.destroy', $class))
            ->assertForbidden();

        $this->actingAs($developer)
            ->delete(route('admin.competitions.destroy', $competition))
            ->assertForbidden();

        $this->actingAs($developer)
            ->post(route('admin.students.bulk-move'), [
                'student_ids' => [$student->id],
                'school_class_id' => $class->id,
            ])
            ->assertForbidden();
    }

    public function test_admin_cannot_access_admin_account_management(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.admins.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.admins.create'))->assertForbidden();
        $this->actingAs($admin)
            ->post(route('admin.admins.store'), [
                'name' => 'Admin Baru',
                'email' => 'admin.baru@example.test',
            ])
            ->assertForbidden();
    }

    public function test_developer_can_create_update_and_delete_admin_accounts(): void
    {
        $developer = $this->developer();

        $create = $this->actingAs($developer)->post(route('admin.admins.store'), [
            'name' => 'Admin Baru',
            'email' => 'admin.baru@example.test',
        ]);

        $create->assertRedirect(route('admin.admins.index'));
        $create->assertSessionHas('credential.login', 'admin.baru@example.test');

        $newAdmin = User::where('email', 'admin.baru@example.test')->firstOrFail();
        $this->assertSame(UserRole::Admin, $newAdmin->role);
        $this->assertTrue($newAdmin->must_change_password);

        $this->actingAs($developer)->put(route('admin.admins.update', $newAdmin), [
            'name' => 'Admin Updated',
            'email' => 'admin.updated@example.test',
            'is_active' => '1',
        ])->assertRedirect(route('admin.admins.index'));

        $newAdmin->refresh();
        $this->assertSame('Admin Updated', $newAdmin->name);
        $this->assertSame('admin.updated@example.test', $newAdmin->email);

        $this->actingAs($developer)
            ->delete(route('admin.admins.destroy', $newAdmin))
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $newAdmin->id]);
    }

    public function test_developer_cannot_manage_non_admin_via_admins_routes(): void
    {
        $developer = $this->developer();
        $guru = User::where('role', UserRole::Guru)->firstOrFail();

        $this->actingAs($developer)
            ->get(route('admin.admins.edit', $guru))
            ->assertNotFound();

        $this->actingAs($developer)
            ->put(route('admin.admins.update', $guru), [
                'name' => 'Hacked',
                'email' => $guru->email,
            ])
            ->assertNotFound();

        $this->actingAs($developer)
            ->delete(route('admin.admins.destroy', $guru))
            ->assertNotFound();
    }

    public function test_developer_can_reset_admin_password(): void
    {
        $developer = $this->developer();
        $admin = $this->admin();
        $oldHash = $admin->password;

        $response = $this->actingAs($developer)->post(route('admin.admins.reset-password', $admin));

        $response->assertRedirect(route('admin.admins.index'));
        $response->assertSessionHas('credential.login', $admin->email);

        $admin->refresh();
        $this->assertNotSame($oldHash, $admin->password);
        $this->assertTrue($admin->must_change_password);
    }

    public function test_developer_read_pages_hide_write_actions(): void
    {
        $developer = $this->developer();

        $this->actingAs($developer)
            ->get(route('admin.teachers.index'))
            ->assertOk()
            ->assertDontSee('+ Tambah Guru');

        $this->actingAs($developer)
            ->get(route('admin.classes.index'))
            ->assertOk()
            ->assertDontSee('+ Tambah Kelas');

        $this->actingAs($developer)
            ->get(route('admin.competitions.index'))
            ->assertOk()
            ->assertDontSee('+ Tambah Lomba');
    }
}

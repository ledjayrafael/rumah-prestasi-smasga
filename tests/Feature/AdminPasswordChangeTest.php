<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_view_password_change_page(): void
    {
        $admin = User::where('role', UserRole::Admin)->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.password.edit'))
            ->assertOk()
            ->assertSee('Ganti Password');
    }

    public function test_developer_can_view_password_change_page(): void
    {
        $developer = User::where('role', UserRole::Developer)->firstOrFail();

        $this->actingAs($developer)
            ->get(route('admin.password.edit'))
            ->assertOk();
    }

    public function test_admin_can_change_password_with_valid_current_password(): void
    {
        $admin = User::where('role', UserRole::Admin)->firstOrFail();
        $admin->update(['password' => 'password']);

        $response = $this->actingAs($admin)->put(route('admin.password.update'), [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $admin->refresh();
        $this->assertTrue(Hash::check('newpassword123', $admin->password));
    }

    public function test_admin_cannot_change_password_with_wrong_current_password(): void
    {
        $admin = User::where('role', UserRole::Admin)->firstOrFail();
        $admin->update(['password' => 'password']);

        $response = $this->actingAs($admin)->from(route('admin.password.edit'))->put(route('admin.password.update'), [
            'current_password' => 'salah',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('admin.password.edit'));
        $response->assertSessionHasErrors('current_password');

        $admin->refresh();
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    public function test_guru_cannot_access_admin_password_page(): void
    {
        $guru = User::where('role', UserRole::Guru)->firstOrFail();

        $this->actingAs($guru)
            ->get(route('admin.password.edit'))
            ->assertForbidden();
    }
}

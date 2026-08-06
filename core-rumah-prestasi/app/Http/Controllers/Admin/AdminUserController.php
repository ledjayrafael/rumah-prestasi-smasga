<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $admins = User::query()
            ->where('role', UserRole::Admin)
            ->orderBy('name')
            ->paginate(15);

        return view('admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        return view('admin.admins.create');
    }

    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $temporaryPassword = Str::password(10, symbols: false);

        $admin = User::create([
            'name' => $validated['name'],
            'username' => $validated['email'],
            'email' => $validated['email'],
            'role' => UserRole::Admin,
            'password' => $temporaryPassword,
            'must_change_password' => true,
        ]);

        return redirect()->route('admin.admins.index')->with('credential', [
            'name' => $admin->name,
            'login' => $admin->email,
            'password' => $temporaryPassword,
        ]);
    }

    public function resetPassword(User $admin): RedirectResponse
    {
        abort_unless($admin->role === UserRole::Admin, 404);

        $temporaryPassword = Str::password(10, symbols: false);

        $admin->update([
            'password' => $temporaryPassword,
            'must_change_password' => true,
        ]);

        return redirect()->route('admin.admins.index')->with('credential', [
            'name' => $admin->name,
            'login' => $admin->email,
            'password' => $temporaryPassword,
        ]);
    }

    public function edit(User $admin): View
    {
        abort_unless($admin->role === UserRole::Admin, 404);

        return view('admin.admins.edit', compact('admin'));
    }

    public function update(UpdateAdminRequest $request, User $admin): RedirectResponse
    {
        abort_unless($admin->role === UserRole::Admin, 404);

        $validated = $request->validated();

        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['email'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.admins.index')->with('status', 'Akun admin berhasil diperbarui.');
    }

    public function destroy(User $admin): RedirectResponse
    {
        abort_unless($admin->role === UserRole::Admin, 404);

        $admin->delete();

        return back()->with('status', 'Akun admin berhasil dihapus.');
    }
}

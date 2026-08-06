<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Username/NIS atau kata sandi salah.']);
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();

            return back()->withErrors(['username' => 'Akun Anda tidak aktif. Hubungi admin sekolah.']);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->dashboardFor($user->role));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function dashboardFor(UserRole $role): string
    {
        return match ($role) {
            UserRole::Siswa => route('siswa.dashboard'),
            UserRole::Guru => route('guru.dashboard'),
            UserRole::Admin, UserRole::Developer => route('admin.dashboard'),
        };
    }
}

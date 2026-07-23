<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        return match ($user->role) {
            UserRole::Siswa => redirect()->route('siswa.dashboard'),
            UserRole::Guru => redirect()->route('guru.dashboard'),
            UserRole::Admin => redirect()->route('admin.dashboard'),
        };
    }
}

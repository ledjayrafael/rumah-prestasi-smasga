<?php

namespace App\Providers;

use App\Enums\AchievementStatus;
use App\Enums\UserRole;
use App\Models\Achievement;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $sharedPublic = dirname(base_path()).'/public_html/rumah-prestasi';

        if (is_dir($sharedPublic)) {
            $this->app->usePublicPath($sharedPublic);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($root = config('app.url')) {
            URL::forceRootUrl($root);
        }

        View::composer('layouts.desktop', function ($view) {
            $user = auth()->user();

            if (! $user) {
                return;
            }

            $view->with('navItems', match ($user->role) {
                UserRole::Guru => $this->guruNavItems($user),
                UserRole::Admin => $this->adminNavItems(),
                UserRole::Developer => $this->developerNavItems(),
                default => [],
            });
        });
    }

    private function guruNavItems($teacher): array
    {
        $pendingCount = Achievement::whereHas(
            'student.studentProfile',
            fn ($q) => $q->whereIn('school_class_id', $teacher->taughtClasses()->pluck('school_classes.id'))
        )->where('status', AchievementStatus::Pending)->count();

        $items = [
            [
                'label' => 'Dashboard',
                'url' => route('guru.dashboard'),
                'active' => request()->routeIs('guru.dashboard'),
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>',
            ],
            [
                'label' => 'Verifikasi Prestasi',
                'url' => route('guru.verification.index'),
                'active' => request()->routeIs('guru.verification.*'),
                'badge' => $pendingCount,
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>',
            ],
        ];

        if ($teacher->isWaliKelas()) {
            $items[] = [
                'label' => 'Kelola Siswa',
                'url' => route('guru.students.index'),
                'active' => request()->routeIs('guru.students.*'),
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/></svg>',
            ];
        }

        return $items;
    }

    private function adminNavItems(): array
    {
        return [
            [
                'label' => 'Dashboard',
                'url' => route('admin.dashboard'),
                'active' => request()->routeIs('admin.dashboard'),
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>',
            ],
            [
                'label' => 'Kelola Kelas',
                'url' => route('admin.classes.index'),
                'active' => request()->routeIs('admin.classes.*'),
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5-10-5Z"/><path d="M6 12v5c0 1.1 2.7 2 6 2s6-.9 6-2v-5"/></svg>',
            ],
            [
                'label' => 'Kelola Guru',
                'url' => route('admin.teachers.index'),
                'active' => request()->routeIs('admin.teachers.*'),
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
            ],
            [
                'label' => 'Kelola Siswa',
                'url' => route('admin.students.index'),
                'active' => request()->routeIs('admin.students.*'),
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/></svg>',
            ],
            [
                'label' => 'Info Lomba',
                'url' => route('admin.competitions.index'),
                'active' => request()->routeIs('admin.competitions.*'),
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>',
            ],
            [
                'label' => 'Ganti Password',
                'url' => route('admin.password.edit'),
                'active' => request()->routeIs('admin.password.*'),
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
            ],
        ];
    }

    private function developerNavItems(): array
    {
        $items = $this->adminNavItems();

        // Sisipkan "Kelola Admin" sebelum "Ganti Password"
        $passwordItem = array_pop($items);

        $items[] = [
            'label' => 'Kelola Admin',
            'url' => route('admin.admins.index'),
            'active' => request()->routeIs('admin.admins.*'),
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>',
        ];

        $items[] = $passwordItem;

        return $items;
    }
}

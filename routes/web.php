<?php

use App\Http\Controllers\AchievementFileController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\CompetitionController as AdminCompetitionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PasswordController as AdminPasswordController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ForcePasswordController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\NotificationController as GuruNotificationController;
use App\Http\Controllers\Guru\StudentController as GuruStudentController;
use App\Http\Controllers\Guru\StudentImportController;
use App\Http\Controllers\Guru\VerificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Siswa\AchievementController;
use App\Http\Controllers\Siswa\AvatarController;
use App\Http\Controllers\Siswa\CompetitionController as SiswaCompetitionController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/ganti-password-wajib', [ForcePasswordController::class, 'edit'])->name('password.force.edit');
    Route::put('/ganti-password-wajib', [ForcePasswordController::class, 'update'])->name('password.force.update');
});

Route::middleware(['auth', 'password.current'])->group(function () {
    Route::get('/prestasi/berkas/{file}', [AchievementFileController::class, 'show'])
        ->name('achievement-files.show');
    Route::get('/avatar/{user}', [AvatarController::class, 'show'])
        ->name('siswa.avatar.show');
});

Route::middleware(['auth', 'password.current', 'role:siswa'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {
        Route::get('/beranda', SiswaDashboardController::class)->name('dashboard');

        Route::get('/prestasi', [AchievementController::class, 'index'])->name('achievements.index');
        Route::get('/prestasi/tambah', [AchievementController::class, 'create'])->name('achievements.create');
        Route::post('/prestasi', [AchievementController::class, 'store'])->middleware('throttle:6,1')->name('achievements.store');
        Route::get('/prestasi/{achievement}/perbaiki', [AchievementController::class, 'edit'])->name('achievements.edit');
        Route::put('/prestasi/{achievement}', [AchievementController::class, 'update'])->middleware('throttle:6,1')->name('achievements.update');
        Route::get('/prestasi/{achievement}', [AchievementController::class, 'show'])->name('achievements.show');

        Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profil/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

        Route::get('/info-lomba', [SiswaCompetitionController::class, 'index'])->name('competitions.index');
    });

Route::middleware(['auth', 'password.current', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {
        Route::get('/dashboard', GuruDashboardController::class)->name('dashboard');

        Route::get('/notifikasi/{notification}/baca', [GuruNotificationController::class, 'read'])->name('notifications.read');

        Route::get('/verifikasi', [VerificationController::class, 'index'])->name('verification.index');
        Route::get('/verifikasi/{achievement}', [VerificationController::class, 'show'])->name('verification.show');
        Route::post('/verifikasi/{achievement}/setujui', [VerificationController::class, 'approve'])->name('verification.approve');
        Route::post('/verifikasi/{achievement}/revisi', [VerificationController::class, 'requestRevision'])->name('verification.revise');

        Route::middleware('wali_kelas')->group(function () {
            Route::get('/siswa/import/template', [StudentImportController::class, 'template'])->name('students.import.template');
            Route::get('/siswa/import/kredensial', [StudentImportController::class, 'credentials'])->name('students.import.credentials');
            Route::get('/siswa/import', [StudentImportController::class, 'create'])->name('students.import.create');
            Route::post('/siswa/import', [StudentImportController::class, 'store'])
                ->middleware('throttle:3,1')
                ->name('students.import.store');
            Route::get('/siswa/{student}/kredensial', [GuruStudentController::class, 'credentials'])->name('students.credentials');
            Route::post('/siswa/{student}/reset-password', [GuruStudentController::class, 'resetPassword'])->name('students.reset-password');
            Route::post('/siswa/bulk-hapus', [GuruStudentController::class, 'bulkDestroy'])->name('students.bulk-destroy');
            Route::resource('siswa', GuruStudentController::class)
                ->parameters(['siswa' => 'student'])
                ->names('students')
                ->except(['show']);
        });
    });

Route::middleware(['auth', 'password.current', 'role:admin,developer'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        Route::get('/ganti-password', [AdminPasswordController::class, 'edit'])->name('password.edit');
        Route::put('/ganti-password', [AdminPasswordController::class, 'update'])->name('password.update');

        Route::get('/kelas', [ClassController::class, 'index'])->name('classes.index');
        Route::get('/guru', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/siswa', [AdminStudentController::class, 'index'])->name('students.index');
        Route::get('/info-lomba', [AdminCompetitionController::class, 'index'])->name('competitions.index');

        Route::middleware('role:admin')->group(function () {
            Route::post('/guru/{teacher}/reset-password', [TeacherController::class, 'resetPassword'])->name('teachers.reset-password');

            Route::resource('kelas', ClassController::class)
                ->parameters(['kelas' => 'class'])
                ->names('classes')
                ->except(['show', 'index']);

            Route::resource('guru', TeacherController::class)
                ->parameters(['guru' => 'teacher'])
                ->names('teachers')
                ->except(['show', 'index']);

            Route::post('/siswa/pindah-kelas', [AdminStudentController::class, 'bulkMove'])->name('students.bulk-move');

            Route::resource('info-lomba', AdminCompetitionController::class)
                ->parameters(['info-lomba' => 'competition'])
                ->names('competitions')
                ->except(['show', 'index']);
        });

        Route::middleware('role:developer')->group(function () {
            Route::post('/admins/{admin}/reset-password', [AdminUserController::class, 'resetPassword'])->name('admins.reset-password');
            Route::resource('admins', AdminUserController::class)->except(['show']);
        });
    });

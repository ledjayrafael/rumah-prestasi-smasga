<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeacherRequest;
use App\Http\Requests\Admin\UpdateTeacherRequest;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(): View
    {
        $teachers = User::query()
            ->where('role', UserRole::Guru)
            ->with(['teacherProfile', 'taughtClasses'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        $classes = SchoolClass::orderBy('grade_level')->orderBy('name')->get();

        return view('admin.teachers.create', compact('classes'));
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $temporaryPassword = Str::password(10, symbols: false);

        $teacher = DB::transaction(function () use ($validated, $temporaryPassword) {
            $teacher = User::create([
                'name' => $validated['name'],
                'username' => $validated['email'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'role' => UserRole::Guru,
                'password' => $temporaryPassword,
                'must_change_password' => true,
            ]);

            $teacher->teacherProfile()->create([
                'subject' => $validated['subject'] ?? null,
                'position' => $validated['position'],
            ]);

            $teacher->taughtClasses()->sync($validated['class_ids'] ?? []);

            return $teacher;
        });

        Cache::put("temp_credential:{$teacher->id}", [
            'name' => $teacher->name,
            'login' => $teacher->email,
            'password' => $temporaryPassword,
        ], now()->addMinutes(3));

        return redirect()->route('admin.teachers.credentials', $teacher);
    }

    public function credentials(User $teacher): View
    {
        abort_unless($teacher->role === UserRole::Guru, 404);

        $credential = Cache::pull("temp_credential:{$teacher->id}");

        return view('admin.teachers.credentials', compact('credential'));
    }

    public function edit(User $teacher): View
    {
        abort_unless($teacher->role === UserRole::Guru, 404);

        $teacher->load(['teacherProfile', 'taughtClasses']);
        $classes = SchoolClass::orderBy('grade_level')->orderBy('name')->get();

        return view('admin.teachers.edit', compact('teacher', 'classes'));
    }

    public function update(UpdateTeacherRequest $request, User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === UserRole::Guru, 404);

        $validated = $request->validated();

        DB::transaction(function () use ($teacher, $validated) {
            $teacher->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $teacher->teacherProfile()->update([
                'subject' => $validated['subject'] ?? null,
                'position' => $validated['position'],
            ]);

            $teacher->taughtClasses()->sync($validated['class_ids'] ?? []);
        });

        return redirect()->route('admin.teachers.index')->with('status', 'Akun guru berhasil diperbarui.');
    }

    public function destroy(User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === UserRole::Guru, 404);

        $teacher->delete();

        return back()->with('status', 'Akun guru berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = User::query()
            ->where('role', UserRole::Siswa)
            ->with(['studentProfile.schoolClass'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.students.index', compact('students'));
    }

    public function create(): View
    {
        $classes = SchoolClass::orderBy('grade_level')->orderBy('name')->get();

        return view('admin.students.create', compact('classes'));
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $temporaryPassword = Str::password(10, symbols: false);

        $student = DB::transaction(function () use ($validated, $temporaryPassword) {
            $student = User::create([
                'name' => $validated['name'],
                'username' => $validated['nis'],
                'role' => UserRole::Siswa,
                'password' => $temporaryPassword,
                'must_change_password' => true,
            ]);

            $student->studentProfile()->create([
                'nis' => $validated['nis'],
                'school_class_id' => $validated['school_class_id'],
            ]);

            return $student;
        });

        Cache::put("temp_credential:{$student->id}", [
            'name' => $student->name,
            'login' => $validated['nis'],
            'password' => $temporaryPassword,
        ], now()->addMinutes(3));

        return redirect()->route('admin.students.credentials', $student);
    }

    public function credentials(User $student): View
    {
        abort_unless($student->role === UserRole::Siswa, 404);

        $credential = Cache::pull("temp_credential:{$student->id}");

        return view('admin.students.credentials', compact('credential'));
    }

    public function edit(User $student): View
    {
        abort_unless($student->role === UserRole::Siswa, 404);

        $student->load('studentProfile');
        $classes = SchoolClass::orderBy('grade_level')->orderBy('name')->get();

        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(UpdateStudentRequest $request, User $student): RedirectResponse
    {
        abort_unless($student->role === UserRole::Siswa, 404);

        $validated = $request->validated();

        DB::transaction(function () use ($student, $validated) {
            $student->update([
                'name' => $validated['name'],
                'username' => $validated['nis'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $student->studentProfile()->update([
                'nis' => $validated['nis'],
                'school_class_id' => $validated['school_class_id'],
            ]);
        });

        return redirect()->route('admin.students.index')->with('status', 'Akun siswa berhasil diperbarui.');
    }

    public function destroy(User $student): RedirectResponse
    {
        abort_unless($student->role === UserRole::Siswa, 404);

        $student->delete();

        return back()->with('status', 'Akun siswa berhasil dihapus.');
    }
}

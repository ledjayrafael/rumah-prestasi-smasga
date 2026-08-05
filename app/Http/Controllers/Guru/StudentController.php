<?php

namespace App\Http\Controllers\Guru;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\BulkDestroyStudentsRequest;
use App\Http\Requests\Guru\StoreStudentRequest;
use App\Http\Requests\Guru\UpdateStudentRequest;
use App\Models\User;
use App\Services\StudentAccountCreator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $classIds = Auth::user()->manageableClassIds();

        $students = User::query()
            ->where('role', UserRole::Siswa)
            ->whereHas('studentProfile', fn ($q) => $q->whereIn('school_class_id', $classIds))
            ->with(['studentProfile.schoolClass'])
            ->join('student_profiles', 'student_profiles.user_id', '=', 'users.id')
            ->join('school_classes', 'school_classes.id', '=', 'student_profiles.school_class_id')
            ->orderBy('school_classes.grade_level')
            ->orderBy('school_classes.name')
            ->orderBy('users.name')
            ->select('users.*')
            ->get();

        $homeroomClasses = Auth::user()
            ->homeroomClasses()
            ->orderBy('grade_level')
            ->orderBy('name')
            ->pluck('name');

        return view('guru.students.index', compact('students', 'homeroomClasses'));
    }

    public function create(): View
    {
        $classes = Auth::user()
            ->homeroomClasses()
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        return view('guru.students.create', compact('classes'));
    }

    public function store(StoreStudentRequest $request, StudentAccountCreator $creator): RedirectResponse
    {
        $validated = $request->validated();

        $result = $creator->create(
            $validated['name'],
            $validated['nis'],
            (int) $validated['school_class_id'],
        );

        Cache::put("temp_credential:{$result['student']->id}", [
            'name' => $result['student']->name,
            'login' => $result['login'],
            'password' => $result['password'],
        ], now()->addMinutes(3));

        return redirect()->route('guru.students.credentials', $result['student']);
    }

    public function credentials(User $student): View
    {
        abort_unless(Auth::user()->canManageStudent($student), 404);

        $credential = Cache::pull("temp_credential:{$student->id}");

        return view('guru.students.credentials', compact('credential'));
    }

    public function edit(User $student): View
    {
        abort_unless(Auth::user()->canManageStudent($student), 404);

        $student->load('studentProfile');
        $classes = Auth::user()
            ->homeroomClasses()
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        return view('guru.students.edit', compact('student', 'classes'));
    }

    public function update(UpdateStudentRequest $request, User $student): RedirectResponse
    {
        abort_unless(Auth::user()->canManageStudent($student), 404);

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

        return redirect()->route('guru.students.index')->with('status', 'Akun siswa berhasil diperbarui.');
    }

    public function destroy(User $student): RedirectResponse
    {
        abort_unless(Auth::user()->canManageStudent($student), 404);

        $student->delete();

        return back()->with('status', 'Akun siswa berhasil dihapus.');
    }

    public function bulkDestroy(BulkDestroyStudentsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $classIds = Auth::user()->manageableClassIds();

        $deleted = DB::transaction(function () use ($validated, $classIds) {
            $students = User::query()
                ->where('role', UserRole::Siswa)
                ->whereIn('id', $validated['student_ids'])
                ->whereHas('studentProfile', fn ($q) => $q->whereIn('school_class_id', $classIds))
                ->get();

            $students->each->delete();

            return $students->count();
        });

        return back()->with('status', "{$deleted} akun siswa berhasil dihapus.");
    }
}

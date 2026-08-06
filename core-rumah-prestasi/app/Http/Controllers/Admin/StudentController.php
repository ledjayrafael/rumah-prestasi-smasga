<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkMoveStudentsRequest;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $classFilter = $request->query('kelas', '');

        $query = User::query()
            ->where('role', UserRole::Siswa)
            ->with('studentProfile.schoolClass');

        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';

            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhereHas('studentProfile', fn ($q) => $q->where('nis', 'like', $like));
            });
        }

        if ($classFilter === 'unassigned') {
            $query->whereHas('studentProfile', fn ($q) => $q->whereNull('school_class_id'));
        } elseif ($classFilter !== '') {
            $query->whereHas('studentProfile', fn ($q) => $q->where('school_class_id', $classFilter));
        }

        $students = $query->orderBy('name')->paginate(70)->withQueryString();

        $classes = SchoolClass::query()
            ->withCount('students')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $unassignedCount = StudentProfile::query()->whereNull('school_class_id')->count();
        $totalStudents = User::query()->where('role', UserRole::Siswa)->count();

        return view('admin.students.index', compact(
            'students',
            'classes',
            'search',
            'classFilter',
            'unassignedCount',
            'totalStudents'
        ));
    }

    public function bulkMove(BulkMoveStudentsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $targetClass = SchoolClass::findOrFail($validated['school_class_id']);

        $moved = DB::transaction(function () use ($validated, $targetClass) {
            $studentIds = User::query()
                ->where('role', UserRole::Siswa)
                ->whereIn('id', $validated['student_ids'])
                ->pluck('id');

            return StudentProfile::query()
                ->whereIn('user_id', $studentIds)
                ->update(['school_class_id' => $targetClass->id]);
        });

        return back()->with('status', "{$moved} siswa dipindahkan ke {$targetClass->name}.");
    }
}

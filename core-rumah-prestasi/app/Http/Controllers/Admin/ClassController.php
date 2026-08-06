<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClassRequest;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(): View
    {
        $classes = SchoolClass::withCount('students')->with('homeroomTeacher')->orderBy('grade_level')->orderBy('name')->get();

        return view('admin.classes.index', compact('classes'));
    }

    public function create(): View
    {
        $teachers = User::query()->where('role', 'guru')->orderBy('name')->get();

        return view('admin.classes.create', compact('teachers'));
    }

    public function store(StoreClassRequest $request): RedirectResponse
    {
        $class = SchoolClass::create($request->validated());
        $this->syncHomeroomTeacher($class);

        return redirect()->route('admin.classes.index')->with('status', 'Kelas berhasil ditambahkan.');
    }

    public function edit(SchoolClass $class): View
    {
        $teachers = User::query()->where('role', 'guru')->orderBy('name')->get();

        return view('admin.classes.edit', compact('class', 'teachers'));
    }

    public function update(StoreClassRequest $request, SchoolClass $class): RedirectResponse
    {
        $class->update($request->validated());
        $this->syncHomeroomTeacher($class->fresh());

        return redirect()->route('admin.classes.index')->with('status', 'Kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $class->delete();

        return back()->with('status', 'Kelas berhasil dihapus.');
    }

    private function syncHomeroomTeacher(SchoolClass $class): void
    {
        if ($class->homeroom_teacher_id) {
            $class->teachers()->syncWithoutDetaching([$class->homeroom_teacher_id]);
        }
    }
}

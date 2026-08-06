<?php

namespace App\Http\Controllers\Guru;

use App\Exports\StudentsImportCredentialsExport;
use App\Exports\StudentsTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\ImportStudentsRequest;
use App\Imports\StudentsImport;
use App\Services\StudentAccountCreator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentImportController extends Controller
{
    public function create(): View
    {
        $homeroomClasses = Auth::user()
            ->homeroomClasses()
            ->orderBy('grade_level')
            ->orderBy('name')
            ->pluck('name');

        return view('guru.students.import', compact('homeroomClasses'));
    }

    public function store(ImportStudentsRequest $request, StudentAccountCreator $creator): RedirectResponse
    {
        $import = new StudentsImport(Auth::user(), $creator);

        Excel::import($import, $request->file('file'));

        $redirect = redirect()
            ->route('guru.students.import.create')
            ->with('import_success', $import->successCount)
            ->with('import_errors', $import->errors);

        if ($import->credentials !== []) {
            $cacheKey = 'import_credentials:'.Auth::id();
            Cache::put($cacheKey, $import->credentials, now()->addMinutes(10));
            $redirect->with('import_credentials_available', true);
        }

        return $redirect;
    }

    public function template(): BinaryFileResponse
    {
        return Excel::download(
            new StudentsTemplateExport(Auth::user()),
            'template-import-siswa.xlsx',
        );
    }

    public function credentials(): BinaryFileResponse|RedirectResponse
    {
        $credentials = Cache::pull('import_credentials:'.Auth::id());

        if ($credentials === null || $credentials === []) {
            return redirect()
                ->route('guru.students.import.create')
                ->with('status', 'File kredensial tidak tersedia atau sudah kedaluwarsa.');
        }

        return Excel::download(
            new StudentsImportCredentialsExport($credentials),
            'kredensial-import-siswa-'.now()->format('Y-m-d-His').'.xlsx',
        );
    }
}

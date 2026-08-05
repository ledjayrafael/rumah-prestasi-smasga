<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\AchievementStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAchievementRequest;
use App\Http\Requests\UpdateAchievementRequest;
use App\Models\Achievement;
use App\Notifications\NewAchievementSubmitted;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class AchievementController extends Controller
{
    public function index(Request $request): View
    {
        $base = Auth::user()->achievements();

        $counts = [
            'all' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'approved' => (clone $base)->where('status', 'approved')->count(),
            'revision' => (clone $base)->where('status', 'revision')->count(),
        ];

        $query = Auth::user()->achievements()->latest('event_date');

        if ($status = $request->query('status')) {
            if (in_array($status, ['pending', 'approved', 'revision'], true)) {
                $query->where('status', $status);
            }
        }

        $achievements = $query->paginate(10)->withQueryString();

        return view('siswa.achievements.index', compact('achievements', 'counts'));
    }

    public function create(): View
    {
        return view('siswa.achievements.create');
    }

    public function store(StoreAchievementRequest $request): RedirectResponse|JsonResponse
    {
        $storedPaths = [];

        try {
            $achievement = DB::transaction(function () use ($request, &$storedPaths) {
                $achievement = Auth::user()->achievements()->create($request->safe()->except('files'));

                $this->storeUploadedFiles($achievement, $request->file('files', []), $storedPaths);

                return $achievement;
            });
        } catch (Throwable $e) {
            $this->cleanupStoredFiles($storedPaths);
            report($e);

            return $this->submissionFailedResponse($request);
        }

        $this->notifyClassTeachersSafely($achievement);

        return redirect()->route('siswa.achievements.index')
            ->with('status', 'Prestasi berhasil diajukan dan menunggu verifikasi guru.');
    }

    public function show(Achievement $achievement): View
    {
        abort_unless($achievement->student_id === Auth::id(), 403);

        return view('siswa.achievements.show', compact('achievement'));
    }

    public function edit(Achievement $achievement): View
    {
        abort_unless($achievement->student_id === Auth::id(), 403);
        abort_unless($achievement->status === AchievementStatus::Revision, 403);

        $achievement->load('files');

        return view('siswa.achievements.edit', compact('achievement'));
    }

    public function update(UpdateAchievementRequest $request, Achievement $achievement): RedirectResponse|JsonResponse
    {
        abort_unless($achievement->student_id === Auth::id(), 403);
        abort_unless($achievement->status === AchievementStatus::Revision, 403);

        $newPaths = [];
        $oldPaths = [];

        try {
            DB::transaction(function () use ($request, $achievement, &$newPaths, &$oldPaths) {
                $achievement->update([
                    ...$request->safe()->except('files'),
                    'status' => AchievementStatus::Pending,
                    'reviewer_notes' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                ]);

                $uploaded = array_filter($request->file('files', []) ?? []);

                if ($uploaded !== []) {
                    // Keep the old files on disk until the new ones are confirmed
                    // stored, so a mid-upload failure (factor X) can't wipe out
                    // evidence the student already had approved for revision.
                    $oldPaths = $achievement->files->pluck('path')->all();
                    $achievement->files()->delete();

                    $this->storeUploadedFiles($achievement, $uploaded, $newPaths);
                }
            });
        } catch (Throwable $e) {
            $this->cleanupStoredFiles($newPaths);
            report($e);

            return $this->submissionFailedResponse($request);
        }

        $this->cleanupStoredFiles($oldPaths);

        $achievement->refresh();
        $this->notifyClassTeachersSafely($achievement);

        return redirect()->route('siswa.achievements.show', $achievement)
            ->with('status', 'Perbaikan prestasi dikirim ulang dan menunggu verifikasi guru.');
    }

    /**
     * @param  list<UploadedFile|null>  $files
     * @param  list<string>  $storedPaths  appended to as each file is written, by
     *                                     reference, so a mid-loop failure still
     *                                     leaves the caller able to clean up
     */
    private function storeUploadedFiles(Achievement $achievement, array $files, array &$storedPaths): void
    {
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $path = $file->store('bukti-prestasi', 'local');
            $storedPaths[] = $path;

            $achievement->files()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }

    /**
     * @param  list<string>  $paths
     */
    private function cleanupStoredFiles(array $paths): void
    {
        foreach ($paths as $path) {
            Storage::disk('local')->delete($path);
        }
    }

    /**
     * Notification delivery (mail/db) shouldn't undo an already-saved
     * submission, so failures here are logged instead of bubbled up.
     */
    private function notifyClassTeachersSafely(Achievement $achievement): void
    {
        try {
            $this->notifyClassTeachers($achievement);
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function notifyClassTeachers(Achievement $achievement): void
    {
        $classTeachers = $achievement->student->studentProfile?->schoolClass?->teachers;

        if ($classTeachers && $classTeachers->isNotEmpty()) {
            Notification::send($classTeachers, new NewAchievementSubmitted($achievement));
        }
    }

    private function submissionFailedResponse(Request $request): RedirectResponse|JsonResponse
    {
        $message = 'Terjadi kesalahan pada server saat menyimpan prestasi. Data yang sudah Anda isi tidak hilang — silakan coba kirim lagi.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 500);
        }

        return redirect()->back()->withInput()->with('error', $message);
    }
}

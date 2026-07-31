<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\AchievementStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAchievementRequest;
use App\Http\Requests\UpdateAchievementRequest;
use App\Models\Achievement;
use App\Notifications\NewAchievementSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

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

    public function store(StoreAchievementRequest $request): RedirectResponse
    {
        $achievement = DB::transaction(function () use ($request) {
            $achievement = Auth::user()->achievements()->create($request->safe()->except('files'));

            $this->storeUploadedFiles($achievement, $request->file('files', []));

            return $achievement;
        });

        $this->notifyClassTeachers($achievement);

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

    public function update(UpdateAchievementRequest $request, Achievement $achievement): RedirectResponse
    {
        abort_unless($achievement->student_id === Auth::id(), 403);
        abort_unless($achievement->status === AchievementStatus::Revision, 403);

        DB::transaction(function () use ($request, $achievement) {
            $achievement->update([
                ...$request->safe()->except('files'),
                'status' => AchievementStatus::Pending,
                'reviewer_notes' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]);

            $uploaded = array_filter($request->file('files', []) ?? []);

            if ($uploaded !== []) {
                foreach ($achievement->files as $existing) {
                    Storage::disk('local')->delete($existing->path);
                    $existing->delete();
                }

                $this->storeUploadedFiles($achievement, $uploaded);
            }
        });

        $achievement->refresh();
        $this->notifyClassTeachers($achievement);

        return redirect()->route('siswa.achievements.show', $achievement)
            ->with('status', 'Perbaikan prestasi dikirim ulang dan menunggu verifikasi guru.');
    }

    /**
     * @param  list<UploadedFile|null>  $files
     */
    private function storeUploadedFiles(Achievement $achievement, array $files): void
    {
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $path = $file->store('bukti-prestasi', 'local');

            $achievement->files()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }

    private function notifyClassTeachers(Achievement $achievement): void
    {
        $classTeachers = $achievement->student->studentProfile?->schoolClass?->teachers;

        if ($classTeachers && $classTeachers->isNotEmpty()) {
            Notification::send($classTeachers, new NewAchievementSubmitted($achievement));
        }
    }
}

<?php

namespace App\Http\Controllers\Guru;

use App\Enums\AchievementStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestRevisionRequest;
use App\Models\Achievement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = Auth::user();
        $classIds = $teacher->taughtClasses()->pluck('school_classes.id');

        $status = $request->query('status', AchievementStatus::Pending->value);

        $query = Achievement::with(['student.studentProfile.schoolClass', 'files'])
            ->whereHas('student.studentProfile', fn ($q) => $q->whereIn('school_class_id', $classIds));

        $counts = [
            'pending' => (clone $query)->where('status', AchievementStatus::Pending)->count(),
            'approved' => (clone $query)->where('status', AchievementStatus::Approved)->count(),
            'revision' => (clone $query)->where('status', AchievementStatus::Revision)->count(),
            'all' => (clone $query)->count(),
        ];

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $achievements = $query->latest('event_date')->paginate(10)->withQueryString();

        return view('guru.verification.index', compact('achievements', 'counts', 'status'));
    }

    public function show(Achievement $achievement): View
    {
        $this->authorizeAccess($achievement);

        $achievement->load(['student.studentProfile.schoolClass', 'files']);

        return view('guru.verification.show', compact('achievement'));
    }

    public function approve(Achievement $achievement): RedirectResponse
    {
        $this->authorizeAccess($achievement);
        $this->ensurePending($achievement);

        $achievement->update([
            'status' => AchievementStatus::Approved,
            'reviewer_notes' => null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('guru.verification.index')
            ->with('status', 'Prestasi disetujui.');
    }

    public function requestRevision(RequestRevisionRequest $request, Achievement $achievement): RedirectResponse
    {
        $this->authorizeAccess($achievement);
        $this->ensurePending($achievement);

        $achievement->update([
            'status' => AchievementStatus::Revision,
            'reviewer_notes' => $request->validated('reviewer_notes'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('guru.verification.index')
            ->with('status', 'Permintaan revisi dikirim ke siswa.');
    }

    private function authorizeAccess(Achievement $achievement): void
    {
        $teacher = Auth::user();
        $classIds = $teacher->taughtClasses()->pluck('school_classes.id');
        $studentClassId = $achievement->student->studentProfile?->school_class_id;

        abort_unless($studentClassId && $classIds->contains($studentClassId), 403);
    }

    private function ensurePending(Achievement $achievement): void
    {
        abort_unless($achievement->status === AchievementStatus::Pending, 403);
    }
}

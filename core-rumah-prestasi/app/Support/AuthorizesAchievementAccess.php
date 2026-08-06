<?php

namespace App\Support;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait AuthorizesAchievementAccess
{
    protected function userCanAccessAchievement(Achievement $achievement): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isSiswa()) {
            return $achievement->student_id === $user->id;
        }

        if ($user->isGuru()) {
            $classIds = $user->taughtClasses()->pluck('school_classes.id');
            $studentClassId = $achievement->student->studentProfile?->school_class_id;

            return $studentClassId && $classIds->contains($studentClassId);
        }

        return false;
    }

    protected function authorizeAchievementAccess(Achievement $achievement): void
    {
        abort_unless($this->userCanAccessAchievement($achievement), 403);
    }

    protected function userCanViewStudentAvatar(User $student): bool
    {
        $user = Auth::user();

        if (! $user || ! $student->isSiswa()) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isSiswa()) {
            return $student->id === $user->id;
        }

        if ($user->isGuru()) {
            $classIds = $user->taughtClasses()->pluck('school_classes.id');
            $studentClassId = $student->studentProfile?->school_class_id;

            return $studentClassId && $classIds->contains($studentClassId);
        }

        return false;
    }
}

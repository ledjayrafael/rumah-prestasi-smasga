<?php

namespace App\Http\Controllers;

use App\Models\AchievementFile;
use App\Support\AuthorizesAchievementAccess;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AchievementFileController extends Controller
{
    use AuthorizesAchievementAccess;

    public function show(AchievementFile $file): StreamedResponse
    {
        $file->load('achievement.student.studentProfile');

        $this->authorizeAchievementAccess($file->achievement);

        abort_unless(Storage::disk('local')->exists($file->path), 404);

        return Storage::disk('local')->response(
            $file->path,
            $file->original_name,
            [
                'Content-Type' => $file->mime_type,
                'Content-Disposition' => 'inline; filename="'.addslashes($file->original_name).'"',
            ]
        );
    }
}

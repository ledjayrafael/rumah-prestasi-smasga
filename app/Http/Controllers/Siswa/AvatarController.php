<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuthorizesAchievementAccess;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AvatarController extends Controller
{
    use AuthorizesAchievementAccess;

    public function show(User $user): StreamedResponse
    {
        abort_unless($user->isSiswa(), 404);
        abort_unless($user->avatar_path, 404);

        abort_unless($this->userCanViewStudentAvatar($user), 403);

        abort_unless(Storage::disk('local')->exists($user->avatar_path), 404);

        return Storage::disk('local')->response(
            $user->avatar_path,
            basename($user->avatar_path),
            [
                'Content-Type' => 'image/webp',
                'Content-Disposition' => 'inline',
            ]
        );
    }
}

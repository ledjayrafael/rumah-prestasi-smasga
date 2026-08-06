<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function read(string $notification): RedirectResponse
    {
        $record = Auth::user()->notifications()->findOrFail($notification);
        $record->markAsRead();

        $achievementId = $record->data['achievement_id'] ?? null;

        if ($achievementId) {
            return redirect()->route('guru.verification.show', $achievementId);
        }

        return redirect()->route('guru.dashboard');
    }
}

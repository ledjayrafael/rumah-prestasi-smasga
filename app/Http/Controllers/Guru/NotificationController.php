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

        return redirect()->to($record->data['url'] ?? route('guru.dashboard'));
    }
}

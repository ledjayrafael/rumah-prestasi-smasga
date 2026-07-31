<?php

namespace App\Notifications;

use App\Models\Achievement;
use Illuminate\Notifications\Notification;

class NewAchievementSubmitted extends Notification
{
    public function __construct(private readonly Achievement $achievement) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'achievement_id' => $this->achievement->id,
            'title' => $this->achievement->title,
            'student_name' => $this->achievement->student->name,
            'url' => route('guru.verification.show', $this->achievement),
        ];
    }
}

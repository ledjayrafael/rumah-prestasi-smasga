<?php

namespace App\Enums;

enum AchievementStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Revision = 'revision';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Approved => 'Disetujui',
            self::Revision => 'Perlu Revisi',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Approved => 'green',
            self::Revision => 'red',
        };
    }
}

<?php

namespace App\Enums;

enum AchievementCategory: string
{
    case Akademik = 'akademik';
    case NonAkademik = 'non_akademik';
    case Organisasi = 'organisasi';

    public function label(): string
    {
        return match ($this) {
            self::Akademik => 'Akademik',
            self::NonAkademik => 'Non-Akademik',
            self::Organisasi => 'Organisasi',
        };
    }
}

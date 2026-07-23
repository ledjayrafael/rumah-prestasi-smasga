<?php

namespace App\Enums;

enum TeacherPosition: string
{
    case WaliKelas = 'wali_kelas';
    case GuruMapel = 'guru_mapel';

    public function label(): string
    {
        return match ($this) {
            self::WaliKelas => 'Wali Kelas',
            self::GuruMapel => 'Guru Mapel',
        };
    }
}

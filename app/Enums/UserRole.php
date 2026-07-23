<?php

namespace App\Enums;

enum UserRole: string
{
    case Siswa = 'siswa';
    case Guru = 'guru';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Siswa => 'Siswa',
            self::Guru => 'Guru',
            self::Admin => 'Admin Sekolah',
        };
    }
}

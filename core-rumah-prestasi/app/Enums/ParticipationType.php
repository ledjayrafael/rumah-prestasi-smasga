<?php

namespace App\Enums;

enum ParticipationType: string
{
    case Perorangan = 'perorangan';
    case Tim = 'tim';

    public function label(): string
    {
        return match ($this) {
            self::Perorangan => 'Perorangan',
            self::Tim => 'Tim',
        };
    }
}

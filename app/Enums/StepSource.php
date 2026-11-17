<?php

namespace App\Enums;

enum StepSource: string
{
    case Shortcut = 'shortcut';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Shortcut => 'ショートカット',
            self::Manual => '手動',
        };
    }
}

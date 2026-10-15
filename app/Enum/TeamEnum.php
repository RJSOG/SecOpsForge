<?php

namespace App\Enum;

enum TeamEnum: string
{
    case RED_TEAM = 'redteam';
    case BLUE_TEAM = 'blueteam';
    case AUTOMATION = 'automation';

    /**
     * @param string $value
     * @return bool
     */
    public static function has(string $value): bool {
        return self::tryFrom($value) !== null;
    }

    /**
     * @return array
     */
    public static function getAllValues(): array {
        return array_column(self::cases(), 'value');
    }
}

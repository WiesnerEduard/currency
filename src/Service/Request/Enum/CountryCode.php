<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Enum;

enum CountryCode: string
{
    case Sk = 'SK';
    case Cz = 'CZ';

    public static function getAllValues(): string
    {
        return self::getValuesOfCollection(self::cases());
    }

    /**
     * @param CountryCode[] $countryCodes
     */
    public static function getValuesOfCollection(array $countryCodes): string
    {
        return implode(',', array_values(array_column($countryCodes, 'value')));
    }
}

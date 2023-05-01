<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Enum;

enum CurrencyCode: string
{
    case Eur = 'EUR';
    case Usd = 'USD';
    case Czk = 'CZK';

    public static function getAllValues(): string
    {
        return self::getValuesOfCollection(self::cases());
    }

    /**
     * @param CurrencyCode[] $currencyCodes
     */
    public static function getValuesOfCollection(array $currencyCodes): string
    {
        return implode(',', array_values(array_column($currencyCodes, 'value')));
    }
}

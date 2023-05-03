<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response\ValueObject;

use Wiesner\Currency\Service\Request\Enum\CurrencyCode;

class Rate
{
    public function __construct(
        private readonly CurrencyCode $currencyCode,
        private readonly float $value
    ) {
    }

    public function getCurrencyCode(): CurrencyCode
    {
        return $this->currencyCode;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}

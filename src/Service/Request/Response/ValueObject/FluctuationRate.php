<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response\ValueObject;

use Wiesner\Currency\Service\Request\Enum\CurrencyCode;

class FluctuationRate
{
    public function __construct(
        private readonly CurrencyCode $currencyCode,
        private readonly float $startValue,
        private readonly float $endValue,
        private readonly float $changeValue,
        private readonly float $changePercentageValue
    ) {
    }

    public function getCurrencyCode(): CurrencyCode
    {
        return $this->currencyCode;
    }

    public function getStartValue(): float
    {
        return $this->startValue;
    }

    public function getEndValue(): float
    {
        return $this->endValue;
    }

    public function getChangeValue(): float
    {
        return $this->changeValue;
    }

    public function getChangePercentageValue(): float
    {
        return $this->changePercentageValue;
    }
}

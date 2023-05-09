<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response\ValueObject;

use Wiesner\Currency\Service\Request\Enum\CurrencyCode;

class Rate implements \Stringable
{
    public function __construct(
        private readonly \DateTimeImmutable $date,
        private readonly CurrencyCode $baseCurrency,
        private readonly CurrencyCode $targetCurrency,
        private readonly float $baseAmount,
        private readonly float $targetAmount,
    ) {
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getBaseCurrency(): CurrencyCode
    {
        return $this->baseCurrency;
    }

    public function getTargetCurrency(): CurrencyCode
    {
        return $this->targetCurrency;
    }

    public function getBaseAmount(): float
    {
        return $this->baseAmount;
    }

    public function getTargetAmount(): float
    {
        return $this->targetAmount;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s: %.2f%s = %.2f%s',
            $this->date->format('Y-m-d'),
            $this->baseAmount,
            $this->baseCurrency->value,
            $this->targetAmount,
            $this->targetCurrency->value
        );
    }
}

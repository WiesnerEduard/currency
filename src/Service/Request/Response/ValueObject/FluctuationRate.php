<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response\ValueObject;

use Wiesner\Currency\Service\Request\Enum\CurrencyCode;

class FluctuationRate implements \Stringable
{
    public function __construct(
        private readonly CurrencyCode $baseCurrency,
        private readonly CurrencyCode $targetCurrency,
        private readonly \DateTimeImmutable $startDate,
        private readonly \DateTimeImmutable $endDate,
        private readonly float $baseAmount,
        private readonly float $targetStartAmount,
        private readonly float $targetEndAmount,
        private readonly float $targetChange,
        private readonly float $targetChangePercentage
    ) {
    }

    public function getBaseCurrency(): CurrencyCode
    {
        return $this->baseCurrency;
    }

    public function getTargetCurrency(): CurrencyCode
    {
        return $this->targetCurrency;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getBaseAmount(): float
    {
        return $this->baseAmount;
    }

    public function getTargetStartAmount(): float
    {
        return $this->targetStartAmount;
    }

    public function getTargetEndAmount(): float
    {
        return $this->targetEndAmount;
    }

    public function getTargetChange(): float
    {
        return $this->targetChange;
    }

    public function getTargetChangePercentage(): float
    {
        return $this->targetChangePercentage;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s: %.2f%s = %.2f%s, %s: %.2f%s = %.2f%s',
            $this->startDate->format('Y-m-d'),
            $this->baseAmount,
            $this->baseCurrency->value,
            $this->targetStartAmount,
            $this->targetCurrency->value,
            $this->endDate->format('Y-m-d'),
            $this->baseAmount,
            $this->baseCurrency->value,
            $this->targetEndAmount,
            $this->targetCurrency->value
        );
    }
}

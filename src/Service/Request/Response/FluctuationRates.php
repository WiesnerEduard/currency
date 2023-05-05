<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response;

use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\Response\ValueObject\FluctuationRate;

final class FluctuationRates
{
    /**
     * @param FluctuationRate[] $fluctuationRates
     */
    private function __construct(
        private readonly CurrencyCode $baseCurrency,
        private readonly \DateTimeImmutable $startDate,
        private readonly \DateTimeImmutable $endDate,
        private readonly array $fluctuationRates
    ) {
    }

    /**
     * @throws \Exception
     */
    public static function createFromArrayAndCurrency(array $responseArray, CurrencyCode $baseCurrency): FluctuationRates
    {
        $rates = [];

        foreach ($responseArray['rates'] as $currency => $fluctuationRate) {
            $rates[] = new FluctuationRate(
                currencyCode: CurrencyCode::from($currency),
                startValue: $fluctuationRate['start_rate'],
                endValue: $fluctuationRate['end_rate'],
                changeValue: $fluctuationRate['change'],
                changePercentageValue: $fluctuationRate['change_pct'],
            );
        }

        return new self(
            baseCurrency: $baseCurrency,
            startDate: new \DateTimeImmutable($responseArray['start_date']),
            endDate: new \DateTimeImmutable($responseArray['end_date']),
            fluctuationRates: $rates
        );
    }

    public function getBaseCurrency(): CurrencyCode
    {
        return $this->baseCurrency;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    /**
     * @return FluctuationRate[]
     */
    public function getAllFluctuationRates(): array
    {
        return $this->fluctuationRates;
    }

    public function getFluctuationRate(CurrencyCode $currencyCode): ?FluctuationRate
    {
        foreach ($this->fluctuationRates as $rate) {
            if ($rate->getCurrencyCode() === $currencyCode) {
                return $rate;
            }
        }

        return null;
    }
}

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
        $startDate = new \DateTimeImmutable($responseArray['start_date']);
        $endDate = new \DateTimeImmutable($responseArray['end_date']);
        $baseAmount = (float) $responseArray['rates'][$baseCurrency->value]['start_rate'];

        $rates = [];
        foreach ($responseArray['rates'] as $currency => $fluctuationRate) {
            if ($currency !== $baseCurrency->value) {
                $rates[] = new FluctuationRate($baseCurrency, CurrencyCode::from($currency), $startDate, $endDate, $baseAmount, (float) $fluctuationRate['start_rate'], (float) $fluctuationRate['end_rate'], (float) $fluctuationRate['change'], (float) $fluctuationRate['change_pct']);
            }
        }

        return new self($baseCurrency, $startDate, $endDate, $rates);
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
            if ($rate->getTargetCurrency() === $currencyCode) {
                return $rate;
            }
        }

        return null;
    }
}

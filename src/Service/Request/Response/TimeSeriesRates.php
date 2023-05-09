<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response;

use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\Response\ValueObject\Rate;

final class TimeSeriesRates
{
    /**
     * @param Rate[][] $rates
     */
    private function __construct(
        private readonly CurrencyCode $baseCurrency,
        private readonly \DateTimeImmutable $startDate,
        private readonly \DateTimeImmutable $endDate,
        private readonly array $rates
    ) {
    }

    /**
     * @throws \Exception
     */
    public static function createFromArray(array $responseArray): TimeSeriesRates
    {
        $ratesByDay = $responseArray['rates'];
        $baseCurrency = CurrencyCode::from($responseArray['base']);
        $firstRate = reset($responseArray['rates']);

        if (false !== $firstRate) {
            $baseAmount = (float) $firstRate[$responseArray['base']];
        } else {
            $baseAmount = 0.0;
        }

        $result = [];

        foreach ($ratesByDay as $date => $rates) {
            $dateTime = new \DateTimeImmutable($date);
            foreach ($rates as $currency => $amount) {
                if ($currency !== $responseArray['base']) {
                    $result[$date][] = new Rate($dateTime, $baseCurrency, CurrencyCode::from($currency), $baseAmount, (float) $amount);
                }
            }
        }

        return new self($baseCurrency, new \DateTimeImmutable($responseArray['start_date']), new \DateTimeImmutable($responseArray['end_date']), $result);
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
     * @return Rate[][]
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     * @return Rate[]
     */
    public function getRatesByDate(\DateTimeImmutable $date): array
    {
        $index = $date->format('Y-m-d');

        return $this->rates[$index] ?? [];
    }

    public function getRateByDateAndCurrency(\DateTimeImmutable $date, CurrencyCode $currency): ?Rate
    {
        foreach ($this->getRatesByDate($date) as $rate) {
            if ($rate->getTargetCurrency() === $currency) {
                return $rate;
            }
        }

        return null;
    }
}

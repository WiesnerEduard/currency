<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response;

use Symfony\Contracts\HttpClient\ResponseInterface;
use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\RequestServiceException;
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
     * @throws RequestServiceException
     */
    public static function createFromResponseAndBaseCurrency(ResponseInterface $response, CurrencyCode $baseCurrency): FluctuationRates
    {
        try {
            $responseArray = $response->toArray();
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
        } catch (\Throwable $e) {
            throw RequestServiceException::createInResponseContext('FluctuationRates', $e);
        }
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

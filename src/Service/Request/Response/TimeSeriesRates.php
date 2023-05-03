<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response;

use Symfony\Contracts\HttpClient\ResponseInterface;
use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\RequestServiceException;
use Wiesner\Currency\Service\Request\Response\ValueObject\Rate;

final class TimeSeriesRates
{
    /**
     * @param Rate[] $rates
     */
    private function __construct(
        private readonly CurrencyCode $baseCurrency,
        private readonly \DateTimeImmutable $startDate,
        private readonly \DateTimeImmutable $endDate,
        private readonly array $rates
    ) {
    }

    /**
     * @throws RequestServiceException
     */
    public static function createFromResponse(ResponseInterface $response): TimeSeriesRates
    {
        try {
            $responseArray = $response->toArray();
            $rates = [];

            foreach ($responseArray['rates'] as $currency => $rate) {
                $rates[] = new Rate(CurrencyCode::from($currency), $rate);
            }

            return new self(
                CurrencyCode::from($responseArray['base']),
                new \DateTimeImmutable($responseArray['start_date']),
                new \DateTimeImmutable($responseArray['end_date']),
                $rates
            );
        } catch (\Throwable $e) {
            throw RequestServiceException::createInResponseContext('TimeSeriesRates', $e);
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
     * @return Rate[]
     */
    public function getAllRates(): array
    {
        return $this->rates;
    }

    public function getRate(CurrencyCode $currencyCode): ?Rate
    {
        foreach ($this->rates as $rate) {
            if ($rate->getCurrencyCode() === $currencyCode) {
                return $rate;
            }
        }

        return null;
    }
}

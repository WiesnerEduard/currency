<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response;

use Symfony\Contracts\HttpClient\ResponseInterface;
use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\RequestServiceException;
use Wiesner\Currency\Service\Request\Response\ValueObject\Rate;

final class LatestRates
{
    /**
     * @param Rate[] $rates
     */
    private function __construct(
        private readonly CurrencyCode $baseCurrency,
        private readonly \DateTimeImmutable $updatedDate,
        private readonly array $rates
    ) {
    }

    /**
     * @throws RequestServiceException
     */
    public static function createFromResponse(ResponseInterface $response): self
    {
        try {
            $responseArray = $response->toArray();
            $rates = [];

            foreach ($responseArray['rates'] as $currency => $rate) {
                $rates[] = new Rate(CurrencyCode::from($currency), $rate);
            }

            return new self(
                CurrencyCode::from($responseArray['base']),
                new \DateTimeImmutable($responseArray['date']),
                $rates
            );
        } catch (\Throwable $e) {
            throw RequestServiceException::createInResponseContext('LatestRates', $e);
        }
    }

    public function getBaseCurrency(): CurrencyCode
    {
        return $this->baseCurrency;
    }

    public function getUpdatedDate(): \DateTimeImmutable
    {
        return $this->updatedDate;
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

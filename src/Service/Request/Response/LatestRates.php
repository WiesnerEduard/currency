<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
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
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public static function createFromResponse(ResponseInterface $response): self
    {
        $responseArray = $response->toArray();
        $rates = [];

        foreach ($responseArray['rates'] as $currency => $rate) {
            $rates[] = new Rate(CurrencyCode::from($currency), $rate);
        }

        return new self(
            CurrencyCode::from($responseArray['base']),
            \DateTimeImmutable::createFromFormat('Y-m-d', $responseArray['date']),
            $rates
        );
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

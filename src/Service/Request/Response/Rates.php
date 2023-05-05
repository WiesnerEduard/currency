<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response;

use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\Response\ValueObject\Rate;

final class Rates
{
    /**
     * @param Rate[] $rates
     */
    private function __construct(
        private readonly CurrencyCode $baseCurrency,
        private readonly \DateTimeImmutable $updatedDate,
        private readonly array $rates,
        private readonly bool $historical
    ) {
    }

    /**
     * @throws \Exception
     */
    public static function createFromArray(array $responseArray): Rates
    {
        $rates = [];

        foreach ($responseArray['rates'] as $currency => $rate) {
            $rates[] = new Rate(CurrencyCode::from($currency), $rate);
        }

        return new self(
            CurrencyCode::from($responseArray['base']),
            new \DateTimeImmutable($responseArray['date']),
            $rates,
            array_key_exists('historical', $responseArray) ? $responseArray['historical'] : false
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

    public function isHistorical(): bool
    {
        return $this->historical;
    }
}

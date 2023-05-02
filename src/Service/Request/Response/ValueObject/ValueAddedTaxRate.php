<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response\ValueObject;

use Wiesner\Currency\Service\Request\Enum\CountryCode;

class ValueAddedTaxRate
{
    /**
     * @param int[] $reduced_rates
     * @param int[] $superReducedRates
     * @param int[] $parkingRates
     */
    public function __construct(
        private readonly CountryCode $countryCode,
        private readonly int $standardRate,
        private readonly array $reduced_rates,
        private readonly array $superReducedRates,
        private readonly array $parkingRates
    ) {
    }

    public function getCountryCode(): CountryCode
    {
        return $this->countryCode;
    }

    public function getStandardRate(): int
    {
        return $this->standardRate;
    }

    /**
     * @return int[]
     */
    public function getReducedRates(): array
    {
        return $this->reduced_rates;
    }

    /**
     * @return int[]
     */
    public function getSuperReducedRates(): array
    {
        return $this->superReducedRates;
    }

    /**
     * @return int[]
     */
    public function getParkingRates(): array
    {
        return $this->parkingRates;
    }
}

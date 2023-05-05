<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response;

use Wiesner\Currency\Service\Request\Enum\CountryCode;
use Wiesner\Currency\Service\Request\Response\ValueObject\ValueAddedTaxRate;

final class ValueAddedTaxRates
{
    /**
     * @param ValueAddedTaxRate[] $taxRates
     */
    private function __construct(
        private readonly array $taxRates
    ) {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function createFromArray(array $responseArray): ValueAddedTaxRates
    {
        foreach ($responseArray['rates'] as $countryCode => $rateResponse) {
            $taxRates[] = new ValueAddedTaxRate(
                CountryCode::from($countryCode),
                $rateResponse['standard_rate'],
                $rateResponse['reduced_rates'],
                $rateResponse['super_reduced_rates'],
                $rateResponse['parking_rates']
            );
        }

        if (!isset($taxRates)) {
            throw new \InvalidArgumentException(sprintf('There are not %s in array parameter of %s method in %s class.', 'rates', __FUNCTION__, self::class));
        }

        return new self($taxRates);
    }

    /**
     * @return ValueAddedTaxRate[]
     */
    public function getTaxRates(): array
    {
        return $this->taxRates;
    }

    public function getTaxRate(CountryCode $countryCode): ?ValueAddedTaxRate
    {
        foreach ($this->taxRates as $taxRate) {
            if ($taxRate->getCountryCode() === $countryCode) {
                return $taxRate;
            }
        }

        return null;
    }
}

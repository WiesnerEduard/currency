<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response;

use Symfony\Contracts\HttpClient\ResponseInterface;
use Wiesner\Currency\Service\Request\Enum\CountryCode;
use Wiesner\Currency\Service\Request\RequestServiceException;
use Wiesner\Currency\Service\Request\Response\ValueObject\ValueAddedTaxRate;

final class ValueAddedTaxRates implements ResponseObjectInterface
{
    /**
     * @param ValueAddedTaxRate[] $taxRates
     */
    private function __construct(
        private readonly array $taxRates
    ) {
    }

    /**
     * @throws RequestServiceException
     */
    public static function createFromResponse(ResponseInterface $response): ValueAddedTaxRates
    {
        try {
            $ratesResponse = $response->toArray()['rates'];

            $taxRates = [];

            foreach ($ratesResponse as $countryCode => $rateResponse) {
                $taxRates[] = new ValueAddedTaxRate(
                    CountryCode::from($countryCode),
                    $rateResponse['standard_rate'],
                    $rateResponse['reduced_rates'],
                    $rateResponse['super_reduced_rates'],
                    $rateResponse['parking_rates']
                );
            }

            return new self($taxRates);


        } catch (\Throwable $e) {
            throw RequestServiceException::createInResponseContext('ValueAddedTaxRates', $e);
        }
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

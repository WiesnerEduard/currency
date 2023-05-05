<?php

declare(strict_types=1);

namespace Wiesner\Currency\Endpoint;

use Wiesner\Currency\Service\Request\Enum\CountryCode;
use Wiesner\Currency\Service\Request\QueryParameters;
use Wiesner\Currency\Service\Request\RequestServiceException;
use Wiesner\Currency\Service\Request\RequestServiceInterface;
use Wiesner\Currency\Service\Request\Response\ValueAddedTaxRates;
use Wiesner\Currency\Service\Request\Response\ValueObject\ValueAddedTaxRate;

/**
 * VatRatesEndpoint PHP Class, which provides methods to retrieve information about VAT Rates in various countries.
 *
 * It provides methods, to get standard, reduced, super-reduced and parking ValueAddedTax Rates.
 */
class VatRatesEndpoint
{
    public function __construct(
        private readonly RequestServiceInterface $requestService
    ) {
    }

    /**
     * Get VAT Rates for countries from European Union.
     *
     * @param CountryCode[] $countries array of countries used to filter response
     *
     * @throws RequestServiceException
     */
    public function getEuropeanUnionVatRates(array $countries = null): ValueAddedTaxRates
    {
        return $this->requestService->getValueAddedTaxRates($this->createQueryParameters($countries));
    }

    /**
     * Get VAT Rates for countries from European Union as array.
     *
     * @param CountryCode[] $countries array of countries used to filter response
     *
     * @throws RequestServiceException
     */
    public function getEuropeanUnionVatRatesAsArray(array $countries = null): array
    {
        return $this->requestService->getValueAddedTaxRates($this->createQueryParameters($countries), true);
    }

    /**
     * Get VAT Rates for specific country from European Union.
     *
     * @param CountryCode $countryCode source country of VAT
     *
     * @throws RequestServiceException
     */
    public function getEuropeanUnionVatRate(CountryCode $countryCode): ValueAddedTaxRate
    {
        return $this->getEuropeanUnionVatRates([$countryCode])->getTaxRate($countryCode);
    }

    /**
     * @param CountryCode[] $symbols
     *
     * @internal This method is not covered by the backward compatibility promise for wiesner/currency
     */
    protected function createQueryParameters(array $symbols = null): QueryParameters
    {
        return (new QueryParameters())
            ->add('symbols', (null === $symbols) ? CountryCode::getAllValues() : CountryCode::getValuesOfCollection($symbols));
    }
}

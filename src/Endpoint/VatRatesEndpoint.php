<?php

declare(strict_types=1);

namespace Wiesner\Currency\Endpoint;

use Wiesner\Currency\Service\Request\Enum\CountryCode;
use Wiesner\Currency\Service\Request\QueryParameters;
use Wiesner\Currency\Service\Request\RequestService;
use Wiesner\Currency\Service\Request\RequestServiceException;
use Wiesner\Currency\Service\Request\Response\ValueAddedTaxRates;
use Wiesner\Currency\Service\Request\Response\ValueObject\ValueAddedTaxRate;

class VatRatesEndpoint
{
    public function __construct(
        private readonly RequestService $requestService
    ) {
    }

    /**
     * @param CountryCode[] $countries
     *
     * @throws RequestServiceException
     */
    public function getEuropeanUnionVatRates(array $countries = null): ValueAddedTaxRates
    {
        return $this->requestService->getValueAddedTaxRates($this->createQueryParameters($countries));
    }

    /**
     * @param CountryCode[] $countries
     *
     * @throws RequestServiceException
     */
    public function getEuropeanUnionVatRatesAsArray(array $countries = null): array
    {
        return $this->requestService->getValueAddedTaxRates($this->createQueryParameters($countries), true);
    }

    /**
     * @throws RequestServiceException
     */
    public function getEuropeanUnionVatRate(CountryCode $countryCode): ValueAddedTaxRate
    {
        return $this->getEuropeanUnionVatRates([$countryCode])->getTaxRate($countryCode);
    }

    /**
     * @param CountryCode[] $symbols
     */
    protected function createQueryParameters(array $symbols = null): QueryParameters
    {
        return (new QueryParameters())
            ->add('symbols', (null === $symbols) ? CountryCode::getAllValues() : CountryCode::getValuesOfCollection($symbols));
    }
}

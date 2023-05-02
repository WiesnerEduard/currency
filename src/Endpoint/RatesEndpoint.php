<?php

declare(strict_types=1);

namespace Wiesner\Currency\Endpoint;

use Wiesner\Currency\Service\Request\Enum\BankSource;
use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\QueryParameters;
use Wiesner\Currency\Service\Request\RequestService;
use Wiesner\Currency\Service\Request\RequestServiceException;
use Wiesner\Currency\Service\Request\Response\FluctuationRates;
use Wiesner\Currency\Service\Request\Response\Rates;
use Wiesner\Currency\Service\Request\Response\TimeSeriesRates;
use Wiesner\Currency\Service\Request\Response\ValueObject\FluctuationRate;
use Wiesner\Currency\Service\Request\Response\ValueObject\Rate;

/**
 * RatesEndpoint PHP Class, which provides methods to retrieve currency exchange rates.
 *
 * The RatesEndpoint class serves as a central repository for accessing relevant and up-to-date information about currency rates across multiple currencies.
 * This class provides a reliable and efficient way to retrieve and compare currency rates from various bank sources, helping users to make informed decisions about currency conversions.
 * RatesEndpoint class also offers historical data, allowing users to view and analyze past trends in currency exchange rates.
 * This feature can be particularly useful for businesses or individuals who need to track currency fluctuations over time,
 * such as when making long-term financial plans or analyzing historical trends.
 */
class RatesEndpoint
{
    public function __construct(
        private readonly RequestService $requestService,
        private readonly CurrencyCode $defaultCurrency,
        private readonly BankSource $defaultBankSource,
    ) {
    }

    /**
     * Retrieve actual currency exchange rates.
     *
     * @param CurrencyCode $base
     * @param CurrencyCode[] $symbols
     * @param float $amount
     * @param int $places
     * @param BankSource $source
     *
     * @return Rates
     * @throws RequestServiceException
     */
    public function getRates(CurrencyCode $base = null, array $symbols = null, float $amount = null, int $places = null, BankSource $source = null): Rates
    {
        return $this->requestService->getLatestRates($this->createQueryParameters($base, $symbols, $amount, $places, $source));
    }

    /**
     * @param CurrencyCode[] $symbols
     *
     * @throws RequestServiceException
     */
    public function getRatesAsArray(CurrencyCode $base = null, array $symbols = null, float $amount = null, int $places = null, BankSource $source = null): array
    {
        return $this->requestService->getLatestRates($this->createQueryParameters($base, $symbols, $amount, $places, $source), true);
    }

    /**
     * @throws RequestServiceException
     */
    public function getRate(CurrencyCode $base, CurrencyCode $to, float $amount = null, int $places = null, BankSource $source = null): Rate
    {
        return $this->getRates($base, [$to], $amount, $places, $source)->getRate($to);
    }

    /**
     * @param CurrencyCode[] $symbols
     *
     * @throws RequestServiceException
     */
    public function getHistoricalRates(\DateTimeImmutable $toDate, CurrencyCode $base = null, array $symbols = null, float $amount = null, int $places = null, BankSource $source = null): Rates
    {
        return $this->requestService->getHistoricalRates($toDate, $this->createQueryParameters($base, $symbols, $amount, $places, $source));
    }

    /**
     * @param CurrencyCode[] $symbols
     *
     * @throws RequestServiceException
     */
    public function getHistoricalRatesAsArray(\DateTimeImmutable $toDate, CurrencyCode $base = null, array $symbols = null, float $amount = null, int $places = null, BankSource $source = null): array
    {
        return $this->requestService->getHistoricalRates($toDate, $this->createQueryParameters($base, $symbols, $amount, $places, $source), true);
    }

    /**
     * @throws RequestServiceException
     */
    public function getHistoricalRate(\DateTimeImmutable $toDate, CurrencyCode $base, CurrencyCode $to, float $amount = null, int $places = null, BankSource $source = null): Rate
    {
        return $this->getHistoricalRates($toDate, $base, [$to], $amount, $places, $source)->getRate($to);
    }

    /**
     * @param CurrencyCode[] $symbols
     *
     * @throws RequestServiceException
     */
    public function getTimeSeriesRates(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, CurrencyCode $base = null, array $symbols = null, float $amount = null, int $places = null, BankSource $source = null): TimeSeriesRates
    {
        return $this->requestService->getTimeSeriesRates(
            startDate: $startDate,
            endDate: $endDate,
            parameters: $this->createQueryParameters($base, $symbols, $amount, $places, $source)
        );
    }

    /**
     * @param CurrencyCode[] $symbols
     *
     * @throws RequestServiceException
     */
    public function getTimeSeriesRatesAsArray(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, CurrencyCode $base = null, array $symbols = null, float $amount = null, int $places = null, BankSource $source = null): array
    {
        return $this->requestService->getTimeSeriesRates(
            startDate: $startDate,
            endDate: $endDate,
            parameters: $this->createQueryParameters($base, $symbols, $amount, $places, $source),
            rawResponse: true
        );
    }

    /**
     * @throws RequestServiceException
     */
    public function getTimeSeriesRate(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, CurrencyCode $base, CurrencyCode $to, float $amount = null, int $places = null, BankSource $source = null): Rate
    {
        return $this->getTimeSeriesRates($startDate, $endDate, $base, [$to], $amount, $places, $source)->getRate($to);
    }

    /**
     * @param CurrencyCode[] $symbols
     *
     * @throws RequestServiceException
     */
    public function getFluctuationRates(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, CurrencyCode $base = null, array $symbols = null, float $amount = null, int $places = null, BankSource $source = null): FluctuationRates
    {
        return $this->requestService->getFluctuationRates(
            baseCurrency: $base ?: $this->defaultCurrency,
            parameters: $this->createQueryParameters($base, $symbols, $amount, $places, $source)
        );
    }

    /**
     * @param CurrencyCode[] $symbols
     *
     * @throws RequestServiceException
     */
    public function getFluctuationRatesAsArray(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, CurrencyCode $base = null, array $symbols = null, float $amount = null, int $places = null, BankSource $source = null): array
    {
        return $this->requestService->getFluctuationRates(
            baseCurrency: $base ?: $this->defaultCurrency,
            parameters: $this->createQueryParameters($base, $symbols, $amount, $places, $source),
            rawResponse: true
        );
    }

    /**
     * @throws RequestServiceException
     */
    public function getFluctuationRate(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, CurrencyCode $base, CurrencyCode $to, float $amount = null, int $places = null, BankSource $source = null): FluctuationRate
    {
        return $this->getFluctuationRates($startDate, $endDate, $base, [$to], $amount, $places, $source)->getFluctuationRate($to);
    }

    /**
     * @param CurrencyCode[] $symbols
     */
    protected function createQueryParameters(CurrencyCode $base = null, array $symbols = null, float $amount = null, int $places = null, BankSource $source = null): QueryParameters
    {
        return (new QueryParameters())
            ->add('base', ($base ?: $this->defaultCurrency)->value)
            ->add('source', ($source ?: $this->defaultBankSource)->value)
            ->add('symbols', (null === $symbols) ? CurrencyCode::getAllValues() : CurrencyCode::getValuesOfCollection($symbols))
            ->add('places', $places)
            ->add('amount', $amount);
    }
}

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
     * Latest endpoint will return exchange rate data updated on daily basis.
     *
     * @param CurrencyCode|null   $base    currency used to calculate rates from
     * @param CurrencyCode[]|null $symbols array of currencies used to filter response
     * @param float|null          $amount  the amount to be converted from base currency
     * @param int|null            $places  round numbers to decimal place
     * @param BankSource|null     $source  source institution that provide rates
     *
     * @throws RequestServiceException
     *
     * @uses self::$defaultCurrency
     * @uses self::$defaultBankSource
     *
     * @api
     */
    public function getRates(CurrencyCode $base = null, array $symbols = null, float $amount = null, int $places = null, BankSource $source = null): Rates
    {
        return $this->requestService->getLatestRates($this->createQueryParameters($base, $symbols, $amount, $places, $source));
    }

    /**
     * Retrieve actual currency exchange rates as array.
     *
     * Latest endpoint will return exchange rate data updated on daily basis.
     *
     * @param CurrencyCode|null   $base    currency used to calculate rates from
     * @param CurrencyCode[]|null $symbols array of currencies used to filter response
     * @param float|null          $amount  the amount to be converted from base currency
     * @param int|null            $places  round numbers to decimal place
     * @param BankSource|null     $source  source institution that provide rates
     *
     * @throws RequestServiceException
     *
     * @uses self::$defaultCurrency
     * @uses self::$defaultBankSource
     *
     * @api
     */
    public function getRatesAsArray(CurrencyCode $base = null, array $symbols = null, float $amount = null, int $places = null, BankSource $source = null): array
    {
        return $this->requestService->getLatestRates($this->createQueryParameters($base, $symbols, $amount, $places, $source), true);
    }

    /**
     * Retrieve actual currency exchange rate for single currency.
     *
     * Latest endpoint will return exchange rate data updated on daily basis.
     *
     * @param CurrencyCode    $base   currency used to calculate rate from
     * @param CurrencyCode    $to     currency used to calculate rate to
     * @param float|null      $amount the amount to be converted from base currency
     * @param int|null        $places round numbers to decimal place
     * @param BankSource|null $source source institution that provide rates
     *
     * @throws RequestServiceException
     *
     * @uses self::$defaultBankSource
     *
     * @api
     */
    public function getRate(CurrencyCode $base, CurrencyCode $to, float $amount = null, int $places = null, BankSource $source = null): Rate
    {
        return $this->getRates($base, [$to], $amount, $places, $source)->getRate($to);
    }

    /**
     * Retrieve historical currency exchange rates.
     *
     * Historical rates are available for most currencies all the way back to the year of 1999.
     *
     * @param \DateTimeImmutable  $toDate  rates valid to date
     * @param CurrencyCode|null   $base    currency used to calculate rate from
     * @param CurrencyCode[]|null $symbols array of currencies used to filter response
     * @param float|null          $amount  the amount to be converted from base currency
     * @param int|null            $places  round numbers to decimal place
     * @param BankSource|null     $source  source institution that provide rates
     *
     * @throws RequestServiceException
     *
     * @uses self::$defaultCurrency
     * @uses self::$defaultBankSource
     *
     * @api
     */
    public function getHistoricalRates(\DateTimeImmutable $toDate, CurrencyCode $base = null, array $symbols = null, float $amount = null, int $places = null, BankSource $source = null): Rates
    {
        return $this->requestService->getHistoricalRates($toDate, $this->createQueryParameters($base, $symbols, $amount, $places, $source));
    }

    /**
     * Retrieve historical currency exchange rates as array.
     *
     * Historical rates are available for most currencies all the way back to the year of 1999.
     *
     * @param \DateTimeImmutable  $toDate  rates valid to date
     * @param CurrencyCode|null   $base    currency used to calculate rate from
     * @param CurrencyCode[]|null $symbols array of currencies used to filter response
     * @param float|null          $amount  the amount to be converted from base currency
     * @param int|null            $places  round numbers to decimal place
     * @param BankSource|null     $source  source institution that provide rates
     *
     * @throws RequestServiceException
     *
     * @uses self::$defaultCurrency
     * @uses self::$defaultBankSource
     *
     * @api
     */
    public function getHistoricalRatesAsArray(\DateTimeImmutable $toDate, CurrencyCode $base = null, array $symbols = null, float $amount = null, int $places = null, BankSource $source = null): array
    {
        return $this->requestService->getHistoricalRates($toDate, $this->createQueryParameters($base, $symbols, $amount, $places, $source), true);
    }

    /**
     * Retrieve historical currency exchange rate for single currency.
     *
     * Historical rates are available for most currencies all the way back to the year of 1999.
     *
     * @param \DateTimeImmutable $toDate rates valid to date
     * @param CurrencyCode       $base   currency used to calculate rate from
     * @param CurrencyCode       $to     currency used to calculate rate to
     * @param float|null         $amount the amount to be converted from base currency
     * @param int|null           $places round numbers to decimal place
     * @param BankSource|null    $source source institution that provide rates
     *
     * @throws RequestServiceException
     *
     * @uses self::$defaultBankSource
     *
     * @api
     */
    public function getHistoricalRate(\DateTimeImmutable $toDate, CurrencyCode $base, CurrencyCode $to, float $amount = null, int $places = null, BankSource $source = null): Rate
    {
        return $this->getHistoricalRates($toDate, $base, [$to], $amount, $places, $source)->getRate($to);
    }

    /**
     * Retrieve timeseries currency exchange rates.
     *
     * Timeseries rates are for daily historical rates between two dates of your choice, with a maximum time frame of 366 days.
     *
     * @param \DateTimeImmutable  $startDate the start date of your preferred timeframe
     * @param \DateTimeImmutable  $endDate   the end date of your preferred timeframe
     * @param CurrencyCode|null   $base      currency used to calculate rate from
     * @param CurrencyCode[]|null $symbols   array of currencies used to filter response
     * @param float|null          $amount    the amount to be converted from base currency
     * @param int|null            $places    round numbers to decimal place
     * @param BankSource|null     $source    source institution that provide rates
     *
     * @throws RequestServiceException
     *
     * @uses self::$defaultCurrency
     * @uses self::$defaultBankSource
     *
     * @api
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
     * Retrieve timeseries currency exchange rates as array.
     *
     * Timeseries rates are for daily historical rates between two dates of your choice, with a maximum time frame of 366 days.
     *
     * @param \DateTimeImmutable  $startDate the start date of your preferred timeframe
     * @param \DateTimeImmutable  $endDate   the end date of your preferred timeframe
     * @param CurrencyCode|null   $base      currency used to calculate rate from
     * @param CurrencyCode[]|null $symbols   array of currencies used to filter response
     * @param float|null          $amount    the amount to be converted from base currency
     * @param int|null            $places    round numbers to decimal place
     * @param BankSource|null     $source    source institution that provide rates
     *
     * @throws RequestServiceException
     *
     * @uses self::$defaultCurrency
     * @uses self::$defaultBankSource
     *
     * @api
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
     * Retrieve timeseries currency exchange rate for single currency.
     *
     * Timeseries rates are for daily historical rates between two dates of your choice, with a maximum time frame of 366 days.
     *
     * @param \DateTimeImmutable $startDate the start date of your preferred timeframe
     * @param \DateTimeImmutable $endDate   the end date of your preferred timeframe
     * @param CurrencyCode       $base      currency used to calculate rate from
     * @param CurrencyCode       $to        currency used to calculate rate to
     * @param float|null         $amount    the amount to be converted from base currency
     * @param int|null           $places    round numbers to decimal place
     * @param BankSource|null    $source    source institution that provide rates
     *
     * @throws RequestServiceException
     *
     * @uses self::$defaultBankSource
     *
     * @api
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

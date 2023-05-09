<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\Enum\Server;
use Wiesner\Currency\Service\Request\Response\ConvertCurrency;
use Wiesner\Currency\Service\Request\Response\FluctuationRates;
use Wiesner\Currency\Service\Request\Response\Rates;
use Wiesner\Currency\Service\Request\Response\TimeSeriesRates;
use Wiesner\Currency\Service\Request\Response\ValueAddedTaxRates;

class RequestService implements RequestServiceInterface
{
    private const LATEST_PATH = 'latest';
    private const TIME_SERIES_PATH = 'timeseries';
    private const FLUCTUATION_PATH = 'fluctuation';
    private const CONVERT_PATH = 'convert';
    private const VAT_RATES_PATH = 'vat_rates';

    public function __construct(
        private readonly Server $server,
        private readonly HttpClientInterface $client,
    ) {
    }

    /**
     * @throws RequestServiceException
     */
    public function makeRequest(string $path = '', QueryParameters $parameters = null): array
    {
        try {
            return $this->client->request('GET', sprintf('%s/%s', $this->server->value, $path), null === $parameters ? [] : $parameters->getQuery())->toArray();
        } catch (\Throwable $e) {
            throw RequestServiceException::create($this, __FUNCTION__, $e);
        }
    }

    /**
     * @throws RequestServiceException
     */
    public function getLatestRates(QueryParameters $parameters = null, bool $rawResponse = false): Rates|array
    {
        if ($rawResponse) {
            return $this->makeRequest(self::LATEST_PATH, $parameters);
        }

        try {
            return Rates::createFromArray($this->makeRequest(self::LATEST_PATH, $parameters));
        } catch (\Throwable $e) {
            throw RequestServiceException::createInResponseContext(Rates::class, $e);
        }
    }

    /**
     * @throws RequestServiceException
     */
    public function getHistoricalRates(\DateTimeImmutable $toDate, QueryParameters $parameters = null, bool $rawResponse = false): Rates|array
    {
        if ($rawResponse) {
            return $this->makeRequest($toDate->format('Y-m-d'), $parameters);
        }

        try {
            return Rates::createFromArray($this->makeRequest($toDate->format('Y-m-d'), $parameters));
        } catch (\Throwable $e) {
            throw RequestServiceException::createInResponseContext(Rates::class, $e);
        }
    }

    /**
     * @throws RequestServiceException
     */
    public function getTimeSeriesRates(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, QueryParameters $parameters = null, bool $rawResponse = false): TimeSeriesRates|array
    {
        $parameters = ($parameters ?: new QueryParameters())
            ->add('start_date', $startDate->format('Y-m-d'))
            ->add('end_date', $endDate->format('Y-m-d'));

        if ($rawResponse) {
            return $this->makeRequest(self::TIME_SERIES_PATH, $parameters);
        }

        try {
            return TimeSeriesRates::createFromArray($this->makeRequest(self::TIME_SERIES_PATH, $parameters));
        } catch (\Throwable $e) {
            throw RequestServiceException::createInResponseContext(TimeSeriesRates::class, $e);
        }
    }

    /**
     * @throws RequestServiceException
     */
    public function getFluctuationRates(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, CurrencyCode $baseCurrency, QueryParameters $parameters = null, bool $rawResponse = false): FluctuationRates|array
    {
        $parameters = ($parameters ?: new QueryParameters())
            ->add('base', $baseCurrency->value)
            ->add('start_date', $startDate->format('Y-m-d'))
            ->add('end_date', $endDate->format('Y-m-d'));

        if ($rawResponse) {
            return $this->makeRequest(self::FLUCTUATION_PATH, $parameters);
        }

        try {
            return FluctuationRates::createFromArrayAndCurrency($this->makeRequest(self::FLUCTUATION_PATH, $parameters), $baseCurrency);
        } catch (\Throwable $e) {
            throw RequestServiceException::createInResponseContext(FluctuationRates::class, $e);
        }
    }

    /**
     * @throws RequestServiceException
     */
    public function getConvertCurrency(QueryParameters $parameters = null, bool $rawResponse = false): ConvertCurrency|array
    {
        if ($rawResponse) {
            return $this->makeRequest(self::CONVERT_PATH, $parameters);
        }

        try {
            return ConvertCurrency::createFromArray($this->makeRequest(self::CONVERT_PATH, $parameters));
        } catch (\Throwable $e) {
            throw RequestServiceException::createInResponseContext(ConvertCurrency::class, $e);
        }
    }

    /**
     * @throws RequestServiceException
     */
    public function getValueAddedTaxRates(QueryParameters $parameters = null, bool $rawResponse = false): ValueAddedTaxRates|array
    {
        if ($rawResponse) {
            return $this->makeRequest(self::VAT_RATES_PATH, $parameters);
        }

        try {
            return ValueAddedTaxRates::createFromArray($this->makeRequest(self::VAT_RATES_PATH, $parameters));
        } catch (\Throwable $e) {
            throw RequestServiceException::createInResponseContext(ValueAddedTaxRates::class, $e);
        }
    }
}

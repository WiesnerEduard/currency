<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request;

use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\Enum\Server;
use Wiesner\Currency\Service\Request\Response\ConvertCurrency;
use Wiesner\Currency\Service\Request\Response\FluctuationRates;
use Wiesner\Currency\Service\Request\Response\Rates;
use Wiesner\Currency\Service\Request\Response\TimeSeriesRates;

class RequestService
{
    private const LATEST_PATH = 'latest';
    private const TIME_SERIES_PATH = 'timeseries';
    private const FLUCTUATION_PATH = 'fluctuation';
    private const CONVERT_PATH = 'convert';

    public function __construct(
        private readonly Server $server,
        private readonly HttpClientInterface $client,
    ) {
    }

    /**
     * @throws RequestServiceException
     */
    public function makeRequest(string $path = '', QueryParameters $parameters = null): ResponseInterface
    {
        try {
            return $this->client->request('GET', sprintf('%s/%s', $this->server->value, $path), $parameters?->getQuery());
        } catch (TransportExceptionInterface $e) {
            throw RequestServiceException::create('makeRequest', $e);
        }
    }

    /**
     * @throws RequestServiceException
     */
    public function getLatestRates(QueryParameters $parameters = null, bool $rawResponse = false): Rates|array
    {
        if ($rawResponse) {
            try {
                return $this->makeRequest(self::LATEST_PATH, $parameters)->toArray();
            } catch (ExceptionInterface $e) {
                throw RequestServiceException::create('getLatestRates', $e);
            }
        }

        return Rates::createFromResponse($this->makeRequest(self::LATEST_PATH, $parameters));
    }

    /**
     * @throws RequestServiceException
     */
    public function getHistoricalRates(\DateTimeImmutable $toDate, QueryParameters $parameters = null, bool $rawResponse = false): Rates|array
    {
        if ($rawResponse) {
            try {
                $this->makeRequest($toDate->format('Y-m-d'), $parameters)->toArray();
            } catch (ExceptionInterface $e) {
                throw RequestServiceException::create('getConvertCurrency', $e);
            }
        }

        return Rates::createFromResponse($this->makeRequest($toDate->format('Y-m-d'), $parameters));
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
            try {
                $this->makeRequest(self::TIME_SERIES_PATH, $parameters)->toArray();
            } catch (ExceptionInterface $e) {
                throw RequestServiceException::create('getConvertCurrency', $e);
            }
        }

        return TimeSeriesRates::createFromResponse($this->makeRequest(self::TIME_SERIES_PATH, $parameters));
    }

    /**
     * @throws RequestServiceException
     */
    public function getFluctuationRates(CurrencyCode $baseCurrency, QueryParameters $parameters = null, bool $rawResponse = false): FluctuationRates|array
    {
        $parameters = ($parameters ?: new QueryParameters())->add('base', $baseCurrency->value);

        if ($rawResponse) {
            try {
                $this->makeRequest(self::FLUCTUATION_PATH, $parameters)->toArray();
            } catch (ExceptionInterface $e) {
                throw RequestServiceException::create('getFluctuationRates', $e);
            }
        }

        return FluctuationRates::createFromResponseAndBaseCurrency($this->makeRequest(self::FLUCTUATION_PATH, $parameters), $baseCurrency);
    }

    /**
     * @throws RequestServiceException
     */
    public function getConvertCurrency(QueryParameters $parameters = null, bool $rawResponse = false): ConvertCurrency|array
    {
        if ($rawResponse) {
            try {
                return $this->makeRequest(self::CONVERT_PATH, $parameters)->toArray();
            } catch (ExceptionInterface $e) {
                throw RequestServiceException::create('getConvertCurrency', $e);
            }
        }

        return ConvertCurrency::createFromResponse($this->makeRequest(self::CONVERT_PATH, $parameters));
    }
}

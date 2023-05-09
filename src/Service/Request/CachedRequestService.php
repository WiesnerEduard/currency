<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request;

use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\Response\ConvertCurrency;
use Wiesner\Currency\Service\Request\Response\FluctuationRates;
use Wiesner\Currency\Service\Request\Response\Rates;
use Wiesner\Currency\Service\Request\Response\TimeSeriesRates;
use Wiesner\Currency\Service\Request\Response\ValueAddedTaxRates;

class CachedRequestService implements RequestServiceInterface
{
    private const CACHE_EXPIRATION_TIME_IN_SECONDS = 1;

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly RequestService $requestService
    ) {
    }

    /**
     * @throws RequestServiceException
     */
    public function getLatestRates(QueryParameters $parameters = null, bool $rawResponse = false): Rates|array
    {
        $cachedResponse = $this->getCachedResponse(__FUNCTION__, $parameters);

        try {
            return (true === $rawResponse) ? $cachedResponse['data'] : Rates::createFromArray($cachedResponse['data']);
        } catch (\Exception $e) {
            $this->deleteKeyFromCache($cachedResponse['key']);
            throw RequestServiceException::createInResponseContext(Rates::class, $e);
        }
    }

    /**
     * @throws RequestServiceException
     */
    public function getHistoricalRates(\DateTimeImmutable $toDate, QueryParameters $parameters = null, bool $rawResponse = false): Rates|array
    {
        $cachedResponse = $this->getCachedResponse(__FUNCTION__, $toDate, $parameters);

        try {
            return (true === $rawResponse) ? $cachedResponse['data'] : Rates::createFromArray($cachedResponse['data']);
        } catch (\Exception $e) {
            $this->deleteKeyFromCache($cachedResponse['key']);
            throw RequestServiceException::createInResponseContext(Rates::class, $e);
        }
    }

    /**
     * @throws RequestServiceException
     */
    public function getTimeSeriesRates(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, QueryParameters $parameters = null, bool $rawResponse = false): TimeSeriesRates|array
    {
        $cachedResponse = $this->getCachedResponse(__FUNCTION__, $startDate, $endDate, $parameters);

        try {
            return (true === $rawResponse) ? $cachedResponse['data'] : TimeSeriesRates::createFromArray($cachedResponse['data']);
        } catch (\Throwable $e) {
            $this->deleteKeyFromCache($cachedResponse['key']);
            throw RequestServiceException::createInResponseContext(TimeSeriesRates::class, $e);
        }
    }

    /**
     * @throws RequestServiceException
     */
    public function getFluctuationRates(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, CurrencyCode $baseCurrency, QueryParameters $parameters = null, bool $rawResponse = false): FluctuationRates|array
    {
        $cachedResponse = $this->getCachedResponse(__FUNCTION__, $startDate, $endDate, $baseCurrency, $parameters);

        try {
            return (true === $rawResponse) ? $cachedResponse : FluctuationRates::createFromArrayAndCurrency($cachedResponse['data'], $baseCurrency);
        } catch (\Throwable $e) {
            $this->deleteKeyFromCache($cachedResponse['key']);
            throw RequestServiceException::createInResponseContext(FluctuationRates::class, $e);
        }
    }

    /**
     * @throws RequestServiceException
     */
    public function getConvertCurrency(QueryParameters $parameters = null, bool $rawResponse = false): ConvertCurrency|array
    {
        $cachedResponse = $this->getCachedResponse(__FUNCTION__, $parameters);

        try {
            return (true === $rawResponse) ? $cachedResponse : ConvertCurrency::createFromArray($cachedResponse['data']);
        } catch (\Throwable $e) {
            $this->deleteKeyFromCache($cachedResponse['key']);
            throw RequestServiceException::createInResponseContext(ConvertCurrency::class, $e);
        }
    }

    /**
     * @throws RequestServiceException
     */
    public function getValueAddedTaxRates(QueryParameters $parameters = null, bool $rawResponse = false): ValueAddedTaxRates|array
    {
        $cachedResponse = $this->getCachedResponse(__FUNCTION__, $parameters);

        try {
            return (null === $rawResponse) ? $cachedResponse : ValueAddedTaxRates::createFromArray($cachedResponse['data']);
        } catch (\Throwable $e) {
            $this->deleteKeyFromCache($cachedResponse['key']);
            throw RequestServiceException::createInResponseContext(ValueAddedTaxRates::class, $e);
        }
    }

    /**
     * @param string $method methodName to call on RequestService::CLASS
     * @param mixed  $args   arguments passed to $method, Last argument must be QueryParameters::CLASS Type
     *
     * @return array{key: string, data: array}
     *
     * @throws RequestServiceException
     *
     * @internal
     */
    private function getCachedResponse(string $method, mixed ...$args): array
    {
        try {
            $parameters = end($args);
            reset($args);

            if (!($parameters instanceof QueryParameters)) {
                throw new \InvalidArgumentException(sprintf('Last argument of %s method must be %s type.', __FUNCTION__, QueryParameters::class));
            }

            $key = $parameters->encode($method) ?? $method;

            $args[] = true;

            $data = $this->cache->get($key, function (ItemInterface $item) use ($method, $args) {
                $item->expiresAfter(self::CACHE_EXPIRATION_TIME_IN_SECONDS);

                return $this->requestService->$method(...$args);
            });

            return ['key' => $key, 'data' => $data];
        } catch (\Throwable $e) {
            throw RequestServiceException::create($this, __FUNCTION__, $e);
        }
    }

    /**
     * @internal
     */
    private function deleteKeyFromCache(string $key): void
    {
        try {
            $this->cache->delete($key);
        } catch (InvalidArgumentException $e) {
            trigger_error(sprintf('Warning in method %s of %s class: Cannot delete key %s from cache, because it is invalid.', __FUNCTION__, $this::class, $key), E_USER_WARNING);
        }
    }
}

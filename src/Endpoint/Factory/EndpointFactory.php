<?php

declare(strict_types=1);

namespace Wiesner\Currency\Endpoint\Factory;

use Symfony\Component\HttpClient\HttpClient;
use Wiesner\Currency\Configuration\ApplicationConfiguration;
use Wiesner\Currency\Endpoint\ConvertCurrencyEndpoint;
use Wiesner\Currency\Endpoint\RatesEndpoint;
use Wiesner\Currency\Endpoint\VatRatesEndpoint;
use Wiesner\Currency\Service\Request\CachedRequestService;
use Wiesner\Currency\Service\Request\RequestService;
use Wiesner\Currency\Service\Request\RequestServiceInterface;

class EndpointFactory
{
    public static function createConvertCurrencyEndpoint(ApplicationConfiguration $configuration): ConvertCurrencyEndpoint
    {
        return new ConvertCurrencyEndpoint(self::createRequestService($configuration), $configuration->getDefaultBankSource());
    }

    public static function createRatesEndpoint(ApplicationConfiguration $configuration): RatesEndpoint
    {
        return new RatesEndpoint(self::createRequestService($configuration), $configuration->getDefaultCurrency(), $configuration->getDefaultBankSource());
    }

    public static function createVatRatesEndpoint(ApplicationConfiguration $configuration): VatRatesEndpoint
    {
        return new VatRatesEndpoint(self::createRequestService($configuration));
    }

    private static function createRequestService(ApplicationConfiguration $configuration): RequestServiceInterface
    {
        $requestService = new RequestService($configuration->getServer(), HttpClient::create());

        return null === $configuration->getCache() ? $requestService : new CachedRequestService($configuration->getCache(), $requestService);
    }
}

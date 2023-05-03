<?php

declare(strict_types=1);

namespace Wiesner\Currency\Endpoint;

use Symfony\Component\HttpClient\HttpClient;
use Wiesner\Currency\Service\Request\Enum\BankSource;
use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\Enum\Server;
use Wiesner\Currency\Service\Request\RequestService;

class EndpointFactory
{
    public const DEFAULT_SERVER = Server::EuCentral;
    public const DEFAULT_CURRENCY = CurrencyCode::Eur;
    public const DEFAULT_BANK_SOURCE = BankSource::EuropeanCentralBank;

    public static function RatesEndpoint(Server $server = self::DEFAULT_SERVER, CurrencyCode $defaultCurrency = self::DEFAULT_CURRENCY, BankSource $source = self::DEFAULT_BANK_SOURCE): RatesEndpoint
    {
        return new RatesEndpoint(static::createRequestService($server), $defaultCurrency, $source);
    }

    public static function ConvertCurrencyEndpoint(Server $server = self::DEFAULT_SERVER, BankSource $source = self::DEFAULT_BANK_SOURCE): ConvertCurrencyEndpoint
    {
        return new ConvertCurrencyEndpoint(static::createRequestService($server), $source);
    }

    public static function VatRatesEndpoint(Server $server = self::DEFAULT_SERVER): VatRatesEndpoint
    {
        return new VatRatesEndpoint(static::createRequestService($server));
    }

    private static function createRequestService(Server $server): RequestService
    {
        return new RequestService($server, HttpClient::create());
    }
}

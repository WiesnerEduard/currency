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
    public static function latestEndpoint(Server $server = Server::EuCentral, CurrencyCode $defaultCurrency = CurrencyCode::Eur, BankSource $source = BankSource::EuropeanCentralBank): LatestEndpoint
    {
        return new LatestEndpoint(static::createRequestService($server), $defaultCurrency, $source);
    }

    private static function createRequestService(Server $server): RequestService
    {
        return new RequestService($server, HttpClient::create());
    }
}

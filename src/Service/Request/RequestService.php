<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Wiesner\Currency\Service\Request\Enum\Server;
use Wiesner\Currency\Service\Request\Response\LatestRates;

class RequestService
{
    private const LATEST_PATH = 'latest';

    public function __construct(
        private readonly Server $server,
        private readonly HttpClientInterface $client,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function makeRequest(string $path = '', QueryParameters $parameters = null): ResponseInterface
    {
        return $this->client->request('GET', sprintf('%s/%s', $this->server->value, $path), $parameters?->getQuery());
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getLatestRates(QueryParameters $parameters = null, bool $rawResponse = false): LatestRates|array
    {
        if ($rawResponse) {
            return $this->makeRequest(self::LATEST_PATH, $parameters)->toArray();
        }

        return LatestRates::createFromResponse($this->makeRequest(self::LATEST_PATH, $parameters));
    }
}

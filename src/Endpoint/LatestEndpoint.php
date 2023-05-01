<?php

declare(strict_types=1);

namespace Wiesner\Currency\Endpoint;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Wiesner\Currency\Service\Request\Enum\BankSource;
use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\QueryParameters;
use Wiesner\Currency\Service\Request\RequestService;
use Wiesner\Currency\Service\Request\Response\LatestRates;

class LatestEndpoint
{
    public function __construct(
        private readonly RequestService $requestService,
        private readonly CurrencyCode $defaultCurrency,
        private readonly BankSource $defaultBankSource,
    ) {
    }

    /**
     * @param CurrencyCode[] $symbols
     *
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getRates(CurrencyCode $base = null, array $symbols = null, int $amount = null, int $places = null, BankSource $source = null): LatestRates
    {
        return $this->requestService->getLatestRates($this->createQueryParameters($base, $symbols, $amount, $places, $source));
    }

    /**
     * @param CurrencyCode[] $symbols
     *
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getRatesAsArray(CurrencyCode $base = null, array $symbols = null, int $amount = null, int $places = null, BankSource $source = null): array
    {
        return $this->requestService->getLatestRates($this->createQueryParameters($base, $symbols, $amount, $places, $source), true);
    }

    /**
     * @param CurrencyCode[] $symbols
     */
    protected function createQueryParameters(CurrencyCode $base = null, array $symbols = null, int $amount = null, int $places = null, BankSource $source = null): QueryParameters
    {
        return (new QueryParameters())
            ->add('base', (null === $base) ? $this->defaultCurrency->value : $base->value)
            ->add('source', (null === $source) ? $this->defaultBankSource->value : $source->value)
            ->add('symbols', (null === $symbols) ? CurrencyCode::getAllValues() : CurrencyCode::getValuesOfCollection($symbols))
            ->add('places', $places)
            ->add('amount', $amount);
    }
}

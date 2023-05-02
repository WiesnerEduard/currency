<?php

declare(strict_types=1);

namespace Wiesner\Currency\Endpoint;

use Wiesner\Currency\Service\Request\Enum\BankSource;
use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\QueryParameters;
use Wiesner\Currency\Service\Request\RequestService;
use Wiesner\Currency\Service\Request\RequestServiceException;
use Wiesner\Currency\Service\Request\Response\ConvertCurrency;

class ConvertCurrencyEndpoint
{
    public function __construct(
        private readonly RequestService $requestService,
        private readonly BankSource $defaultBankSource
    ) {
    }

    /**
     * @throws RequestServiceException
     */
    public function convert(CurrencyCode $from, CurrencyCode $to, float $amount = null, \DateTimeImmutable $toDate = null, int $places = null, BankSource $source = null): ConvertCurrency
    {
        return $this->requestService->getConvertCurrency($this->creteQueryParameters($from, $to, $amount, $toDate, $places, $source));
    }

    /**
     * @throws RequestServiceException
     */
    public function convertAsArray(CurrencyCode $from, CurrencyCode $to, float $amount = null, \DateTimeImmutable $toDate = null, int $places = null, BankSource $source = null): array
    {
        return $this->requestService->getConvertCurrency($this->creteQueryParameters($from, $to, $amount, $toDate, $places, $source), true);
    }

    protected function creteQueryParameters(CurrencyCode $from, CurrencyCode $to, float $amount = null, \DateTimeImmutable $toDate = null, int $places = null, BankSource $source = null): QueryParameters
    {
        return (new QueryParameters())
            ->add('from', $from->value)
            ->add('to', $to->value)
            ->add('amount', $amount)
            ->add('date', $toDate?->format('Y-m-d'))
            ->add('places', $places)
            ->add('source', (null === $source) ? $this->defaultBankSource->value : $source->value);
    }
}

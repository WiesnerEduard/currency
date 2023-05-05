<?php

declare(strict_types=1);

namespace Wiesner\Currency\Endpoint;

use Wiesner\Currency\Service\Request\Enum\BankSource;
use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\QueryParameters;
use Wiesner\Currency\Service\Request\RequestServiceException;
use Wiesner\Currency\Service\Request\RequestServiceInterface;
use Wiesner\Currency\Service\Request\Response\ConvertCurrency;

/**
 * ConvertCurrencyEndpoint PHP Class, which provides methods to convert currencies.
 */
class ConvertCurrencyEndpoint
{
    public function __construct(
        private readonly RequestServiceInterface $requestService,
        private readonly BankSource $defaultBankSource
    ) {
    }

    /**
     * Convert currency from one to another.
     *
     * @param CurrencyCode            $from   source currency used to convert from
     * @param CurrencyCode            $to     destination currency used to convert to
     * @param float|null              $amount teh amount of source currency
     * @param \DateTimeImmutable|null $toDate date of rates used to calculate result
     * @param int|null                $places round numbers to decimal place
     * @param BankSource|null         $source source institution that provide rates
     *
     * @throws RequestServiceException
     */
    public function convert(CurrencyCode $from, CurrencyCode $to, float $amount = null, \DateTimeImmutable $toDate = null, int $places = null, BankSource $source = null): ConvertCurrency
    {
        return $this->requestService->getConvertCurrency($this->creteQueryParameters($from, $to, $amount, $toDate, $places, $source));
    }

    /**
     * Convert currency from one to another and return result as array.
     *
     * @param CurrencyCode            $from   source currency used to convert from
     * @param CurrencyCode            $to     destination currency used to convert to
     * @param float|null              $amount teh amount of source currency
     * @param \DateTimeImmutable|null $toDate date of rates used to calculate result
     * @param int|null                $places round numbers to decimal place
     * @param BankSource|null         $source source institution that provide rates
     *
     * @throws RequestServiceException
     */
    public function convertAsArray(CurrencyCode $from, CurrencyCode $to, float $amount = null, \DateTimeImmutable $toDate = null, int $places = null, BankSource $source = null): array
    {
        return $this->requestService->getConvertCurrency($this->creteQueryParameters($from, $to, $amount, $toDate, $places, $source), true);
    }

    /**
     * @internal This method is not covered by the backward compatibility promise for wiesner/currency
     */
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

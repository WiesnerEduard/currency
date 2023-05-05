<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request;

use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\Response\ConvertCurrency;
use Wiesner\Currency\Service\Request\Response\FluctuationRates;
use Wiesner\Currency\Service\Request\Response\Rates;
use Wiesner\Currency\Service\Request\Response\TimeSeriesRates;
use Wiesner\Currency\Service\Request\Response\ValueAddedTaxRates;

interface RequestServiceInterface
{
    public function getLatestRates(QueryParameters $parameters = null, bool $rawResponse = false): Rates|array;

    public function getHistoricalRates(\DateTimeImmutable $toDate, QueryParameters $parameters = null, bool $rawResponse = false): Rates|array;

    public function getTimeSeriesRates(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, QueryParameters $parameters = null, bool $rawResponse = false): TimeSeriesRates|array;

    public function getFluctuationRates(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, CurrencyCode $baseCurrency, QueryParameters $parameters = null, bool $rawResponse = false): FluctuationRates|array;

    public function getConvertCurrency(QueryParameters $parameters = null, bool $rawResponse = false): ConvertCurrency|array;

    public function getValueAddedTaxRates(QueryParameters $parameters = null, bool $rawResponse = false): ValueAddedTaxRates|array;
}

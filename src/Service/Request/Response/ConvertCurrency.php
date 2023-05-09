<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response;

use Wiesner\Currency\Service\Request\Enum\CurrencyCode;

final class ConvertCurrency
{
    private function __construct(
        private readonly CurrencyCode $from,
        private readonly CurrencyCode $to,
        private readonly float $fromAmount,
        private readonly float $toAmount,
        private readonly \DateTimeImmutable $date,
    ) {
    }

    /**
     * @throws \Exception
     */
    public static function createFromArray(array $responseArray): ConvertCurrency
    {
        return new self(
            CurrencyCode::from($responseArray['query']['from']),
            CurrencyCode::from($responseArray['query']['to']),
            (float) $responseArray['query']['amount'],
            (float) $responseArray['result'],
            new \DateTimeImmutable($responseArray['date'])
        );
    }

    public function getFrom(): CurrencyCode
    {
        return $this->from;
    }

    public function getTo(): CurrencyCode
    {
        return $this->to;
    }

    public function getFromAmount(): float
    {
        return $this->fromAmount;
    }

    public function getResult(): float
    {
        return $this->toAmount;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }
}

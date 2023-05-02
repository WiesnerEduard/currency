<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response;

use Symfony\Contracts\HttpClient\ResponseInterface;
use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\RequestServiceException;
use Wiesner\Currency\Service\Request\Response\ValueObject\Rate;

final class ConvertCurrency implements ResponseObjectInterface
{
    private function __construct(
        private readonly CurrencyCode $from,
        private readonly float $amount,
        private readonly float $result,
        private readonly Rate $rate,
        private readonly \DateTimeImmutable $toDate,
        private readonly bool $historical
    ) {
    }

    /**
     * @throws RequestServiceException
     */
    public static function createFromResponse(ResponseInterface $response): ConvertCurrency
    {
        try {
            $responseArray = $response->toArray();

            return new self(
                CurrencyCode::from($responseArray['query']['from']),
                $responseArray['query']['amount'],
                $responseArray['result'],
                new Rate(CurrencyCode::from($responseArray['query']['to']), $responseArray['info']['rate']),
                new \DateTimeImmutable($responseArray['date']),
                $responseArray['historical']
            );
        } catch (\Throwable $e) {
            throw RequestServiceException::createInResponseContext('ConvertCurrency', $e);
        }
    }

    public function getFrom(): CurrencyCode
    {
        return $this->from;
    }

    public function getTo(): CurrencyCode
    {
        return $this->rate->getCurrencyCode();
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getResult(): float
    {
        return $this->result;
    }

    public function getRate(): Rate
    {
        return $this->rate;
    }

    public function getToDate(): \DateTimeImmutable
    {
        return $this->toDate;
    }

    public function isHistorical(): bool
    {
        return $this->historical;
    }
}

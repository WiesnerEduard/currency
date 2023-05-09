<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response;

use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\Response\ValueObject\Rate;

final class Rates
{
    /**
     * @param Rate[] $rates
     */
    private function __construct(
        private readonly array $rates
    ) {
    }

    /**
     * @throws \Exception
     */
    public static function createFromArray(array $responseArray): Rates
    {
        $rates = [];
        $baseCurrency = CurrencyCode::from($responseArray['base']);
        $baseAmount = (float) $responseArray['rates'][$responseArray['base']];
        $date = new \DateTimeImmutable($responseArray['date']);

        foreach ($responseArray['rates'] as $currency => $targetAmount) {
            $rates[] = new Rate($date, $baseCurrency, CurrencyCode::from($currency), $baseAmount, (float) $targetAmount);
        }

        return new self($rates);
    }

    /**
     * @return Rate[]
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    public function getRateByCurrency(CurrencyCode $currencyCode): ?Rate
    {
        foreach ($this->rates as $rate) {
            if ($rate->getTargetCurrency() === $currencyCode) {
                return $rate;
            }
        }

        return null;
    }


}

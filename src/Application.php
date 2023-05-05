<?php

declare(strict_types=1);

namespace Wiesner\Currency;

use Wiesner\Currency\Configuration\ApplicationConfiguration;
use Wiesner\Currency\Endpoint\ConvertCurrencyEndpoint;
use Wiesner\Currency\Endpoint\Factory\EndpointFactory;
use Wiesner\Currency\Endpoint\RatesEndpoint;
use Wiesner\Currency\Endpoint\VatRatesEndpoint;

class Application
{
    private readonly ApplicationConfiguration $configuration;
    private ?ConvertCurrencyEndpoint $convertCurrencyEndpoint = null;
    private ?RatesEndpoint $ratesEndpoint = null;
    private ?VatRatesEndpoint $vatRatesEndpoint = null;

    public function __construct(ApplicationConfiguration $configuration = null)
    {
        if (null === $configuration) {
            $this->configuration = ApplicationConfiguration::createSystemCacheConfiguration();
        } else {
            $this->configuration = $configuration;
        }
    }

    public function getConvertCurrencyEndpoint(): ConvertCurrencyEndpoint
    {
        if (null === $this->convertCurrencyEndpoint) {
            $this->convertCurrencyEndpoint = EndpointFactory::createConvertCurrencyEndpoint($this->configuration);
        }

        return $this->convertCurrencyEndpoint;
    }

    public function getRatesEndpoint(): RatesEndpoint
    {
        if (null === $this->ratesEndpoint) {
            $this->ratesEndpoint = EndpointFactory::createRatesEndpoint($this->configuration);
        }

        return $this->ratesEndpoint;
    }

    public function getVatRatesEndpoint(): VatRatesEndpoint
    {
        if (null === $this->vatRatesEndpoint) {
            $this->vatRatesEndpoint = EndpointFactory::createVatRatesEndpoint($this->configuration);
        }

        return $this->vatRatesEndpoint;
    }
}

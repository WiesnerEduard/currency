<?php

declare(strict_types=1);

namespace Wiesner\Currency\Configuration;

use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Wiesner\Currency\Service\Request\Enum\BankSource;
use Wiesner\Currency\Service\Request\Enum\CurrencyCode;
use Wiesner\Currency\Service\Request\Enum\Server;

final class ApplicationConfiguration
{
    private const DEFAULT_SERVER = Server::EuCentral;
    private const DEFAULT_CURRENCY = CurrencyCode::Eur;
    private const DEFAULT_BANK_SOURCE = BankSource::EuropeanCentralBank;

    private function __construct(
        private ?CacheInterface $cache,
        private Server $server,
        private CurrencyCode $defaultCurrency,
        private BankSource $defaultBankSource
    ) {
    }

    /**
     * Create default configuration without cache.
     *
     * Use this method carefully, recommended way how to use Currency library is using cache with cache.
     *
     * @see ApplicationConfiguration::createSystemCacheConfiguration()
     */
    public static function createConfiguration(): self
    {
        return new self(null, self::DEFAULT_SERVER, self::DEFAULT_CURRENCY, self::DEFAULT_BANK_SOURCE);
    }

    /**
     * Create default configuration with cache.
     *
     * Configuration Uses the best possible cache that your runtime supports.
     */
    public static function createSystemCacheConfiguration(): self
    {
        return self::createConfiguration()->useSystemCache();
    }

    public function getCache(): ?CacheInterface
    {
        return $this->cache;
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function getDefaultCurrency(): CurrencyCode
    {
        return $this->defaultCurrency;
    }

    public function getDefaultBankSource(): BankSource
    {
        return $this->defaultBankSource;
    }

    public function useCustomCache(CacheInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    public function useSystemCache(string $namespace = 'currency', int $defaultLifetime = 0, string $version = '', LoggerInterface $logger = null): self
    {
        $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$namespace;
        $adapter = AbstractAdapter::createSystemCache(
            namespace: $namespace,
            defaultLifetime: $defaultLifetime,
            version: $version,
            directory: $dir,
            logger: $logger
        );

        if ($adapter instanceof CacheInterface) {
            $this->cache = $adapter;
        } else {
            $this->cache = new FilesystemAdapter($namespace, $defaultLifetime, $dir);
        }

        return $this;
    }

    public function setServer(Server $server): self
    {
        $this->server = $server;

        return $this;
    }

    public function setDefaultCurrency(CurrencyCode $defaultCurrency): self
    {
        $this->defaultCurrency = $defaultCurrency;

        return $this;
    }

    public function setDefaultBankSource(BankSource $defaultBankSource): self
    {
        $this->defaultBankSource = $defaultBankSource;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request;

final class QueryParameters
{
    private array $params = [];

    public function add(string $name, mixed $value): self
    {
        if (null !== $value) {
            $this->params[$name] = $value;
        }

        return $this;
    }

    public function getQuery(): array
    {
        return empty($this->params) ? [] : ['query' => $this->params];
    }
}

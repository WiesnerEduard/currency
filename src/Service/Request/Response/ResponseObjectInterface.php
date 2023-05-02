<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Response;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface ResponseObjectInterface
{
    public static function createFromResponse(ResponseInterface $response): self;
}

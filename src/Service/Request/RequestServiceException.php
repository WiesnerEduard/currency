<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request;

class RequestServiceException extends \Exception
{
    public static function create(RequestServiceInterface $requestService, string $methodName, \Throwable $previous): RequestServiceException
    {
        return new self(message: sprintf('Cannot process request by calling method %s in %s.', $methodName, $requestService::class), previous: $previous);
    }

    public static function createInResponseContext(string $responseObjectName, \Throwable $previous): RequestServiceException
    {
        return new self(message: sprintf('Cannot create %s, in RequestService.', $responseObjectName), previous: $previous);
    }
}

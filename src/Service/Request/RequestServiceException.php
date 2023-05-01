<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request;

class RequestServiceException extends \Exception
{
    public static function create(string $methodName, \Throwable $previous): RequestServiceException
    {
        return new self(message: "Cannot process request by calling method $methodName() in RequestService.", previous: $previous);
    }

    public static function createInResponseContext(string $responseObjectName, \Throwable $previous): RequestServiceException
    {
        return new self(message: "Cannot create $responseObjectName, in RequestService.", previous: $previous);
    }
}

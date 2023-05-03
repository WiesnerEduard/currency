<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Enum;

enum Server: string
{
    case EuCentral = 'https://api.exchangerate.host';
    case EuScandinavian = 'https://api-eu.exchangerate.host';
    case UsCentral = 'https://api-us.exchangerate.host';
}

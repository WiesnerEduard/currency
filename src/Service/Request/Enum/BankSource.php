<?php

declare(strict_types=1);

namespace Wiesner\Currency\Service\Request\Enum;

enum BankSource: string
{
    case InternationalMonetaryFund = 'imf';
    case ReverseBankOfAustralia = 'rba';
    case BankOfCanada = 'boc';
    case SwissNationalBank = 'snb';
    case TheCentralBankOfTheRussianFederation = 'cbr';
    case NationalBankOfUkraine = 'nbu';
    case NationalBankOfRomania = 'bnro';
    case BankOfIsrael = 'boi';
    case NorgesBank = 'nob';
    case CentralBankOfNigeria = 'cbn';
    case EuropeanCentralBank = 'ecb';
}

<?php

declare(strict_types=1);

namespace App\Wallet\Domain\ValueObject;

enum TransactionType: string
{
    case CREDIT = 'credit';
    case DEBIT = 'debit';
}

<?php

declare(strict_types=1);

namespace App\Wallet\Application\Query\GetTransactionHistory;

final readonly class GetTransactionHistoryQuery
{
    public function __construct(
        public string $walletId,
    ) {
    }
}

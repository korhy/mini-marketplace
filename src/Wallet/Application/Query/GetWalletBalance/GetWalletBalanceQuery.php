<?php

declare(strict_types=1);

namespace App\Wallet\Application\Query\GetWalletBalance;

final readonly class GetWalletBalanceQuery
{
    public function __construct(
        public string $walletId,
    ) {
    }
}

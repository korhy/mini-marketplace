<?php

declare(strict_types=1);

namespace App\Wallet\Application\Query\GetWalletBalance;

final readonly class GetWalletBalanceViewModel
{
    public function __construct(
        public string $id,
        public int $balance,
        public string $currency,
    ) {
    }
}

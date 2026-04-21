<?php

declare(strict_types=1);

namespace App\Wallet\Application\Command\CreditWallet;

final readonly class CreditWalletCommand
{
    public function __construct(
        public string $walletId,
        public int $amount,
        public string $currency,
    ) {
    }
}

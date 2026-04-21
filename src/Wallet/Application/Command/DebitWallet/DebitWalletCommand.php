<?php

declare(strict_types=1);

namespace App\Wallet\Application\Command\DebitWallet;

final readonly class DebitWalletCommand
{
    public function __construct(
        public string $walletId,
        public int $amount,
        public string $currency,
    ) {
    }
}

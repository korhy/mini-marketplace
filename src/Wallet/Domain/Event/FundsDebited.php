<?php

declare(strict_types=1);

namespace App\Wallet\Domain\Event;

use App\Shared\Domain\Events\DomainEvent;
use App\Shared\Domain\ValueObject\Money;
use App\Wallet\Domain\ValueObject\WalletId;

final class FundsDebited extends DomainEvent
{
    public function __construct(
        public readonly WalletId $walletId,
        public readonly Money $amount,
    ) {
        parent::__construct();
    }
}

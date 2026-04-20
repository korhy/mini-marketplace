<?php

declare(strict_types=1);

namespace App\Wallet\Domain\Exception;

final class InsufficientFundsException extends \DomainException
{
    public static function forWallet(string $walletId): self
    {
        return new self(sprintf('Wallet with id %s has insufficient funds.', $walletId));
    }
}
